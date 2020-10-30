<?php
/**
 * @filesource modules/inventory/models/inventory.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Inventory;

use Kotchasan\Database\Sql;

/**
 * module=inventory-write&tab=inventory
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลสำหรับใส่ลงในตาราง
     *
     * @param array $params
     *
     * @return \Kotchasan\Database\QueryBuilder
     */
    public static function toDataTable($params)
    {
        $where = array(
            array('C.product_id', $params['id']),
            Sql::create('(C.`order_id`=0 OR O.`status`=C.`status`)'),
        );
        if ($params['status'] != '') {
            $where[] = array('C.status', $params['status']);
        }
        if ($params['year'] > 0) {
            $where[] = array(Sql::YEAR('C.create_date'), $params['year']);
        }
        if ($params['month'] > 0) {
            $where[] = array(Sql::MONTH('C.create_date'), $params['month']);
        }

        return static::createQuery()
            ->select('C.create_date', 'C.order_id', 'O.order_no', 'C.quantity', 'C.price', 'C.vat', 'C.total', 'C.id', 'C.status')
            ->from('stock C')
            ->join('orders O', 'LEFT', array('O.id', 'C.order_id'))
            ->where($where);
    }
}
