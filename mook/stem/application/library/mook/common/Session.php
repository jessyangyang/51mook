<?php
/**
* Session Functions Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\common;

use \lib\models\system\Session as customSession;
use \mook\control\common\ImagesManage;
use \Yaf\Registry;

class Session{

	const VERSION = "1.0";

	// Instance Self
    protected static $instance;
	protected $lifetime = 1800; // 有效期，单位：秒（s），默认30分钟
	protected $db;


	public static function instance()
    {
        return self::$instance ? self::$instance : new Session();
    }

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->db = customSession::instance();
		$server = \Yaf\Application::app()->getConfig()->server->toArray();
		$this->lifetime = $server['sessionTime'] ? $server['sessionTime'] : 1800;
		ini_set('session.cookie_lifetime',$this->lifetime);
		ini_set('session.gc_maxlifetime',$this->lifetime);
		session_set_save_handler(
			array(&$this, 'open'),		// 在运行session_start()时执行
			array(&$this, 'close'),		// 在脚本执行完成 或 调用session_write_close() 或 session_destroy()时被执行，即在所有session操作完后被执行
			array(&$this, 'read'),		// 在运行session_start()时执行，因为在session_start时，会去read当前session数据
			array(&$this, 'write'),		// 此方法在脚本结束和使用session_write_close()强制提交SESSION数据时执行
			array(&$this, 'destroy'),	// 在运行session_destroy()时执行
			array(&$this, 'gc')			// 执行概率由session.gc_probability 和 session.gc_divisor的值决定，时机是在open，read之后，session_start会相继执行open，read和gc
		);
		if (session_id() === "") session_start(); // 这也是必须的，打开session，必须在session_set_save_handler后面执行
	}

	// function __destruct() {
 //    }

	/**
	 * session_set_save_handler open方法
	 *
	 * @param $savePath
	 * @param $sessionName
	 * @return true
	 */
	public function open($savePath, $sessionName) {
		return true;
	}

	/**
	 * session_set_save_handler close方法
	 *
	 * @return bool
	 */
	public function close() {
		return $this->gc($this->lifetime);
	}

	/**
	 * 读取session_id
	 *
	 * session_set_save_handler read方法
	 * @return string 读取session_id
	 */
	public function read($sessionId) {
		$row = $this->db->field('session_data')->where("session_id = '$sessionId' AND session_expires >" . UPDATE_TIME)->fetchRow();
		return $row ? $row['session_data'] : '';
	}

	/**
	 * 写入session_id 的值
	 *
	 * @param $sessionId 会话ID
	 * @param $data 值
	 * @return mixed query 执行结果
	 */
	public function write($sessionId, $data) {

		$row = $this->db->where(array('session_id' => $sessionId))->fetchRow();

		$new_expires = UPDATE_TIME + $this->lifetime;

		if ($row) {
			return $this->db->where(array('session_id' => $sessionId))->update(array(
				'session_expires' => $new_expires,
				'session_data' => $data));
		}
		else
		{
			$sid =  $this->db->insert(array(
				'session_id' => $sessionId,
				'session_data' => $data,
				'session_expires' => $new_expires));
			return $sid ? true : false;
		}

		return false;
	}

	/**
	 * 删除指定的session_id
	 *
	 * @param string $sessionId 会话ID
	 * @return bool
	 */
	public function destroy($sessionId) {
		return $this->db->where(array('session_id' => $sessionId))->delete();
	}

	/**
	 * 删除过期的 session
	 *
	 * @param $lifetime session有效期（单位：秒）
	 * @return bool
	*/
	public function gc($lifetime) {
		return $this->db->where("session_expires < " . UPDATE_TIME)->delete();
	}
}
?>