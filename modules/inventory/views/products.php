<?php
/**
 * @filesource modules/inventory/views/products.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Products;

use Kotchasan\Currency;
use Kotchasan\DataTable;
use Kotchasan\Http\Request;

/**
 * module=inventory-products
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * @var object
     */
    private $categories;

    /**
     * ตารางรายการ สินค้า
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $params = array(
            'module' => 'inventory-products',
            'typ' => 'print',
            'category_id' => $request->request('category_id')->toInt(),
            'search' => $request->request('search')->topic(),
            'sort' => $request->request('sort')->topic(),
        );
        $this->categories = \Inventory\Category\Model::init();
        // URL สำหรับส่งให้ตาราง
        $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Inventory\Products\Model::toDataTable($params),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('inventoryProducts_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('inventoryProducts_sort', 'id desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id', 'unit'),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('product_no', 'topic'),
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' => array(
                array(
                    'name' => 'category_id',
                    'text' => '{LNG_Category}',
                    'options' => array(0 => '{LNG_all items}') + $this->categories->toSelect('category_id'),
                    'value' => $params['category_id'],
                ),
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'product_no' => array(
                    'text' => '{LNG_Product code}',
                    'sort' => 'product_no',
                ),
                'topic' => array(
                    'text' => '{LNG_Topic}',
                    'sort' => 'topic',
                ),
                'category_id' => array(
                    'text' => '{LNG_Category}',
                    'class' => 'center',
                ),
                'price' => array(
                    'text' => '{LNG_Unit price}',
                    'class' => 'center',
                    'sort' => 'price',
                ),
                'stock' => array(
                    'text' => '{LNG_Stock}',
                    'class' => 'center',
                    'sort' => 'stock',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'description' => array(
                    'class' => 'tablet',
                ),
                'price' => array(
                    'class' => 'right',
                ),
                'category_id' => array(
                    'class' => 'center',
                ),
                'stock' => array(
                    'class' => 'center',
                ),
            ),
        ));
        // save cookie
        setcookie('inventoryProducts_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('inventoryProducts_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML

        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        $item['price'] = Currency::format($item['price']);
        $item['category_id'] = $this->categories->get('category_id', $item['category_id']);
        $item['stock'] .= ' '.$this->categories->get('unit', $item['unit']);

        return $item;
    }
}
