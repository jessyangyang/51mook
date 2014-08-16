<?php
/**
* CourseStudy DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class CourseStudy extends \local\db\ORM 
{
    public $table = 'course_study';

    public $fields = array(
        'csid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'csid'),
        'ccid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ccid'),
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
        return self::$instance ? self::$instance : new CourseStudy($key);
    }
}