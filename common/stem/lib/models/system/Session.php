<?php
/**
* BookCategory DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\system;

class Session extends \local\db\ORM 
{
    public $table = 'session';

    public $fields = array(
        'session_id' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'session_id'),
        'session_expires' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'session_expires'),
        'session_data' => array(
            'type' => 'text',
            'default' => 0,
            'comment' => 'session_data')
        );

    public $primaryKey = "session_id";

    // Instance Self
    protected static $instance;


    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new Session($key);
    }
}