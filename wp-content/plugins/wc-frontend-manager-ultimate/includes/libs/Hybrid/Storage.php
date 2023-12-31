<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

require_once realpath(dirname(__FILE__)).'/StorageInterface.php';

/**
 * HybridAuth storage manager
 */
class Hybrid_Storage implements Hybrid_Storage_Interface
{


    /**
     * Constructor
     */
    function __construct()
    {
        if (! session_id()) {
            if (! session_start()) {
                throw new Exception("Hybridauth requires the use of 'session_start()' at the start of your script, which appears to be disabled.", 1);
            }
        }

        $this->config('php_session_id', session_id());
        $this->config('version', Hybrid_Auth::$version);

    }//end __construct()


    /**
     * Config
     *
     * @param string $key
     * @param string $value
     */
    public function config($key, $value=null)
    {
        $key = strtolower($key);

        if ($value) {
            $_SESSION['HA::CONFIG'][$key] = serialize($value);
        } else if (isset($_SESSION['HA::CONFIG'][$key])) {
            return unserialize($_SESSION['HA::CONFIG'][$key]);
        }

        return null;

    }//end config()


    /**
     * Get a key
     *
     * @param string $key
     */
    public function get($key)
    {
        $key = strtolower($key);

        if (isset($_SESSION['HA::STORE'], $_SESSION['HA::STORE'][$key])) {
            return unserialize($_SESSION['HA::STORE'][$key]);
        }

        return null;

    }//end get()


    /**
     * GEt a set of key and value
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $key = strtolower($key);

        $_SESSION['HA::STORE'][$key] = serialize($value);

    }//end set()


    /**
     * Clear session storage
     */
    function clear()
    {
        $_SESSION['HA::STORE'] = [];

    }//end clear()


    /**
     * Delete a specific key
     *
     * @param string $key
     */
    function delete($key)
    {
        $key = strtolower($key);

        if (isset($_SESSION['HA::STORE'], $_SESSION['HA::STORE'][$key])) {
            $f = $_SESSION['HA::STORE'];
            unset($f[$key]);
            $_SESSION['HA::STORE'] = $f;
        }

    }//end delete()


    /**
     * Delete a set
     *
     * @param string $key
     */
    function deleteMatch($key)
    {
        $key = strtolower($key);

        if (isset($_SESSION['HA::STORE']) && count($_SESSION['HA::STORE'])) {
            $f = $_SESSION['HA::STORE'];
            foreach ($f as $k => $v) {
                if (strstr($k, $key)) {
                    unset($f[$k]);
                }
            }

            $_SESSION['HA::STORE'] = $f;
        }

    }//end deleteMatch()


    /**
     * Get the storage session data into an array
     *
     * @return array
     */
    function getSessionData()
    {
        if (isset($_SESSION['HA::STORE'])) {
            return serialize($_SESSION['HA::STORE']);
        }

        return null;

    }//end getSessionData()


    /**
     * Restore the storage back into session from an array
     *
     * @param array $sessiondata
     */
    function restoreSessionData($sessiondata=null)
    {
        $_SESSION['HA::STORE'] = unserialize($sessiondata);

    }//end restoreSessionData()


}//end class
