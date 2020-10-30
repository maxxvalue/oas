<?php
/**
 * @filesource modules/inventory/models/stock.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Stock;

use Kotchasan\Database\Sql;

/**
 * ข้อมูล.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านรายการสินค้าในใบเสร็จ
     * ถ้าไมมีคืนค่ารายการว่าง 1 รายการ
     *
     * @param int $order_id
     * @param string $status
     *
     * @return array
     */
    public static function get($order_id, $status)
    {
        if ($order_id > 0) {
            $result = static::createQuery()
                ->select('S.id', 'S.quantity', 'S.price', 'S.vat', 'S.discount', 'S.product_id', 'S.topic', 'N.topic unit')
                ->from('stock S')
                ->join('inventory P', 'LEFT', array(array('P.id', 'S.product_id')))
                ->join('category N', 'LEFT', array(array('N.type', 'unit'), array('N.category_id', 'P.unit')))
                ->where(array(
                    array('S.order_id', $order_id),
                    array('S.status', $status),
                ))
                ->order('S.id')
                ->toArray()
                ->execute();
        }
        if (empty($result)) {
            // ถ้าไม่มีผลลัพท์ คืนค่ารายการเปล่าๆ 1 รายการ
            return array(
                0 => array(
                    'id' => 0,
                    'quantity' => 1,
                    'price' => 0,
                    'vat' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'product_id' => 0,
                    'topic' => '',
                ),
            );
        } else {
            return $result;
        }
    }

    /**
     * สรุปรายละเอียดของสินค้าคงคลัง (เข้า, ออก, คงเหลือ)
     * รายเดือน ตามปีที่เลือก
     *
     * @param int $id
     * @param int $year
     *
     * @return array
     */
    public static function monthlyReport($id, $year)
    {
        $model = new \Kotchasan\Model();
        $db = $model->db();
        $q1 = $db->createQuery()
            ->select(Sql::MONTH('S.create_date', 'm'), 'S.status', Sql::SUM('S.quantity', 'quantity'))
            ->from('stock S')
            ->join('orders O', 'LEFT', array('O.id', 'S.order_id'))
            ->where(array(
                array('S.product_id', $id),
                array(Sql::YEAR('S.create_date'), $year),
                Sql::create('(S.`order_id`=0 OR O.`status`=S.`status`)'),
            ))
            ->groupBy('m', 'S.status');
        $query = $db->createQuery()
            ->select(
                'm',
                Sql::create("SUM(IF(`status` IN ('".implode("','", self::$cfg->in_stock_status)."'), `quantity`, NULL)) AS `Buy`"),
                Sql::create("SUM(IF(`status` IN ('".implode("','", self::$cfg->out_stock_status)."'), `quantity`, NULL)) AS `Sell`")
            )
            ->from(array($q1, 'Q'))
            ->groupBy('m')
            ->toArray();
        $result = array();
        foreach ($query->execute() as $item) {
            $result['Sell'][$item['m']] = $item['Sell'];
            $result['Buy'][$item['m']] = $item['Buy'];
        }

        return $result;
    }

    /**
     * อ่านรายการปี ที่มีการทำรายการ สินค้าที่เลือก
     * สำหรับใส่ลงใน select.
     *
     * @param int $id
     *
     * @return array
     */
    public static function listYears($id)
    {
        $model = new \Kotchasan\Model();
        $query = $model->db()->createQuery()
            ->select(Sql::create('DISTINCT YEAR(`create_date`) AS `y`'))
            ->from('stock')
            ->where(array(
                array('product_id', $id),
            ))
            ->toArray();
        $year_offset = \Kotchasan\Language::get('YEAR_OFFSET');
        $result = array();
        foreach ($query->execute() as $item) {
            $result[$item['y']] = $item['y'] + $year_offset;
        }
        // ปีนี้
        $y = date('Y');
        $result[$y] = $y + $year_offset;

        return $result;
    }
}
