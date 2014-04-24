<?php
/**
* UserRolePermission Model 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\models\users;

class UserRolePermission extends \local\db\ORM 
{
    public $table = 'user_role_permission';

    public $fields = array(
        'urid' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'urid'),
        'permission' => array(
            'type' => 'varchar',
            'default' => 0,
            'comment' => 'permission'),
        'published' => array(
            'type' => 'int',
            'default' => 0,
            'comment' => 'published')
        );

    public $primaryKey = "urid";

    // Instance Self
    protected static $instance;

    /**
     * Instance 
     *
     */
    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new UserRolePermission($key);
    }

}