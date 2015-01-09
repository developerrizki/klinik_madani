<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 16, 2010, 11:48 PM
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
 * Element button
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 */
class Button extends FormElement
{
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
        $this->_type = 'button';

        $this->updateAttribute(array('class' => 'button'));

        parent::__construct($name, $value, $attributes);
    }
}