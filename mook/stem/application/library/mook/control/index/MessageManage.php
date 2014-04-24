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
}