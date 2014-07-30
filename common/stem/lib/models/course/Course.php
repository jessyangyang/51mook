<?php
/**
* Course DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class Course extends \local\db\ORM 
{
    public $table = 'course';

    public $fields = array(
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'title' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'title'),
        'uid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'uid'),
        'ccid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ccid'),
        'cover' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cover'),
        'private' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'private'),
        'verified' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'verified'),
        'published' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'published'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline'),
        'modified' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'modified'),
        'tags' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'tags'),
        'price' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'price'),
        'summary' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'summary')
        );

    public $primaryKey = "cid";

    // Instance Self
    protected static $instance;

    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new Course($key);
    }
}