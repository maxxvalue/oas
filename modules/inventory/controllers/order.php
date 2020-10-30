<?php
/**
 * @filesource modules/inventory/controllers/order.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Order;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-order
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * เพิ่ม-แก้ไข Order
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ค่าที่ส่งมา
        $status = $request->request('status')->filter('A-Z');
        $id = $request->request('id')->toInt();
        // ข้อมูลที่ต้องการ
        $index = \Inventory\Order\Model::get($id, $status);
        // สามารถ ซื้อ/ขาย ได้
        if ($index && Login::checkPermission(Login::isMember(), 'can_inventory_order')) {
            $index->order_status = array();
            if (in_array($index->status, self::$cfg->buy_status)) {
                // ซื้อ
                $this->menu = 'buy';
                $sub_title = '{LNG_Buy}';
                $title = '{LNG_Order report} ';
                foreach (Language::get('ORDER_STATUS') as $k => $v) {
                    if (in_array($k, self::$cfg->buy_status)) {
                        $index->order_status[$k] = $v;
                    }
                }
            } else {
                // ขาย
                $this->menu = 'sell';
                $sub_title = '{LNG_Sell}';
                $title = '{LNG_Sales report} ';
                foreach (Language::get('ORDER_STATUS') as $k => $v) {
                    if (in_array($k, self::$cfg->sell_status)) {
                        $index->order_status[$k] = $v;
                    }
                }
                if (!isset($index->order_status[$index->status])) {
                    $index->status = self::$cfg->out_stock_status[0];
                }
            }
            $index->menu = $this->menu;
            // ข้อความ title bar
            $title = Language::get($id > 0 ? 'Edit' : 'Create');
            $this->title = $title.' '.$index->order_status[$index->status];
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><a href="index.php" class="icon-home">{LNG_Home}</a></li>');
            $ul->appendChild('<li><span>'.$sub_title.'</span></li>');
            $ul->appendChild('<li><span>'.$title.'</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-file">'.$this->title.'</h2>',
            ));
            // แสดงตาราง
            $section->appendChild(createClass('Inventory\Order\View')->render($index));
            // คืนค่า HTML

            return $section->render();
        }
        // 404

        return \Index\Error\Controller::execute($this);
    }
}
