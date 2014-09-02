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
use \mook\common\Session as CustomSession;
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
        CustomSession::instance();
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
    public function register($email,$username,$password,$imgUrl = false,$is_session = true)
    {
        $email = addslashes($email);

        $arr = array(
            'email' => $email,
            'username' => $this->members->escapeString($username),
            'password' => md5($this->members->escapeString($password)),
            'published' => UPDATE_TIME,
            'role_id' => self::ROLE_NORMAL
        );
            
        if ($this->isRegistered($email)) return false;

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

            $app = array();

            if ($imgUrl and $avatarId) 
            {
                $images = new ImagesManage();
                $cover = $images->getImagesMemberForID($avatarId);
                if ($cover) {
                    $app['cover_small'] = ImagesManage::getRealCoverSize($cover['path']);
                    $app['cover_medium'] = ImagesManage::getRealCoverSize($cover['path'],"medium");
                    $app['cover'] = ImagesManage::getRelativeImage($cover['path']);
                }
            }


            if ($is_session) {
                $app['uid'] = $userId;
                $app['email'] = $email;
                $app['username'] = $username;
                $app['cover'] = false;
                $app['role_id'] = self::ROLE_NORMAL;
                $app['permission'] = $permission;

                $_SESSION['app'] = $app;
            }

            return true;
        }
        return false;
    }

    public function getCurrentMember()
    {
        if (!isset($_SESSION['app'])) return false;

        $table = $this->members->table;

        $list = $this->members->field("$table.id,$table.email,$table.username,$table.published,$table.role_id,r.permission,mi.avatar_id as cover, mi.summary")
            ->joinQuery("user_role_permission as r","$table.role_id=r.urid")
            ->joinQuery("member_info as mi","mi.id=$table.id")
            ->joinQuery("images_member as im","im.imid=mi.avatar_id")
            ->order("$table.published DESC")->limit("1")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover_small'] = ImagesManage::getRealCoverSize($value['cover']);
                    $list[$key]['cover_medium'] = ImagesManage::getRealCoverSize($value['cover'],"medium");
                    $list[$key]['cover'] = ImagesManage::getRelativeImage($value['cover']);
                }
            }
            return $list[0];
        }

        return false;
    }

    public function getCurrentSession()
    {
        if (isset($_SESSION['app'])) {
            return $_SESSION['app'];
        }
    	return false;
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

                $info_table = $this->memberInfo->table;
        		$info = $this->memberInfo->field("$info_table.id, $info_table.avatar_id, im.path as cover")
                            ->joinQuery("images_member as im","im.imid=$info_table.avatar_id")
                            ->where("$info_table.id='". $row['id']."'")
                            ->order("$info_table.last_dateline DESC")->limit("1")->fetchList();

        		$app = array(
	            	'uid'=> $row['id'],
	            	'email' => $row['email'],
	            	'username' => $row['username'],
	            	'cover' => false,
                    'role_id' => $row['role_id'],
	            	'permission' => $permission);

        		$infoArr = array(
	                'last_ip' => Registry::get('common')->ip(),
	                'last_dateline' => UPDATE_TIME);

	            $this->memberInfo->where("id='". $row['id'] ."'")->update($infoArr);

        		if (is_array($info))
                {
                    foreach ($info as $key => $value) {
                        if (isset($value['avatar_id']) and $value['avatar_id']) {
                            $app['cover_small'] = ImagesManage::getRealCoverSize($value['cover']);
                            $app['cover_medium'] = ImagesManage::getRealCoverSize($value['cover'],"medium");
                            $app['cover'] = ImagesManage::getRelativeImage($value['cover']);
                        }
                    }
                }

                $_SESSION['app'] = $app;
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
        if (isset($_SESSION['app'])) {
            unset($_SESSION['app']);
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