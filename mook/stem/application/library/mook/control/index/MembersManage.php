<?php
/**
* MembersManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\index;

use \lib\dao\MembersControl;
use \mook\control\common\ImagesManage;
use \lib\dao\RolesControl;
use \Yaf\Registry;

class MembersManage extends MembersControl 
{
    protected static $instance;

    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new MembersManage();
        }
        return self::$instance;
    }

    /**
     * Instance construct
     */
    function __construct($uid = false) {
        parent::__construct();
        $this->images = new ImagesManage();
    }

	/**
     * Register User 
     *
     * @param String , $email
     * @param String , $username
     * @param String , $password
     * @param String , $img , image url
     * @return Boolean
     */
    public function register($email,$username,$password,$imgUrl = false)
    {
        $email = addslashes($email);

        $arr = array(
            'email' => $email,
            'username' => addslashes($username),
            'password' => md5($this->members->escapeString($password)),
            'published' => UPDATE_TIME,
            'role_id' => self::ROLE_NORMAL
        );
        if ($this->isRegistered($email) or $this->getCurrentSession()) return false;

        if ($userId = $this->members->insert($arr)) {

        	$roles = new RolesControl();
        	$role = $roles->getRolePermissionForId(self::ROLE_NORMAL);
        	$permission = $role ? $role['permission'] : false; 

            $avatarId = 0;

            if ($imgUrl) {
                $coverpath = $this->images->saveWebImageToLocal($imgUrl, $userId, 'head');
                if ($coverpath) $avatarId = $this->images->saveImageMemberFromPath($coverpath,$userId);
            }


            $infoArr = array(
                'id' => $userId,
                'avatar_id' => $avatarId,
                'ip' => Registry::get('common')->ip());

            $this->memberInfo->insert($infoArr);

            $app = array(
            	'uid'=> $userId,
            	'email' => $email,
            	'username' => $username,
            	'cover' => false,
            	'super' => false,
                'role_id' => self::ROLE_NORMAL,
            	'permission' => $permission);

            if ($avatarId) $app['cover'] = ImagesManage::getRelativeImage($avatarId);

            $this->session->set('app',$app);

            return true;
        }
        return false;
    }

    public static function getCurrentUser()
    {
        if (!$this->session->has("app")) return false;

        $table = $this->members->table;

        $list = $this->members->field("$table.id,$table.email,$table.username,$table.published,$table.role_id,r.permission")
            ->joinQuery("user_role_permission as r","$table.role_id=r.urid")
            ->where($sql)->order("$table.published")->fetchList();

        if (is_array($list)) {
        	$info = $this->memberInfo->where("id='". $list[0]['id']."'")->fetchRow();
            if (isset($info['avatar_id']) and $info['avatar_id']) $list[0]['cover'] = ImagesManage::getRelativeImage($info['avatar_id']);
            else $list[0]['cover'] = false;
            return $list[0];
        }

        return false;
    }

    public function getCurrentSession()
    {
    	return $this->session->get('app');
    }

    /**
     * Check register with the user.
     * @param  [type]  $email [description]
     * @return boolean | int  return primary_key ,or return false
     */
    public function isRegistered($email)
    {
        $email = $this->members->escapeString($email);
        if ($data = $this->members->where("email='" . $email ."'")->fetchRow()) return $data;
        return false;
    }

    /**
     * Login
     *
     * @param String ,$email
     * @param String ,$password
     * @return Boolean or Array
     */
    public function login($email,$password)
    {
        if ($this->getCurrentSession()) return false;

        $wherearr = "email='" . $this->members->escapeString(trim($email)) . "' AND password='" . md5($this->members->escapeString($password)) . "'";
        $row = $this->members->field("id,email,username,role_id,published")->where($wherearr)->fetchRow();
        if ($row) {
            if ($user = $this->getCurrentSession()) {
                if($user['uid'] == $row['id']) return false;
            }
            else
            {
            	$roles = new RolesControl();
                $role = $roles->getRolePermissionForId($row['role_id']);
                $permission = $role ? $role['permission'] : false; 

        		$info = $this->memberInfo->where("id='". $row['id']."'")->fetchRow();

        		$app = array(
	            	'uid'=> $row['id'],
	            	'email' => $row['email'],
	            	'username' => $row['username'],
	            	'cover' => false,
 	            	'super' => false,
                    'role_id' => $row['role_id'],
	            	'permission' => $permission);

        		$infoArr = array(
	                'last_ip' => Registry::get('common')->ip(),
	                'last_dateline' => UPDATE_TIME);

	            $this->memberInfo->where("id='". $row['id'] ."'")->update($infoArr);

        		if (isset($info['avatar_id']) and $info['avatar_id']) $app['cover'] = ImagesManage::getRelativeImage($info['avatar_id']);

                if ($row['role_id'] <= 3) $app['super'] = true;

                $this->session->set('app',$app);
                return $row['id'];
            }
        }
        return false;
    }

    /**
     * Logout
     *
     * @return Boolean , if unset session ,return true.
     */
    public function logout()
    {
        if ($this->session->has("app")) {
            $this->session->__unset('app');
            return true;
        }
        return false;
    }


    /**
     * [updateMembers description]
     * @param  [type] $uid    [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function updateMembers($uid, $fields = array())
    {
        if (!$uid and !is_array($fields)) return false;

        return $this->members->where("id='$uid'")->update($fields);
    }
}