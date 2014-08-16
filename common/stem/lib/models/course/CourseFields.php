<?php
/**
* CourseFields DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class CourseFields extends \local\db\ORM 
{
    public $table = 'course_fields';

    public $fields = array(
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'click' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'click'),
        'student' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'student'),
        'chapters' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'chapters')
        );

    public $primaryKey = "cid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new CourseFields($key);
    }
}