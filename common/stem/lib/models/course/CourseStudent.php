<?php
/**
* CourseStudent DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class CourseStudent extends \local\db\ORM 
{
    public $table = 'course_student';

    public $fields = array(
        'csid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'csid'),
        'uid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'uid'),
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "csid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new CourseStudent($key);
    }
}