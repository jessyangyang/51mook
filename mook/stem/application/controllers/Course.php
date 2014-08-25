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

        $courseControl = AdminCourseManage::instance();

        $category = $courseControl->getCategory();
        if ($data->isPost()) {
        	
            $datas = $data->getPost();
            $datas['published'] = 4;
            $datas['verified'] = 3;

            if ($cid = $courseControl->createCourse($app['uid'] , $datas)){
               header("Location: /course/$cid/" . $courseControl->convert($data->getPost("title")));
               exit();
            }
        }

        $views->assign('title', "创建墨客");
        $views->assign('categories', $category);
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
        $category = $courseControl->getCategory();
        $articles = $courseControl->getChapterForCID($cid);

        $owner = $students = false;
        if (isset($app['uid']) and $app['uid'])
        {
            if ($app['uid'] == $course['uid']) $owner = true;

            $students = $courseControl->getCourseStudentList(array('course_student.uid' => $app['uid']));
        }


        $views->assign('title',$course['title']);
        $views->assign('course', $course);
        $views->assign('menus', $articles);
        $views->assign('students', $students);
        $views->assign('categories', $category);
        $views->assign('app', $app);
        $views->assign('owner', $owner);
        $views->display("index/course/course.html.twig");
    }

    public function articleAction($cid, $ccid, $title = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $course = $courseControl->getCourseRow(array('course.cid' => $cid));
        $chapters = $courseControl->getChapterForCID($cid);

        $article = $next = $prev = false;
        if (is_array($chapters)) {
            foreach ($chapters as $key => $value) {
                if ($value['ccid'] == $ccid) {
                    $article = $value;
                    isset($chapters[$key + 1]) and $next = $chapters[$key + 1];
                    isset($chapters[$key - 1]) and $prev = $chapters[$key - 1];
                }
            }
        }

        // if (!$app and $article) {
        //     $article['body'] = mb_substr($article['body'], 0, 100, 'utf-8') . ' ...';
        // }

        $views->assign('title',$article['title']);
        $views->assign('app',$app);
        $views->assign('course', $course);
        $views->assign('article', $article);
        $views->assign('next', $next);
        $views->assign('prev', $prev);
        $views->display("index/course/article.html.twig");
    }

    public function linkAddAction($cid)
    {
        $rest = Restful::instance();
        $data = $this->getRequest();

        $courseControl = AdminCourseManage::instance();

        $message = array(
            'error' => '无法收集该链接内容',
            'content' => ''
        );

        $success = false;

        if ($data->isPost()) {

            $contents = $courseControl->addLinkToArticle($cid, $data->getPost('_link'),$data->getPost('_summary'));
            $message['content'] = $contents ? $contents : array();
            $message['error'] = '';
            $success = true;  
        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }
}