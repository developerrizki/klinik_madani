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
 * Element select box
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class SelectBox extends FormElement
{
    /**
     * Element's match value
     *
     * @var mixed
     */
	private $_matchValue = array();

    /**
     * Options
     *
     * @var array
     */
	private $_options    = array();

    /**
     * Flag to indicate select is multiple group, TRUE on multiple group or FALSE otherwise
     *
     * @var bool
     */
	private $_isGroup    = false;


    /**
     * Constructor
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param array $options Element's options
     * @param mixed $matchValue Match value
     * @param array $attributes Element's attributes
     * @param bool $group Flag to indicate select is multiple group, TRUE on multiple group or FALSE otherwise
     * @param string $label Element's label
     *
     * @return void
     */
    public function __construct($name, $options, $matchValue, $attributes = '', $group = false, $label = '')
    {
	$this->_label   = $label;
	$this->_isGroup = $group;
	$this->_type    = 'select';

	$this->updateAttribute(array('name'  => $name));
	$this->updateAttribute(array('id'    => $name));
	$this->updateAttribute(array('class' => 'select'));
	$this->setValue($matchValue);

	if (is_array($attributes) && sizeof($attributes)) {
	    $this->updateAttribute($attributes);
       }

       if (is_array($options) && sizeof($options)) {
           $this->_options = $options;
       }
    }

    /**
     * Get element's value
     *
     * @return mixed Element's value
     */
    public function getValue()
    {
		$name = $this->getAttribute('name');

		if (isset($_GET[$name])) {
            return $_GET[$name];
        } elseif (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return (array_key_exists('multiple', $this->_attributes)) ? $this->_matchValue : $this->_matchValue[0];
    }

    /**
     * Set element's mathc value
     *
     * @param mixed $matchValue Element's match value
     *
     * @return void
     */
    public function setValue($matchValue)
    {
        if (!is_array($matchValue)) {
            if (!empty($matchValue)) {
                $this->_matchValue = array($matchValue);
            }
        } else {
            $this->_matchValue = $matchValue;
        }
    }

    /**
     * Merge all attribuets
     *
     * @return void
     */
    protected function mergeAttribute()
    {
        $attributes = array();
        $keys       = array_keys($this->_attributes);
        $isMultiple = (in_array('multiple', $keys)) ? true : false;

        if (sizeof($this->_attributes)) {
            foreach ($this->_attributes as $key => $val) {
                if ($val === true) {
                    $attributes[] = $key;
                } elseif ($val === false) {
                    $attributes[] = '';
                } else {
                    if ($isMultiple && $key == 'name') {
                        $val = $val . '[]';
                    }

                    $attributes[] = "$key=\"$val\"";
                }
            }
        }

        return implode(" ", $attributes);
    }

    /**
     * Get HTML tag of element
     *
     * @return string HTML tag of element
     */
    public function toString()
    {
		$strOutput  = "<select " . $this->mergeAttribute() . ">\r\n";

        if (sizeof($this->_options)) {
            if (!$this->_isGroup) {
                foreach ($this->_options as $key => $val) {
                    $selected   = (in_array($key, $this->_matchValue)) ? ' selected ' : '';
                    $strOutput .= "   <option value=\"$key\" $selected>$val</option>\r\n";
                }
            } else {
                foreach ($this->_options as $key => $val) {
                    $strOutput .= "   <optgroup label=\"$key\">\r\n";

                    if (is_array($val) && sizeof($val)) {
                        foreach ($val as $k => $v) {
                            $selected   = (in_array($k, $this->_matchValue)) ? ' selected ' : '';
                            $strOutput .= "      <option value=\"$k\" $selected>$v</option>\r\n";
                        }
                    }

                    $strOutput .= "   </optgroup>\r\n";
                }
            }
        }

		$strOutput .= "</select>\r\n";

		return $this->_label . ' ' . $strOutput;
    }
}