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
use \mook\control\index\MembersManage;
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

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        $views->assign('title',"mook");
        $views->assign('app',$app);
        $views->display('admin/index/index.html.twig');
    }

    public function indexNewBookAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $views->assign('title',"mook");
        $views->display('admin/index/new-book-table.html.twig');
    }

    public function indexLogoutAction()
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
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

        $members = new MembersManage();
        $app = $members->getCurrentSession();

    	$list = $members->getMembersList(null,$limit, $page);

        $roles = new RolesControl();
        $rolelist = $roles->getUserRolesList();

        if ($data->isPost()) {

            switch ($data->getPost('type')) {
                case 'create':
                    $members->register($data->getPost('email'),$data->getPost('name'),$data->getPost('password'),false);
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

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

    	$views->assign('title','');
    	$views->display('admin/user/create-modal.html.twig');
    }

    public function userIdAction($id = false)
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

    	$views->assign('title','');
        $views->assign('user',$user);
    	$views->display('admin/user/show-modal.html.twig');
    }

    public function userPostAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $views->assign('title','');
        $views->assign('profile',$user);
        $views->display('admin/user/edit-modal.html.twig');
    }

    public function userRolesAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $user = array();

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $roles = new RolesControl();
        $rolelist = $roles->getUserRolesList();

        $views->assign('title','');
        $views->assign('user',$user);
        $views->assign('roles',$rolelist);
        $views->assign('app',$app);
        $views->display('admin/user/roles-modal.html.twig');
    }

    public function userAvatarAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $views->assign('title','');
        $views->assign('user',$user);
        $views->assign('app',$app);
        $views->display('admin/user/user-avatar-modal.html.twig');
    }

    public function userResetAction($id = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        if ($id) {
            $member = new AdminUserManage();
            $user = $member->getUserForId($id);
        }

        $views->assign('title','');
        $views->assign('user',$user);
        $views->assign('app',$app);
        $views->display('admin/user/change-password-modal.html.twig');
    }

    public function userGroupsAction()
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = new MembersManage();
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

    	$views->assign('title','');
    	$views->assign('roles',$list);
        $views->assign('app',$app);
    	$views->display('admin/usergroups/index.html.twig');
    }

    public function userGroupsShowAction($rcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        if (!$rcid) return;

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $roles = Roles::instance();
        $rolegroups = $roles->getAllRoles();

        $roleControl = new RolesControl();
        $currentRole = $roleControl->getUserPermissionForId($rcid);


        $views->assign('title','');
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
        print_r($rolegroups);

        $roleControl = new RolesControl();
 
        $subtitle = '添加';
        $role = $permission = $currentRole = array();
        $currentRole = '';

        if ($rcid > 0)
        {
            $subtitle = '编辑';
            $currentRole = $roleControl->getUserPermissionForId($rcid);
        }

        $views->assign('title','');
        $views->assign('subtitle',$subtitle);
        $views->assign('rolegroups',$rolegroups);
        $views->assign('currentRole',$currentRole);
        $views->display('admin/usergroups/post-modal.html.twig');
    }

    public function userGroupsDeleteAction($rcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
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
    public function booksAction($limit = 10, $page = 1)
    {
    	$views = $this->getView();
    	$data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();

        $bookControl = new AdminBookManage();

        $books = $bookControl->getBookList(false,$limit = 10, $page = 1);

    	$views->assign('title','');
        $views->assign('books',$books);
        $views->assign('app',$app);
    	$views->display('admin/books/index.html.twig');

    }

    public function bookPostAction($action = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit(); 

        $views->assign('title','');
        $views->assign('app',$app);
        $views->display('admin/books/index.html.twig');
    }

    public function bookDeleteAction($bid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
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

        $members = new MembersManage();
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
        $views->assign('title','');
        $views->assign('app',$app);
        $views->assign('categories',$categories);
        $views->display($display);
    }

    public function bookCategoryPostAction($bcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
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


        $views->assign('title','');
        $views->assign('category',$category);
        $views->assign('type',$type);
        $views->display('admin/bookcategory/post-modal.html.twig');
    }

    public function bookCategoryDeleteAction($bcid = false)
    {
        $views = $this->getView();
        $data = $this->getRequest();

        $members = new MembersManage();
        $app = $members->getCurrentSession();
        if (!$app) exit();

        $control = new AdminBookManage();

        if ($bcid > 0) {
            $subtitle = '编辑';
            $control = new AdminBookManage();
            $category = $control->deleteCategoryForId($bcid);
        }

        $views->assign('categories',$control->getCategory());
        $views->display('admin/bookcategory/tbody.html.twig');
    }

}

?>