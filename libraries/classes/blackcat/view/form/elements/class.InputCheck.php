<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 16, 2010, 11:50 PM
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
 * Element input checkbox
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class InputCheck extends FormElement
{
    /**
     * Element's match value
     *
     * @var mixed
     */
    private $_matchValue = array();

    /**
     * Element's value
     *
     * @var mixed
     */
    private $_value      = array();

    /**
     * Column
     *
     * @var int
     */
    private $_col        = 4;


    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param mixed $matchValue Match value
     * @param array $attributes Element's attributes
     * @param string $label Element's label
     * @param int $col Column
     *
     * @return void
     */
    public function __construct($name, $value, $matchValue, $attributes = '', $label = '', $col = 4)
    {
        $this->_type       = 'checkbox';
        $this->_value      = $value;
        $this->_col        = $col;
        $this->_label      = $label;

        $this->setValue($matchValue);
        $this->setAttribute('name', $name . '[]');
        $this->updateAttribute(array('class' => 'checkbox'));
        
        if (is_array($attributes) && sizeof($attributes)) 
            $this->updateAttribute($attributes);
    }

    /**
     * Get element's value
     *
     * @return mixed Element's value
     */
    public function getValue()
    {
        $name = $this->getAttribute('name');

        if (isset($_GET[$name])) 
            return $_GET[$name];
        elseif (isset($_POST[$name])) 
            return $_POST[$name];

        return $this->_matchValue;
    }

    /**
     * Set element's match value
     *
     * @param mixed $matchValue Element's match value
     *
     * @return void
     */
    public function setValue($matchValue)
    {
        if (!is_array($matchValue)) {
            if (!empty($matchValue)) 
                $this->_matchValue = array($matchValue);
        } else 
            $this->_matchValue = $matchValue;
    }

    /**
     * Get HTML tag of this element
     *
     * @return string HTML tag of element
     */
    public function toString()
    {
        $attributes = $this->mergeAttribute();

        if (is_array($this->_value) && sizeof($this->_value)) {
            $str = '';

            foreach ($this->_value as $value => $label) {
                $str .= "<span style='vertical-align:middle'><input type=\"checkbox\"  value=\"$value\" "
                     .  ((in_array($value, $this->_matchValue)) ? " checked " : "" ) . " $attributes ></span>"
                     .  "$label\r\n";
            }
        } else {
            $str .= "<input type=\"checkbox\" id=\"" . $this->getAttribute('name') . "\" value='" . $this->_value
                 .  "' " . (in_array($value, $this->_matchValue) ? " checked " : "" ) . " $attributes>$label &nbsp";
        }

        if (!empty($this->_label)) {
            $str = "<fieldset>\r\n"
                 . "<legend>\r\n"
                 . $this->_label . "\r\n"
                 . "</legend>\r\n"
                 . "$str\r\n"
                 . "</fieldset>";
        }

        return $str;
    }
}