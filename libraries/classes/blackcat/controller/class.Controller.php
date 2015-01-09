<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:39 PM
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Registry class
 */
require_once(CLASS_DIR . '/blackcat/system/class.Registry.php');

/**
 * Registry class
 */
require_once(CLASS_DIR . '/blackcat/system/class.Loader.php');

/**
 * View class
 */
require_once(CLASS_DIR . '/blackcat/system/class.SystemException.php');

/**
 * Array to XML
 */
require_once(CLASS_DIR . '/blackcat/utils/class.ArrayXML.php');


/**
 * Controller class
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Controller
{
    protected $params;

    /**
     * Theme object
     *
     * @var object
     */
    protected $theme;

    /**
     * View object
     *
     * @var object
     */
    protected $view;

    /**
     * View's template tag
     *
     * @var string
     */
    protected $tags;

    /**
     * Controller's name
     *
     * @var string
     */
    protected $name;

    /**
     * Database object
     */
    protected $sysObj;

    /**
     * Session object
     */
    protected $sessObj;

    /**
     * Response type
     *
     * @var string
     */
    protected $response;

    /**
     * Database object
     */
    protected $dbObj;

    /**
     * Constructor.
     *
     * Create a new instance of this class
     */
    public function __construct()
    {
        $this->tags     = array();
        $this->params   = array();

        $this->dbObj    = Registry::get('db');
        $this->theme    = Registry::get('theme');
        $this->sessObj  = Registry::get('session');
        $this->sysObj   = Registry::get('system');

        $this->view     = new View();
    }

    /**
     * Set response type
     *
     * @param string $response Response type (xml or json)
     *
     * @return void
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setTheme($theme){
        $this->theme = $theme;
    }

    

	/**
	 * Load model
	 *
	 * @param string $model Model
	 *
	 * @return object Model object
	 */
	protected function loadModel($model)
    {
        try {
            $className = $model . 'Model';

            Loader::loadClass($className, ROOT_DIR . '/models');

            if (class_exists($className)) {
                $modelObj  = new $className;

                return $modelObj;
            } else {
                app_shutdown("Model class <i>$className</i> does not exists!");
            }
        } catch (SystemException $e) {
            app_shutdown("Model file <i>$className</i> does not exists!");
        }
    }

	/**
	 * Display results
	 *
	 * @param array $data data
	 * @param bool $success, TRUE if success or FALSE if error
	 *
	 * @return void
	 */
	public function render($data)
	{
		$data = array_merge($data, array('url' => HTTP::getCurrentURL()));
        $data = array_merge($data, array('time' => date('Y-m-d H:i:s')));

		if ($this->response == 'xml') {
			header('Content-type:text/xml');

			$result = ArrayToXML::toXML($data, 'ils');
		} else {
			header('Content-type:application/json');

			$result = json_encode(array('ils' => $data));
		}

		die($result);
	}

    public function setName($name)
    {
        $this->name = $name;
       // $this->view->setControllerName($name);
    }


    protected function set($id, $value)
    {
        $this->tags[$id] = $value;
    }


     protected function getHTML($name, $wrap=true)
    {
        $html = '';

        $this->view->setTemplate($name);

        if (is_array($this->tags) && sizeof($this->tags)) {

            while (list($id, $val) = each($this->tags)) {
                $this->view->setValue($id, $val);
            }
        }

        $this->view->parse();

        return $this->view->toString();
    }


    protected function loadClass($class, $dir='')
    {
        Loader::loadClass($class, $dir);
    }


     protected function loadLib($file)
    {
        $file = ROOT_DIR . '/functions/' . $this->name . "/$file.php";

        try {
            Loader::loadLib($file);
        } catch (SystemException $e) {
            Error::store("Controller", "Error load lib file <i>$file</i>");
        }
    }

    /**
     * Include javascript
     *
     * @param string $name Javascript file name
     *
     * @return void
     */
    protected function addJavaScript($name)
    {
        $this->theme->addJavaScript(ROOT_URL . '/javascript/' . $this->name . "/$name.js");
    }

    protected function isAuthorized($module, $task)
    {
        $rbacObj    = Registry::get('rbac');
        $userObj    = Registry::get('user');

        $userId     = $this->sessObj->getUserID();
        $groups     = $userObj->getGroupIdList($userId);

        return $rbacObj->authorize($groups, $module, $task);
    }


    protected function authorize($module, $task)
    {
        if (!$this->isAuthorized($module, $task)) die ('Unauthorized');
    }


    protected function requireLogin()
    {
        if (!$this->sessObj->getUserID()) HTTP::redirect(ROOT_URL);
    }


    protected function postParam($key)
    {
        return $this->_cleanXSS($_POST[$key]);
    }


    protected function getParam($key)
    {
        return $this->_cleanXSS($_GET[$key]);
    }
	
	private function _cleanXSS($val)
    {
        if (empty($val)) return '';

        # source from http://quickwired.com/kallahar/smallprojects/php_xss_filter_function.php
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
        }
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true;
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
                $val = preg_replace($pattern, $replacement, $val);
                if ($val_before == $val) {$found = false;}
            }
        }
        $allowedtags = "<strong><em><ul><li><pre><hr><blockquote><span>";
        $cstring = strip_tags($val, $allowedtags);
        $cstring = nl2br($cstring);
        return $cstring;
    }
}
?>