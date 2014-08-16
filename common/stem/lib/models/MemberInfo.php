<?php
/**
* Members Infomation Model 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models;

class MemberInfo extends \local\db\ORM 
{
    public $table = 'member_info';

    public $fields = array(
        'id' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'id'),
        'avatar_id' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'avatar_id'),
        'name' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'name'),
        'sex' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'sex'),
        'birthday' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'birthday'),
        'phone' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'phone'),
        'conntry' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'conntry'),
        'province' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'province'),
        'city' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'city'),
        'address' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'address'),
        'weiboid' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'weiboid'),
        'ip' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'ip'),
        'summary' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'summary'),
        'last_ip' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'last_ip'),
        'last_dateline' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'last_dateline')
        );

    public $primaryKey = "id";

    protected static $instance;

    /**
     * Instance Members
     * @param  boolean $id | the primaryKey
     * @return Object with self
     */
    public static function instance($id = false)
    {
        return self::$instance ? self::$instance : new MemberInfo($id);
    }

}