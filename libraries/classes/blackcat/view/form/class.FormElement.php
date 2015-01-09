<?php
/**
 * BlackCat PHP 5 Framework
 *
 *Last updated: June 16, 2010, 11:46 PM
 *
 * @package   form
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @author    Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version   1.0
 * @copyright Copyright (c) 2005-2010 Lorensius W. L. T
 */


/**
 * Form element abstract class
 *
 * @package    form
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
abstract class FormElement
{
    /**
     * Element's label
     *
     * @var string
     */
	protected $_label;

    /**
     * Element's type
     *
     * @var string
     */
	protected $_type;

    /**
     * Element's attributes
     *
     * @var array
     */
	protected $_attributes = array();

    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attributes
     *
     * @return void
     */
	public function __construct($name, $value, $attributes = '')
	{
        $this->updateAttribute(array('name'  => $name));
        $this->updateAttribute(array('id'    => $name));
        $this->updateAttribute(array('value' => $value));

        if (is_array($attributes) && sizeof($attributes)) 
            $this->updateAttribute($attributes);
	}

    /**
     * Set element's label
     *
     * @param string $label Element's label
     *
     * @return void
     */
	public function setLabel($label)
	{
		$this->_label = $label;
	}

    /**
     * Get element's label
     *
     * @return string Element's label
     */
	public function getLabel()
	{
		return $this->_label;
	}

	/**
	 * Set element's type
	 *
	 * @param string $type Element's type
	 *
	 * @return void
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}

    /**
     * Get element's type
     *
     * @return string Element's type
     */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Set element's value
	 *
	 * @param mixed $value Element's value
	 *
	 * @return void
	 */
	public function setValue($value)
	{
		$this->updateAttribute(array('value' => $value));
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
        
        return $this->getAttribute('value');
    }

	/**
	 * Set element's attribute
	 *
	 * @param string $key Attribute's key
	 * @param string $val Attribute's value
	 *
	 * @return void
	 */
    public function setAttribute($key, $val)
    {
        $this->_attributes[$key] = $val;
    }

    /**
     * Get attribute's value
     *
     * @param string $key Attribute's key
     *
     * @return string Attribute's value
     */
    public function getAttribute($key)
    {
        return (array_key_exists($key, $this->_attributes)) ? $this->_attributes[$key] : '';
    }

    /**
     * Update attributes
     *
     * @param array $attributes Element's attributes. Format: array('key' => 'val')
     *
     * @return void
     */
    public function updateAttribute($attributes)
    {
        if (is_array($attributes) && sizeof($attributes)) 
            $this->_attributes = array_merge($this->_attributes, $attributes);
    }

    /**
     * Remove attribute
     *
     * @param string $key Attribute's key
     *
     * @return void
     */
	public function removeAttribute($key)
	{
		if (array_key_exists($key, $this->_attributes)) 
			unset($this->_attributes[$key]);
	}

    /**
     * Merge attributes
     *
     * @return void
     */
    protected function mergeAttribute()
    {
        $attributes = array();

        if (sizeof($this->_attributes)) {
            foreach ($this->_attributes as $key => $val) {
                if ($val === true) 
                    $attributes[] = $key;
                elseif ($val === false) 
                    $attributes[] = '';
                else 
                    $attributes[] = "$key=\"$val\"";
            }
        }

        return implode(" ", $attributes);
    }

    /**
     * Get HTML tag of this element
     *
     * @return string HTML tag of element
     */
	public function toString()
	{
        return '<input type="' . $this->_type . '" ' . $this->mergeAttribute() . '/>';
    }
}