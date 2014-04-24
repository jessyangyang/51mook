<?php
/**
* BookFields DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models;

use \lib\models\Members;

class BookFields extends \local\db\ORM 
{
    public $table = 'book_fields';

    public $fields = array(
        'bid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'bid'),
        'uid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'uid'),
        'published' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'published'),
        'verified' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'verified'),
        'download_count' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'download_count'),
        'modified' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'modified time')
    );

    public $primaryKey = "bid";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new BookFields($key);
    }


    /**
     * [updateBookStatus description]
     * @param  [type] $bid   [description]
     * @param  [type] $state [description]
     * @return [Boolean]        [description]
     */
    public function updateBookStatus($bid, $published = false , $verified = false)
    {
        $bookfields = self::instance();
        $table = $bookfields->table;

        $status = array();
        if ($published > 0 and $published < 5) $status['published'] = $published;
        if ($verified > 0 and $published < 5) $status['verified'] = $verified;

        if ($bookfields->where("bid='$bid'")->update($status)) return true;

        return false;
    }
}