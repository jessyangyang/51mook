<?php
/**
 * Bootstrap.php
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

use \Yaf\Dispatcher;
use \Yaf\Loader;
use \local\db\MySQL;
use \local\log\Logger;
use \local\log\SystemLogger;
use \mook\common;
use \local\template\TwigAdapter;
use \mook\rest\RegisterRest;

class Bootstrap extends \Yaf\Bootstrap_Abstract 
{
    public function _initException(Dispatcher $dispatcher)
    {
        // $exception = new Exception();
        // $dispatcher->setErrorHandler(array(get_class($this),'error_handler'));
        register_shutdown_function(function() {
            SystemLogger::close();
        });

        //Initialized the SystemLogger
        $systemLogDir = Yaf\Application::app()->getConfig()->get('log')->get('dir');
        if ($systemLogDir) {
            SystemLogger::init($systemLogDir, Logger::LEVEL_DEBUG);
        }
    }

    public function _initConfig(Dispatcher $dispatcher)
    {
        Yaf\Registry::set("common", common::instance());
    }

    public function _initDataBase(Dispatcher $dispatcher)
    {
        $config = Yaf\Application::app()->getConfig()->get("mysql")->toArray();
        MySQL::setInstance('default', $config, true);
    }

    public function _initRoute(Dispatcher $dispatcher) {
        $router = $dispatcher->getRouter();
        $rest = RegisterRest::initRegister();
        $router->addConfig(new Yaf\Config\Simple($rest));
    }

    public function _initTwig(Dispatcher $dispatcher)
    {
        $twig = new TwigAdapter(null, \Yaf\Application::app()->getConfig()->twig->toArray());
        $dispatcher->setView($twig);
        Yaf\Registry::set("twig", $twig);
        // $dispatcher->registerPlugin(new SmartyControllerPlugin());
    }

    public function _initPlugins(Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new XhprofControllerPlugin());
        $dispatcher->registerPlugin(new PermissionControllerPlugin());
        // $dispatcher->registerPlugin(new OAUTH2Plugin());
    }

    public function _initLoader(Dispatcher $dispatcher)
    {
    }

    public function _initModules(Dispatcher $dispatcher)
    {
        // foreach ($dispatcher->getApplication()->getModules() as $key => $module) {
        //     if ( 'index' == strtolower($module)) continue;
        // }

    }

    /**
     * Custom error handler
     * 
     * @param  [type] $errno   [description]
     * @param  [type] $errstr  [description]
     * @param  [type] $errfile [description]
     * @param  [type] $errline [description]
     * @return [type]          [description]
     */
    public static function error_handler($errno,$errstr,$errfile,$errline)
    {
        // See (@link http://www.php.net/set_error_handler)
        
        if (error_reporting() === 0) return;

        throw new ErrorException($errstr,0,$errno,$errfile,$errline);
    }
}

?>