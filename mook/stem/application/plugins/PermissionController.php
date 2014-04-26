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
use \mook\control\index\MembersManage;
use \mook\rest\RegisterRest;
use \Yaf\Session;


class PermissionControllerPlugin extends Plugin_Abstract 
{
    private $current_key;

    public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
        // $this->checkSystemPermission($request, $response);
    }

    public function routerShutdown(Request_Abstract $request, Response_Abstract $response) 
    {
        $this->checkMemberPermission($request, $response);
    }

    private function checkSystemPermission(Request_Abstract $request, Response_Abstract $response)
    {
        $config = Application::app()->getConfig()->get("roles")->toArray();

        if ($config and $config['permission'] == false) {
           return;
        }
    }

    public function checkMemberPermission(Request_Abstract $request, Response_Abstract $response)
    {
        $config = Application::app()->getConfig()->get("roles")->toArray();

        if ($config and $config['permission'] == false) {
           return;
        }

        $rest = RegisterRest::initRegister();
        $this->current_key = $this->changedSystemAction($request->getControllerName(),$request->getActionName(),$rest);

        if (!$this->current_key) 
        {
            $request->setControllerName('Index');
            $request->setActionName('index');
        }

        $check = explode(',', $config['check']);
        $member = explode(',', $config['member']);
        
        if ($this->current_key) {
            $members = MembersManage::instance();
            $user = $members->getCurrentSession();

            $key = explode('_', $this->current_key);
            $userpermission = isset($user['permission']) ? explode(',', $user['permission']) : array();

            if (in_array($key[0], $check) and !$user) {
                $request->setControllerName('Index');
                $request->setActionName('index');
            }
            else if (in_array($key[0], $check) and $user['super'] == false) {
                $request->setControllerName('Index');
                $request->setActionName('index');
            }
            else if (in_array($key[0], $check) and $user['role_id'] > 3 and !in_array($this->current_key, $userpermission)) {
                $request->setControllerName('Index');
                $request->setActionName('index');
            }
    
        }
    }

    private function changedSystemAction($controller,$action,$fields)
    {
        if (!is_array($fields)) return false;

        foreach ($fields as $key => $field) {
            foreach ($field as $key2 => $value) {
                if(strtolower($value['controller']) == strtolower($controller) and strtolower($value['action']) == strtolower($action))
                {
                    return $key;
                }
            }
        }
        return false;
    }

    private function changedPermissionAction($fields)
    {
        if (!is_array($fields)) return array();
        $arr = array();
        foreach ($fields as $key => $value) {
            $tmp = explode("_", $value);
            if (in_array($tmp[0], $arr)) {
                $arr[] = $tmp[0];
            }
        }
        return $arr;
    }

}