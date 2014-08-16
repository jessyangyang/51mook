<?php
/**
 * Book Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \mook\control\index\MembersManage;
use \mook\control\index\MessageManage;
use \mook\control\admin\AdminUserManage;
use \mook\control\admin\AdminCourseManage;
use \mook\control\pagesControl;
use \mook\control\common\ImagesManage;
use \local\rest\Restful;

class LessonController extends \Yaf\Controller_Abstract 
{

    public function indexAction($id = false) 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $views->assign('title',"mook");
    }

    public function lessonNewAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if ($data->isPost()) {
            $courseControl = AdminCourseManage::instance();
            if ($cid = $courseControl->createCourse($app['uid'] , $data->getPost())){
                header('Location: /lesson/' . $cid . '/manage/base');
                exit();
            }
        }

        $views->assign('app',$app);
        $views->assign('title','创建新书');
        $views->display('index/lesson/new.html.twig');
    }

    public function lessonPostAction($cid = false, $action = 'base')
    {
        $views = $this->getView();
        $data = $this->getRequest();
        $rest = Restful::instance();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();
        $users = new AdminUserManage();

        $course = $courseControl->getCourseRow(array('course.cid' => $cid));

        $user = $category = $menus = array();

        if ($course) $user = $users->getUserForId($course['uid']);

        $image = new ImagesManage();

        if ($action and $tmp = explode("?", $action)) $action = $tmp[0];
        
        if ($data->isPost()) {

            switch ($action) {
                case 'base':
                    $courseControl->updateCourse($cid, $data->getPost());
                    break;
                case 'chapter':
                    $aid = $courseControl->createArticle($cid, $data->getPost());
                    break;
                case 'sort':
                    $action = 'chapter';

                    $page = $data->getQuery('page') ? $data->getQuery('page') - 1 : 0;
                    $limit = $data->getQuery('limit') ? $data->getQuery('limit') : 10;
                    $menus = $data->getPost('ids');

                    $menulist = array();

                    foreach ($menus as $key => $value) {
                        $menu_id = explode("-", $value);
                        $menulist[$key + ($page * $limit + 1)] = intval($menu_id[2]);
                    }
                    
                    $courseControl->updateChapterSort($cid,$menulist);
                    exit();
                    break;
                case 'picture':
                    $file = $data->getFiles('picture');
                    $path = $image->save($file, $user['id'], 'tmp');

                    $scaled = getimagesize(ImagesManage::getRealPath($path));

                    if ($scaled[0] >= 1200 or $scaled[1] >= 1200) {
                       MessageManage::createResponse($views,'上传格式错误','上传图片格式错误，图片长宽小于 1200px。');
                       ImagesManage::unlink($path);
                    }
                    else if (!ImagesManage::hasImageType($scaled[2],true)) {
                       MessageManage::createResponse($views,'上传格式错误','上传图片格式错误，请上传jpg, gif, png格式的文件。');
                    }
                    if ($path) {
                        header('Location: /lesson/' . $cid . '/manage/upload' . '?file=' . $path);
                        exit();
                    }
                    break;
                case 'upload':
                    $action = 'picture-crop';
                    if ($file = $data->getQuery('file')) {
                        $avatar_id = $image->saveImagesCourseFromCut($data->getQuery('file'),$data->getPost('x'),$data->getPost('y'),$data->getPost('width'),$data->getPost('height'), $user['id'], $cid, 1, true);
                        if($avatar_id) {
                            $courseControl->updateCourse($cid, array('cover' => $avatar_id));
                            ImagesManage::unlink(ImagesManage::getRealPath($file));
                        }
                        header("Location: /lesson/$cid/manage/picture");
                        exit();
                    }
                    break;
                default:
                    # code...
                    break;
            }

            $course = $courseControl->getCourseRow(array('course.cid' => $cid));
        }

        switch ($action) {
                case 'base':
                    $category = $courseControl->getCategory();
                    $views->assign('categories',$category);
                    break;
                case 'chapter':
                    $page = $data->getQuery('page') ? $data->getQuery('page') : 1;
                    $limit = $data->getQuery('limit') ? $data->getQuery('limit') : 10;
                    $menus = $courseControl->getChapterForCID($cid,$limit,$page);

                    $pages = new pagesControl("lesson/$cid/manage/chapter",$courseControl->getChapterCountForCID($cid),$limit,$page,true);
                    $views->assign('menus',$menus);
                    $views->assign('paginator',$pages);
                    break;
                case 'picture':
                    $courseImage = $image->getImagesCourseForCID($cid,1);
                    $coverPath = isset($courseImage['path']) ?  ImagesManage::getRelativeImage($courseImage['path']) : false;
                    $views->assign('image',$coverPath);
                    break;
                case 'upload':
                    $action = 'picture-crop';
                    if ($file = $data->getQuery('file')) {
                        $views->assign('scaled',ImagesManage::getImageSizeForPath($file,480));
                        $views->assign('file',ImagesManage::getRelativeImage($file));
                    }
                    break;
                default:
                    # code...
                    break;
        }

        $views->assign('title','');
        $views->assign('app',$app);
        $views->assign('course',$course);
        $views->assign('user',$user);
        $views->display('index/lessonmanage/' .  $action . '.html.twig');
    }

    public function lessonArticleAction($cid = false, $ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $courseControl = AdminCourseManage::instance();
        $course = $courseControl->getCourseRow(array('course.cid' => $cid));

        $article = array();

        if ($ccid) {
            $article = $courseControl->getArticleForID($ccid);
        }

        $views->assign('app',$app);
        $views->assign('article',$article);
        $views->assign('course',$course);
        $views->assign('ccid',$ccid);
        $views->display('index/lessonmanage/content-modal.html.twig');
    }

    public function lessonArticleContentAction($cid = false, $ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();
        $course = $courseControl->getCourseRow(array('course.cid' => $cid));

        $article = $courseControl->getArticleForID($ccid);
        $category = $courseControl->getCategory();


        $views->assign('app',$app);
        $views->assign('article',$article);
        $views->assign('course',$course);
        $views->assign('category',$category);

        $views->display('index/books/article.html.twig');
        
    }

    public function lessonArticleDeleteAction($cid = false, $ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $courseControl = AdminCourseManage::instance();
        $courseControl->deleteArticle($ccid);
        exit();

    }

    public function lessonArticleImageAction($cid , $ccid, $action = 'upload')
    {
        $data = $this->getRequest();
        $rest = Restful::instance();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $image = new ImagesManage();

        if ($action == 'upload' and $data->isPost()) {
            
            if ($filepath = $image->saveImagesCourseArticle($data->getFiles('file'),$cid, $ccid, $app['uid'],true,1,true)) {
                $rest->assign('filelink',ImagesManage::getRelativeImage($filepath));
                $rest->response();
            }
        }
        elseif ($action == 'list') {
           $list = $image->getImagesCourseArticleForID($cid);
           if ($list) {
               $images = array();
               foreach ($list as $key => $value) {
                    $thumb = $value['thumb'] > 0 ? $image->getRealCoverSize($value['path'],'small','jpg') : '';
                    $images[] = array(
                    'thumb' => $thumb,
                    'image' => ImagesManage::getRelativeImage($value['path']),
                    'title' => $value['filename'],
                    'folder' => $ccid);
                }
                echo stripslashes(json_encode($images));
                exit();
           }
        }

        exit();
    }

}

?>