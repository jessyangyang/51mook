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
use \mook\control\index\MessageManage;
use \mook\control\admin\AdminCourseManage;
use \mool\control\pagesControl;
use \local\rest\Restful;
use \Yaf\Registry;

class CourseController extends \Yaf\Controller_Abstract 
{
	public function indexAction() 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();


        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display($display);


    }

    public function createAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if ($data->isPost()) {
        	$courseControl = AdminCourseManage::instance();
            if ($cid = $courseControl->createCourse($app['uid'] , $data->getPost())){
               header("Location: /course/$cid/" . $courseControl->convert($data->getPost("title")));
               exit();
            }
        }

        $views->assign('title', "mook");
        $views->assign('app', $app);
        $views->display("index/course/create.html.twig");
    }

    public function courseAction($cid , $title)
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $course = $courseControl->getCourseRow(array('course.cid' => $cid));
        $articles = $courseControl->getChapterForCID($cid);

        $owner = false;
        if ($app['uid'] == $course['uid']) $owner = true;

        $views->assign('title',"mook");
        $views->assign('course', $course);
        $views->assign('menus', $articles);
        $views->assign('app', $app);
        $views->assign('owner', $owner);
        $views->display("index/course/course.html.twig");
    }
}