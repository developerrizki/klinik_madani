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
 * Element input file
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class InputFile extends FormElement
{
    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param string $value Element's value
     * @param array $attributes Element's attributes
     * @param string $label Element's label
     *
     * @return void
     */
    public function __construct($name, $value, $attributes = '', $label = '')
    {
        $this->_type  = 'file';
        $this->_label = $label;

        $this->updateAttribute(array('class' => 'text'));

        parent::__construct($name, $value, $attributes);
    }

    /**
     * Set maximum allowed file size for uploading
     *
     * @param int $size Maxium allowed file size for upload
     *
     * @return void
     */
    public function setSize($size)
    {
        $this->setAttribute('size', $size);
    }

    /**
     * Get element's value
     *
     * @return string Element's value
     */
    public function getValue()
    {
        return $_FILE[$this->getAttribute('name')];
    }
}