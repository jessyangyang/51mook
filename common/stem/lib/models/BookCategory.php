<?php
/**
* BookCategory DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models;

class BookCategory extends \local\db\ORM 
{
    public $table = 'book_category';

    public $fields = array(
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'name' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'name'),
        'sort' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'sort'),
        'pid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'pid'),
        'type' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'type'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "cid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new BookCategory($key);
    }

    /**
     * [getCategory description]
     * @return [type] [description]
     */
    public function getCategory()
    {
        $category = self::instance();
        $table = $this->table;

        $list = $category->field("$table.cid,$table.name, $table.sort, $table.dateline, i.path as cover")->joinQuery("images as i","$table.pid=i.pid")->limit(20)->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover'] = \lib\dao\ImageControl::getRelativeImage($value['cover']);
                }
            }
            
            $books->joinTables = array();
            return $list;
        }

        return "";
    }
}