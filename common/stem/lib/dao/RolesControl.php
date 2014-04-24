<?php
/**
* RolesControl  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\dao;

use \lib\models\Members;
use \lib\models\users\Roles;
use \lib\models\users\RoleCategory;
use \lib\models\users\UserRole;
use \lib\models\users\UserRolePermission;

class RolesControl
{
    const VERSION = 1.0;

    // Role Type
    const ROLE_TYPE_ALL = 0;
    const ROLE_TYPE_CUSTOM = 1;

    // MEMBERS ROLES
    const MEMBER_SUPER_ADMIN = 1;
    const MEMBER_ADMIN = 2;
    const MEMBER_DEVELOPER = 3;

    const MEMBER_NORMAL_USER = 500;

    // ROLES OBJECTS
    private $roles;
    private $roleCategory;
    private $userRole;
    private $userRolePermission;

    /**
     * Instance construct
     */
    function __construct() {
        $this->roles = Roles::instance();
        $this->roleCategory = RoleCategory::instance();
        $this->userRole = UserRole::instance();
        $this->userRolePermission = UserRolePermission::instance();
    }


    /**
    * Class destructor
    *
    * @return void
    * @TODO make sure elements in the destructor match the current class elements
    */
    function __destruct() {
        $this->roles = NULL;
        $this->roleCategory = NULL;
    }

    public function initRoles()
    {
        
    }

    /**
     * [getUserRolesList description]
     * @return [type] [description]
     */
    public function getUserRolesList()
    {
        $table = $this->userRole->table;

        $list = $this->userRole->order("$table.id")->fetchList();

        if (is_array($list)) return $list;

        return false;
    }

    public function getUserRoleForId($id)
    {
        return $this->userRole->where("id='$id'")->fetchRow();
    }

    public function getRoleCategoryForId($rcid)
    {
        return $this->roleCategory->where("rcid='$rcid'")->fetchRow();
    }

    public function getRolePermissionForId($urid)
    {
        return $this->userRolePermission->where("urid='$urid'")->fetchRow();
    }

    public function getUserPermissionForId($rid)
    {
        $table = $this->userRole->table;

        $list = $this->userRole->field("$table.id,$table.name,$table.published,ur.permission")
                ->joinQuery('user_role_permission as ur',"$table.id=ur.urid")
                ->where("$table.id='$rid'")->order("$table.id")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['permission']) and $value['permission']) {
                    $list[$key]['permission'] = explode(',', $value['permission']);
                }
            }
            return $list[0];
        }
        return false;
    }

    /**
     * [getAllRoles description]
     * @return [type] [description]
     */
    public function getAllRoles()
    {
        $table = $this->roleCategory->table;

        $categorys = $this->roleCategory->order("$table.sort")->fetchList();

        $table = $this->roles->table;

        $roles = $this->roles->order("$table.sort")->fetchList();

        $rolesgroup = array();

        if ($categorys) {
            foreach ($categorys as $key => $category) {
                $rolesgroup[$key]['name'] = $category['summary'];
                $rolesgroup[$key]['key'] = $category['name'];
                foreach ($roles as $key2 => $value) {
                    $controller = explode('_', $value['name']);
                    if ($category['name'] == $controller[0]) {
                        $rolesgroup[$key]['group'][] = $value;
                    }
                }
            }
        }

        return $rolesgroup;
    }

    /**
     * [addRoleCategory description]
     * @param array $fields [description]
     */
    public function addRoleCategory($fields = array())
    {
        if (!is_array($fields) or !$fields) return false;
        return $this->roleCategory->insert($fields);

    }

    /**
     * [updateRoleCategory description]
     * @param  [type] $rcid   [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function updateRoleCategory($rcid,$fields = array())
    {
        if(!is_array($fields) or isset($fields['rcid'])) return false;
        return $this->roleCategory->where("rcid='$rcid'")->update($fields);
    }

    /**
     * [deleteRoleCategoryForId description]
     * @param  boolean $rcid [description]
     * @return [type]        [description]
     */
    public function deleteRoleCategoryForId($rcid = false)
    {
        if(!$rcid) return false;
        return $this->roleCategory->where("rcid='$rcid'")->delete();
    }

     /**
     * [addUserRole description]
     * @param array $fields [description]
     */
    public function addUserRole($fields = array())
    {
        if (!is_array($fields) or !$fields) return false;
        return $this->userRole->insert($fields);

    }

    /**
     * [updateRoleCategory description]
     * @param  [type] $rcid   [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function updateUserRole($id,$fields = array())
    {
        if(!is_array($fields) or isset($fields['id'])) return false;
        return $this->userRole->where("id='$id'")->update($fields);
    }

    /**
     * [deleteRoleCategoryForId description]
     * @param  boolean $rcid [description]
     * @return [type]        [description]
     */
    public function deleteUserRoleForId($id = false)
    {
        if(!$id) return false;
        return $this->userRole->where("id='$id'")->delete();
    }

    /**
     * [addRolePermission description]
     * @param array $fields [description]
     */
    public function addRolePermission($fields = array())
    {
        if (!is_array($fields) or !$fields) return false;
        return $this->userRolePermission->insert($fields);
    }

    /**
     * [updateRolePermission description]
     * @param  boolean $urid [description]
     * @return [type]        [description]
     */
    public function updateRolePermission($urid,$fields = array())
    {
        if(!$urid) return false;
        return $this->userRolePermission->where("urid='$urid'")->update($fields);
    }

    /**
     * [deleteRolePermissionForId description]
     * @param  boolean $urid [description]
     * @return [type]        [description]
     */
    public function deleteRolePermissionForId($urid = false)
    {
        if(!$urid) return false;
        return $this->userRolePermission->where("urid='$urid'")->delete();
    }

}