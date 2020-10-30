<?php
/**
 * @filesource modules/inventory/models/autocomplete.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Autocomplete;

use Gcms\Login;
use Kotchasan\Http\Request;

/**
 * ค้นหา สำหรับ autocomplete
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * ค้นหา สำหรับ autocomplete
     * คืนค่าเป็น JSON
     *
     * @param Request $request
     */
    public function findCustomer(Request $request)
    {
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {
                $search = $request->post('name')->topic();
                $where = array();
                $select = array('id', 'name', 'email');
                $order = array();
                foreach (explode(',', $request->post('from', 'name,email')->filter('a-z,')) as $item) {
                    if ($item == 'name') {
                        if ($search != '') {
                            $where[] = array('name', 'LIKE', "%$search%");
                        }
                        $order[] = 'name';
                    }
                    if ($item == 'email') {
                        if ($search != '') {
                            $where[] = array('email', 'LIKE', "%$search%");
                        }
                        $order[] = 'email';
                    }
                    if ($item == 'phone') {
                        if ($search != '') {
                            $where[] = array('phone', 'LIKE', "$search%");
                        }
                        $select[] = 'phone';
                        $order[] = 'phone';
                    }
                    if ($item == 'company') {
                        if ($search != '') {
                            $where[] = array('company', 'LIKE', "$search%");
                        }
                        $select[] = 'company';
                        $order[] = 'company';
                    }
                    if ($item == 'discount') {
                        $select[] = 'discount';
                    }
                }
                $query = $this->db()->createQuery()
                    ->select($select)
                    ->from('customer')
                    ->order($order)
                    ->limit($request->post('count')->toInt())
                    ->toArray();
                if (!empty($where)) {
                    $query->andWhere($where, 'OR');
                }
                $result = $query->execute();
                // คืนค่า JSON
                if (!empty($result)) {
                    echo json_encode($query->execute());
                }
            } catch (\Kotchasan\InputItemException $e) {
            }
        }
    }

    /**
     * ค้นหาสินค้า สำหรับ autocomplete
     * คืนค่าเป็น JSON
     *
     * @param Request $request
     */
    public function findProduct(Request $request)
    {
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {
                $search = $request->post('name')->topic();
                $where = array();
                $order = array();
                foreach (explode(',', $request->post('from', 'product_no,topic')->filter('a-z_,')) as $item) {
                    if ($item == 'product_no') {
                        if ($search != '') {
                            $where[] = array('product_no', 'LIKE', "%$search%");
                        }
                        $order[] = 'product_no';
                    }
                    if ($item == 'topic') {
                        if ($search != '') {
                            $where[] = array('topic', 'LIKE', "%$search%");
                        }
                        $order[] = 'topic';
                    }
                }
                $query = $this->db()->createQuery()
                    ->select('id', 'product_no', 'topic')
                    ->from('inventory')
                    ->order($order)
                    ->limit($request->post('count')->toInt())
                    ->toArray();
                if (!empty($where)) {
                    $query->andWhere($where, 'OR');
                }
                $result = array();
                foreach ($query->execute() as $item) {
                    $result[$item['id']] = $item;
                }
                // คืนค่า JSON
                if (!empty($result)) {
                    echo json_encode($result);
                }
            } catch (\Kotchasan\InputItemException $e) {
            }
        }
    }
}
