<?php
/**
* Collection DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\collection;

class Collection extends \local\db\ORM 
{
    public $table = 'collection';

    public $fields = array(
        'ctid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ctid'),
        'cabid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cabid'),
        'title' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'title'),
        'author' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'author'),
        'ccid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ccid'),
        'url' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'url'),
        'blog_id' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'blog_id'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "ctid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new Collection($key);
    }
}