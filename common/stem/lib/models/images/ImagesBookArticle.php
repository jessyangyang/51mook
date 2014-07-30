<?php
/**
* Images DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\images;

class ImagesBookArticle extends \local\db\ORM 
{
    public $table = 'images_book_article';

    public $fields = array(
        'ibaid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'ibaid'),
        'bid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'bid'),
        'bmid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'bid'),
        'uid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'uid'),
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

    public $primaryKey = "ibaid";

    protected static $instance;

    /**
     * Instance self
     * 
     * @param String $key ,primary_key
     * @return Images Object
     */
    public static function instance($key = false)
    {
        return self::$instance ? self::$instance : new ImagesBookArticle($key);
    }
}