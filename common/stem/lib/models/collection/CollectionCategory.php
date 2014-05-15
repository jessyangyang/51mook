<?php
/**
* CollectionCategory DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\collection;

class CollectionCategory extends \local\db\ORM 
{
    public $table = 'collection_category';

    public $fields = array(
        'ccid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ccid'),
        'name' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'name'),
        'sort' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'sort'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "ccid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new CollectionCategory($key);
    }
}