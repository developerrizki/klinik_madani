<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 16, 2010, 11:55 PM
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 */

/**
 * FormElement class
 */
require_once CLASS_DIR . '/blackcat/view/form/class.FormElement.php';


/**
 * Element input date
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class InputDate extends FormElement
{
    /**
     * Flag for append time 
     * 
     * @var bool
     */                   
    private $_time = true;
    
    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param string $value Element's value
     * @param array $attributes Element's attributes
     * @param string $time Append time
     *
     * @return void
     */
    public function __construct($name, $value, $attributes='', $time=true)
    {
        $this->_type  = 'date';
        $this->_time  = $time;

        $this->updateAttribute(array('class' => 'text'));

        parent::__construct($name, $value, $attributes);
    }

    /**
     * Get element id
     * 
     * @return string Element id
     */                   
    private function _getElementID()
    {
        return 'id_' . rand();
    }
    
    /**
     * Get HTML tag of this element
     *
     * @return string HTML tag of element
     */
    public function toString()
    {
        global $cfg;
        
        $id       = $this->_getElementID();

        $format   = ($this->_time === 'm' ? '%Y-%m' : ($this->_time ? '%Y-%m-%d %H:%M' : '%Y-%m-%d'));
        $showtime = (($this->_time === true) ? 'true' : 'false'); 
        
        $js = '
        <script type="text/javascript">
        //<![CDATA[
            var cal = Calendar.setup({
                showTime: 24,
                onSelect: function(cal) { cal.hide() }
            });
            
            cal.manageFields("' . $id. '", "' . $this->_attributes['id']  . '", "' . $format . '");
        //]]>
        </script>';
      
        $at = array();
    
        foreach($this->_attributes as $name => $value) {
            $at[] = "$name=\"$value\"";
        }

        $str = '<input type="text"  ' . implode(' ', $at) . '/>&nbsp;'
             . '<input type="button" name="' . $id . '" id="' . $id . '" class="button" value="get date">' . $js;
        
        $themeObj = Registry::get('theme');
        
        // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/calendar/jscal2.css');
        // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/calendar/border-radius.css');
        // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/calendar/mvi/mvi.css');
        
        // $themeObj->addJavaScript(ROOT_URL . '/jscript/calendar/jscal2.js');
        // $themeObj->addJavaScript(ROOT_URL . '/jscript/calendar/lang/en.js');

        echo"
        <script language='javascript' src='".ROOT_URL."/jscript/calendar/jscal2.js'></script>
        <script language='javascript' src='".ROOT_URL."/jscript/calendar/lang/en.js'></script>

        <link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/calendar/jscal2.css'>
        <link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/calendar/border-radius.css'>
        <link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/calendar/mvi/mvi.css'>
        ";
        
        return $str;
    }
}