<?php
/**
 * Abstract Filter class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\LogMessage;

/**
 *  Filter can be userd to control the log message. beyone the control
 *  provided by log levels.
 *
 * Logger or log handlers can have a filter
 */
interface Filter {
	/**
	 * [isLoggable description]
	 * @param  LogMessage $msg [description]
	 * @return boolean         [description]
	 */
	public function isLoggable(LogMessage $msg);
} 

?>