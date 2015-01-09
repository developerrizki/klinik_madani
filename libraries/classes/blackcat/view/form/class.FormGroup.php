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
 * FormFactory class
 */
require_once CLASS_DIR . '/blackcat/view/form/class.FormFactory.php';

/**
 * Error handler class
 */
require_once CLASS_DIR . '/blackcat/system/class.Error.php';

/**
 * Registry class
 */
require_once CLASS_DIR . '/blackcat/system/class.Registry.php';

/**
 * Form group class
 *
 * @package    form
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 */
class FormGroup
{
    /**
     * Form's name
     *
     * @var string
     */
    private $_name;

    /**
     * Form's title
     *
     * @var string
     */
    private $_title;

    /**
     * Form's action
     *
     * @var string
     */
    private $_action;

    /**
     * Form's method
     *
     * @var string
     */
    private $_method;

    /**
     * Form's enctype
     *
     * @var string
     */
    private $_enctype;

    /**
     * Target
     *
     * @var string
     */
    private $_target;

    /**
     * Row of element
     *
     * @var array
     */
    private $_rowElement  = array();

    /**
     * Element container
     *
     * @var array
     */
    private $_tempElement = array();

    /**
     * Form's description
     *
     * @var array
     */
    private $_description = array();

    /**
     * Forms' rules
     *
     * @var array
     */
    private $_rules = array();

    private $_legend = array();

    /**
     * Form's filter
     *
     * @var array
     */
    private $_filter = array();

    /**
     * Form's error message
     *
     * @var array
     */
    private $_err = array();

    /**
     * Container width
     *
     * @var int
     */
    // private $_width = 900;

    /**
     * Element's note
     *
     * @var string
     */
    private $_note;

    /**
     * Form's value
     *
     * @var mixed
     */
    private $_value;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $name Form's name
     * @param string $action Form's action
     * @param string $method Form's method
     * @param string $enctype Form's enctype
     * @param string $target Target
     *
     * @return void
     */
    public function __construct($name = '', $action = '', $method = 'post', $enctype = 'MULTIPART/FORM-DATA', $target = '')
    {
        $this->_name    = $name;
        $this->_action  = $action;
        $this->_method  = $method;
        $this->_enctype = $enctype;
        $this->_target  = $target;
    }

    /**
     * Set container width
     *
     * @param int $width Container width
     *
     * @return void
     */
    // public function setWidth($width)
    // {
    //     $this->_width = $width;
    // }

    /**
     * Set value
     *
     * @param mixed $value Value
     *
     * @return void
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            if (!empty($value)) {
                $this->_value = array($value);
            }
        } else {
            $this->_value = $value;
        }
    }

    /**
     * Set form's title
     *
     * @param string $title Form's title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Add input text element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     * @param string $label Element's label (optional)
     *
     * @return void
     */
    public function addText($name, $value, $attributes = '', $label = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputText', array('name'  => $name,
                                                                'value' => $value,
                                                                'attr'  => $attributes,
                                                                'label' => $label)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input date element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     * @param string $time TRUE if append time and vice versa (optional)
     *
     * @return void
     */
    public function addDate($name, $value, $attributes = '', $time = true)
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputDate', array('name'  => $name,
                                                                'value' => $value,
                                                                'attr'  => $attributes,
                                                                'time'  => $time)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input editor element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addEditor($name, $value = '', $config = '', $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputEditor', array('name'   => $name,
                                                                  'config' => $config,
                                                                  'value'  => $value,
                                                                  'attr'   => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input password element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     * @param string $label Element's label (optional)
     *
     * @return void
     */
    public function addPassword($name, $value, $attributes = '', $label = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputPassword', array('name'  => $name,
                                                                    'value' => $value,
                                                                    'attr'  => $attributes,
                                                                    'label' => $label)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input file element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     * @param int $size Maximum uploaded file size (optional)
     *
     * @return void
     */
    public function addFile($name, $value, $attributes = '', $size = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputFile', array('name'  => $name,
                                                                'value' => $value,
                                                                'attr'  => $attributes)
                                            );
            if (!empty($size)) {
                $eObj->setSize($size);
            }

            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input file element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param mixed $matchValue Element's match value
     * @param array $attributes Element's attribuets (optional)
     * @param int $col Column (optional)
     *
     * @return void
     */
    public function addCheckBox($name, $value, $matchValue, $attributes = '', $col = 4)
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputCheck', array('name'  => $name,
                                                                 'value' => $value,
                                                                 'matchValue' => $matchValue,
                                                                 'attr'  => $attributes)
                                            );

            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input radio element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param mixed $matchValue Element's match value
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addRadio($name, $value, $matchValue, $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputRadio', array('name'  => $name,
                                                                 'value' => $value,
                                                                 'matchValue' => $matchValue,
                                                                 'attr'  => $attributes)
                                            );

            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add select box element
     *
     * @param string $name Element's name
     * @param mixed $optins Element's options
     * @param mixed $matchValue Element's match value
     * @param array $attributes Element's attribuets (optional)
     * @param bool $group Flag to indicate select is multiple group, TRUE on multiple group or otherwise (optional)
     * @param string $label Element's label (optional)
     *
     * @return void
     */
    public function addSelect($name, $options, $matchValue, $attributes = '', $group = false, $label = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('SelectBox', array('name'       => $name,
                                                                'options'    => $options,
                                                                'matchValue' => $matchValue,
                                                                'attr'       => $attributes,
                                                                'group'      => $group,
                                                                'label'      => $label)
                                             );

            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add textarea element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addTextArea($name, $value, $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('TextArea', array('name'  => $name,
                                                               'value' => $value,
                                                               'attr'  => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input hidden element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addHidden($name, $value, $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputHidden', array('name'  => $name,
                                                                  'value' => $value,
                                                                  'attr'  => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input submit element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value (optional, default is submit)
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addSubmit($name, $value = 'submit', $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputSubmit', array('name'  => $name,
                                                                  'value' => $value,
                                                                  'attr'  => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add input reset element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value (optional, default is reset)
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addReset($name, $value = 'reset', $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('InputReset', array('name'  => $name,
                                                                 'value' => $value,
                                                                 'attr'  => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add button element
     *
     * @param string $name Element's name
     * @param mixed $value Element's value (optional, default is button)
     * @param array $attributes Element's attribuets (optional)
     *
     * @return void
     */
    public function addButton($name, $value = 'button', $attributes = '')
    {
        if (!array_key_exists($name, $this->_tempElement)) {
            $eObj = FormFactory::getInstance('Button', array('name'  => $name,
                                                             'value' => $value,
                                                             'attr'  => $attributes)
                                            );
            if ($eObj != null) $this->_tempElement[$name] = $eObj;
        } else {
            Error::store("Form: Element <i>$name</i> already exists!");
        }
    }

    /**
     * Add string
     *
     * @param string $label String label
     * @param string $string String
     *
     * @return void
     */
    public function addString($label, $string)
    {
        $this->_rowElement[] = array('label' => $label, 'string' => $string);
    }

    /**
     * Add element's rule
     *
     * @param string $name Element's name
     * @param string $rule Element's rule
     * @param mixed $args Rule arguments
     *
     * @return void
     */
    public function addRule($name, $rule, $arg = null)
    {
        $this->_rules[$name][] = array('rule' => $rule, 'arg' => $arg);
    }

    /**
     * Add description text
     *
     * @param string $name Name
     * @param string $string Description text
     *
     * @return void
     */
    public function addDesc($name, $string)
    {
        $this->_tempElement["***$name"] = $string;
    }

    /**
     * Add note to a an element
     *
     * @param string $name Element's name
     * @param string $note Element's note
     *
     * @return void
     */
    public function addNote($name, $note)
    {
        $this->_note[$name] = $note;
    }

    /**
     * Add span area betwen element row
     *
     * @param mixed $id Span id
     *
     * @return void
     */
    public function addArea($id, $value='')
    {
        $this->_rowElement[] = array('type' => 'area', 'id' => $id, 'value' => $value);
    }

    /**
     * Group elements in a row
     *
     * @param string $label Label for elements in a row (optional, default is &nbsp;)
     * @param string $separator Separator between elements (optional)
     * @param string $type Type
     *
     * @return void
     */
    public function groupAsRow($label = '', $separator = '&nbsp;', $type = 'element')
    {
        $this->_rowElement[] = array('type'      => $type,
                                     'element'   => $this->_tempElement,
                                     'label'     => $label,
                                     'separator' => $separator
                                    );

        $this->_tempElement  = array();
    }

    /**
     * Check if form has been submitted
     *
     * @return bool TRUE on submited or FALSE otherwise
     */
    public function submitted()
    {
        if ($_POST['submitted']) {
            return $_POST['submitted'];
        } else {
            return $_GET['submitted'];
        }
    }

    public function groupAsLegend($title, $width='')
    {
        $this->_legend[sizeof($this->_rowElement) - 1]['title'] = $title;
        $this->_legend[sizeof($this->_rowElement) - 1]['width'] = $width;
    }

    /**
     * Validate elements
     *
     * @return bool TRUE on valid or FALSE otherwise
     */
    public function validateElement()
    {
        $this->loadValidator();

        if (sizeof($this->_rowElement)) {

            foreach ($this->_rowElement as $row => $element) {

                if (is_array($element['element']) && sizeof($element['element'])) {

                    foreach ($element['element'] as $name => $item) {

                        if (array_key_exists($name, $this->_rules)) {
                            $itemRules = $this->_rules[$name];
                            $err       = array();

                            foreach ($itemRules as $rule) {

                                if (is_array($rule['arg']) && sizeof($rule['arg'])) {
                                    $param = $rule['arg'];

                                    array_unshift($param, $item->getValue());
                                } else {
                                    $param = array($item->getValue());
                                }

                                $string = @call_user_func_array($rule['rule'], $param);

                                if (!$string) continue;

                                array_unshift($err, $string);
                            }

                            if (sizeof($err) != 0) {
                                $this->_err[$name] = implode(" , ", $err);
                            }
                        }
                    }
                }
            }
        }

        return (sizeof($this->_err)) ? false : true;
    }

    public function toString()
    {
        global $cfg;

        $sessObj  = Registry::get('session');
        $sysObj   = Registry::get('system');
        $themeObj = Registry::get('theme');

        $elm2     = '';
        $ps       = false;

        foreach ($this->_rowElement as $row => $item) {
            $tmp = '';

            if (is_array($item['element']) && sizeof($item['element'])) {
                $elementNum = sizeof($item['element']);
                $num        = 0;

                foreach ($item['element'] as $name => $element) {
                    $err = '';
                    if (sizeof($this->_err)) {
                        if (array_key_exists($name, $this->_err)) {
                            $err = "&nbsp;<span class=\"error\">" . $this->_err[$name] . " !</span>";
                        }
                    }

                    if (substr($name, 0, 3) == '***') {
                        $tmp .= "<span class='desc'><i>$element</i></span>";
                    } else {
                        $element->setValue($element->getValue());

                        $asterix = '';
                        if (sizeof($this->_rules)) {
                            if (array_key_exists($name, $this->_rules)) {

                                $rule = $this->_rules[$name];
                                if ($rule[0]['rule'] == 'required') {
                                    $asterix = ' *';
                                    $ps      = true;
                                }
                            }
                        }

                        $sid  = $name . '_elmError';
                        $note =  (isset($this->_note[$name])) ? $this->_note[$name] : '';
                        $tmp .= $element->toString() . " $note $asterix $err <span class=\"error\" id=\"$sid\"></span>";
                    }

                    if ($num !=  ($elementNum - 1)) $tmp .=  $item['separator'];

                    $num++;
                }
            }


           $elm .= (empty($item['label'])) ? ((($item['type'] == 'area')) ?  "<span id=\"$item[id]\">$item[value]</span>" : "<br />$tmp\n") : "<label>$item[label]</label>\n$tmp\n";

            if (isset($this->_legend[$row])) {
                if (!empty($this->_legend[$row]['title'])) {
                    $width = (!empty($this->_legend[$row]['width'])) ? 'style="width:'.$this->_legend[$row]['width'].'"' : '';
                    $elm2 .= "<fieldset $width><legend >" . $this->_legend[$row]['title'] . "</legend>$elm</fieldset>";
                } else
                    $elm2 .= $elm;

                $elm   = '';
            }
        }

        $error     = $sessObj->getVar('formError');

        $error     = (!empty($error)) ? "<div class=\"errormsg\"><b><u>Error</u></b><br />$error</div>\n" : '';

        if (!empty($this->_action)) {
            $str  .= "<div id=\"FormContainer\" style=\"width:" . $this->_width . "px\">\n$error"
                  .  "<form action=\"" . $this->_action . "\" name=\"" . $this->_name . "\" id=\"" . $this->_name
                  . "\"method=\""
                  . $this->_method . "\" enctype=\"" .  $this->_enctype . "\">\n"
                  .  "<input type=\"hidden\" name=\"submitted\" value=\"1\">\n"
                  .  $elm2 . $elm
                  .  "</form>\n";

        } else { $str .= "<div id=\"FormContainerExt_" . $this->_name . "\">$elm2 $elm"; }

        if ($ps) $str .= "<br /><b>Catatan:</b> Kolom dengan tanda (*) harus diisi";

        $str .= "</div>\n";

        $sessObj->setVar('formError', '');

        // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/form.css');

        echo"<link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/form.css'>";

        return $str;
    }

    private function loadValidator()
    {
        $file = FUNCTION_DIR . "/lib.validator.php";

        try {
            Loader::loadLib($file);
        } catch (SystemException $e) {
            Error::store("Form", "Error load validator file <i>$file</i>");
        }
    }
}