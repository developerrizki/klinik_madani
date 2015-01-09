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
 * Element text area
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class TextArea extends FormElement
{
    /**
     * Element's value
     *
     * @var string
     */
    private $_value;

    /**
     * Constructor
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param string $value Element's value
     * @param array $attributes Element's attributes
     *
     * @return void
     */
    public function __construct($name, $value, $attributes = '')
    {
        $this->_type  = 'textarea';
        $this->_value = $value;

        $this->updateAttribute(array('name'  => $name));
        $this->updateAttribute(array('class'  => 'text'));

        if (is_array($attributes) && sizeof($attributes)) {
            $this->updateAttribute($attributes);
        }
    }

    /**
     * Set element's value
     *
     * @param string $value Element's value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get element's value
     *
     * @return string Element's value
     */
	public function getValue()
	{
		$name = $this->getAttribute('name');

		if (isset($_GET[$name])) {
            return $_GET[$name];
        } elseif (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $this->_value;
	}

	/**
	 * Get HTML tag of element
	 *
	 * @return string HTML tag of element
	 */
    public function toString()
    {
		return "<textarea " . $this->mergeAttribute() . ">" . $this->_value . "</textarea>";
    }
}