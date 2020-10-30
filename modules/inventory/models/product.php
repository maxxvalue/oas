<?php
/**
 * @filesource modules/inventory/models/product.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Product;

/**
 * เพิ่ม/แก้ไข ข้อมูล Inventory
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{
    /**
     * เพิ่มสินค้าใหม่
     *
     * @param array $save
     *
     * @return int
     */
    public static function create($save)
    {
        // product
        $product = array(
            'topic' => $save['topic'],
            'product_no' => $save['product_no'],
            'price' => $save['price'],
            'create_date' => isset($save['create_date']) ? $save['create_date'] : date('Y-m-d H:i:s'),
            'description' => isset($save['description']) ? $save['description'] : '',
            'cost' => isset($save['cost']) ? $save['cost'] : 0,
            'vat' => isset($save['vat']) ? $save['vat'] : 0,
            'count_stock' => isset($save['count_stock']) ? $save['count_stock'] : 1,
            'stock' => empty($save['stock']) ? 0 : $save['stock'],
        );
        // หมวดหมู่สินค้า
        if (isset($save['category'])) {
            $product['category_id'] = \Inventory\Category\Model::save('category_id', $save['category']);
        }
        // หน่วยสินค้า
        if (isset($save['unit'])) {
            $product['unit'] = \Inventory\Category\Model::save('unit', $save['unit']);
        }
        // Model
        $model = new \Kotchasan\Model;
        // save product
        $product_id = $model->db()->insert($model->getTableName('inventory'), $product);
        if ($product['stock'] > 0) {
            // stock
            $inventory = array(
                'order_id' => 0,
                'member_id' => $save['member_id'],
                'product_id' => $product_id,
                'status' => 'IN',
                'create_date' => isset($save['create_date']) ? $save['create_date'].date(' H:i:s') : $product['create_date'],
                'topic' => '',
                'quantity' => $product['stock'],
                'used' => 0,
                'price' => $product['cost'],
                'vat' => 0,
                'total' => $product['cost'] * $product['stock'],
            );
            if (!empty($save['buy_vat'])) {
                if ($save['buy_vat'] == 1) {
                    // ราคาสินค้าไม่รวม vat
                    $inventory['vat'] = (float) number_format(\Kotchasan\Currency::calcVat($inventory['total'], self::$cfg->vat, true), 2);
                } else {
                    // ราคาสินค้ารวม vat
                    $inventory['vat'] = (float) number_format(\Kotchasan\Currency::calcVat($inventory['total'], self::$cfg->vat, false), 2);
                    $inventory['total'] -= $inventory['vat'];
                }
            }
            // บันทึก
            $model->db()->insert($model->getTableName('stock'), $inventory);
        }

        return $product_id;
    }

    /**
     * อัปเดตข้อมูลสินค้าและ Stock
     *
     * @param object $src
     * @param array $save
     *
     * @return int
     */
    public static function update($src, $save)
    {
        $columns = array(
            'product_no', 'topic', 'price', 'description', 'vat',
            'count_stock', 'cost', 'category', 'unit',
        );
        $product = array();
        foreach ($save as $key => $value) {
            if ($key == 'category') {
                $product['category_id'] = \Inventory\Category\Model::save('category_id', $value);
            } elseif ($key == 'unit') {
                $product['unit'] = \Inventory\Category\Model::save('unit', $value);
            } elseif (in_array($key, $columns)) {
                $product[$key] = $value;
            }
        }
        // Model
        $model = new \Kotchasan\Model;
        // save product
        $model->db()->update($model->getTableName('inventory'), $src->id, $product);
        // อัปเดต Stock
        if (isset($save['stock']) && $src->stock != $save['stock']) {
            $inventory = array(
                'order_id' => 0,
                'product_id' => $src->id,
                'create_date' => isset($product['create_date']) ? $product['create_date'] : date('Y-m-d H:i:s'),
                'member_id' => $save['member_id'],
                'topic' => '',
                'used' => 0,
                'vat' => 0,
            );
            if ($src->stock > $save['stock']) {
                // ขาย
                $inventory['price'] = empty($product['price']) ? $src->price : $product['price'];
                $inventory['status'] = 'OUT';
                $inventory['quantity'] = $src->stock - $save['stock'];
            } elseif ($src->stock < $save['stock']) {
                // ซื้อ
                $inventory['price'] = empty($product['cost']) ? $src->cost : $product['cost'];
                $inventory['status'] = 'IN';
                $inventory['quantity'] = $save['stock'] - $src->stock;
            }
            $inventory['total'] = $inventory['price'] * $inventory['quantity'];
            // save Order
            $model->db()->insert($model->getTableName('stock'), $inventory);
            // update Stock
            \Inventory\Fifo\Model::update($src->id);
        }
    }
}
