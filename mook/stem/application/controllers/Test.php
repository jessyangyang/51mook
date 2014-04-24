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

    public function rolesAction() 
    {
        $roles = new Roles();
        $roles->initRoles();
        exit();
    }
}