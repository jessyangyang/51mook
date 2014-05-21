<?php
/**
 * Test Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \mook\dao\Roles;
use \local\rss\Feed;
use \local\rss\DOMHtml;
use \mook\control\common\BlogManage;
use \mook\control\admin\AdminBookManage;

class TestController extends \Yaf\Controller_Abstract 
{

    public function indexAction($action = false)
    {
        $display = $this->getView();
        $data = $this->getRequest();
        $roles = array(
            'admin' => array(
                'model' => 1,
                'custom' => array(
                    'admin_index',
                    'admin_post'
                    )),
            'web' => array(
                'model' => 0,
                'custom' => false));
        echo "<pre>";
        print_r($data);

        exit();
    }

    public function feedAction()
    {
        $data = $this->getRequest();

        $book = AdminBookManage::instance();

        print_r($book->getCategoryForId(array('cid' => '1')));
        // $rss = Feed::loadRss('http://blog.sina.com.cn/rss/twocold.xml');
        // $dom = DOMHtml::loadHtml('http://blog.sina.com.cn/twocold');
        // echo "<pre>";
        // // foreach ($rss->item as $key => $value) {
        // //     if (isset($value->{'content:encoded'})){
        // //         echo $value->{'content:encoded'};
        // //     }
        // //     else
        // //     {
        // //         echo htmlSpecialChars($value->description);
        // //     }
        // // }
        // $elements = $dom->query("//link[@title='RSS']");
        // foreach ($elements as $key => $value) {
        //     $value->getAttribute('href') and $uid = basename($value->getAttribute('href'),".xml");
        //     print_r($uid);
        // }
        // 
        // BlogManage::loadSinaBlog("http://blog.sina.com.cn/gaoweiweiusa");
        exit();
    }


    public function rolesAction() 
    {
        $roles = new Roles();
        $roles->initRoles();
        exit();
    }
}