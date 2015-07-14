<?php
/**
 *  SystemLogger class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use local\log\Formatter;
use local\log\LogMessage;

class SimpleFormatter extends Formatter {
    public function format(LogMessage $msg) {
        return '[ ' . date('Y-m-d H:i:s', $msg->getTimestamp()) . ' ] [ ' . Logger::getLevelName($msg->getLevel()) . ' ] ' . $msg->getMessage() . "\n";
    }
}

