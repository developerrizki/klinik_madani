<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 06, 2010, 13:41 PM
 *
 * @package   view
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Simple template management
 *
 * @package   view
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class View
{
    /**
     * Tag list
     *
     * @var array
     */
    private $_tag = array();

    /**
     * HTML contents
     *
     * @var string
     */
    private $_contents;

    /**
     * Start tag
     *
     * @var string
     */
    private $_startTag = "[--";

    /**
     * End tag
     *
     * @var string
     */
    private $_endTag = "--]";

    /**
     * Default path
     *
     * @var string
     */
    private $_rootPath;
    
    /**
     * Template name
     *
     * @var string
     */
    private $_templateName;
    
    /**
     * Controller name
     *
     * @var string
     */
    private $_controllerName;
    
    
    /** Constructor.
      * Create a new instance of this class
      *
      * @return void
      */
    public function __construct()
    {
        $this->_rootPath = ROOT_DIR . '/views';
    }

    /**
     * Set template default path
     *
     * @param string $path Template default path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->_rootPath = $path;
    }

    /**
     * Set controller name
     *
     * @param string $controller Controller name
     *
     * @return void
     */
    public function setControllerName($controller)
    {
        $this->_controllerName = strtolower($controller);
    }
    
    /**
     * Set start and end tag
     *
     * @param string $startTag Start tag
     * @param string $endTag End tag
     *
     * @return void
     */
    public function setTag($startTag, $endTag)
    {
        $this->_startTag = $startTag;
        $this->_endTag   = $endTag;
    }
    
    /**
     * Set template file
     *
     * @param string $tmplName Template name
     *
     * @return void
     */
    public function setTemplate($tmplName)
    {
        $this->_templateName = $tmplName;
    }
    
    /**
     * Set value for a tag
     *
     * @param string $tagName Tag name
     * @param string $value Tag value
     *
     * @return void
     */
    public function setValue($tagName, $value)
    {    
        if (is_array($tagName)) {
            while (list($key,$val) = each($tagName)) {
                $this->_tag[$key] = $val;
            }
        } else {
            $this->_tag[$tagName] = $value;
        }
    }
    
    /**
     * Create tag
     *
     * @param string $tag Tag
     *
     * @return void
     */
    private function createTag($tag)
    {
        return $this->_startTag . $tag . $this->_endTag;
    }

    /**
     * Parse  template , replace all tags with it's corresponding value
     *
     * @return void
     */
    public function parse()
    {
        $tmplFile = $this->_rootPath . '/' . ((!empty($this->_controllerName)) ? $this->_controllerName . '/' : '') . $this->_templateName . '.tpl';
  
        if (!file_exists($tmplFile)) {
            Error::store('View', "Template <i>$tmplFile</i> does not exists!");
            
            return;
        }

        $this->_contents = implode('', file($tmplFile));
                           
        if (is_array($this->_tag) && sizeof($this->_tag)) {

            while (list($key, $val) = each($this->_tag)) {
                $tag                = $this->createTag($key);
                $this->_contents    = str_replace($tag, $val, $this->_contents);    
            }            
        }
    }

    /**
     * Render html contents
     *
     * @return void
     */
    public function render()
    {
        echo $this->_contents;
    }

    /**
     * Get contents
     *
     * @return string parsed template
     */
    public function getContents()
    {
        return $this->_contents;
    }
    
    /**
     * Get contents.
     * Alias for getContents function
     *
     * @return string parsed template
     */
    public function toString()
    {
        return $this->getContents();
    }
}