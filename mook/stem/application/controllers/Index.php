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

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display('index/index/index.html.twig');
    }

    public function loginAction()
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
        	$members = new MembersManage();
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
        $views->display('index/login/index.html.twig');
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