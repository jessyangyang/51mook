<?php
/**
 * Abstract Logger class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\Handler;

/**
* This logger interface is destined for code level 
* logging, such as exception, program errors, etc.
*/
class Logger {
	
	const LEVEL_DEBUG = 10;
	const LEVEL_WARNING = 30;
	const LEVEL_ERROR = 40;
	const LEVEL_INFO = 20;

	/**
	 * [$levlNames Lelver enum]
	 * @var array
	 */
	protected static $levelNames = array(
		self::LEVEL_ERROR => 'ERROR',
		self::LEVEL_WARNING => 'WARNING',
		self::LEVEL_INFO => 'INFO',
		self::LEVEL_DEBUG => 'DEBUG',
	);

	/**
	 * [$name The name of the logger]
	 * @var [type]
	 */
	protected $name;

	/**
	 * [$handlers Keep an array of handlers]
	 * @var array
	 */
	protected $handlers;

	/**
	 * [$level description]
	 * @var [type]
	 */
	protected $level;
	
	protected $filter;

	function __construct($name = 'default')
	{
		$this->name = $name;
		$this->handlers = array();
		// set the default log level to LEVEL_INFO
		$this->level = self::LEVEL_INFO;
	}

	public static function getLevelName($level) {
		if (isset(self::$levelNames[$level])) {
			return self::$levelNames[$level];
		}
		return $level;
	}

	public function addHandler(Handler $handler) {
		$name = $handler->getName();
		if (!isset($this->handlers[$name])) {
			$this->handlers[$name] = $handler;
		} else {
			throw new \Exception('Handler' . $name . ' already exists.'); 
		}
	}

	/**
	 * [log description]
	 * @param  [type] $message [description]
	 * @param  [type] $level   [description]
	 * @return [type]          [description]
	 */
	public function log($message, $level) {
		$msg = new LogMessage($message, $level);
		$this->logMessage($msg);
	}

	/**
	 * [logMessage description]
	 * @param  [type] $msg [description]
	 * @return [type]      [description]
	 */
	public function logMessage(LogMessage $msg) {
		if ($msg->getLevel() < $this->level) {
			return;
		}

		if ($this->filter and !$this->filter->isLoggable($msg)) {
			return;
		}

		foreach ($this->handlers as $key => $handler) {
			$handler->log($msg);
		}
		unset($handler);
	}

	/**
	 * [close all handlers]
	 * @return [type] [description]
	 */
	public function close() {
		foreach ($this->handlers as $key => $handler) {
			$handler->close();
		}
		unset($handler);
	}

	public function debug($message) {
		$this->log($message, self::LEVEL_DEBUG);
	}

	public function info($message) {
		$this->log($message, self::LEVEL_INFO);
	}

	public function warning($message) {
		$this->log($message, self::LEVEL_WARNING);
	}

	public function error($error) {
		$this->log($message, self::LEVEL_ERROR);
	}

	public function getName() {
		return $this->name;
	}

	public function setFilter(Filter $filter) {
		$this->filter = $filter;
	}

	public function getFilter() {
		return $this->filter;
	}

	public function setLevel($level) {
		$this->level = $level;
	}

	public function getLevel() {
		return $this->level;
	}

}

?>