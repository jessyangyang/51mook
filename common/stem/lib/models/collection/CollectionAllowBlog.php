<?php
/**
* CollectionAllowBlog DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\collection;

class CollectionAllowBlog extends \local\db\ORM 
{
    public $table = 'collection_allow_blog';

    public $fields = array(
        'cabid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cabid'),
        'name' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'name'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "cabid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new CollectionAllowBlog($key);
    }
}