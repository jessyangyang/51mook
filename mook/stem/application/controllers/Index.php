<?php
/**
 * Index Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \mook\control\index\MembersManage;
use \mook\control\admin\AdminCourseManage;
use \mool\control\pagesControl;
use \local\rest\Restful;
use \Yaf\Registry;

class IndexController extends \Yaf\Controller_Abstract 
{

    public function indexAction() 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        $display = "index/index/index.html.twig";

        $controlControl = AdminCourseManage::instance();


        if ($app) {
           $display = "index/index/play.html.twig";
        }
        else
        {
            $course = $controlControl->getCourseList(array("course.verified" => 3,"course.published" => 4),16,1);
            $views->assign('courses',$course);
        }

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display($display);
    }

    public function usersAction($name = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $member = array();
        
        preg_match("/^[a-zA-Z0-9]+/", $name , $matches);

        if (!$matches)
        {
            header('Location: /');
            exit();
        }

        $name = $matches[0];

        $member = $members->getMemberForName(trim($name));

        if ($app and strnatcasecmp($name,$app['username'])) {
            $member = $members->getCurrentMember();
        }
        
        if (!$member) {
            header('Location: /');
            exit();
        }

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->assign('user',$member);
        $views->display("index/index/users.html.twig");

    }

    public function settingAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        if (!$app) {
            header('Location: /');
            exit();
        }

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display("index/index/settings.html.twig");
    }

    public function loginAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();


        if ($app) {
        	header('Location: /');
            exit();
        }


        if ($data->isPost()) {
        	if ($uid = $members->login($data->getPost('email'),$data->getPost('password')))
        	{
        		header('Location: /');
            	exit();
        	}
        	else
        	{
        		$views->assign('error',array('code'=> 1, 'message' => '帐号或密码不正确'));
        	}
        }

        $views->assign('title',"mook");
        $views->display('index/auth/login.html.twig');
    }

    public function registerAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        if ($app) {
        	header('Location: /');
            exit();
        }

        if ($data->isPost()) {
        	$username = explode('@',$data->getPost('email'));
        	if ($uid = $members->register($data->getPost('email'),$username[0],$data->getPost('password'),false)) {
        		header('Location: /');
            	exit();
        	}
        }

        $views->assign('isLoginEnabled',true);
        $views->assign('title',"mook");
        $views->display('index/register/index.html.twig');
    }

    public function registerCheckAction()
    {
        $rest = Restful::instance();
        $data = $this->getRequest();

        $success = true;
        $message = "";

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        if ($app) {
            header('Location: /');
            exit();
        }

        if ($email = $data->getQuery('value') and $members->isRegistered($data->getQuery('value'))) {
            $success = false;
            $message = "已注册.";
        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }

    public function logoutAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        if ($members->logout()) {
        	header('Location: /login');
        }
        exit();
    }

    public function resetPasswordAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();
    }

}

?>