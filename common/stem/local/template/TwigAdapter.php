<?php
/**
 * TwigAdapter.php
 * 
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

namespace local\template;

require_once(substr(__DIR__,0,strrpos(__DIR__,"common"))."/common/third/twig/lib/Twig/Autoloader.php");
\Twig_Autoloader::register();

use \Yaf\Dispatcher;

class TwigAdapter implements \Yaf\View_Interface
{
	/** @var \Twig_Loader_Filesystem */
	protected $loader;
	/** @var \Twig_Environment */
	protected $twig;

	protected $variables = array();

	protected $script_path;

	/**
	 * @param string $views
	 * @param array  $options
	 */
	public function __construct($views, array $options = array())
	{   
		if ($views == null) {
			$views = APPLICATION_PATH . '/application/views';
		}

		$this->script_path = $views;
		$this->loader = new \Twig_Loader_Filesystem($views);
		$this->twig   = new \Twig_Environment($this->loader, $options);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function __set($name, $value)
	{
		$this->variables[$name] = $value;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->variables[$name];
	}

	/**
	 * @param string $name
	 */
	public function __unset($name)
	{
		unset($this->variables[$name]);
	}

	/**
	 * Return twig instance
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
		return $this->twig;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function assign($name, $value = null)
	{
		$this->variables[$name] = $value;
	}

	/**
	 * @param string $template
	 * @param array  $variables
	 * @return bool
	 */
	public function display($template, $variables = null)
	{
		echo $this->render($template, $variables);
		exit();
	}

	/**
	 * @param string $template
	 * @param array  $variables
	 * @return string
	 */
	public function render($template, $variables = null)
	{
		if ( is_array($variables) )
		{
			$this->variables = array_merge($this->variables, $variables);
		}

		// $tmp_paths = explode('/',$template);
		// if (is_array($tmp_paths)) {
		// 	$tmp_path = '';
		// 	for ($i=0; $i < count($tmp_paths) - 1 ; $i++) { 
		// 		$tmp_path .= '/' . $tmp_paths[$i];
		// 	}

		// 	$this->script_path .= $tmp_path;
		// 	$this->setScriptPath($this->script_path);
		// }
		// return $this->twig->loadTemplate(end($tmp_paths))->render($this->variables);
		
		return $this->twig->loadTemplate($template)->render($this->variables);
	}

	/**
	 * @return string
	 */
	public function getScriptPath()
	{
		$this->script_path = $this->loader->getPaths();

		return reset($this->script_path);
	}

	/**
	 * @param string $templateDir
	 * @return void
	 */
	public function setScriptPath($templateDir)
	{
		$this->script_path = $templateDir;
		$this->loader->setPaths($this->script_path);
	}

	/**
	 * [clearCacheFiles description]
	 * @return [type] [description]
	 */
	public function clearCacheFiles()
	{
		$this->twig->clearCacheFiles();
	}

	/**
	 * [clearTemplateCache description]
	 * @return [type] [description]
	 */
	public function clearTemplateCache()
	{
		$this->twig->clearTemplateCache();
	}
}