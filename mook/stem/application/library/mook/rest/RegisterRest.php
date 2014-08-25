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
        self::regRestURL('admin_user_reset','/admin/user/reset/:id','admin','userReset','重置密码');
        self::regRestURL('admin_user_delete','/admin/user/delete/:id','admin','userDelete','删除用户');

        // admin usergroups
        self::regRestURL('admin_user_groups','/admin/user/groups','admin','userGroups','用户组');
        self::regRestURL('admin_user_groups_show','/admin/user/groups/show/:rcid','admin','userGroupsShow','查看用户组');
        self::regRestURL('admin_user_groups_post','/admin/user/groups/post/:rcid','admin','userGroupsPost','编辑用户组');
        self::regRestURL('admin_user_groups_delete','/admin/user/groups/delete/:rcid','admin','userGroupsDelete','删除用户组');

        // admin books
        self::regRestURL('admin_book_index','/admin/books/:limit/:page','admin','books','图书管理');
        self::regRestURL('admin_book_post','/admin/books/post/:action/:bid/:id','admin','bookPost','图书操作');
        self::regRestURL('admin_book_delete','/admin/books/delete/:bid','admin','bookDelete','图书删除');
        self::regRestURL('admin_book_category','/admin/books/category','admin','bookCategory','图书分类');
        self::regRestURL('admin_book_category_post','/admin/books/category/post/:bcid','admin','bookCategoryPost','编辑图书分类');
        self::regRestURL('admin_book_category_delete','/admin/books/category/delete/:bcid','admin','bookCategoryDelete','删除图书分类');

        // admin course
        self::regRestURL('admin_course_index','/admin/course/:limit/:page','admin','course','课程管理');
        self::regRestURL('admin_course_post','/admin/course/post/:action/:cid/:id','admin','coursePost','课程操作');
        self::regRestURL('admin_course_delete','/admin/course/delete/:cid','admin','courseDelete','课程删除');
        self::regRestURL('admin_course_category','/admin/course/category','admin','courseCategory','课程分类');
        self::regRestURL('admin_course_category_post','/admin/course/category/post/:ccid','admin','courseCategoryPost','编辑课程分类');
        self::regRestURL('admin_course_category_delete','/admin/course/category/delete/:ccid','admin','courseCategoryDelete','删除课程分类');

        // admin feeds
        self::regRestURL('admin_feed_index','/admin/feed/:limit/:page','admin','feed','Rss管理');

        // admin collection
        self::regRestURL('admin_collection_index','/admin/collection/:limit/:page','admin','collection','采集管理');
        self::regRestURL('admin_collection_post','/admin/collection/post/:ctid','admin','collectionPost','编辑采集');
        self::regRestURL('admin_collection_blog','/admin/collection/blog/:ctid','admin','collectionBlog','采集博客');
        self::regRestURL('admin_collection_delete','/admin/collection/delete/:ctid','admin','collectionDelete','删除采集');
        self::regRestURL('admin_collection_category','/admin/collection/category','admin','collectionCategory','采集分类管理');
        self::regRestURL('admin_collection_category_post','/admin/collection/category/post/:ccid','admin','collectionCategoryPost','采集分类编辑');
        self::regRestURL('admin_collection_category_delete','/admin/collection/category/delete/:ccid','admin','collectionCategoryDelete','采集分类删除');



        
        // Test
        self::regRestURL('test_index','/test/index','test','index','测试首页');
        self::regRestURL('test_roles','/test/roles','test','roles','权限更新');
        self::regRestURL('test_feed','/test/feed','test','feed','rss订阅测试');

        /**
         * Web System
         */
        self::regRestURL('web_index','/index','index','index','网站首页');
        self::regRestURL('web_flows','/flows','index','flows','网站文库');
        self::regRestURL('web_users','/u/:name','index','users','个人主页');
        self::regRestURL('web_users_setting','/settings','index','setting','设置');
        self::regRestURL('web_login','/login','index','login','登录');
        self::regRestURL('web_login_check','/login/email/check','index','loginCheck','登录验证');
        self::regRestURL('web_register','/register','index','register','注册');
        self::regRestURL('web_register_check','/register/email/check','index','registerCheck','注册验证');
        self::regRestURL('web_logout','/logout','index','logout','退出');
        self::regRestURL('web_password_reset','/password/rest','index','resetPassword','找回密码');


        // Course
        self::regRestURL('course_create','/create','course','create',"新建课程");
        self::regRestURL('course_course_index','/course/:cid/:title','course','course',"课程首页");
        self::regRestURL('course_article','/course/:cid/:ccid/:title','course','article',"文章页");
        self::regRestURL('course_add_link','/course/add/link/:cid','course','linkAdd','添加链接');

        // Books
        self::regRestURL('book_new','/book/new','book','bookNew','添加图书');
        self::regRestURL('book_post','/book/:bid/manage/:action','book','bookPost','图书管理');
        self::regRestURL('book_article','/book/:bid/manage/article/:mid','book','bookArticle','编辑文章');
        self::regRestURL('book_article_delete','/book/:bid/manage/article/delete/:mid','book','bookArticleDelete','删除文章');
        self::regRestURL('book_article_content','/book/:bid/manage/article/content/:mid','book','bookArticleContent','文章');
        self::regRestURL('book_article_image','/book/:bid/manage/article/image/:mid/:action','book','bookArticleImage','文章');

        // Lesson
        self::regRestURL('lesson_new','/lesson/new','lesson','lessonNew','添加课程');
        self::regRestURL('lesson_post','/lesson/:cid/manage/:action','lesson','lessonPost','课程管理');
        self::regRestURL('lesson_article','/lesson/:cid/manage/article/:ccid','lesson','lessonArticle','编辑课程');
        self::regRestURL('lesson_article_delete','/lesson/:cid/manage/article/delete/:ccid','lesson','lessonArticleDelete','删除课程');
        self::regRestURL('lesson_article_content','/lesson/:cid/manage/article/content/:ccid','lesson','lessonArticleContent','编辑课程内容');
        self::regRestURL('lesson_article_image','/lesson/:cid/manage/article/image/:ccid/:action','lesson','lessonArticleImage','课程内容图片');

        // Files Upload
        self::regRestURL('files_image_upload','/files/upload/:type','files','upload','更新图片');
        self::regRestURL('files_image_load','/files/load/:type','files','load','加载图片');
        self::regRestURL('files_image_send','/files/send/:fileName','files','send','发送图片');
        
        return self::$restURL;
    }

}
