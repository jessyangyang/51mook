<?php
/**
 * PermissionPlugin
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \Yaf\Request_Abstract;
use \Yaf\Response_Abstract;
use \Yaf\Plugin_Abstract;
use \Yaf\Application;
use \Yaf\Session;


class XhprofControllerPlugin extends Plugin_Abstract 
{
	public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
        $this->initXhpro($request, $response);
    }

    public function routerShutdown(Request_Abstract $request, Response_Abstract $response) 
    {

    }

    public function preDispatch(Request_Abstract $request, Response_Abstract $response) {
        $this->debug($request, $response);
    }

    public function postDispatch(Request_Abstract $request, Response_Abstract $response) {
        $this->debug($request, $response);
    }

    public function dispatchLoopShutdown(Request_Abstract $request, Response_Abstract $response) 
    {echo "dispatchLoopShutdown,";
        $this->debug($request, $response);
    }


    public function initXhpro($request, $response)
    {
    	$config = Application::app()->getConfig()->get("xhprof")->toArray();

        if ($config and $config['debug'] == false) {
           return;
        }
        else
        {
        	xhprof_enable();
        	$this->start =  microtime(true);
        }
    }

    public function debug($request, $response)
    {
    	$config = Application::app()->getConfig()->get("xhprof")->toArray();

        if ($config and $config['debug'] == false) {
           return;
        }
        $xhprof_data = xhprof_disable();

        $this->stop =  microtime(true);

        echo (($this->stop-$this->start)*1000).'ms';
  //       echo "<pre>";
		// print_r($xhprof_data);

    }
}