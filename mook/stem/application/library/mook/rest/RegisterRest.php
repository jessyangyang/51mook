<?php
/**
 * Register
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 * 
 */

namespace mook\rest;



class RegisterRest extends \local\rest\Restful{

    public static function initRegister()
    {
        //*** Mook Admin 
        //***************/
        self::regRestURL('admin_index','/admin/index','admin','index','管理首页');
        self::regRestURL('admin_index_newbook','/admin/newbook','admin','indexNewBook','查看新图书');
        self::regRestURL('admin_index_logout','/admin/logout','admin','indexLogout','退出后台');

        // admin users
        self::regRestURL('admin_user_index','/admin/user/:limit/:page','admin','user','用户管理');
        self::regRestURL('admin_user_add','/admin/user/add','admin','userAdd','添加用户');
        self::regRestURL('admin_user_id','/admin/user/id/:id','admin','userId','用户信息');
        self::regRestURL('admin_user_post','/admin/user/post/:id','admin','userPost','编辑信息');
        self::regRestURL('admin_user_roles','/admin/user/roles/:id','admin','userRoles','编辑用户组');
        self::regRestURL('admin_user_avatar','/admin/user/avatar/:id','admin','userAvatar','用户头像');
        self::regRestURL('admin_user_reset','/admin/user/reset/:id','admin','userReset','用户头像');

        // admin usergroups
        self::regRestURL('admin_user_groups','/admin/user/groups','admin','userGroups','用户组');
        self::regRestURL('admin_user_groups_show','/admin/user/groups/show/:rcid','admin','userGroupsShow','查看用户组');
        self::regRestURL('admin_user_groups_post','/admin/user/groups/post/:rcid','admin','userGroupsPost','编辑用户组');
        self::regRestURL('admin_user_groups_delete','/admin/user/groups/delete/:rcid','admin','userGroupsDelete','删除用户组');

        // admin books
        self::regRestURL('admin_book_index','/admin/books/:limit/:page','admin','books','图书管理');
        self::regRestURL('admin_book_post','/admin/books/:action','admin','bookPost','图书操作');
        self::regRestURL('admin_book_delete','/admin/books/delete/:bid','admin','bookDelete','图书删除');
        self::regRestURL('admin_book_category','/admin/books/category','admin','bookCategory','图书分类');
        self::regRestURL('admin_book_category_post','/admin/books/category/post/:bcid','admin','bookCategoryPost','编辑图书分类');
        self::regRestURL('admin_book_category_delete','/admin/books/category/delete/:bcid','admin','bookCategoryDelete','删除图书分类');
        
        // Test
        self::regRestURL('test_index','/test/index','test','index','测试首页');
        self::regRestURL('test_roles','/test/roles','test','roles','权限更新');

        /**
         * Web System
         */
        self::regRestURL('web_index','/index','index','index','网站首页');
        self::regRestURL('web_login','/login','index','login','登录');
        self::regRestURL('web_register','/register','index','register','注册');
        self::regRestURL('web_register_check','/register/email/check','index','registerCheck','注册验证');
        self::regRestURL('web_logout','/logout','index','logout','退出');
        self::regRestURL('web_password_reset','/password/rest','index','resetPassword','找回密码');

        // Books
        self::regRestURL('book_new','/book/new','book','bookNew','添加图书');
        self::regRestURL('book_post','/book/:bid/manage/:action','book','bookPost','图书管理');
        self::regRestURL('book_article','/book/:bid/manage/article/:mid','book','bookArticle','编辑文章');
        self::regRestURL('book_article_delete','/book/:bid/manage/article/delete/:mid','book','bookArticleDelete','删除文章');
        self::regRestURL('book_article_content','/book/:bid/manage/article/content/:mid','book','bookArticleContent','文章');
        self::regRestURL('book_article_image','/book/:bid/manage/article/image/:mid/:action','book','bookArticleImage','文章');

        // Files Upload
        self::regRestURL('files_image_upload','/files/upload/:type','files','upload','更新图片');
        self::regRestURL('files_image_load','/files/load/:type','files','load','加载图片');
        self::regRestURL('files_image_send','/files/send/:fileName','files','send','发送图片');
        
        return self::$restURL;
    }

}
