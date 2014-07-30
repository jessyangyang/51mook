<?php
/**
* MembersManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\index;

use \Yaf\Registry;

class MessageManage 
{
	/**
     * Instance construct
     */
    function __construct() {
    	
    }

    /**
     * [createResponse description]
     * @param  [type] $display [description]
     * @param  [type] $title   [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public static function createResponse($display,$title,$message)
    {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$message = array(
            'title' => $title,
            'message' => $message,
            'referer' => $referer);
        $display->assign('message',$message);
        $display->display('base/message.html.twig');
    }

    /**
     * [redirect description]
     * @param  boolean $url [description]
     * @return [type]       [description]
     */
    public static function redirect($url = false)
    {
        if ($url) {
           header('Location: /' . $url);
        }
        else
        {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
            header('Location:' . $url);
        }
        exit();
    }
}