<?php
/**
 * @filesource modules/index/models/category.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Category;

use Kotchasan\Database\Sql;

/**
 * คลาสสำหรับอ่านข้อมูลหมวดหมู่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * @var array
     */
    protected $datas = array();

    /**
     * อ่านรายชื่อหมวดหมู่จากฐานข้อมูลตามภาษาปัจจุบัน
     * สำหรับการแสดงผล
     *
     * @param string $type
     *
     * @return \static
     */
    public static function init($type)
    {
        $obj = new static();
        // อ่านรายชื่อตำแหน่งจากฐานข้อมูล
        foreach (self::generate($type) as $item) {
            $obj->datas[$item['category_id']] = $item['topic'];
        }

        return $obj;
    }

    /**
     * Query ข้อมูลหมวดหมู่จากฐานข้อมูล
     *
     * @param string $type
     *
     * @return array
     */
    public static function generate($type)
    {
        // Model
        $model = new static();
        // Query
        $query = $model->db()->createQuery()
            ->select('category_id', 'topic')
            ->from('category')
            ->where(array(
                array('type', $type),
            ))
            ->order('category_id')
            ->toArray()
            ->cacheOn();
        $result = array();
        foreach ($query->execute() as $item) {
            $result[$item['category_id']] = array(
                'category_id' => $item['category_id'],
                'topic' => $item['topic'],
            );
        }

        return $result;
    }

    /**
     * อ่านหมวดหมู่สำหรับใส่ลงใน DataTable
     * ถ้าไม่มีคืนค่าข้อมูลเปล่าๆ 1 แถว
     *
     * @param string $type
     *
     * @return array
     */
    public static function toDataTable($type)
    {
        // Query ข้อมูลหมวดหมู่จากฐานข้อมูล
        $result = self::generate($type);
        if (empty($result)) {
            $result = array(array('category_id' => 1, 'topic' => ''));
        }

        return $result;
    }

    /**
     * ลิสต์รายการหมวดหมู่
     * สำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelect()
    {
        return $this->datas;
    }

    /**
     * อ่านหมวดหมู่จาก $category_id
     * ไม่พบ คืนค่าว่าง
     *
     * @param int $category_id
     *
     * @return string
     */
    public function get($category_id)
    {
        return isset($this->datas[$category_id]) ? $this->datas[$category_id] : '';
    }

    /**
     * ฟังก์ชั่นอ่านหมวดหมู่ หรือ บันทึก ถ้าไม่มีหมวดหมู่
     *
     * @param string    $type
     * @param string $topic
     *
     * @return int คืนค่า category_id
     */
    protected static function check($type, $topic)
    {
        $topic = trim($topic);
        if ($topic == '') {
            return 0;
        } else {
            $search = static::createQuery()
                ->from('category')
                ->where(array(
                    array('type', $type),
                    array('topic', $topic),
                ))
                ->toArray()
                ->first('category_id');
            if ($search) {
                // มีหมวดหมู่อยู่แล้ว
                return $search['category_id'];
            } else {
                // ไม่มีหมวดหมู่ ตรวจสอบ category_id ใหม่
                $search = static::createQuery()
                    ->from('category')
                    ->where(array('type', $type))
                    ->toArray()
                    ->first(Sql::MAX('category_id', 'category_id'));
                $category_id = empty($search['category_id']) ? 1 : (1 + (int) $search['category_id']);
                // save
                $model = new static();
                $model->db()->insert($model->getTableName('category'), array(
                    'type' => $type,
                    'category_id' => $category_id,
                    'topic' => $topic,
                ));

                return $category_id;
            }
        }
    }
}
