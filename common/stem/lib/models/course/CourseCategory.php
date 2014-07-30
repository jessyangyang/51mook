<?php
/**
* CourseCategory DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class CourseCategory extends \local\db\ORM 
{
    public $table = 'course_category';

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
        'cover' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cover'),
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
        return self::$instance ? self::$instance : new CourseCategory($key);
    }
}