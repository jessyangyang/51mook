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
use \mook\control\admin\AdminBookManage;
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
        	$bookControl = new AdminBookManage();
            if ($bid = $bookControl->createBook($app['uid'] , $data->getPost())){
                header('Location: /' . $app . '/');
                exit();
            }
        }

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display("index/course/create.html.twig");
    }

    public function courseAction($bid , $title)
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        $bookControl = new AdminBookManage();

        $course = $bookControl->getBookRow(array('books.bid' => $bid));

        print_r($course);

        $views->assign('title',"mook");
        $views->assign('course', $course);
        $views->assign('app',$app);
        $views->display("index/course/course.html.twig");
    }
}