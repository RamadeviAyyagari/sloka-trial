<?php

/**
 * Session Class
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Framework;

/**
 * Prefix sessions with useful methods.
 */
class Session
{

    /**
     * Determine if session has started.
     *
     * @var boolean
     */
    private static $sessionStarted = false;

    /** @var \PDO */
    private static $database;

    /** @var string */
    private static $tableName;

    /**
     * if session has not started, start sessions
     */
    public static function init($name = 'APPSESS', $lifetime = 288000)
    {
        if (self::$sessionStarted == false && !headers_sent()) {
            unset($_COOKIE['PHPSESSID']);
            session_name($name);

            /*
             * Set security options
             */
            $sessionHash = "sha512";
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set("session.cookie_httponly", 1);
            ini_set("session.use_trans_sid", 0);
            ini_set("session.cookie_secure", Security::isHttps() ? 1 : 0);
            ini_set("session.gc_maxlifetime", $lifetime);
            ini_set("session.hash_bits_per_character", 8);
            if (in_array($sessionHash, hash_algos())) {
                ini_set("session.hash_function", $sessionHash);
            }

            session_start();
            self::regenerate();
            self::$sessionStarted = true;
        }
    }

    /**
     * Add value to a session.
     *
     * @param string $key
     *            name the data to save
     * @param string $value
     *            the data to save
     */
    public static function set($key, $value = null)
    {
        /*
         * Check whether session is set in array or not
         * If array then set all session key-values in foreach loop
         */
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                $_SESSION[Box::$framework['sessionPrefix'] . $name] = $value;
            }
        } else {
            $_SESSION[Box::$framework['sessionPrefix'] . $key] = $value;
        }
    }

    /**
     * Extract item from session then delete from the session, finally return the item.
     *
     * @param string $key
     *            item to extract
     *
     * @return mixed|null return item or null when key does not exists
     */
    public static function delete($key)
    {
        if (isset($_SESSION[Box::$framework['sessionPrefix'] . $key])) {
            $value = $_SESSION[Box::$framework['sessionPrefix'] . $key];
            unset($_SESSION[Box::$framework['sessionPrefix'] . $key]);
            return $value;
        }

        return null;
    }

    /**
     * Get item from session.
     *
     * @param string $key
     *            item to look for in session
     * @param boolean $secondkey
     *            if used then use as a second key
     *
     * @return mixed|null returns the key value, or null if key doesn't exists
     */
    public static function get($key, $secondkey = false)
    {
        if ($secondkey == true) {
            if (isset($_SESSION[Box::$framework['sessionPrefix'] . $key][$secondkey])) {
                return $_SESSION[Box::$framework['sessionPrefix'] . $key][$secondkey];
            }
        } else {
            if (isset($_SESSION[Box::$framework['sessionPrefix'] . $key])) {
                return $_SESSION[Box::$framework['sessionPrefix'] . $key];
            }
        }

        return null;
    }

    /**
     * id
     *
     * @return string with the session id.
     */
    public static function id()
    {
        return session_id();
    }

    /**
     * Regenerate session_id.
     *
     * @return string session_id
     */
    public static function regenerate()
    {
        session_regenerate_id(true);
        return session_id();
    }

    /**
     * Empties and destroys the session.
     *
     * @param string $key
     *            - session name to destroy
     * @param boolean $prefix
     *            - if set to true clear all sessions for current SESSION_PREFIX
     *
     */
    public static function destroy($key = '', $prefix = false)
    {
        /*
         * if key is empty and $prefix is false
         */
        if ($key == '' && $prefix == false) {
            session_unset();
            session_destroy();
        } else if ($prefix == true) {
            /*
             * clear all session for set SESSION_PREFIX
             */
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, Box::$framework['sessionPrefix']) === 0) {
                    unset($_SESSION[$key]);
                }
            }
        } else {
            /*
             * clear specified session key
             */
            unset($_SESSION[Box::$framework['sessionPrefix'] . $key]);
        }
    }

    /*
     * Database session handlers
     */
    public static function initDatabaseSessions($pdoConnection, $tableName = null, $sessionName = 'APPSESS', $lifetime = 288000)
    {
        /*
         * 'CREATE TABLE ' . self::$tableName . '(
                id VARCHAR(128) NOT NULL,
                client_ip VARCHAR(15) NULL DEFAULT NULL,
                last_access DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                data TEXT NULL DEFAULT NULL,
                PRIMARY KEY (id));'
         */
        if ($tableName == null) {
            $tableName = (Box::$application['sessionsTableName'] ?? 'sessions');
        }

        self::$database  = $pdoConnection;
        self::$tableName = $tableName;

        // Set handler to override SESSION
        session_set_save_handler(
            [
                __CLASS__,
                "_session_open",
            ],
            [
                __CLASS__,
                "_session_close",
            ],
            [
                __CLASS__,
                "_session_read",
            ],
            [
                __CLASS__,
                "_session_write",
            ],
            [
                __CLASS__,
                "_session_destroy",
            ],
            [
                __CLASS__,
                "_session_gc",
            ]
        );

        self::init($sessionName = 'APPSESS', $lifetime = 288000);
    }

    /**
     * Open session
     * @return bool
     */
    public static function _session_open()
    {
        if (self::$database) {
            return true;
        }

        return false;
    }

    /**
     * Close session
     * @return bool
     */
    public static function _session_close()
    {
        return true;
    }

    /**
     * Read session
     * @param $id
     * @return string
     */
    public static function _session_read($id)
    {
        // Set query
        $stmt = self::$database->prepare('SELECT `data` FROM ' . self::$tableName . ' WHERE id = :id');
        if ($stmt) {
            // Bind the Id
            $stmt->bindParam(':id', $id);
            // Attempt execution
            // If successful
            if ($stmt->execute()) {
                // Save returned row
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                // Return the data
                return base64_decode($row['data']);
            } else {
                // Return an empty string
                return '';
            }
        }
    }

    /**
     * Write session
     * @param $id
     * @param $data
     * @return bool
     */
    public static function _session_write($id, $data)
    {
        // Create time stamp
        $access = date('Y-m-d H:i:s', time());
        // Set query
        $stmt = self::$database->prepare('REPLACE INTO ' . self::$tableName . ' VALUES (:id, :client_ip, :last_access, :data)');
        // Bind data
        if ($stmt) {
            $data = base64_encode($data);
            $ip   = Security::getClientIP();
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':last_access', $access);
            $stmt->bindParam(':client_ip', $ip);
            $stmt->bindParam(':data', $data);
            // Attempt Execution
            // If successful
            if ($stmt->execute()) {
                // Return True
                return true;
            }
        }

        // Return False
        return false;
    }

    /**
     * Destroy session
     * @param $id
     * @return bool
     */
    public static function _session_destroy($id)
    {
        // Set query
        $stmt = self::$database->prepare('DELETE FROM ' . self::$tableName . ' WHERE id = :id');
        if ($stmt) {
            // Bind data
            $stmt->bindParam(':id', $id);
            // Attempt execution
            // If successful
            if ($stmt->execute()) {
                // Return True
                return true;
            }
        }

        // Return False
        return false;
    }

    /**
     * GC
     * @param $max
     * @return bool
     */
    public static function _session_gc($max)
    {
        // Calculate what is to be deemed old
        $old = date('Y-m-d H:i:s', (time() - $max));
        // Set query
        $stmt = self::$database->prepare('DELETE FROM ' . self::$tableName . ' WHERE last_access < :old');
        if ($stmt) {
            // Bind data
            $stmt->bindParam(':old', $old);
            // Attempt execution
            if ($stmt->execute()) {
                // Return True
                return true;
            }
        }

        // Return False
        return false;
    }
}
