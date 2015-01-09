<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 16, 2010, 11:48 PM
 *
 * @package    form
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 */

/**
 * Loader class
 */
require_once CLASS_DIR . '/blackcat/system/class.Loader.php';

/**
 * Form element factory class
 *
 * @package   form
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class FormFactory
{

    /**
     * Get instance of an element
     *
     * @param string $type Element's type
     * @param array $attr Element's attributes
     *
     * @return object Instance of element
     */
    static public function getInstance($type, $attr)
    {
        try {
            $name        = (isset($attr['name'])) ? $attr['name'] : '';
            $value       = (isset($attr['value'])) ? $attr['value'] : '';
            $matchValue  = (isset($attr['matchValue'])) ? $attr['matchValue'] : '';
            $options     = (isset($attr['options'])) ? $attr['options'] : '';
            $label       = (isset($attr['label'])) ? $attr['label'] : '';
            $group       = (isset($attr['group'])) ? $attr['group'] : '';
            $attrib      = (isset($attr['attr'])) ? $attr['attr'] : '';
            $time        = (isset($attr['time'])) ? $attr['time'] : '';
            $config      = (isset($attr['config'])) ? $attr['config'] : '';

            Loader::loadClass($type, CLASS_DIR . '/blackcat/view/form/elements');

            switch ($type) {
                case 'InputText':
                    $obj = new InputText($name, $value, $attrib, $label);
                break;

                case 'InputPassword':
                    $obj = new InputPassword($name, $value, $attrib, $label);
                break;

                case 'InputHidden':
                    $obj = new InputHidden($name, $value);
                break;

                case 'InputSubmit':
                    $obj = new InputSubmit($name, $value, $attrib);
                break;

                case 'InputReset':
                    $obj = new InputReset($name, $value, $attrib);
                break;

                case 'InputCheck':
                    $obj = new InputCheck($name, $value, $matchValue, $attrib, $label);
                break;

                case 'InputRadio':
                    $obj = new InputRadio($name, $value, $matchValue, $attrib, $label);
                break;

                case 'InputFile':
                    $obj = new InputFile($name, $value, $attrib, $label);
                break;
                
                case 'InputDate':
                    $obj = new InputDate($name, $value, $attrib, $time);
                break;
                
                case 'InputEditor':
                    $obj = new InputEditor($name, $value, $config, $attrib); 
                break;
                
                case 'Button':
                    $obj = new Button($name, $value, $attrib);
                break;

                case 'TextArea':
                    $obj = new TextArea($name, $value, $attrib);
                break;

                case 'SelectBox':
                    $obj = new Selectbox($name, $options, $matchValue, $attrib, $group);
                break;

                default:
                    $obj = NULL;
                break;
            }

            return $obj;
        } catch (IOException $e) { Error::store($e->getMessage()); }

        return null;
    }
}