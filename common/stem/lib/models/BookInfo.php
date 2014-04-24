<?php
/**
* BookInfo Model 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models;

class BookInfo extends \local\db\ORM 
{
    public $table = 'book_info';

    public $fields = array(
        'bid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'bid'),
        'subtitle' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'subtitle'),
        'oldtitle' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'oldtitle'),
        'translator' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'translator'),
        'price' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'price'),
        'apple_price' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'apple_price'),
        'download_path' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'download_path'),
        'tags' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'tags'),
        'copyright' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'copyright'),
        'wordcount' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'wordcount'),
        'proofreader' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'proofreader'),
        'designer' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'designer'),
        'dateline' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'dateline')
        );

    public $primaryKey = "bid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new BookInfo($key);
    }

}