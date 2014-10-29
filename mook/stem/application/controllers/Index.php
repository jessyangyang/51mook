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

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $display = "index/index/index.html.twig";

        $controlControl = AdminCourseManage::instance();


        if ($app) {
           // $display = "index/index/play.html.twig";
            header('Location: /u/' . $app['username']);
            exit();
        }
        else
        {
            $course = $controlControl->getCourseList(array("course.verified" => 3,"course.published" => 4),16,1);
            $views->assign('courses',$course);
        }

        $views->assign('title',"墨客");
        $views->assign('app',$app);
        $views->display($display);
    }

    public function flowsAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $controlControl = AdminCourseManage::instance();

        $categories = $controlControl->getCategory();

        $page = $data->getQuery('page') ? $data->getQuery('page') - 1 : 0;
        $category = $data->getQuery('category') ? $data->getQuery('category') : '';

        if ($category and $page) {
            $datas = $controlControl->getCourseGroup(array("course.cid" => $category, "course.verified" => 3,"course.published" => 4),40,$page);
        }

        $course = $controlControl->getCourseGroup(array("course.verified" => 3,"course.published" => 4),160,1);
        $news = $controlControl->getCourseList(array("course.verified" => 3,"course.published" => 4),30,1);
        $hots = $controlControl->getCourseList(array("course.verified" => 3,"course.published" => 4),30,1,"cf.student DESC");

        $views->assign('title',"知识库");
        $views->assign('app',$app);
        $views->assign('courses',$course);
        $views->assign('categories', $categories);
        $views->assign('news', $news);
        $views->assign('hots', $hots);
        $views->display("index/index/flows.html.twig");
    }

    public function searchAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        $result = false;

        if ($data->isGet() and $data->getQuery('q')) {
            $controlControl = AdminCourseManage::instance();
            $result = $controlControl->searchCourse($data->getQuery('q'));
        }

        $views->assign('title',$data->getQuery('q'));
        $views->assign('app',$app);
        $views->assign('result',$result);
        $views->display("index/index/search.html.twig");
    }

    public function usersAction($name = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        $controlControl = AdminCourseManage::instance();

        $owner = $member = false;
        
        preg_match("/^[a-zA-Z0-9]+/", $name , $matches);
        $name = $matches[0];
        $member = $members->getMemberForName(trim($name));

        if (!$matches or !$member)
        {
            header('Location: /');
            exit();
        }

        if ($app and $app['username'] == $name) $owner = true;

        $course = $controlControl->getCourseList(array('course.uid' => $member['id'],'course.verified' => 3,'course.published' => 4), 100, 1);

        $views->assign('title',$member['username']);
        $views->assign('app',$app);
        $views->assign('owner',$owner);
        $views->assign('courses',$course);
        $views->assign('user',$member);
        $views->display("index/index/users.html.twig");

    }

    public function settingsAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        if (!$app) {
            header('Location: /');
            exit();
        }

        $views->assign('title',"个人中心");
        $views->assign('app',$app);
        $views->display("index/index/settings.html.twig");
    }

    /**
     * [loginAction description]
     * @return [type] [description]
     */
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

        $display = 'index/auth/login.html.twig';

        if ($data->getQuery('ajax') == 'true') {
            $display = 'index/index/login.html.twig';
        }
        if ($data->isPost()) {
            $msg = $members->checkUser($data->getPost('email'),$data->getPost('password'));
            if ($msg['code'] = false){
                $views->assign('error',array('code'=> 1, 'message' => $msg['message']));
            }
        	else if ($uid = $members->login($data->getPost('email'),$data->getPost('password')))
        	{
        		header('Location: /');
            	exit();
        	}
        	else
        	{
        		$views->assign('error',array('code'=> 1, 'message' => '邮箱或密码不正确'));
        	}
        }

        $views->assign('title',"登录墨客");
        $views->display($display);
    }

    public function loginCheckAction()
    {
        $rest = Restful::instance();
        $data = $this->getRequest();

        $success = 0;
        $message = "登录失败.";

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        if ($app) {
            $success = 1;
            $message = "登录成功.";
        }

        if ($data->isPost()) {
            $msg = $members->checkUser($data->getPost('email'),$data->getPost('password'));
            if ($msg['code'] == false) {
                $message = $msg['message'];
            }
            else if ($uid = $members->login($data->getPost('email'),$data->getPost('password'))) {
                $success = 1;
                $message = "登录成功.";
            }
            else
            {
                $message = "邮箱或密码错误.";
            }

        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }

    public function registerAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $success = 0;
        $message = "注册失败.";

        if ($app) {
            header('Location: /');
            exit();
        }

        $display = 'index/auth/register.html.twig';

        if ($data->getQuery('ajax') == 'true') {
            $display = 'index/index/register.html.twig';
        }
        
        if ($data->isPost()) {
            $msg = $members->checkUser($data->getPost('email'),$data->getPost('password'));
            $username = explode('@',$data->getPost('email'));
            if ($msg['code'] == false) {
                $views->assign('error',array('code'=> 1, 'message' => $msg['message']));
            }
            elseif ($members->isUserName($username[0])) {
                $message = "用户名已存在.";
            }
            else if ($uid = $members->register($data->getPost('email'),$username[0],$data->getPost('password'))) {
                header('Location: /');
                exit();
            }
            else
            {
                $views->assign('error',array('code'=> 1, 'message' => '邮箱已存在.'));
            }
        }

        $views->assign('title',"注册墨客");
        $views->display($display);
    }
    public function registerCheckAction()
    {
        $rest = Restful::instance();
        $data = $this->getRequest();

        $success = 0;
        $message = "注册失败.";

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        if ($app) {
            $success = 1;
            $message = "注册成功.";
        }

        if ($data->isPost()) {
            $msg = $members->checkUser($data->getPost('email'),$data->getPost('password'));
            $username = explode('@',$data->getPost('email'));
            if ($msg['code'] == false) {
                $message = $msg['message'];
            }
            elseif ($members->isUserName($username[0])) {
                $message = "用户名已存在.";
            }
            else if ($uid = $members->register($data->getPost('email'),$username[0],$data->getPost('password'))) {
                $success = 1;
                $message = "注册成功.";
            }
            else
            {
                $message = "邮箱已存在.";
            }

        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }

    public function logoutAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        if ($members->logout()) {
        	header('Location: /');
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