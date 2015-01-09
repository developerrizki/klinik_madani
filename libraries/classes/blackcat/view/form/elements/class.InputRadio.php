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
class InputRadio extends FormElement
{
    /**
     * Element's match value
     *
     * @var mixed
     */
    private $_matchValue;

    /**
     * Element's value
     *
     * @var mixed
     */
    private $_value      = array();


    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param mixed $matchValue Match value
     * @param array $attributes Element's attributes
     * @param string $label Element's label
     *
     * @return void
     */
    public function __construct($name, $value, $matchValue, $attributes = '', $label = '')
    {
        $this->_type        = 'radio';
        $this->_value       = $value;
        $this->_label       = $label;
        $this->_matchValue  = $matchValue;

        $this->setAttribute('name', $name);

        if (is_array($attributes) && sizeof($attributes)) {
            $this->updateAttribute($attributes);
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

        return $this->_matchValue;
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
        $this->_matchValue = $matchValue;
    }

    /**
     * Get HTML tag of element
     *
     * @return string HTML tag of element
     */
    public function toString()
    {
        $attributes = $this->mergeAttribute();

		foreach($this->_value as $value => $label){
			$str .= "<input type=\"radio\" value=\"$value\" $attributes "
                 .  ($value == $this->_matchValue ? ' checked="checked" ' : '')
                 .  " id=>$label&nbsp;\r\n";

		}

		if (!empty($this->_label)) {
			$str = "<fieldset>\r\n"
                 . "    <legend>\r\n"
                 . "       "
                 . $this->_label . "\r\n"
                 . "    </legend>\r\n"
                 . "    $str\r\n"
                 . "</fieldset>";
		}

		return $str;
    }
}