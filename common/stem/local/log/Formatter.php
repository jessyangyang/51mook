<?php
/**
 * Abstract Filter class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\LogMessage;
use \local\log\Handler;

/**
 * Formatter used for formatting a LogMessage
 *
 * A hanlder should have a formatter associaterd with it.
 */
abstract class Formatter {

	/**
	 * [format a log message]
	 * @param  LogMessage $msg [description]
	 * @return [string]          [description]
	 */
	abstract public function format(LogMessage $msg);

	/**
	 * [return the header string for a set of formatterd log]
	 * @param  Handler $h [description]
	 * @return [string]     [description]
	 */
	public function getHead(Handler $h) {
		return '';
	}

	/**
	 * [Return the tail string for a set of formatted log]
	 * @param  Handler $h [description]
	 * @return [string]     [description]
	 */
	public function getTail(Handler $h) {
		return '';
	}
}