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
use \mook\control\common\ImagesManage;
use \mool\control\pagesControl;
use \local\rest\Restful;
use \Yaf\Registry;

class CourseController extends \Yaf\Controller_Abstract 
{
	public function indexAction() 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();


        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display($display);
    }

    public function createAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        if (!$app) {
            header("Location: /");
            exit();
        }

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

        $views->assign('title', "创建课程");
        $views->assign('categories', $category);
        $views->assign('app', $app);
        $views->display("index/course/create.html.twig");
    }

    public function courseAction($cid , $title)
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $course = $courseControl->getCourseRow(array('course.cid' => $cid,'course.verified' => 3,'course.published' => 4));
        $articles = $courseControl->getChapterForCID($cid);

        if (!$course) {
            header("Location: /");
            exit();
        }

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
        $views->assign('app', $app);
        $views->assign('owner', $owner);
        $views->display("index/course/course.html.twig");
    }

    public function courseChapterModalAction($cid, $action = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $owner = false;

        $display = '';

        $course = $courseControl->getCourseRow(array('course.cid' => $cid,"course.verified" => 3,"course.published" => 4));
        $category = $courseControl->getCategory();

        switch ($action) {
            case 'edit':
                $display = "index/course/course-edit-modal.html.twig";
                break;
            case 'delete':
                $display = "index/course/course-delete-modal.html.twig";
                break;
            default:
                # code...
                break;
        }

        if (isset($app['uid']) and $app['uid'])
        {
            if ($app['uid'] == $course['uid']) $owner = true;
        }

        if (!$app or !$owner) exit();

        $views->assign('app', $app);
        $views->assign('owner', $owner);
        $views->assign('course', $course);
        $views->assign('categories', $category);
        $views->display($display);
    }

    public function courseArticleModalAction($cid, $ccid, $action = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $owner = false;

        switch ($action) {
            case 'edit':
                $display = "index/course/article-edit-modal.html.twig";
                break;
            case 'delete':
                $display = "index/course/article-delete-modal.html.twig";
                break;
            default:
                # code...
                break;
        }

        $menu = $courseControl->getArticleForID($ccid);
        $course = $courseControl->getCourseRow(array('course.cid' => $cid,'course.verified' => 3,'course.published' => 4));

        if (isset($app['uid']) and $app['uid'])
        {
            if ($app['uid'] == $course['uid']) $owner = true;
        }

        if (!$app or !$owner) exit();

        $views->assign('app', $app);
        $views->assign('owner', $owner);
        $views->assign('course', $course);
        $views->assign('menu', $menu);
        $views->display($display);
    }

    public function courseCheckAction($cid, $ccid, $action = false)
    {
        $rest = Restful::instance();
        $data = $this->getRequest();

        $success = 0;
        $message = '';

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

         if (!$app) exit();

        if ($data->isPost()) {

            $datas = array(
                        'title' => $data->getPost('title'),
                        'summary' => $data->getPost('summary'));

            $courseControl = AdminCourseManage::instance();

            switch ($action) {
                case 'chapter':
                    $datas['ccid'] = $data->getPost('ccid');
                    if ($datas and $courseControl->updateCourse($cid, $datas)) {
                        $course = $course = $courseControl->getCourseRow(array('course.cid' => $cid,"course.verified" => 3,"course.published" => 4));
                        $success = 1;
                        $message = $course;
                    }
                    if ($cover = $data->getFiles('cover') and $cover['error'] == 0) {
                        $image = new ImagesManage();

                        $coversize = $cover['size'] * 0.001;
                        $covertype = explode('/', $cover['type']);
                        if ($coversize >= 2048) {
                           $message = '文件大小不能超过 2M.';
                        }
                        else if ($covertype and !ImagesManage::hasImageType($covertype[1])) {
                           $message = '上传图片格式错误，请上传jpg, gif, png格式的文件.';
                        }
                        else if ($cover)
                        {
                            if ($aid = $image->saveImagesCourse($cover, $cid, $app['uid'], 1, 1)) {
                                $courseControl->updateCourse($cid, array('cover' => $aid));
                            }
                        }
                    }
                    break;
                case 'article':
                    $datas['ccid'] = $ccid;
                    if ($datas and $courseControl->createArticle($cid, $datas)) {
                        $success = 1;
                        $message = $courseControl->getArticleForID($ccid);
                    }
                    # code...
                    break;
                case 'chapter-delete':
                    if ($cid and $courseControl->deleteCourse($cid)) {
                        $success = 1;
                        $message = "";
                    }
                    break;
                case 'article-delete':
                    if ($cid and $ccid and $courseControl->deleteArticle($ccid)) {
                        $success = 1;
                        $message = "";
                    }
                    break;
                case 'sort':
                    if ($menus = $data->getPost('ids')) {
                        $menulist = array();

                        foreach ($menus as $key => $value) {
                            $menu_id = explode("-", $value);
                            $menulist[$key + 1] = intval($menu_id[2]);
                        }
                    
                        $courseControl->updateChapterSort($cid,$menulist);
                        $success = 1;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }

    public function articleAction($cid, $ccid, $title = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $course = $courseControl->getCourseRow(array('course.cid' => $cid,"course.verified" => 3,"course.published" => 4));
        $chapters = $courseControl->getChapterForCID($cid);

        if (!$course) {
            header("Location: /");
            exit();
        }

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
        $views = $this->getView();
        $rest = Restful::instance();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        $courseControl = AdminCourseManage::instance();

        $message = array(
            'error' => '无法收集该链接内容',
            'content' => ''
        );

        $success = 0;

        if (!$app) {
            $message['error'] = '没有权限';
        }

        if ($data->isPost()) {
            $contents = $courseControl->addLinkToArticle($cid, $data->getPost('_link'),$data->getPost('_summary'));

            $owner = false;

            if (isset($app['uid']) and $app['uid'])
            {
                $course = $courseControl->getCourseRow(array('course.cid' => $contents['cid'],"course.verified" => 3,"course.published" => 4));
                if ($course and $app['uid'] == $course['uid']) $owner = true;
            }

            $views->assign('owner', $owner);
            $views->assign('menu', $contents);
            $views->display("index/course/article-menu-li-modal.html.twig");
        }

        $rest->assign('success',$success);
        $rest->assign('message',$message);
        $rest->response();
    }
}