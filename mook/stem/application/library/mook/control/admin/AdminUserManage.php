<?php
/**
* Roles DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\admin;

use mook\rest\RegisterRest;
use \lib\dao\RolesControl;
use \lib\dao\ImageControl;
use \lib\models\Members;

class AdminUserManage
{
	// Private Objects
    protected $rolesControl;
	protected $members;


	/**
     * Instance construct
     */
    function __construct() {
        $this->rolesControl = new RolesControl();
        $this->members = new Members();
    }


    /**
    * Class destructor
    *
    * @return void
    * @TODO make sure elements in the destructor match the current class elements
    */
    function __destruct() {
        $this->rolesControl = NULL;
    }

    public function getUserForId($uid)
    {
        $table = $this->members->table;

        $list = $this->members->field("$table.id,$table.email,$table.username,$table.published,$table.role_id,mi.avatar_id as cover,mi.sex,mi.birthday,mi.phone,mi.conntry,mi.province,mi.city,mi.address,mi.ip,mi.last_ip,mi.last_dateline,ur.name as role_name")
                ->joinQuery('member_info as mi',"mi.id=$table.id")
                ->joinQuery('user_role as ur',"ur.id=$table.role_id")
                ->where("$table.id='$uid'")->order("$table.published")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover'] = ImageControl::getRelativeImage($value['cover']);
                }
            }
            return $list[0];
        }

        return false;
    }


    public function getUserRole($rid = false)
    {
    	if (!$rid) return false;


    }


    /**
     * [addUserRole description]
     * @param boolean $datas [description]
     */
    public function addUserRole($datas = false)
    {
    	if (!is_array($datas)) return false;

    	$id = $name = false;
    	$roles = array();

    	if (isset($datas['id']) and $datas['id']) $id = $datas['id'];
    	if (isset($datas['groupname']) and $datas['groupname']) $name = $datas['groupname'];
    	if (isset($datas['roles']) and $datas['roles']) $roles = $datas['roles'];

    	if ($id and $name) {

    		$urid = $this->rolesControl->addUserRole(array(
    			'id' => $id,
    			'name' => $name,
    			'published' => UPDATE_TIME));
    		if ($urid) {
    			$userPermission = $this->rolesControl->addRolePermission(array(
    				'urid' => $urid,
    				'permission' => implode(',',$roles),
    				'published' => UPDATE_TIME));
    			if ($userPermission) return $userPermission;
    		}
    		
    	}

    	return false;
    }

    /**
     * [updateUserRole description]
     * @param  boolean $datas [description]
     * @return [type]         [description]
     */
    public function updateUserRole($datas = false)
    {
    	if (!is_array($datas)) return false;

    	$id = $name = false;
    	$roles = array();

    	if (isset($datas['id']) and $datas['id']) $id = $datas['id'];
    	if (isset($datas['groupname']) and $datas['groupname']) $name = $datas['groupname'];
    	if (isset($datas['roles']) and $datas['roles']) $roles = $datas['roles'];

    	if ($id and $name) {

    		$urid = $this->rolesControl->updateUserRole($id,array(
    			'published' => UPDATE_TIME));

    		if (!$this->rolesControl->getRolePermissionForId($id)) {
                   return $userPermission = $this->rolesControl->addRolePermission(array(
                        'urid' => $id,
                        'permission' => implode(',',$roles),
                        'published' => UPDATE_TIME));
            }
            return $this->rolesControl->updateRolePermission($id, array(
                    'permission' => implode(',',$roles),
                    'published' => UPDATE_TIME));
    		
    	}

    	return false;
    }

    /**
     * [deleteUserRole description]
     * @param  [type] $rcid [description]
     * @return [type]       [description]
     */
    public function deleteUserRole($rcid)
    {
    	if (!$rcid) return false;

    	if ($this->rolesControl->deleteUserRoleForId($rcid)) {
    		return $this->rolesControl->deleteRolePermissionForId($rcid);
    	}

    	return false;
    }
}