<?php
/**
 * PermissionPlugin
 *
 * @package  DuyuMvc
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

    /***/
    private function checkSystemPermission(Request_Abstract $request, Response_Abstract $response)
    {
        $config = Application::app()->getConfig()->get("roles")->toArray();

        if ($config and $config['permission'] == false) {
           return;
        }
    }

    /**
     *  检查用户权限
     * @param  Request_Abstract
     * @param  Response_Abstract
     * @return [type]
     */
    public function checkMemberPermission(Request_Abstract $request, Response_Abstract $response)
    {
        $config = Application::app()->getConfig()->get("roles")->toArray();

        // 是否开启权限检查
        if ($config and $config['permission'] == false) {
           return;
        }

        $rest = RegisterRest::initRegister();
        // 获取当前路由
        $this->current_key = $this->getSystemAction($request->getControllerName(),$request->getActionName(),$rest);

        // 如果路由不存在，跳转到默认路由位置。
        echo "<pre>";
        print_r($this->current_key);
        print_r($rest);
        // if (!$this->current_key) 
        // {
        //     $request->setControllerName('Index');
        //     $request->setActionName('index');
        //     return;
        // }

        $check = explode(',', $config['check']);
        $member = explode(',', $config['member']);
        
        if ($this->current_key) {
            $members = MembersManage::instance();
            $user = $members->getCurrentSession();

            $controlName = explode('_', $this->current_key);
            $userpermission = isset($user['permission']) ? explode(',', $user['permission']) : array();

            // 如果是超级管理员，不检查权限。
            if ($user && $user['role_id'] == 1) {
                return;
            }

            if ($user) {
                // 检查普通用户的权限
                if ($user and $user['role_id'] > 1 and !in_array($this->current_key, $userpermission)) {
                    $request->setControllerName('Index');
                    $request->setActionName('index');
                }
            } else {
                //获取匿名用户禁止路由权限
                if (in_array($controlName[0], $check)) {
                    $request->setControllerName('Index');
                    $request->setActionName('index');
                }
            }
        }
    }

    /**
     * [getSystemAction check system action]
     * 检查系统是否定义了controller和action
     * @param  [type] $controller [description]
     * @param  [type] $action     [description]
     * @param  [type] $fields     [description]
     * @return [type]             [description]
     */
    private function getSystemAction($controller,$action,$fields)
    {
        if (!is_array($fields)) return false;

        foreach ($fields as $key => $field) {
            foreach ($field as $key2 => $value) {
                if( is_array($value) and strtolower($value['controller']) == strtolower($controller) and strtolower($value['action']) == strtolower($action))
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