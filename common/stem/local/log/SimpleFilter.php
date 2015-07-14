<?php
/**
 *  SimpleFilter class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use local\log\Filter;
use local\log\LogMessage;

class SimpleFilter implements Filter {

    public function isLoggable(LogMessage $msg) {
        $message = $msg->getMessage();
        $level = $msg->getLevel();

        if (strlen($message) < 1 || $level < 0) {
            return false;
        }

        return true;
    }
}

