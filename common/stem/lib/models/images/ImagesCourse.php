<?php
/**
* ImagesCourse DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\images;

class ImagesCourse extends \local\db\ORM 
{
    public $table = 'images_course';

    public $fields = array(
        'icid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'icid'),
        'cid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'cid'),
        'uid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'uid'),
        'class' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'class'),
        'title' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'title'),
        'filename' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'filename'),
        'type' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'type'),
        'size' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'size'),
        'path' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'path'),
        'thumb' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'thumb'),
        'dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'dateline')
    );

    public $primaryKey = "icid";

    protected static $instance;

    /**
     * Instance self
     * 
     * @param String $key ,primary_key
     * @return Images Object
     */
    public static function instance($key = false)
    {
        return self::$instance ? self::$instance : new ImagesCourse($key);
    }
}