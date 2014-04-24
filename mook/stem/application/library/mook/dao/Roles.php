<?php
/**
* Roles DAO 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\dao;

use mook\rest\RegisterRest;
use lib\models\users\RoleCategory;

class Roles extends \lib\models\users\Roles
{
    public static function instance($key = 0)
    {
        return self::$instance ? self::$instance : new Roles($key);
    }

    public function initRoles()
    {
        $list = RegisterRest::initRegister();
        $roles = self::instance();
        $category = RoleCategory::instance();

        $datas = $roles->fetchList();
        $categorys = $category->fetchList();
        $names = $cate =array();

        if (is_array($categorys)) {
            foreach ($categorys as $key => $value) {
                $cate[$value['name']] = $value['rcid'];
            }
        }

        if ($datas) {
           foreach ($datas as $key => $value) {
                if (isset($value['name'])) {
                    $names[] = $value['name'];
                }
            }
        }

        $index = 1;
        echo "<pre>";
        
        foreach ($list as $key => $value) {
            $control = explode('_',$key);
            if (in_array($key, $names) and $control) {
                $roles->where("name='$key'")->update(array('controller' => $value['route']['controller'], 'action' => $value['route']['action'],'published' => UPDATE_TIME,'sort' => $index,'summary' => ($value['name'] ? $value['name'] : $value['name']),'rcid' => $cate[$control[0]]));
                 echo 'update :'.$key . "<br>";
            }
            else
            {
                $roles->insert(array('rcid' => $cate[$control[0]],'name' => $key,'controller' => $value['route']['controller'], 'action' => $value['route']['action'],'published' => UPDATE_TIME, 'summary' => $value['name'], 'sort' => $index));
                echo 'insert :'.$key . "<br>";
            }
            $index ++;
        }
    }

    /**
     * [getCurrentController description]
     * @param  [type]  $controller [description]
     * @param  boolean $action     [description]
     * @return [type]              [description]
     */
    public function getCurrentController($controller, $action = false)
    {
        $list = RegisterRest::initRegister();
        if ($list and is_array($list)) {
            foreach ($list as $key => $value) {
                if ($value['route']['controller'] == $controller and $value['route']['action'] == $action ) {
                    return $value;
                }
            }
        }
    }

    /**
     * [getAllRoles description]
     * @return [type] [description]
     */
    public function getAllRoles()
    {
        $cate = RoleCategory::instance();

        $categorys = $cate->order("sort")->fetchList();

        $roles = RegisterRest::initRegister();

        $rolesgroup = array();

        if ($categorys) {
            foreach ($categorys as $key => $category) {
                $rolesgroup[$key]['name'] = $category['summary'];
                $rolesgroup[$key]['key'] = $category['name'];
                foreach ($roles as $key2 => $value) {
                    $controller = explode('_', $key2);
                    if ($category['name'] == $controller[0]) {
                        $rolesgroup[$key]['group'][] = array(
                            'name' => $value['name'],
                            'key' => $key2);
                    }
                }
            }
        }

        return $rolesgroup;
    }

}