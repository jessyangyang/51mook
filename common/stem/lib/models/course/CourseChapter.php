<?php
/**
* CourseChapter DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\course;

class CourseChapter extends \local\db\ORM 
{
    public $table = 'course_chapter';

    public $fields = array(
        'ccid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ccid'),
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'title' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'title'),
        'type' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'type'),
        'sort' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'sort'),
        'summary' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'summary'),
        'url' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'url'),
        'wordcount' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'wordcount'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline'),
        'modified' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'modified'),
        'body' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'body')
    );

    public $primaryKey = "ccid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new CourseChapter($key);
    }
}