<?php
/**
 * @filesource modules/inventory/models/order.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Order;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-order
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูล Order
     *
     * @param int $id คืนค่ารายการใหม่, > คืนค่ารายการที่เลือก
     * @param string $status
     *
     * @return object|null ไม่พบคืนค่า null
     */
    public static function get($id, $status)
    {
        if ($id > 0) {
            return self::createQuery()
                ->from('orders O')
                ->join('customer U', 'LEFT', array('U.id', 'O.customer_id'))
                ->where(array('O.id', $id))
                ->first('O.*', 'U.name customer', 'U.company', 'U.branch', 'U.address', 'U.province', 'U.zipcode', 'U.country', 'U.phone', 'U.email', 'U.tax_id');
        } else {
            return (object) array(
                'id' => 0,
                'customer_id' => 0,
                'customer' => '',
                'order_no' => '',
                'order_date' => date('Y-m-d H:i:s'),
                'discount' => 0,
                'vat' => 0,
                'tax' => 0,
                'comment' => '',
                'status' => $status,
                'tax_status' => 0,
                'vat_status' => self::$request->cookie('vat_status')->toInt(),
                'discount_percent' => 0,
                'due_date' => date('Y-m-d'),
            );
        }
    }

    /**
     * ตรวจสอบ Stock
     *
     * @param array $products
     *
     * @return array
     */
    public static function checkStock($products)
    {
        $query = static::createQuery()
            ->select('id', 'stock')
            ->from('inventory')
            ->where(array(
                array('id', $products),
                array('count_stock', 1),
            ));
        $result = array();
        foreach ($query->execute() as $item) {
            $result[$item->id] = $item->stock;
        }

        return $result;
    }

    /**
     * บันทึกข้อมูลการสั่งซื้อ
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, สามารถ ซื้อ/ขาย ได้, ไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::checkPermission($login, 'can_inventory_order') && Login::notDemoMode($login)) {
                try {
                    $order = array(
                        'order_no' => $request->post('order_no')->topic(),
                        'customer_id' => $request->post('customer_id')->toInt(),
                        'comment' => $request->post('comment')->textarea(),
                        'order_date' => $request->post('order_date')->date(),
                        'due_date' => $request->post('due_date')->date(),
                        'discount_percent' => $request->post('discount_percent')->toDouble(),
                        'discount' => $request->post('total_discount')->toDouble(),
                        'tax' => $request->post('tax_total')->toDouble(),
                        'vat' => $request->post('vat_total')->toDouble(),
                        'total' => $request->post('amount')->toDouble(),
                        'vat_status' => $request->post('vat_status')->toInt(),
                        'tax_status' => $request->post('tax_status')->toInt(),
                        'status' => $request->post('status')->filter('A-Z'),
                    );
                    $order_id = $request->post('order_id')->toInt();
                    // ชื่อตาราง
                    $table_orders = $this->getTableName('orders');
                    $table_stock = $this->getTableName('stock');
                    // Database
                    $db = $this->db();
                    // ตรวจสอบรายการ order ที่เลือก
                    $orders = \Inventory\Order\Model::get($order_id, $order['status']);
                    if (!$orders) {
                        // ไม่พบข้อมูลที่แก้ไข
                        $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
                    } elseif (empty($order['customer_id']) && in_array($order['status'], self::$cfg->buy_status)) {
                        // ทำรายการซื้อ ไม่ได้เลือก Supplyer
                        $ret['ret_customer'] = 'this';
                    } else {
                        // สินค้าที่เลือก
                        $datas = array(
                            'quantity' => $request->post('quantity', array())->toDouble(),
                            'topic' => $request->post('topic', array())->topic(),
                            'price' => $request->post('price', array())->toDouble(),
                            'discount' => $request->post('discount', array())->toDouble(),
                            'total' => $request->post('total', array())->toDouble(),
                            'vat' => $request->post('vat', array())->toDouble(),
                            'id' => $request->post('id', array())->toInt(),
                        );
                        $productStock = array();
                        if ($order['status'] == 'OUT') {
                            // ขาย ตรวจสอบ Stock
                            foreach ($datas['id'] as $key => $value) {
                                $productStock[] = $value;
                            }
                            $productStock = self::checkStock($productStock);
                        }
                        // ตรวจสอบ Stock เดิม
                        $oldStock = array();
                        if ($order_id > 0) {
                            foreach ($db->select($table_stock, array('order_id', $order_id)) as $item) {
                                $oldStock[$item['product_id']] = array(
                                    'id' => $item['id'],
                                    'quantity' => $item['quantity'],
                                );
                                if (isset($productStock[$item['product_id']])) {
                                    $productStock[$item['product_id']] += $item['quantity'];
                                }
                            }
                        }
                        $newStock = array();
                        foreach ($datas['topic'] as $key => $value) {
                            $product_id = $datas['id'][$key];
                            $quantity = $datas['quantity'][$key];
                            if ($value == '') {
                                if (count($datas['topic']) == 1) {
                                    $ret['ret_product_no'] = 'this';
                                } else {
                                    $ret['ret_topic_'.$key] = 'Please fill in';
                                }
                            } elseif (isset($productStock[$product_id]) && $quantity > $productStock[$product_id]) {
                                $ret['ret_quantity_'.$key] = Language::replace('Not enough products, Remaining :stock', array(':stock' => $productStock[$product_id]));
                            } else {
                                $newStock[$product_id] = array(
                                    'quantity' => $quantity,
                                    'topic' => $datas['topic'][$key],
                                    'price' => $datas['price'][$key],
                                    'discount' => $datas['discount'][$key],
                                    'total' => $datas['total'][$key],
                                    'vat' => empty($datas['vat'][$key]) ? 0 : $datas['vat'][$key],
                                    'product_id' => $product_id,
                                    'member_id' => $login['id'],
                                    'create_date' => $order['order_date'],
                                    'status' => $order['status'],
                                );
                            }
                        }
                        if (empty($ret)) {
                            if (empty($newStock)) {
                                // ไม่ได้เลือกสินค้า
                                $ret['ret_product_no'] = 'this';
                            } else {
                                // save order
                                if ($order['order_no'] == '') {
                                    // สร้างเลข running number
                                    $order['order_no'] = \Inventory\Number\Model::get($order_id, $order['status'].'_NO', $table_orders, 'order_no');
                                } else {
                                    // ตรวจสอบ order_no ซ้ำ
                                    $search = $db->first($table_orders, array('order_no', $order['order_no']));
                                    if ($search !== false && $order_id != $search->id) {
                                        $ret['ret_order_no'] = Language::replace('This :name already exist', array(':name' => Language::get('Order No.')));
                                    }
                                }
                            }
                        }
                        if (empty($ret)) {
                            if ($order_id > 0) {
                                // แก้ไข
                                $db->update($table_orders, array('id', $order_id), $order);
                            } else {
                                // ใหม่
                                $order['member_id'] = $login['id'];
                                $order_id = $db->insert($table_orders, $order);
                            }
                            // ลบ Stock เก่า
                            $db->delete($table_stock, array('order_id', $order_id), 0);
                            // save Stock
                            foreach ($newStock as $save) {
                                if (isset($oldStock[$save['product_id']])) {
                                    $save['id'] = $oldStock[$save['product_id']]['id'];
                                }
                                $save['order_id'] = $order_id;
                                $db->insert($table_stock, $save);
                            }
                            // อัปเดต Stock
                            \Inventory\Fifo\Model::update(array_keys($newStock));
                            // คืนค่า
                            $ret['alert'] = Language::get('Saved successfully');
                            $save_and_create = $request->post('save_and_create')->toInt();
                            if ($save_and_create == 1) {
                                // reload
                                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'inventory-order', 'status' => $order['status'], 'id' => null));
                            } else {
                                // กลับไปหน้ารวมรายการ Order
                                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'inventory-orders', 'status' => $order['status'], 'id' => null));
                            }
                            // save cookie
                            setcookie('save_and_create', $save_and_create, time() + 2592000, '/', HOST, HTTPS, true);
                            setcookie('vat_status', $order['vat_status'], time() + 2592000, '/', HOST, HTTPS, true);
                            // เคลียร์
                            $request->removeToken();
                        }
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}
