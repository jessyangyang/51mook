<?php
/**
 *  FileHandler class
 *
 * @author Jessyangyang<shawnisun@gmail.com>
 * @version 1.0
 */

namespace local\log;

use \local\log\Handler;
use \local\log\LogMessage;

class FileHandler extends Handler {

    const TYPE_BY_SIZE = 1;
    const TYPE_BY_DATE = 2;

    /**
     * File pointer
     *
     * @var resource
     */
    private $fp;

    /**
     * Buffered logs 
     *
     * @var string
     */
    private $buffers;

    /**
     * Buffered log count
     *
     * @var integer
     */
    private $bufferCount;

    /**
     * Flush buffer when $bufferCount reaches this value
     *
     * @var $bufferFlushCount
     */
    private $bufferFlushCount;

    private $basePath;
    private $filePrefix;
    private $type;
    private $pattern;

    private $extension;

    /**
     * Contruct a FileHandler
     *
     * @param string $name the name for this handler
     * @param string $basePath the base directory for log files
     * @param string $prefix for filename, allowed pattern: '[A-Za-z\.\-\_]*',
     *      prefix should not be blank if type is TYPE_BY_SIZE
     * @param integer $type separate logs by log file size or by date
     * @param mixed $pattern, if type is TYPE_BY_SIZE, then $pattern specifies the
     * file size in bytes, if type is TYPE_BY_DATE, its the pattern for date($pattern) 
     */
    public function __construct($name, $basePath, $prefix = 'log', $type = self::TYPE_BY_DATE, $pattern = 'Ymd', $extension = '.log', $bufferFlushCount = 10) {

        if (!is_dir($basePath) || !is_writable($basePath)) {
            throw new \Exception($basePath . ' is not a directory or has no permission to write');
        }

        $this->basePath = $basePath;

        if (!preg_match('/^[A-Za-z\.\-_]*$/', $prefix)) {
            throw new \Exception('Invalid prefix: ' . $prefix);
        }

        $this->filePrefix = $prefix;

        if ($type == self::TYPE_BY_DATE) {
            $this->pattern = $pattern; 
        } else if ($type == self::TYPE_BY_SIZE) {
            if (strlen($this->filePrefix) < 1) {
                throw new Exception('Prefix should be specified');
            }
            $this->pattern = (int) $pattern;

            if ($this->pattern < 512) {
                throw new \Exception('File size limitation for log file should be larger than 512 bytes');
            }
        } else {
            throw new \Exception('Invalid type for FileHandler');
        }

        parent::__construct($name);

        $this->type = $type;
        $this->extension = trim($extension);

        $this->buffers = '';
        $this->bufferCount = 0;
        $this->bufferFlushCount = (int) $bufferFlushCount;

        $this->open();
    }

    /**
     * Log a message to file
     * 
     * @param $msg a LogMessage
     */
    public function log(LogMessage $msg) {
        if ($this->formatter) {

            $message = $this->formatter->format($msg);

            if (strlen($message) > 0) {
                $this->buffers .= $message;
                $this->bufferCount++;

                if ($this->bufferCount >= $this->bufferFlushCount) {
                    $this->flush();
                }
            }
            
        } else {
            throw new Exception('No log formatter associated with this Handler');
        }
    }

    public function close() {
        $this->flush();
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    public function flush() {
        if ($this->fp && strlen($this->buffers) > 0) {
            fwrite($this->fp, $this->buffers);
            $this->buffers = '';
            $this->bufferCount = 0;
            $this->resetLogFile();
        }
    }

    private function getFullPath() {
        if ($this->type == self::TYPE_BY_DATE) {
            $part = date($this->pattern);
            return $this->basePath . DIRECTORY_SEPARATOR . $this->filePrefix . '-' . $part . $this->extension; 
        } else if ($this->type == self::TYPE_BY_SIZE) {
            return $this->basePath . DIRECTORY_SEPARATOR . $this->filePrefix . $this->extension;
        }
        return null;
    }

    private function open() {
        $fullPath = $this->getFullPath();
        $this->fp = fopen($fullPath, 'a');

        if (!$this->fp) {
            throw new Exception('Failed to open log file ' . $fullPath);
        }
    }

    /**
     * Reset log file for type TYPE_BY_SIZE
     */
    private function resetLogFile() {
        if ($this->type == self::TYPE_BY_SIZE) {
            $fullPath = $this->getFullPath();
            clearstatcache();
            $fileSize = (int) filesize($fullPath);
            if ($fileSize >= $this->pattern) {
                $this->close();
                $i = 1;
                do {
                    $newFile = $fullPath . '.' . $i;
                    $i++;
                } while (file_exists($newFile));
                rename($fullPath, $newFile);
                $this->open();
            }
        }
    }
}

