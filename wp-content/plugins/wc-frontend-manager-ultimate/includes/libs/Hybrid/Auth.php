<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

/**
 * Hybrid_Auth class
 *
 * Hybrid_Auth class provide a simple way to authenticate users via OpenID and OAuth.
 *
 * Generally, Hybrid_Auth is the only class you should instanciate and use throughout your application.
 */
class Hybrid_Auth
{

    public static $version = '2.3.0';

    public static $config = [];

    public static $store = null;

    public static $error = null;

    public static $logger = null;

    // --------------------------------------------------------------------


    /**
     * Try to start a new session of none then initialize Hybrid_Auth
     *
     * Hybrid_Auth constructor will require either a valid config array or
     * a path for a configuration file as parameter. To know more please
     * refer to the Configuration section:
     * http://hybridauth.sourceforge.net/userguide/Configuration.html
     */
    function __construct($config)
    {
        self::initialize($config);

    }//end __construct()


    // --------------------------------------------------------------------


    /**
     * Try to initialize Hybrid_Auth with given $config hash or file
     */
    public static function initialize($config)
    {
        if (! is_array($config) && ! file_exists($config)) {
            throw new Exception('Hybriauth config does not exist on the given path.', 1);
        }

        if (! is_array($config)) {
            $config = include $config;
        }

        // build some need'd paths
        $config['path_base']      = realpath(dirname(__FILE__)).'/';
        $config['path_libraries'] = $config['path_base'].'thirdparty/';
        $config['path_resources'] = $config['path_base'].'resources/';
        $config['path_providers'] = $config['path_base'].'Providers/';

        // reset debug mode
        if (! isset($config['debug_mode'])) {
            $config['debug_mode'] = false;
            $config['debug_file'] = null;
        }

        // load hybridauth required files, a autoload is on the way...
        include_once $config['path_base'].'Error.php';
        include_once $config['path_base'].'Exception.php';
        include_once $config['path_base'].'Logger.php';

        include_once $config['path_base'].'Provider_Adapter.php';

        include_once $config['path_base'].'Provider_Model.php';
        include_once $config['path_base'].'Provider_Model_OpenID.php';
        include_once $config['path_base'].'Provider_Model_OAuth1.php';
        include_once $config['path_base'].'Provider_Model_OAuth2.php';

        include_once $config['path_base'].'User.php';
        include_once $config['path_base'].'User_Profile.php';
        include_once $config['path_base'].'User_Contact.php';
        include_once $config['path_base'].'User_Activity.php';

        if (! class_exists('Hybrid_Storage', false)) {
            include_once $config['path_base'].'Storage.php';
        }

        // hash given config
        self::$config = $config;

        // instance of log mng
        self::$logger = new Hybrid_Logger();

        // instance of errors mng
        self::$error = new Hybrid_Error();

        // start session storage mng
        self::$store = new Hybrid_Storage();

        Hybrid_Logger::info('Enter Hybrid_Auth::initialize()');
        Hybrid_Logger::info('Hybrid_Auth::initialize(). PHP version: '.PHP_VERSION);
        Hybrid_Logger::info('Hybrid_Auth::initialize(). Hybrid_Auth version: '.self::$version);
        Hybrid_Logger::info('Hybrid_Auth::initialize(). Hybrid_Auth called from: '.self::getCurrentUrl());

        // PHP Curl extension [http://www.php.net/manual/en/intro.curl.php]
        if (! function_exists('curl_init')) {
            Hybrid_Logger::error('Hybridauth Library needs the CURL PHP extension.');
            throw new Exception('Hybridauth Library needs the CURL PHP extension.');
        }

        // PHP JSON extension [http://php.net/manual/en/book.json.php]
        if (! function_exists('json_decode')) {
            Hybrid_Logger::error('Hybridauth Library needs the JSON PHP extension.');
            throw new Exception('Hybridauth Library needs the JSON PHP extension.');
        }

        // session.name
        if (session_name() != 'PHPSESSID') {
            Hybrid_Logger::info('PHP session.name diff from default PHPSESSID. http://php.net/manual/en/session.configuration.php#ini.session.name.');
        }

        // safe_mode is on
        if (ini_get('safe_mode')) {
            Hybrid_Logger::info('PHP safe_mode is on. http://php.net/safe-mode.');
        }

        // open basedir is on
        if (ini_get('open_basedir')) {
            Hybrid_Logger::info('PHP open_basedir is on. http://php.net/open-basedir.');
        }

        Hybrid_Logger::debug('Hybrid_Auth initialize. dump used config: ', serialize($config));
        Hybrid_Logger::debug('Hybrid_Auth initialize. dump current session: ', self::storage()->getSessionData());
        Hybrid_Logger::info('Hybrid_Auth initialize: check if any error is stored on the endpoint...');

        if (Hybrid_Error::hasError()) {
            $m = Hybrid_Error::getErrorMessage();
            $c = Hybrid_Error::getErrorCode();
            $p = Hybrid_Error::getErrorPrevious();

            Hybrid_Logger::error("Hybrid_Auth initialize: A stored Error found, Throw an new Exception and delete it from the store: Error#$c, '$m'");

            Hybrid_Error::clearError();

            // try to provide the previous if any
            // Exception::getPrevious (PHP 5 >= 5.3.0) http://php.net/manual/en/exception.getprevious.php
            if (version_compare(PHP_VERSION, '5.3.0', '>=') && ( $p instanceof Exception )) {
                throw new Exception($m, $c, $p);
            } else {
                throw new Exception($m, $c);
            }
        }

        Hybrid_Logger::info('Hybrid_Auth initialize: no error found. initialization succeed.');

        // Endof initialize

    }//end initialize()


    // --------------------------------------------------------------------


    /**
     * Hybrid storage system accessor
     *
     * Users sessions are stored using HybridAuth storage system ( HybridAuth 2.0 handle PHP Session only) and can be accessed directly by
     * Hybrid_Auth::storage()->get($key) to retrieves the data for the given key, or calling
     * Hybrid_Auth::storage()->set($key, $value) to store the key => $value set.
     */
    public static function storage()
    {
        return self::$store;

    }//end storage()


    // --------------------------------------------------------------------


    /**
     * Get hybridauth session data.
     */
    function getSessionData()
    {
         return self::storage()->getSessionData();

    }//end getSessionData()


    // --------------------------------------------------------------------


    /**
     * restore hybridauth session data.
     */
    function restoreSessionData($sessiondata=null)
    {
        self::storage()->restoreSessionData($sessiondata);

    }//end restoreSessionData()


    // --------------------------------------------------------------------


    /**
     * Try to authenticate the user with a given provider.
     *
     * If the user is already connected we just return and instance of provider adapter,
     * ELSE, try to authenticate and authorize the user with the provider.
     *
     * $params is generally an array with required info in order for this provider and HybridAuth to work,
     *  like :
     *          hauth_return_to: URL to call back after authentication is done
     *        openid_identifier: The OpenID identity provider identifier
     *           google_service: can be "Users" for Google user accounts service or "Apps" for Google hosted Apps
     */
    public static function authenticate($providerId, $params=null)
    {
        Hybrid_Logger::info("Enter Hybrid_Auth::authenticate( $providerId )");

        // if user not connected to $providerId then try setup a new adapter and start the login process for this provider
        if (! self::storage()->get("hauth_session.$providerId.is_logged_in")) {
            Hybrid_Logger::info("Hybrid_Auth::authenticate( $providerId ), User not connected to the provider. Try to authenticate..");

            $provider_adapter = self::setup($providerId, $params);

            $provider_adapter->login();
        }

        // else, then return the adapter instance for the given provider
        else {
            Hybrid_Logger::info("Hybrid_Auth::authenticate( $providerId ), User is already connected to this provider. Return the adapter instance.");

            return self::getAdapter($providerId);
        }

    }//end authenticate()


    // --------------------------------------------------------------------


    /**
     * Return the adapter instance for an authenticated provider
     */
    public static function getAdapter($providerId=null)
    {
        Hybrid_Logger::info("Enter Hybrid_Auth::getAdapter( $providerId )");

        return self::setup($providerId);

    }//end getAdapter()


    // --------------------------------------------------------------------


    /**
     * Setup an adapter for a given provider
     */
    public static function setup($providerId, $params=null)
    {
        Hybrid_Logger::debug("Enter Hybrid_Auth::setup( $providerId )", $params);

        if (! $params) {
            $params = self::storage()->get("hauth_session.$providerId.id_provider_params");

            Hybrid_Logger::debug("Hybrid_Auth::setup( $providerId ), no params given. Trying to get the stored for this provider.", $params);
        }

        if (! $params) {
            $params = [];

            Hybrid_Logger::info("Hybrid_Auth::setup( $providerId ), no stored params found for this provider. Initialize a new one for new session");
        }

        if (is_array($params) && ! isset($params['hauth_return_to'])) {
            $params['hauth_return_to'] = self::getCurrentUrl();

            Hybrid_Logger::debug("Hybrid_Auth::setup( $providerId ). HybridAuth Callback URL set to: ", $params['hauth_return_to']);
        }

        // instantiate a new IDProvider Adapter
        $provider = new Hybrid_Provider_Adapter();

        $provider->factory($providerId, $params);

        return $provider;

    }//end setup()


    // --------------------------------------------------------------------


    /**
     * Check if the current user is connected to a given provider
     */
    public static function isConnectedWith($providerId)
    {
         return (bool) self::storage()->get("hauth_session.{$providerId}.is_logged_in");

    }//end isConnectedWith()


    // --------------------------------------------------------------------


    /**
     * Return array listing all authenticated providers
     */
    public static function getConnectedProviders()
    {
        $idps = [];

        foreach (self::$config['providers'] as $idpid => $params) {
            if (self::isConnectedWith($idpid)) {
                $idps[] = $idpid;
            }
        }

        return $idps;

    }//end getConnectedProviders()


    // --------------------------------------------------------------------


    /**
     * Return array listing all enabled providers as well as a flag if you are connected.
     */
    public static function getProviders()
    {
         $idps = [];

        foreach (self::$config['providers'] as $idpid => $params) {
            if ($params['enabled']) {
                $idps[$idpid] = [ 'connected' => false ];

                if (self::isConnectedWith($idpid)) {
                    $idps[$idpid]['connected'] = true;
                }
            }
        }

        return $idps;

    }//end getProviders()


    // --------------------------------------------------------------------


    /**
     * A generic function to logout all connected provider at once
     */
    public static function logoutAllProviders()
    {
        $idps = self::getConnectedProviders();

        foreach ($idps as $idp) {
            $adapter = self::getAdapter($idp);

            $adapter->logout();
        }

    }//end logoutAllProviders()


    // --------------------------------------------------------------------


    /**
     * Utility function, redirect to a given URL with php header or using javascript location.href
     */
    public static function redirect($url, $mode='PHP')
    {
        Hybrid_Logger::info("Enter Hybrid_Auth::redirect( $url, $mode )");

        if ($mode == 'PHP') {
            header("Location: $url");
        } else if ($mode == 'JS') {
            echo '<html>';
            echo '<head>';
            echo '<script type="text/javascript">';
            echo 'function redirect(){ window.top.location.href="'.$url.'"; }';
            echo '</script>';
            echo '</head>';
            echo '<body onload="redirect()">';
            echo 'Redirecting, please wait...';
            echo '</body>';
            echo '</html>';
        }

        die();

    }//end redirect()


    // --------------------------------------------------------------------


    /**
     * Utility function, return the current url. TRUE to get $_SERVER['REQUEST_URI'], FALSE for $_SERVER['PHP_SELF']
     */
    public static function getCurrentUrl($request_uri=true)
    {
        if (isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 )
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol.$_SERVER['HTTP_HOST'];

        if ($request_uri) {
            $url .= $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['PHP_SELF'];
        }

        // return current url
        return $url;

    }//end getCurrentUrl()


}//end class
