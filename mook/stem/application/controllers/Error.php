<?php
/**
 * Error Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \mook\control\index\MessageManage;

class ErrorController extends \Yaf\Controller_Abstract 
{

    public function errorAction()
    {
    	$views = $this->getView();
        $data = $this->getRequest();

        MessageManage::createResponse($views,'404','404');
    }
}