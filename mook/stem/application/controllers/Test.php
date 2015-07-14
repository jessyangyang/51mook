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
use \local\common\Readability;
use \mook\control\common\BlogManage;
use \mook\control\admin\AdminBookManage;
use \mook\control\common\LinkManage;
use \local\social\WeChatSDK;


class TestController extends \Yaf\Controller_Abstract 
{

    public function indexAction($action = false)
    {
        $display = $this->getView();
        $data = $this->getRequest();

        $common =  \Yaf\Registry::get('common');

        // print_r(LinkManage::link('http://games.sina.com.cn/y/n/2014-07-02/1630795937.shtml'));

        // $html = $common->curl_request('http://games.sina.com.cn/y/n/2014-07-02/1630795937.shtml');

        // $doc = new \DOMDocument();
        // libxml_use_internal_errors(true);
        // $doc->loadHTML($html->response);

        // $metas = $doc->getElementsByTagName('meta');


        // for ($i = 0; $i < $metas->length; $i++)
        // {
        //     $meta = $metas->item($i);
        //     if ($meta->getAttribute('http-equiv') == 'Content-Type') {
        //         echo $meta->getAttribute('content');
        //     }
        // }

        // $readability = new Readability($html->response);
        // $data = $readability->getContent();
        // print_r($html);
        
        // preg_match('/=(\w+\b)/','text/html; charset=GBK',$matchs);
        // print_r($matchs);
        // $roles = array(
        //     'admin' => array(
        //         'model' => 1,
        //         'custom' => array(
        //             'admin_index',
        //             'admin_post'
        //             )),
        //     'web' => array(
        //         'model' => 0,
        //         'custom' => false));
        // echo "<pre>";
        // print_r($data);

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
        echo "sussce";
        exit();
    }

    public function clearSessionAction()
    {
        session_destroy();
        echo "clear";
        exit();
    }

    public function clearTemplateCacheAction()
    {
        $twig = \Yaf\Registry::get('twig');
        $twig->clearTemplateCache();
        $twig->clearCacheFiles();
        echo "clear";
        exit();
    }

    public function phpinfoAction()
    {
        print_r(phpinfo());
        exit();
    }

    public function wechatAction() {
        $options = array(
             'token'=>'krxkid', //填写你设定的key
             'encodingaeskey'=>'2un8rwmEnk2icZnqoIR8niY2M2292zwJjxbEEyiUveL', //填写加密用的EncodingAESKey
             'appid'=>'wx9294ba1287a3f37e', //填写高级调用功能的app id
             'appsecret'=>'ee5cdb887bc9b61625a0c59b75f6919b' //填写高级调用功能的密钥
        );

        $weObj = new WeChatSDK($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();

        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                    $weObj->text("hello, I'm wechat")->reply();
                    exit;
                    break;
            case Wechat::MSGTYPE_EVENT:
                    break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $weObj->text("help info")->reply();
        }

        //获取菜单操作:
        $menu = $weObj->getMenu();

         //设置菜单
        $newmenu =  array(
            "button"=>
            array(
                array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
                array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
            )
        );
        $result = $weObj->createMenu($newmenu);
        exit();
    }
}