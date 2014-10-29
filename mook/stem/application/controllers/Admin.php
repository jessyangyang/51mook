<?php
/**
 * Admin Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \lib\dao\BookControllers;
use \lib\dao\RolesControl;
use \mook\dao\Roles;
use \mook\control\admin\AdminUserManage;
use \mook\control\admin\AdminBookManage;
use \mook\control\admin\AdminCourseManage;
use \mook\control\admin\AdminCollectionManage;
use \mook\control\index\MembersManage;
use \mook\control\index\MessageManage;
use \mook\control\common\ImagesManage;
use \mook\control\pagesControl;

class AdminController extends \Yaf\Controller_Abstract 
{
    /**
     * [indexAction description]
     * @return [type] [description]
     */
    public function indexAction() 
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $views->assign('title',"管理首页");
        $views->assign('app',$app);
        $views->display('admin/index/index.html.twig');
    }

    public function indexNewBookAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $views->assign('title',"创建图书");
        $views->display('admin/index/new-book-table.html.twig');
    }

    public function indexLogoutAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $members->logout();
        header('Location: /login');
        exit();
    }

    /**
     * [userAction description]
     * @param  integer $limit [description]
     * @param  integer $page  [description]
     * @return [type]         [description]
     */
    public function userAction($limit = 10, $page = 1)
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

    	$list = $members->getMembersList(null,$limit, $page);

        $roles = new RolesControl();
        $rolelist = $roles->getUserRolesList();

        if ($data->isPost()) {
            switch ($data->getPost('type')) {
                case 'create':
                    $members->register($data->getPost('email'),$data->getPost('name'),$data->getPost('password'),false,false);
                    break;
                case 'edit':
                    # code...
                    break;
                case 'roles':
                    $members->updateMembers($data->getPost('uid'),array('role_id' => $data->getPost('roles')));
                    break;
                default:
                    # code...
                    break;
            }
        }


        $pages = new pagesControl('/admin/user',$members->getMembersCount(),$limit,$page);

    	$views->assign('title','用户管理');
    	$views->assign('users',$list);
        $views->assign('paginator',$pages);
        $views->assign('roles',$rolelist);
        $views->assign('app',$app);
    	$views->display('admin/user/index.html.twig');
    }

    public function userAddAction()
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

    	$views->assign('title','用户添加');
    	$views->display('admin/user/create-modal.html.twig');
    }

    public function userIdAction($id = false)
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

    	$views->assign('title','用户信息');
        $views->assign('user',$user);
    	$views->display('admin/user/show-modal.html.twig');
    }

    public function userPostAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $views->assign('title','用户编辑');
        $views->assign('profile',$user);
        $views->display('admin/user/edit-modal.html.twig');
    }

    public function userRolesAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $roles = new RolesControl();
        $rolelist = $roles->getUserRolesList();

        $views->assign('title','用户权限');
        $views->assign('user',$user);
        $views->assign('roles',$rolelist);
        $views->assign('app',$app);
        $views->display('admin/user/roles-modal.html.twig');
    }

    public function userAvatarAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $image = new ImagesManage();
        $userControl = new AdminUserManage();

        if ($id and $tmp = explode("?", $id)) $id = $tmp[0];

        $member = new AdminUserManage();
        $user = $member->getUserForId($id);

        $views->assign('user',$user);
        $views->assign('app',$app);

        if ($data->isPost()) {
            switch ($data->getQuery('action')) {
                case 'upload':
                    if ($file = $data->getQuery('file')) {
                        $avatar_id = $image->saveImagesMemberFromCut($file, $data->getPost('x'),$data->getPost('y'),$data->getPost('width'),$data->getPost('height'), $user['id'], 1, true);
                        if($avatar_id) {
                            $userControl->updateUser($id, array('avatar_id' => $avatar_id));
                            ImagesManage::unlink(ImagesManage::getRealPath($file));
                        }
                    }
                    break;
                case 'crop':
                    $file = $data->getFiles('picture');
                    $path = $image->save($file, $user['id'], 'tmp');

                    $scaled = getimagesize(ImagesManage::getRealPath($path));

                    if ($scaled[0] >= 800 or $scaled[1] >= 800) {
                       MessageManage::createResponse($views,'上传格式错误','上传图片格式错误，图片长宽小于 800px。');
                       ImagesManage::unlink($path);
                    }
                    else if (!ImagesManage::hasImageType($scaled[2],true)) {
                       MessageManage::createResponse($views,'上传格式错误','上传图片格式错误，请上传jpg, gif, png格式的文件。');
                    }
                    if ($path) {
                        header('Location: /admin/user/avatar/' . $id . '?action=upload&file=' . $path);
                        exit();
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        else
        {
            if ($data->getQuery('action') == 'upload') {
                if ($file = $data->getQuery('file')) {
                    $views->assign('scaled',ImagesManage::getImageSizeForPath($file,480));
                    $views->assign('file',ImagesManage::getRelativeImage($file));
                    $views->assign('tmp',$data->getQuery('file'));
                    $views->display('admin/user/user-avatar-crop-modal.html.twig');
                }
            }
            else
            {
                $memberImage = $image->getImagesMemberForID($id, 1);
                $coverPath = isset($memberImage['path']) ?  ImagesManage::getRelativeImage($memberImage['path']) : false;
                $views->assign('image',$coverPath);
            }
        }

        $views->assign('title','编辑用户头像');
        $views->display('admin/user/user-avatar-modal.html.twig');
    }

    public function userResetAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if (!$id) return false;

        $member = new AdminUserManage();
        $user = $member->getUserForId($id);

        if ($data->isPost()) {
            $member->resetPassword($id, $data->getPost('newPassword'));
        }

        $views->assign('title','用户密码修改');
        $views->assign('user',$user);
        $views->assign('app',$app);
        $views->display('admin/user/change-password-modal.html.twig');
    }

    /**
     * [userClosureAction description]
     * 
     * @param  Int $action [1.recover 2.closure 3.delete]
     * @param  boolean $id     [description]
     * @return [type]          [description]
     */
    public function userDeleteAction($action = false, $id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession(); 
        if (!$app) exit(); 

        if ($data->isPost()) {
            switch ($action) {
                case 1:
                    # code...
                    break;
                case 2:
                    # code...
                    break;
                case 3:
                    # code...
                    break;
                default:
                    # code...
                    break;
            }
        }

        $views->assign('app',$app);
        $views->display('admin/user/user-table-tr.html.twig');
    }

    public function userGroupsAction()
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession(); 

    	$roles = new RolesControl();

    	$list = $roles->getUserRolesList();

        if ($data->isPost()) {
            $control = new AdminUserManage();
            if ($data->getPost('type') == 'create') {
                $control->addUserRole($data->getPost());
            }
            else if ($data->getPost('type') == 'edit')
            {
                $control->updateUserRole($data->getPost());
            }
        }

    	$views->assign('title','用户组管理');
    	$views->assign('roles',$list);
        $views->assign('app',$app);
    	$views->display('admin/usergroups/index.html.twig');
    }

    public function userGroupsShowAction($rcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        if (!$rcid) return;

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $roles = Roles::instance();
        $rolegroups = $roles->getAllRoles();

        $roleControl = new RolesControl();
        $currentRole = $roleControl->getUserPermissionForId($rcid);


        $views->assign('title','用户组信息');
        $views->assign('rolegroups',$rolegroups);
        $views->assign('currentRole',$currentRole);
        $views->display('admin/usergroups/show-modal.html.twig');
    }

    public function userGroupsPostAction($rcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $roles = Roles::instance();

        $rolegroups = $roles->getAllRoles();

        $roleControl = new RolesControl();
 
        $subtitle = '添加';
        $role = $permission = $currentRole = array();
        $currentRole = '';

        if ($rcid > 0)
        {
            $subtitle = '编辑';
            $currentRole = $roleControl->getUserPermissionForId($rcid);
        }

        $views->assign('title','用户组编辑');
        $views->assign('subtitle',$subtitle);
        $views->assign('rolegroups',$rolegroups);
        $views->assign('currentRole',$currentRole);
        $views->display('admin/usergroups/post-modal.html.twig');
    }

    public function userGroupsDeleteAction($rcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $control = new AdminUserManage();

        if ($rcid) {
            $deleteId = $control->deleteUserRole($rcid);
        }

        exit();
    }

    /**
     * [booksAction description]
     * @param  integer $limit [description]
     * @param  integer $page  [description]
     * @return [type]         [description]
     */
    public function booksAction($limit = 15, $page = 1)
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $bookControl = new AdminBookManage();

        $books = $bookControl->getBookList(false,$limit, $page);
        $category = $bookControl->getCategory();

        $pages = new pagesControl('/admin/books',$bookControl->getBooksCount(),$limit,$page);

        $views->assign('paginator',$pages);

    	$views->assign('title','图书管理');
        $views->assign('books',$books);
        $views->assign('app',$app);
        $views->assign('category',$category);
    	$views->display('admin/books/index.html.twig');

    }

    public function bookPostAction($action = false,$bid = false, $id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $views->assign('title','图书编辑');
        $views->assign('app',$app);

        $book = array();

        if ($action and $bid and $id) {
            $bookControl = new AdminBookManage();
            switch ($action) {
                case 'publish':
                    $bookControl->updateBook($bid, array('published' => $id));
                    break;
                case 'verify':
                    $bookControl->updateBook($bid, array('verified' => $id));
                    break;
                default:
                    # code...
                    break;
            }
            $book = $bookControl->getBookRow(array('books.bid' => $bid));
        }

        $views->assign('book',$book);
        $views->display('admin/books/tr.html.twig');
    }

    public function bookDeleteAction($bid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $bookControl = new AdminBookManage();

        if ($bid) {
            $bookControl->deleteBook($bid);
        }

        exit();
    }

    public function bookCategoryAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $control = new AdminBookManage();

        $display = 'admin/bookcategory/index.html.twig';

        if ($data->isPost()) {

            $type = $data->getPost('type');
            $control = new AdminBookManage();

            if ( $type == 'create') {
               $control->addCategory($data->getPost());
            }
            else if ($type == 'edit') {
               $control->updateCategory($data->getPost('cid'),$data->getPost());
            }
            $display = 'admin/bookcategory/tbody.html.twig';
        }

        $categories = $control->getCategory();
        $views->assign('title','图书分类管理');
        $views->assign('app',$app);
        $views->assign('categories',$categories);
        $views->display($display);
    }

    public function bookCategoryPostAction($bcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $category = false;
        $type = 'create';

        if ($bcid > 0) {
            $subtitle = '编辑';
            $control = new AdminBookManage();
            $category = $control->getCategoryForId($bcid);
            $type = 'edit';
        }


        $views->assign('title','图书分类编辑');
        $views->assign('category',$category);
        $views->assign('type',$type);
        $views->assign('app',$app);
        $views->display('admin/bookcategory/post-modal.html.twig');
    }

    public function bookCategoryDeleteAction($bcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $control = new AdminBookManage();

        if ($bcid > 0) {
            $category = $control->deleteCategoryForId($bcid);
        }

        $categories = $control->getCategory();

        $views->assign('categories',$categories);
        $views->display('admin/bookcategory/tbody.html.twig');
    }

    /**
     * [CourseAction description]
     * @param integer $limit [description]
     * @param integer $page  [description]
     */
    public function courseAction($limit = 15, $page = 1)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $course = $courseControl->getCourseList(false,$limit, $page);
        $category = $courseControl->getCategory();

        $pages = new pagesControl('/admin/course',$courseControl->getCourseCount(),$limit,$page);

        $views->assign('paginator',$pages);

        $views->assign('title','课程管理');
        $views->assign('app',$app);
        $views->assign('courses',$course);
        $views->assign('category',$category);
        $views->display('admin/course/index.html.twig');

    }

    public function coursePostAction($action = false,$cid = false, $id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $views->assign('title','课程编辑');
        $views->assign('app',$app);

        $course = array();

        if ($action and $cid and $id) {
            $courseControl = AdminCourseManage::instance();
            switch ($action) {
                case 'publish':
                    $courseControl->updateCourse($cid, array('published' => $id));
                    break;
                case 'verify':
                    $courseControl->updateCourse($cid, array('verified' => $id));
                    break;
                default:
                    # code...
                    break;
            }
            $course = $courseControl->getCourseRow(array('course.cid' => $cid));
        }

        $views->assign('course',$course);
        $views->display('admin/course/tr.html.twig');
    }

    public function courseDeleteAction($cid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $courseControl = AdminCourseManage::instance();

        if ($cid) {
            $courseControl->deleteCourse($cid);
        }

        exit();
    }


    public function courseCategoryAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $courseControl = AdminCourseManage::instance();

        $display = 'admin/coursecategory/index.html.twig';

        if ($data->isPost()) {

            $type = $data->getPost('type');

            if ( $type == 'create') {
               $courseControl->addCategory($data->getPost());
            }
            else if ($type == 'edit') {
               $courseControl->updateCategory($data->getPost('ccid'),$data->getPost());
            }
            $display = 'admin/coursecategory/tbody.html.twig';
        }

        $categories = $courseControl->getCategory();
        $views->assign('title','课程分类管理');
        $views->assign('app',$app);
        $views->assign('categories',$categories);
        $views->display($display);
    }

    public function courseCategoryPostAction($ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $category = false;
        $type = 'create';

        if ($ccid > 0) {
            $subtitle = '编辑';
            $courseControl = AdminCourseManage::instance();
            $category = $courseControl->getCategoryForId($ccid);
            $type = 'edit';
        }


        $views->assign('title','课程分类编辑');
        $views->assign('category',$category);
        $views->assign('type',$type);
        $views->assign('app',$app);
        $views->display('admin/coursecategory/post-modal.html.twig');
    }

    public function courseCategoryDeleteAction($ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $courseControl = AdminCourseManage::instance();

        if ($ccid > 0) {
            $category = $courseControl->deleteCategoryForId($ccid);
        }

        $categories = $courseControl->getCategory();

        $views->assign('categories',$categories);
        $views->display('admin/coursecategory/tbody.html.twig');
    }

    /**
     * [feedAction description]
     * @return [type] [description]
     */
    public function feedAction($limit = 10,$page = 1)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $views->assign('title','Rss管理');
        $views->assign('app',$app);
        $views->display('admin/feed/index.html.twig');
    }

    public function collectionAction($limit = 10,$page = 1)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $collectionControl = new AdminCollectionManage();

        $twig = 'admin/collection/index.html.twig';

        $ctid = false;

        if ($data->isPost()) {
            $ctid = $data->getPost('ctid');

            switch ($data->getPost('type')) {
                case 'create':
                    $ctid = $collectionControl->addCollection($data->getPost());
                    break;
                case 'edit':
                    $collectionControl->updateCollection($ctid, $data->getPost());
                    break;
                case 'collection':
                    $collectionControl->createBookFromBlog($ctid,$app['uid'], $data->getPost('cid'), $data->getPost('year'), $data->getPost('page'));
                    exit();
                    break;
                default:
                    break;
            }
        }

        $category = $collectionControl->getCollectionCategory();
        $collection = $collectionControl->getCollectionList();

        $views->assign('title','采集管理');
        $views->assign('category',$category);
        $views->assign('articles', $collection);
        $views->assign('app',$app);
        $views->display($twig);
    }

    public function collectionPostAction($ctid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $collectionControl = new AdminCollectionManage();

        $type = 'create';
        $collection = false;
        if ($ctid > 0) {
            $type = 'edit';
            $collection = $collectionControl->getCollectionList(array('collection.ctid' => intval($ctid)),1,1);
        }

        $categories = $collectionControl->getCollectionCategory();
        $allowtype = $collectionControl->getCollectionAllowBlog();
        
        $views->assign('title','采集编辑');
        $views->assign('type', $type);
        $views->assign('allows', $allowtype);
        $views->assign('categories', $categories);
        $views->assign('collection', $collection[0]);
        $views->display('admin/collection/create-modal.html.twig');
    }

    public function collectionBlogAction($ctid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $collectionControl = new AdminCollectionManage();
        $bookControl = AdminBookManage::instance();

        $categories = $bookControl->getCategory();
        $collection = $collectionControl->getCollectionList(array('collection.ctid' => intval($ctid)),1,1);

        $views->assign('collection', $collection[0]);
        $views->assign('categories', $categories);
        $views->display('admin/collection/collection-modal.html.twig');
    }

    public function collectionDeleteAction($ctid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $collectionControl = new AdminCollectionManage();

        if ($ctid and $data->isPost()) {
           $collectionControl->deleteCollection($ctid);
        }

        exit();
    }

    public function collectionCategoryAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();

        $collectionControl = new AdminCollectionManage();

        $display = 'admin/collectioncategory/index.html.twig';

        if ($data->isPost()) {
            $type = $data->getPost('type');

            if ( $type == 'create') {
               $collectionControl->addCollectionCategory($data->getPost());
            }
            else if ($type == 'edit') {
               $collectionControl->updateCollectionCategory($data->getPost('ccid'),$data->getPost());
            }
            $display = 'admin/collectioncategory/tbody.html.twig';
        }

        $categories = $collectionControl->getCollectionCategory();
        $views->assign('title','采集分类管理');
        $views->assign('categories',$categories);
        $views->assign('app',$app);
        $views->display($display);
    }

    public function collectionCategoryPostAction($ccid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = MembersManage::instance();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $collectionControl = new AdminCollectionManage();

        $type = 'create';
        $category = false;

        if ($ccid > 0) {
            $type = 'edit';
            $category = $collectionControl->getCollectionCategoryForID($ccid);
        }
        
        $views->assign('title','采集分类编辑');
        $views->assign('category', $category);
        $views->assign('type', $type);
        $views->display('admin/collectioncategory/post-modal.html.twig');
    }

    public function collectionCategoryDeleteAction($ccid = false)
    {
        $data = $this->getRequest();
        if ($ccid) {
            
        }

        exit();
    }

}

?>