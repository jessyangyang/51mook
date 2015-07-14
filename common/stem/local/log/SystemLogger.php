<?php
/**
 *  SystemLogger class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\Logger;

/**
* System logger used to keep the programm logs
*/
class SystemLogger {
	
	protected static $instance;

	private $path;
	private $logger;

	/**
	* Keep contruct protected.
	*/
	protected function __construct($path, $level) 
	{
		$this->path = $path;
		$this->level = $level;

		$fileHandler = new FileHandler('default', $path);
		$simpleFormatter = new SimpleFormatter();
		$fileHandler->setFormatter($simpleFormatter);
		$this->logger = new Logger();
		$this->logger->setLevel($this->level);
		$this->logger->addHandler($fileHandler);
	}

    /**
     * Set the base path for log files
     *
     * @param string $path
     */
    public static function init($path, $level) 
    {
        if (self::$instance) {
            throw new \Exception('Cannot re-initialize System logger');
        }

        self::$instance = new SystemLogger($path, $level);
    }

    public static function error($msg)
    {
        self::log($msg, Logger::LEVEL_ERROR);
    }

    public static function warning($msg)
    {
        self::log($msg, Logger::LEVEL_WARNING);
    }

    public static function info($msg)
    {
        self::log($msg, Logger::LEVEL_INFO);
    }

    public static function debug($msg)
    {
        self::log($msg, Logger::LEVEL_DEBUG);
    }

    public static function close() 
    {
        if (self::$instance) {
            self::$instance->logger->close();
            self::$instance = null;
        }
    }

    private static function log($msg, $level)
    {
        if (self::$instance) {
            self::$instance->logger->log($msg, $level);
        }
    }

    private function getLogger() 
    {
        return $this->logger;
    }
}

