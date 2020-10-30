<?php
/**
 * @filesource modules/inventory/controllers/home.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Home;

use Index\Home\Controller as Home;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * Controller สำหรับการแสดงผลหน้า Home.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * ฟังก์ชั่นสร้าง card.
     *
     * @param Request               $request
     * @param \Kotchasan\Collection $card
     * @param array                 $login
     */
    public static function addCard(Request $request, $card, $login)
    {
        $order_status = Language::get('ORDER_STATUS');
        $datas = \Inventory\Home\Model::getCardData($login);
        $url = 'index.php?module=inventory-orders&day='.date('d').'&month='.date('m').'&year='.date('Y');
        Home::renderCard($card, 'icon-billing', $order_status['OUT'], number_format($datas->sell), '{LNG_Sales today}', $url.'&status=OUT');
        Home::renderCard($card, 'icon-cart', $order_status['PO'], number_format($datas->purcashe_order), '{LNG_Waiting for payment}', $url.'&status=PO');
        Home::renderCard($card, 'icon-customer', '{LNG_Customer}', number_format($datas->customers), '{LNG_Customer list}', 'index.php?module=inventory-customers');
        Home::renderCard($card, 'icon-product', '{LNG_Inventory}', number_format($datas->products), '{LNG_List of} {LNG_Product}', 'index.php?module=inventory-setup&amp;sort=quantity%20asc');
    }

    /**
     * ฟังก์ชั่นสร้าง เมนูด่วน.
     *
     * @param Request               $request
     * @param \Kotchasan\Collection $card
     * @param array                 $login
     */
    public static function addMenu(Request $request, $menu, $login)
    {
        foreach (Language::get('ORDER_STATUS') as $k => $label) {
            Home::renderQuickMenu($menu, 'icon-plus', '{LNG_Add New} '.$label, 'index.php?module=inventory-order&amp;typ='.$k);
        }
    }
}
