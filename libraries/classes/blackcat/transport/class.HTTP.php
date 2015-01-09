<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:16 PM
 *
 * @package   transport
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Registry class
 */
require_once CLASS_DIR . '/blackcat/system/class.Registry.php';


/**
 * HTTP handler class.
 *
 * @package    transport
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 */
class HTTP
{
    /**
     * Redirect to specified destination url
     *
     * @param string $url Destination url
     *
     * @return  void
     */
    static public function redirect($url)
    {
        $dbObj   = Registry::get('db');
        $sessObj = Registry::get('session');

        $dbObj->close();
        $sessObj->updateVar();

        $url     = (empty($url)) ? '/' : $url;

        header("Location: $url");

        exit;
    }

    /**
     * Redirect to specified destination url with an alert
     *
     * @param string $msg Alert message
     * @param string $url Destination url
     */
    static public function alertRedirect($msg, $url)
    {
        echo "
        <script language=Javascript>
        alert('$msg');
        document.location='$url';
        </script>";
    }

    /**
     * Get HTTP GET/POST value sent by client
     *
     * @param string $var Variable name
     *
     * @return string HTTP GET/POST value
     */
    static public function getVar($var)
    {
         if (is_array($_GET)) {
             if (isset($_GET[$var]))
                 $result = $_GET[$var];
         } else {
             GLOBAL $HTTP_GET_VARS;
             if (isset($HTTP_GET_VARS[$var]))
                 $result = $HTTP_GET_VARS[$var];
         }


         if (!isset($result)) {
             if (is_array($_POST)) {
                 if (isset($_POST[$var]))
                     $result = $_POST[$var];
             } else {
                 GLOBAL $HTTP_POST_VAR;
                 if (isset($HTTP_POST_VAR[$var]))
                     $result = $HTTP_POST_VAR[$var];
             }
         }


         if (isset($result)) {
             if (get_magic_quotes_gpc() && !is_array($result))
                 return stripslashes($result);
         } else {
             return false;
         }

         return $result;
    }

    /**
     * Get current url
     *
     * @return string Current url
     */
    static public function getCurrentURL()
    {
        if ($_SERVER['REQUEST_URI']) {
           $url = $_SERVER['REQUEST_URI'];
        } else {
            $url = substr($_SERVER['PHP_SELF'], 1);

            if (isset($_SERVER['QUERY_STRING'])) {
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        $url = substr_replace($url, '', 0, strlen(ROOT_URL));

        return $url;
    }

    /**
     * Get path of current url
     *
     * @return string Path of current url
     */
    static public function getCurrentURLPath()
    {
        $httpCURL   = HTTP::getCurrentURL();
        $curURL     = substr($httpCURL, 0, strrpos($httpCURL, '/'));
        $parURL     = substr($httpCURL, strrpos($httpCURL, '/')+1);

        if (!empty($parURL) && !preg_match("/\./", $parURL) && !preg_match("/\?/", $parURL))
            $curURL = $curURL . "/$parURL";

        return ($curURL) ? $curURL : '/';
    }

    /**
     * Get path and file of current url
     *
     * @return string Path and file of current url
     */
    static public function getCurrentURLPathFile()
    {
        if (ROOT_URL) {
            return substr_replace($_SERVER['PHP_SELF'], '', 0, strlen(ROOT_URL));
        } else {
            return $_SERVER['PHP_SELF'];
        }
    }

    static public function fetchRemote($url)
    {
        $ch    = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);

        return $response;
    }
}