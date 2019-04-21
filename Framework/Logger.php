<?php

/**
 * Logger class - Custom errors
 */

namespace Framework;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Record and email/display errors or a custom error message.
 */
class Logger implements LoggerInterface
{

    /**
     * Determines if error should be displayed.
     *
     * @var boolean
     */
    public static $printError = true;

    /**
     * Clear the errorlog.
     *
     * @var boolean
     */
    private static $clear = false;

    /**
     * Path to error file.
     */
    public static function getCurrentErrorLog()
    {
        return ROOT . '/data/logs/log-' . date('Y-m-d') . '.log';
    }

    /**
     * In the event of an error show this message.
     */
    public static function customErrorMsg()
    {
        echo "\n<p>An error occured, The error has been reported.</p>";
        exit();
    }

    /**
     * Saved the exception and calls customer error function.
     *
     * @param \Exception $e
     */
    public static function exceptionHandler($e)
    {
        self::newMessage($e);
        self::customErrorMsg();
    }

    /**
     * Saves error message from exception.
     *
     * @param int $number
     *            error number
     * @param string $message
     *            the error
     * @param string $file
     *            file originated from
     * @param int $line
     *            line number
     *
     * @return int
     */
    public static function errorHandler($number, $message, $file, $line)
    {
        $msg = "$message in $file on line $line";

        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg);
            self::customErrorMsg();
        }

        return 0;
    }

    /**
     * New exception.
     *
     * @param \Exception $exception
     * @return void
     */
    public static function newMessage($exception)
    {
        $trace = $exception->getTraceAsString();
        if (defined('DATABASE')) {
            $trace = str_replace(DATABASE['PASSWORD'], ' ***** ', $trace);
        }

        $logMessage = '[' . date('Y-m-d H:i:s') . '] log.ERROR: '
            . $exception->getMessage() . ' in ' . $exception->getFile()
            . ' on line ' . $exception->getLine() . "\n" . $trace . "\n";

        $errorFile = self::getCurrentErrorLog();

        if (is_file($errorFile) === false) {
            file_put_contents($errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents($errorFile);
        }

        file_put_contents($errorFile, $logMessage . $content);

        if (self::$printError == true) {
            echo $logMessage;
            exit();
        }
    }

    /**
     * Custom error.
     *
     * @param string $error
     */
    public static function errorMessage($error)
    {
        $date       = date('Y-m-d H:i:s');
        $logMessage = "[$date] log.ERROR: $error \n";

        $errorFile = self::getCurrentErrorLog();

        if (is_file($errorFile) === false) {
            file_put_contents($errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents($errorFile);
            file_put_contents($errorFile, $logMessage . $content);
        }

        if (self::$printError == true) {
            echo $logMessage;
            exit();
        }
    }

    /**
     * @param $messageType
     * @param $message
     * @param array $context
     */
    public function writeMessage($messageType, $message, $context = [])
    {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] log.' . $messageType . ': ' . $message . ' [Context: ' . serialize($context) . ']';
        $errorFile  = self::getCurrentErrorLog();

        $errorFile = self::getCurrentErrorLog();

        if (is_file($errorFile) === false) {
            file_put_contents($errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents($errorFile);
            file_put_contents($errorFile, $logMessage . $content);
        }

    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = [])
    {
        $this->writeMessage(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->writeMessage(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = [])
    {
        $this->writeMessage(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->writeMessage(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->writeMessage(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->writeMessage(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->writeMessage(LogLevel::INFO, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        $this->writeMessage(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level = 'message', $message, array $context = [])
    {
        $this->writeMessage($level, $message, $context);
    }

}
