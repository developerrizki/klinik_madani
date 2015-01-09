<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 10:37 PM
 *
 * @package   util
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Export sql query result to CSV
 *
 * @package   util
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class CSV
{
    /**
     * Field terminated characters
     *
     * @var string
     */
    private $_fieldTerminatedChars = ',';

    /**
     * Field enclosed characters
     *
     * @var string
     */
    private $_fieldEnclosedChars   = '"';

    /**
     * Field escaped characters
     *
     * @var string
     */
    private $_fieldEscapedChars    = '\\';

    /**
     * Line terminated characters
     *
     * @var string
     */
    private $_lineTerminatedChars    = "\r\n";

    /**
     * Replace null by
     *
     * @var string
     */
    private $_replaceNull         = null;

    /**
     * Display title
     *
     * @var boolean
     */
    private $_title              = array();

    /**
     * Data list to be exported
     *
     * @var array
     */
    private $_dataList            = array();


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param array $dataList Data list
     *
     * @return void
     */
    public function __construct($dataList)
    {
        $this->_dataList = $dataList;
    }

    /**
     * Set field terminated characters
     *
     * @param string $chars Field terminated characters
     *
     * @return void
     */
    public function setFieldTerminatedChar($chars)
    {
        $this->_fieldTerminatedChars = $chars;
    }

    /**
     * Set field enclosed char
     *
     * @param string $chars Field enclosed characters
     *
     * @return void
     */
    public function setFieldEnclosedChar($chars)
    {
        $this->_fieldEnclosedChars = $chars;
    }

    /**
     * Set field escaped char
     *
     * @param string $chars Field escaped characters
     *
     * @return void
     */
    public function setFieldEscapedChar($chars)
    {
        $this->_fieldEscapedChars = $chars;
    }

    /**
     * Set line terminated char
     *
     * @param string $chars Line terminated characters
     *
     * @return void
     */
    public function setLineTerminatedChar($chars)
    {
        $this->_lineTerminatedChars = $chars;
    }

    /**
     * Set replace null
     *
     * @param string $char Replace null characters
     *
     * @return void
     */
    public function setReplaceNullChar($chars)
    {
        $this->_replaceNull = $chars;
    }

    /**
     * Set title
     *
     * @param string $title Title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Export to csv
     *
     * @param string $sql SQL statement
     *
     * @return string Export result in  CSV format
     */
    function export()
    {
        $csv = "";

        if (sizeof($this->_title)) {
            for ($i = 0; $i < sizeof($this->_title); $i++) {
                if ($this->_fieldEnclosedChars != '') {
                    $val  = str_replace($this->_fieldEnclosedChars,
                                        $this->_fieldEscapedChars . $this->_fieldEnclosedChars,
                                        $this->_title[$i]);
                    $csv  .= $this->_fieldEnclosedChars
                          . $val
                          . $this->_fieldEnclosedChars
                          . $this->_fieldTerminatedChars;
                } else {
                    $csv  .= $this->_title[$i] . $this->_fieldTerminatedChars;
                }

            }

            $csv .= $this->_lineTerminatedChars;
        }

        if (sizeof($this->_dataList)) {
            for ($i = 0; $i < sizeof($this->_dataList); $i++) {
                for ($j = 0; $j < sizeof($this->_dataList[$i]); $j++) {
                    if ($this->_fieldEnclosedChars != '') {
                        $val  = str_replace($this->_fieldEnclosedChars,
                                            $this->_fieldEscapedChars . $this->_fieldEnclosedChars,
                                            $this->_dataList[$i][$j]);
                        $csv  .= $this->_fieldEnclosedChars
                              . $val
                              . $this->_fieldEnclosedChars
                              . $this->_fieldTerminatedChars;
                    } else {
                        $csv  .= $this->_dataList[$i][$j] . $this->_fieldTerminatedChars;
                    }
                }

                $csv .= $this->_lineTerminatedChars;
            }
        }

        return $csv;
    }
}