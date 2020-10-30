<?php
/**
 * @filesource modules/inventory/views/write.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Inventory\Write;

use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-write
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มเพิ่ม/แก้ไข Inventory
     *
     * @param Request $request
     * @param object $index
     *
     * @return string
     */
    public function render(Request $request, $index)
    {
        $form = Html::create('form', array(
            'id' => 'product',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/inventory/model/write/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_Product}',
        ));
        $groups = $fieldset->add('groups');
        // product_no
        $groups->add('text', array(
            'id' => 'write_product_no',
            'itemClass' => 'width50',
            'labelClass' => 'g-input icon-number',
            'label' => '{LNG_Product code}/{LNG_Barcode}',
            'maxlength' => 150,
            'autofocus' => true,
            'value' => $index->product_no,
            'placeholder' => '{LNG_Leave empty for generate auto}',
        ));
        // topic
        $groups->add('text', array(
            'id' => 'write_topic',
            'itemClass' => 'width50',
            'labelClass' => 'g-input icon-product',
            'label' => '{LNG_Product name}/{LNG_Service}',
            'maxlength' => 150,
            'value' => $index->topic,
        ));
        // description
        $fieldset->add('text', array(
            'id' => 'write_description',
            'itemClass' => 'item',
            'labelClass' => 'g-input icon-edit',
            'label' => '{LNG_Description}',
            'maxlength' => 255,
            'value' => $index->description,
        ));
        $groups = $fieldset->add('groups');
        // category
        $groups->add('text', array(
            'id' => 'write_category',
            'itemClass' => 'width33',
            'labelClass' => 'g-input icon-category',
            'label' => '{LNG_Category}',
            'placeholder' => Language::replace('Fill some of the :name to find', array(':name' => '{LNG_Category}')),
            'value' => $index->category,
        ));
        // count_stock
        $groups->add('select', array(
            'id' => 'write_count_stock',
            'itemClass' => 'width33',
            'labelClass' => 'g-input icon-number',
            'label' => '{LNG_Type}',
            'options' => Language::get('COUNT_STOCK'),
            'value' => $index->count_stock,
        ));
        if ($index->id == 0) {
            // create_date
            $groups->add('date', array(
                'id' => 'write_create_date',
                'itemClass' => 'width33',
                'labelClass' => 'g-input icon-calendar',
                'label' => '{LNG_Transaction date}',
                'value' => date('Y-m-d'),
            ));
            // ใหม่
            $groups = $fieldset->add('groups', array(
                'comment' => '{LNG_No need to fill in the purchase price if the product is not counting stock}',
            ));
            // cost
            $groups->add('currency', array(
                'id' => 'write_cost',
                'itemClass' => 'width33',
                'labelClass' => 'g-input icon-money',
                'label' => '{LNG_Purchase price} ({LNG_Cost})',
            ));
            // stock
            $groups->add('number', array(
                'id' => 'write_stock',
                'itemClass' => 'width33'.($index->count_stock == 1 ? '' : ' hidden'),
                'labelClass' => 'g-input icon-number',
                'label' => '{LNG_Stock}',
            ));
            // buy_vat
            $groups->add('select', array(
                'id' => 'write_buy_vat',
                'itemClass' => 'width33',
                'labelClass' => 'g-input icon-money',
                'label' => '{LNG_VAT}',
                'options' => Language::get('TAX_STATUS'),
            ));
        } else {
            $groups = $fieldset->add('groups');
            // cost
            $groups->add('currency', array(
                'id' => 'write_cost',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-money',
                'label' => '{LNG_Purchase price} ({LNG_Cost})',
                'disabled' => true,
                'value' => $index->cost,
            ));
            if ($index->count_stock == 1) {
                // stock
                $groups->add('number', array(
                    'id' => 'write_stock',
                    'itemClass' => 'width50',
                    'labelClass' => 'g-input icon-number',
                    'label' => '{LNG_Stock}',
                    'disabled' => true,
                    'value' => $index->stock,
                ));
            }
        }
        $groups = $fieldset->add('groups');
        // price
        $groups->add('currency', array(
            'id' => 'write_price',
            'itemClass' => 'width33',
            'labelClass' => 'g-input icon-money',
            'label' => '{LNG_Selling price}',
            'value' => $index->price,
        ));
        // unit
        $groups->add('text', array(
            'id' => 'write_unit',
            'itemClass' => 'width33',
            'labelClass' => 'g-input icon-edit',
            'label' => '{LNG_Unit}',
            'placeholder' => Language::replace('Fill some of the :name to find', array(':name' => '{LNG_Unit}')),
            'value' => $index->unit,
        ));
        // vat
        $groups->add('select', array(
            'id' => 'write_vat',
            'itemClass' => 'width33',
            'labelClass' => 'g-input icon-money',
            'label' => '{LNG_VAT}',
            'options' => Language::get('TAX_STATUS'),
            'value' => $index->vat,
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}',
        ));
        if ($index->id == 0) {
            // save_and_create
            $fieldset->add('checkbox', array(
                'id' => 'save_and_create',
                'label' => '&nbsp;{LNG_Save and create new}',
                'value' => 1,
                'checked' => self::$request->cookie('save_and_create')->toInt() == 1,
            ));
        }
        // id
        $fieldset->add('hidden', array(
            'id' => 'write_id',
            'value' => $index->id,
        ));
        $fieldset->add('hidden', array(
            'id' => 'modal',
            'value' => MAIN_INIT,
        ));
        // Javascript
        $form->script('initInventoryWrite();');
        // คืนค่า HTML

        return $form->render();
    }
}
