<?php
/**
 * Abstract Handler class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\LogMessage;
/**
* 
*/
abstract class Handler
{
	/**
	 * [$name The name of the handler]
	 * @var [string]
	 */
	protected $name;

	/**
	 * [$filter Message filter]
	 * @var null
	 */
	protected $filter = null;

	/**
	 * [$formatter Message formatter]
	 * @var null
	 */
	protected $formatter = null;
	
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * [log description]
	 * @param  LogMessage $message [message to be loged]
	 * @return [type]              [description]
	 */
	abstract public function log(LogMessage $message);

	/**
	 * [flush the buffered outputs]
	 * @return [type] [description]
	 */
	abstract public function flush();

	/**
	 * [close cloase the handler]
	 * @return [type] [description]
	 */
	abstract public function close();

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getFilter() {
		return $this->filter;
	}

	public function setFilter(Filter $filter) {
		$this->filter = $filter;
	}

	public function getFormatter() {
		return $this->formatter;
	}

	public function setFormatter(Formatter $formatter) {
		$this->formatter = $formatter;
	}
 }

?>