<?php
/**
 * @filesource modules/inventory/models/search.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Search;

use Gcms\Login;
use Kotchasan\Http\Request;

/**
 * ค้นหาสินค้า.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * ค้นหาสินค้าจาก product_no
     * คืนค่าเป็น JSON เพียงรายการเดียว
     *
     * @param Request $request
     */
    public function fromProductno(Request $request)
    {
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {
                $product_no = $request->post('product_no')->topic();
                if ($product_no != '') {
                    $query = $this->db()->createQuery()
                        ->from('inventory P')
                        ->where(array(
                            array('P.product_no', $product_no),
                        ))
                        ->toArray();
                    if ($request->post('typ')->toString() == 'buy') {
                        $search = $query->first('P.id', 'P.product_no', 'P.topic', 'P.cost price', 'P.unit', 'P.vat');
                    } else {
                        $search = $query->first('P.id', 'P.product_no', 'P.topic', 'P.price', 'P.unit', 'P.vat');
                    }
                }
                // คืนค่า JSON
                if ($search) {
                    echo json_encode($search);
                }
            } catch (\Kotchasan\InputItemException $e) {
            }
        }
    }
}
