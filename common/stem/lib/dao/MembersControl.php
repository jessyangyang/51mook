<?php
/**
* MembersControl  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\dao;

use \lib\models\Members;
use \lib\models\MemberInfo;
use \lib\models\MemberFields;
use \lib\models\OAuthAccessTokens;
use \lib\dao\ImageControl;
use \Yaf\Session;
use \Yaf\Registry;

class MembersControl 
{
    const ROLE_SUPER_ADMIN = 1;
    const ROLE_DEVELOPMENTER = 2;
    const ROLE_ADMIN = 3;

    const ROLE_GENERAL_EDITOR = 101;

    const ROLE_AUTHOR = 120;
    const ROLE_PROOFREADER = 121;

    const ROLE_NORMAL = 500;

    const ROLE_ANONYMITY = 1001;
    const ROLE_LIMITED = 1002;

    const IMAGE_TYPE = 2;
    const IMAGE_PATH = 'head';

    protected $members;
    protected $memberInfo;
    protected $oauthaccesstoken;
    protected $images;
    protected $session;

    /**
     * Instance construct
     */
    function __construct($uid = false) {
        $this->members = Members::instance($uid);
        $this->memberInfo = MemberInfo::instance();
        $this->oauthaccesstoken = OAuthAccessTokens::instance();
        $this->images = new ImageControl();
        $this->session = Session::getInstance();
    }

    /**
    * Class destructor
    *
    * @return void
    * @TODO make sure elements in the destructor match the current class elements
    */
    function __destruct() {
        $this->members = NULL;
        $this->memberInfo = NULL;
        $this->oauthaccesstoken = NULL;
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
    public function register($email,$username,$password,$imgUrl)
    {
        $email = addslashes($email);

        $arr = array(
            'email' => $email,
            'username' => addslashes($username),
            'password' => md5(trim($password)),
            'published' => time(),
            'role_id' => MembersControl::ROLE_NORMAL
        );
                
        if ($userId = $this->members->insert($arr)) {

            $avatarId = $this->images->saveImageFromUrl($imgUrl . ".jpeg", $userId, MembersControl::IMAGE_TYPE, MembersControl::IMAGE_PATH);

            $avatarId = $avatarId ? $avatarId : 0;

            $infoArr = array(
                'id' => $userId,
                'avatar_id' => $avatarId);

            if ($this->memberInfo->insert($infoArr)) {      
                $authToken = md5($userId.$email);
                $authArr = array(
                    'oauth_token' => $authToken,
                    'client_id' => $userId,
                    'user_id' => $userId,
                    'expires' => strtotime("next Monday"));
                $this->oauthaccesstoken->insert($authArr);

                $this->session->set('current_id',$userId);
                $this->session->set('authToken',$authToken);

                header("Auth-Token:".$authToken);
            }

            return true;
        }
        return false;
    }

    public static function getCurrentUser()
    {
        $session = \Yaf\Session::getInstance();
        if ($session->has('current_id')) {
            return Members::getByID($session->current_id);
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
     * [checkUser description]
     * @param  [type] $email    [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    public function checkUser($email,$password,$isRegistered = true)
    {
        $message = array();
        $message['title'] = false;

        if ($email == '' || $password == '')
        {
            $message['message'] = "邮箱或密码不能为空!";
        }
        else if (strlen($password) < 6)
        {
            $message['message'] = "密码长度需大于6!";
        }
        else if (!ereg("^[-a-zA-Z0-9_.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$",$email))
        {
            $message['message'] = "邮箱格式不正确!";
        }
        else if ($isRegistered and $this->isRegistered($email))
        {
            $message['message'] = "邮箱已注册!";
        }
        else
        {
            $message['title'] = true;
        }
        return $message;
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
        $wherearr = "email='" . $this->members->escapeString(trim($email)) . "' AND password='" . md5($this->members->escapeString($password)) . "'";
        $row = $this->members->where($wherearr)->fetchRow();

        if ($row) {
            $session = \Yaf\Session::getInstance();
            if ($state = $this->oauthaccesstoken->hasArrow(md5($row['id'].$email))) {
                $session->set('current_id',$state['user_id']);
                $session->set('authToken',$state['oauth_token']);
                return $state;
            }
            else
            {
                $authArr = array(
                        'oauth_token' => md5($row['id'].$email),
                        'client_id' => $row['id'],
                        'user_id' => $row['id'],
                        'expires' => strtotime("next Monday"));
                $this->oauthaccesstoken->insert($authArr);
                
                $session->set('current_id',$row['id']);
                $session->set('authToken',md5($row['id'].$email));

                return true;
            }

            header("Auth-Token:".$session->get('authToken'));
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
        $session = \Yaf\Session::getInstance();
        if ($session->has("current_id")) {
            $session->__unset('current_id');
            $session->__unset('authToken');
            return true;
        }
        return false;
    }


    public function getCoverForUid($uid)
    {

    }

    public function getMembersCount()
    {
        return $this->members->count("*");
    }


    public function getMembersList($option = array(),$limit = 10,$page = 1)
    {
        $sql = '';

        if (is_array($option) or $option)
        {
            $i = 1;
            $count = count($option);
            foreach ($option as $key => $value) {
                if($i == $count) $sql .= "$key='" . $value . "'";
                else $sql .= "$key='" . $value . "' AND ";
                $i ++;
            }
        }

        $offset = $page == 1 ? 0 : ($page - 1)*$limit; 
        $table = $this->members->table;

        $list = $this->members->field("$table.id,$table.email,$table.username,$table.published,$table.role_id,r.name as role_name,mi.ip,mi.last_ip,mi.avatar_id as cover,mi.last_dateline as dateline")
            ->joinQuery("user_role as r","$table.role_id=r.id")
            ->joinQuery('member_info as mi',"$table.id=mi.id")
            ->where($sql)->order("$table.id")->limit("$offset,$limit")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover'] = ImageControl::getRelativeImage($value['cover']);
                }
            }
            return $list;
        }

        return false;
    }
}