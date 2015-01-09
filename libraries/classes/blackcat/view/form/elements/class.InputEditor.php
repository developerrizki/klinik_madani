<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 16, 2010, 11:58 PM
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
 * Element input editor
 *
 * @package    form
 * @subpackage elements
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @author     Erick Lazuardi <erick@divkom.ee.itb.ac.id>
 * @version    1.0
 * @copyright  Copyright (c) 2005-2010 Lorensius W. L. T
 *
 */
class InputEditor extends FormElement
{
	/**
	 * Name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Value
	 *
	 * @var mixed
	 */
	private $_value;

    /**
     * Tinymce config
     *
     * @var string
     */
    private $_config;

    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $name Element's name
     * @param string $value Element's value
     * @param int $width Element's width
     * @param int $col Number of column
     * @param int $row Number of row
     *
     * @return void
     */
	public function __construct($name, $value = '', $config='', $attributes = '')
	{
        $this->_name  = $name;
        $this->_value = $value;
        $this->_config= $config;

		if (empty($attributes)) $this->_attributes = array('width' => '100', 'col' => '80', 'row' => '15');

		parent::__construct($name, $value, $attributes);
	}

	function toString()
	{
	    $key = (empty($this->_config)) ? 'simple2' : $this->_config;

	    $configArr['simple1'] = "
        {

		mode : 'textareas',
		theme : 'advanced',
	    content_css : 'css/content.css',
		plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',

		theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,bullist,numlist,outdent,indent,blockquote,justifyleft,justifycenter,justifyright,justifyfull,forecolor,backcolor,pagebreak,fontselect,fontsizeselect',
		theme_advanced_buttons2 : '',
        theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : false

	    }
        ";

	    $configArr['simple2'] = "
        {

		mode : 'textareas',
		theme : 'advanced',
	    content_css : 'css/content.css',
		plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',

		theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
		theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview',
        theme_advanced_buttons3 : '',
        theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : false

	    }
        ";

		$configArr['simple3'] = "
        {

		mode : 'textareas',
		theme : 'advanced',
	    content_css : 'css/content.css',
		plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',

		theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,forecolor,backcolor,image,fontselect,fontsizeselect',
		theme_advanced_buttons2: '',
        theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : false

	    }
        ";

	    $configArr['advanced'] = "
        {

		mode : 'textareas',
		theme : 'advanced',
	    content_css : 'css/content.css',
		plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',

		theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
		theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
		theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr',
		theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',

        theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : false

	    }
        ";

		$str = "
        <script type='text/javascript'>
	    var config = $configArr[$key];

	    tinyMCE.settings = config;
        tinyMCE.execCommand('mceAddControl', true, '" . $this->_name . "');

        </script>

        <textarea id='" . $this->_name . "' name='" . $this->_name . "' rows='" . $this->_attributes['row'] . "' cols='"
        . $this->_attributes['col'] . "' style='width: ". $this->_attributes['width'] . "%'>
        " . $this->_value . "
	    </textarea>";

		$themeObj = Registry::get('theme');
        $themeObj->addJavaScript(ROOT_URL . '/jscript/tinymce/tiny_mce.js');

	    return $str;
	}
}