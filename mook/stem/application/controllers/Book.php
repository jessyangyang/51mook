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
use \mook\control\admin\AdminBookManage;
use \mook\control\pagesControl;
use \mook\control\common\ImagesManage;
use \local\rest\Restful;

class BookController extends \Yaf\Controller_Abstract 
{

    public function indexAction($id = false) 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $views->assign('title',"mook");
    }

    public function bookNewAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if ($data->isPost()) {
            $bookControl = new AdminBookManage();
            if ($bid = $bookControl->createBook($app['uid'] , $data->getPost())){
                header('Location: /book/' . $bid . '/manage/base');
                exit();
            }
        }

        $views->assign('app',$app);
        $views->assign('title','创建新书');
        $views->display('index/books/new.html.twig');
    }

    public function bookPostAction($bid = false, $action = 'base')
    {
        $views = $this->getView();
        $data = $this->getRequest();
        $rest = Restful::instance();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $bookControl = new AdminBookManage();
        $users = new AdminUserManage();

        $book = $bookControl->getBookRow(array('books.bid' => $bid));

        $user = $category = $menus = array();

        if ($book) $user = $users->getUserForId($book['uid']);

        $image = new ImagesManage();

        if ($action and $tmp = explode("?", $action)) $action = $tmp[0];
        
        if ($data->isPost()) {

            switch ($action) {
                case 'base':
                    $bookControl->updateBook($bid, $data->getPost());

                    break;
                case 'detail':
                    $bookControl->updateBook($bid, $data->getPost());
                    
                    break;
                case 'chapter':
                    $aid = $bookControl->createArticle($bid, $data->getPost());
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

                    $bookControl->updateMenusSort($bid,$menulist);
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
                        header('Location: /book/' . $bid . '/manage/upload' . '?file=' . $path);
                        exit();
                    }
                    break;
                case 'upload':
                    $action = 'picture-crop';
                    if ($file = $data->getQuery('file')) {
                        $avatar_id = $image->saveImagesBookFromCut($data->getQuery('file'),$data->getPost('x'),$data->getPost('y'),$data->getPost('width'),$data->getPost('height'), $user['id'], $bid, 1, true);
                        if($avatar_id) {
                            $bookControl->updateBook($bid,array('cover' => $avatar_id));
                            ImagesManage::unlink(ImagesManage::getRealPath($file));
                        }
                        header("Location: /book/$bid/manage/picture");
                        exit();
                    }
                    break;
                default:
                    # code...
                    break;
            }

            $book = $bookControl->getBookRow(array('books.bid' => $bid));
        }

        switch ($action) {
                case 'base':
                    $category = $bookControl->getCategory();
                    $views->assign('categories',$category);
                    break;
                case 'chapter':
                    $page = $data->getQuery('page') ? $data->getQuery('page') : 1;
                    $limit = $data->getQuery('limit') ? $data->getQuery('limit') : 10;
                    $menus = $bookControl->getMenusForBookID($bid,$limit,$page);

                    $pages = new pagesControl("book/$bid/manage/chapter",$bookControl->getMenusCountForBookID($bid),$limit,$page,true);
                    $views->assign('menus',$menus);
                    $views->assign('paginator',$pages);
                    break;
                case 'picture':
                    $bookImage = $image->getImagesBookForID($bid,1);
                    $coverPath = isset($bookImage['path']) ?  ImagesManage::getRelativeImage($bookImage['path']) : false;
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
        $views->assign('book',$book);
        $views->assign('user',$user);
        $views->display('index/bookmanage/' .  $action . '.html.twig');
    }

    public function bookArticleAction($bid = false, $mid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $bookControl = new AdminBookManage();
        $book = $bookControl->getBookRow(array('books.bid' => $bid));

        $article = array();

        if ($mid) {
            $article = $bookControl->getArticleForID($mid);
        }

        $views->assign('app',$app);
        $views->assign('article',$article);
        $views->assign('book',$book);
        $views->assign('mid',$mid);
        $views->display('index/bookmanage/content-modal.html.twig');
    }

    public function bookArticleContentAction($bid = false, $mid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $bookControl = new AdminBookManage();
        $book = $bookControl->getBookRow(array('books.bid' => $bid));


        $article = $bookControl->getArticleForID($mid);
        $category = $bookControl->getCategory();


        $views->assign('app',$app);
        $views->assign('article',$article);
        $views->assign('book',$book);
        $views->assign('category',$category);

        $views->display('index/books/article.html.twig');
        
    }

    public function bookArticleDeleteAction($bid = false, $mid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $bookControl = new AdminBookManage();
        $bookControl->deleteArticle($mid);
        
        exit();

    }

    public function bookArticleImageAction($bid , $mid, $action = 'upload')
    {
        $data = $this->getRequest();
        $rest = Restful::instance();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $image = new ImagesManage();

        if ($action == 'upload' and $data->isPost()) {
            
            if ($filepath = $image->saveImagesBookArticle($data->getFiles('file'),$bid, $mid, $app['uid'],true,1,true)) {
                $rest->assign('filelink',ImagesManage::getRelativeImage($filepath));
                $rest->response();
            }
        }
        elseif ($action == 'list') {
           $list = $image->getImagesBookArticleForID($bid);
           if ($list) {
               $images = array();
               foreach ($list as $key => $value) {
                    $thumb = $value['thumb'] > 0 ? $image->getRealCoverSize($value['path'],'small','jpg') : '';
                    $images[] = array(
                    'thumb' => $thumb,
                    'image' => ImagesManage::getRelativeImage($value['path']),
                    'title' => $value['filename'],
                    'folder' => $mid);
                }
                echo stripslashes(json_encode($images));
                exit();
           }
        }

        exit();
    }

}

?>