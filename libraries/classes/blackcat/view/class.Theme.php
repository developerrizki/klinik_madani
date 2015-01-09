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
 * Parent theme class
 *
 * @package   view
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
abstract class BCTheme 
{
    /**
     * Root template
     *
     * @var string
     */
    protected $_rootTmpl;

    /**
     * Path to css include file
     * 
     * @var string
     */                   
    protected $_cssInclude = array();
    
    /**
     * Path to java script include file
     * 
     * @var string
     */                   
    protected $_jsInclude = array();
    
    /**
     * Javascript call method in body tag
     * 
     * @var array
     */                   
    protected $_bodyScript = array();

    /**
     * Theme name
     * 
     * @var string
     */                   
    protected $_theme;
    
    /**
     * Page title
     *
     * @var string
     */
    protected $_title;
    
    
    /**
     * Constructor.
     * Create an instance of this class
     *
     * @param string $theme Theme name
     *
     * @return void
     */
    public function __construct($theme)
    {
        $this->_theme = $theme; 
    }

    /**
     * Set root template
     *
     * @param string $rootTmpl Root template
     *
     * @return void
     */
    public function setRootTemplate($rootTmpl)
    {
        $this->_rootTmpl = $rootTmpl;
    }
 
    /**
     * Add css include file
     * 
     * @param string $path Path to css file
     * 
     * @return void
     */
    public function addCSS($path)
    {
        if (!in_array($path, $this->_cssInclude))
            $this->_cssInclude[] = $path;
    }
    
    /**
     * Get all user defined css path
     *
     * @return string CSS path
     */
    public function parseCSS()
    {
        $css = '';
        if (sizeof($this->_cssInclude)) {
            for ($i = 0; $i < sizeof($this->_cssInclude); $i++) {
                $css .= '<link type="text/css" media="screen" rel="stylesheet" href="' . $this->_cssInclude[$i]. '"/>' . "\n";
            }
        }
        
        return $css;
    }
    
    /**
     * Add java script include file
     * 
     * @param string $path Path to javascript file
     * 
     * @return void
     */
    public function addJavaScript($path)
    {
        if (!in_array($path, $this->_jsInclude))
            $this->_jsInclude[] = $path;
    }

    /**
     * Get all user defined javascript path
     *
     * @return string Javascript path.
     */
    public function parseJavaScript()
    {
        $js = '';
        if (sizeof($this->_jsInclude)) {
            for ($i = 0; $i < sizeof($this->_jsInclude); $i++) {
                $js .= '<script type="text/javascript" src="' . $this->_jsInclude[$i]. '"></script>' . "\n";
            }
        }
        
        return $js;
    }
    
    /**
     * Add java script function call into body tag
     * 
     * @param string $script Java script function call
     * 
     * @return void
     */
    public function addBodyScript($script)
    {
        $this->_bodyScript[] = $script;
    }
    
    /**
     * Get all user defined javascript path
     *
     * @return string Javascript path.
     */
    public function parseBodyScript()
    {
        return (is_array($this->_bodyScript)) ? implode(' ', $this->_bodyScript) : '';
    }

    /**
     * Set page title
     *
     * @param string $title Page title
     *
     * @return void
     */
    public function setTitle($title)
    {
         $this->_title = $title;
    }
    
    /**
     * Get all parsed html based on specified theme file
     *
     * @return string HTML
     */
    abstract public function toString();
}