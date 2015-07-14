<?php 
/**
 * LogMessage class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

/**
* 
*/
class LogMessage {

	/**
	 * [$level log level]
	 * @var [integer]
	 */
	protected $level;

	/**
	 * [$message description]
	 * @var [string]
	 */
	protected $message;

	/**
	 * [$timestamp description]
	 * @var [integer]
	 */
	protected $timestamp;
	
	public function __construct($message, $level, $timestamp = null)
	{
		$this->message = trim($message);
		$this->level = (int) $level;

		if (isset($timestamp)) {
			$this->timestamp = (int) $timestamp;
		} else {
			$this->timestamp = time();
		}
	}

	public function getLevel() {
		return $this->level;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setLevel($level) {
		$this->level = $leve;
	}

	public function setMessage($msg) {
		$this->message = $msg;
	}
}
?>