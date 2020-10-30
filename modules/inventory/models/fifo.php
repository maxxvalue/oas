<?php
/**
 * @filesource modules/inventory/models/fifo.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Fifo;

/**
 * คลาสสำหรับจัดการสินค้าแบบ FIFO
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อัปเดต stock และ ราคา
     *
     * @param int|array $products Product ID
     */
    public static function update($products)
    {
        // Model
        $model = new static;
        // Database
        $db = $model->db();
        // ตรวจสอบ stock แบบ FIFO
        $query = $db->createQuery()
            ->select('S.id', 'S.product_id', 'S.status', 'S.quantity', 'S.used', 'S.price')
            ->from('inventory V')
            ->join('stock S', 'INNER', array('S.product_id', 'V.id'))
            ->where(array(
                array('S.product_id', $products),
                array('S.status', array_merge(self::$cfg->in_stock_status, self::$cfg->out_stock_status)),
            ))
            ->order('S.id');
        $order = array();
        $used = array();
        foreach ($query->execute() as $item) {
            if (in_array($item->status, self::$cfg->in_stock_status)) {
                // In Stock
                $order[$item->product_id][$item->id]['quantity'] = $item->quantity;
                $order[$item->product_id][$item->id]['price'] = $item->price;
            } elseif (isset($used[$item->product_id])) {
                // Out Stock
                $used[$item->product_id] += $item->quantity;
            } else {
                // Out Stock (first)
                $used[$item->product_id] = $item->quantity;
            }
        }
        $product = array();
        foreach ($order as $product_id => $items) {
            if (!empty($items)) {
                foreach ($items as $stock_id => $item) {
                    if (isset($product[$product_id]) && $product[$product_id]['cost'] === null) {
                        $product[$product_id]['cost'] = $item['price'];
                    }
                    if (isset($used[$product_id])) {
                        if ($item['quantity'] < $used[$product_id]) {
                            $order[$product_id][$stock_id]['used'] = $item['quantity'];
                            $used[$product_id] -= $item['quantity'];
                        } elseif ($used[$product_id] > 0) {
                            $order[$product_id][$stock_id]['used'] = $used[$product_id];
                            $product[$product_id] = array(
                                'cost' => $item['price'],
                                'stock' => $item['quantity'] - $used[$product_id],
                            );
                            $used[$product_id] = 0;
                        } else {
                            $order[$product_id][$stock_id]['used'] = 0;
                            if (isset($product[$product_id])) {
                                $product[$product_id]['stock'] += $item['quantity'];
                            } else {
                                $product[$product_id]['stock'] = $item['quantity'];
                            }
                        }
                        if ($used[$product_id] == 0) {
                            $product[$product_id]['cost'] = null;
                        }
                    } elseif (isset($product[$product_id])) {
                        $product[$product_id]['stock'] += $item['quantity'];
                    } else {
                        $product[$product_id] = array(
                            'cost' => $item['price'],
                            'stock' => $item['quantity'],
                        );
                    }
                }
                if (isset($product[$product_id]) && $product[$product_id]['cost'] === null) {
                    $product[$product_id]['cost'] = $item['price'];
                }
            }
        }
        // อัปเดต product
        $table = $model->getTableName('inventory');
        foreach ($product as $id => $item) {
            if (empty($item['stock'])) {
                unset($item['cost']);
            }
            $db->update($table, $id, $item);
        }
        // อัปเดต stock
        $table_stock = $model->getTableName('stock');
        foreach ($order as $product_id => $items) {
            foreach ($items as $stock_id => $item) {
                if (isset($item['used'])) {
                    $db->update($table_stock, $stock_id, array('used' => $item['used']));
                }
            }
        }
    }
}
