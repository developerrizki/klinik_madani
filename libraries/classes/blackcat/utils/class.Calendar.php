<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 10:36 PM
 *
 * @package   util
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Generates calendars as a html table (month and year view)
 *
 * @package   util
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Calendar
{
    /**
     * Default date (format used: yyyy-mm-dd)
     *
     * @var string
     */
    private $_date;

    /**
     * Default year
     *
     * @var string
     */
    private $_year;

    /**
     * Title day format
     *
     * @var string
     */
    private $_dayTitle;

    /**
     * Alt day format
     *
     * @var string
     */
    private $_dayAlt;
    
    /**
     * Month format
     *
     * @var string
     */
    private $_monthTitle;

    /**
     * Boolean flag to indicate today is highlighted
     *
     * @var bool
     */
    private $_todayHighLighted;

    /**
     * Link for each day
     *
     * @var string
     */
    private $_link;
    
    /**
     * Highlited date
     *
     * @var array
     */
    private $_highlightedDate;
    
    /**
     * Enable link on highlighted date
     *
     * @var boolean
     */
    private $_enableHighlightedLink;
    
    /**
     * 
    private $_highlitedDate;
    
    /**
     * Constructor.
     * Create an instance of this class
     *
     * @return void
     */
    public function __construct()
    {
        //Set default variables
        $this->_date                = date('Y-m-d');
        
        $this->_dayTitle            =  array('M',
                                             'S',
                                             'S',
                                             'R',
                                             'K',
                                             'J',
                                             'S');
        
        $this->_dayAlt              = array('Minggu',
                                            'Senin',
                                            'Selasa',
                                            'Rabu',
                                            'Kamis',
                                            'Jumat',
                                            'Sabtu');
        
        $this->_monthTitle          = array('Januari',
                                            'Februari',
                                            'Maret',
                                            'April',
                                            'Mei',
                                            'Juni',
                                            'Juli',
                                            'Agustus',
                                            'September',
                                            'Oktober',
                                            'November',
                                            'Desember');
        $this->_todayHighLighted    = true;
    }

    /**
     * Set default date (format used: yyyy-mm-dd)
     *
     * @param string $dt Default date
     *
     * @return void
     */
    public function setDate($dt)
    {
        $this->_date = $dt;
    }

    /**
     * Set default year
     *
     * @param string $year Default year
     *
     * @return void
     */
    public function setYear($year)
    {
        $this->_year = $year;
    }

    /**
     * Set day format name (ext: Sunday or Sun, Monday or Mon), according to your language
     *
     * @param array $day Array of day format name
     *
     * @return void
     */
    public function setDayFormatName($_dayTitle)
    {
        $this->_dayTitle = $_dayTitle;
    }

    /**
     * Set month format name (ext: January or Jan, etc), according to your language
     *
     * @param array $day Array of month format name
     *
     * @return void
     */
    public function setMonthFormatName($_monthTitle)
    {
        $this->_monthTitle = $_monthTitle;
    }

    /**
     * Enable today to be highlighted
     *
     * @param boolean $todayHighLighted Set TRUE if today is highlighted
     *
     * @return void
     */
    public function enableTodayHighlighted($todayHighLighted = true)
    {
        $this->_todayHighLighted = $todayHighLighted;
    }

    /**
     * Set link for each date
     *
     * @param string $link Link for each date
     *
     * @return void
     */
    public function setLink($link) {
        $this->_link = $link;
    }

    /**
     * Set group of highlighted date
     *
     * @param array $hldate Highlighted date
     * @param boolean $link Enable link on highlighted date
     *
     * @return void
     */
    public function setHighlightedDate($hlDate, $link)
    {
        $this->_highlightedDate         = $hlDate;
        $this->_enableHighlightedLink   = $link;
    }
    
    /**
     * Output calendar
     *
     * @param string $month Month displayed in calendar
     *
     * @return string Calendar
     */
    public function toString($month = '')
    {
        $month        = (!$month) ? date('m', strtotime($this->_date)) : zeroExtend($month);
        $year         = (!$this->_year) ? date('Y', strtotime($this->_date)) : $this->_year;
        $nextMonth    = $month + 1;
        $firstDate    = mktime(0, 0, 0, $month, 1, $year);
        $lastDate     = mktime(0, 0, 0, $nextMonth, 0, $year);
        $numberOfDay  = ($lastDate - $firstDate) / 86400;
        $firstDay     = date('w', $firstDate);
        $today        = date('j');
        $j            = 1;

        if ($month == '12') {
            $prevMonth = '11';
            $nextMonth = '01';
            $prevYear  = $year;
            $nextYear  = $year + 1;
        } elseif ($month == '01') {
            $prevMonth = '12';
            $nextMonth = '02';
            $prevYear  = $year - 1;
            $nextYear  = $year;
        } else {
            $prevMonth = $month - 1;
            $nextMonth = $month + 1;
            $prevYear  = $year;
            $nextYear  = $year;
            $nextMonth = zeroExtend($nextMonth, 2);
            $prevMonth = zeroExtend($prevMonth, 2);
        }

        $nextDate = "$nextYear-$nextMonth-01";
        $prevDate = "$prevYear-$prevMonth-01";
        $cal       = "";

        while ($j <= ($numberOfDay + 1)) {
            $d     = date("d", mktime(0, 0, 0, $month, $j, $year));
            $dt    = "$year-$month-$d";
            $link  = $this->_link;
            
            if (empty($this->_link)) {
                $dlink = $j;
            } else {
                if ($this->_enableHighlightedLink)
                    $dlink = (in_array($dt, $this->_highlightedDate)) ? "<a href=\"$link?date=$dt\">$j</a>" : $j;
                else 
                    $dlink = "<a href=\"$link?date=$dt\">$j</a>";
            }
            
           // $dlink = (in_array($dt, $this->_highlightedDate)) ? "<a href=\"$link?date=$dt\">$j</a>";
            $day   = date("w",mktime(0, 0, 0, $month, $j, $year));
            $class = ($today == $j && $this->_todayHighLighted) ? 'class="today"' : '';
            $class = (in_array($dt, $this->_highlightedDate)) ? 'class="highlighted"' : $class;
            

            if ($j == 1) { //set the position of the first day of a month
                if ($firstDay == 0) {
                    $cal .= "<tr>\n<td $class>$dlink</td>\n";
                } else {
                    $cal .= "<tr>\n<td>&nbsp;</td>\n";
                    for ($i = 0;$i < ($firstDay-1);$i++) {
                         $cal .= "<td>&nbsp;</td>\n";
                    }
                    $cal .= "<td $class>$dlink</td>\n\n";
                }
            } else {
                if (date("w", mktime(0, 0, 0, $month, $j, $year)) == 0) {
                    $cal .= "</tr>\n\n<tr>\n<td $class>$dlink</td>\n";
                } else {
                    $cal .= "<td $class>$dlink</td>\n";
                }
            }
            
            $j++;
        }

        $lastDay = date("w", $lastDate);
        $resid   = 6 - $lastDay;
        if ($lastDay == 6) {
            $cal .= "</tr>\n\n</table>";
        } else {
           for ($k = 0; $k < $resid; $k++) {
                $cal .= "<td>&nbsp;</td>\n";
           }
           $cal .= "</tr>\n\n</table>";
        }

        $qs       = new QueryString();
        
        $qs->update('date', $prevDate);
        $prevLink = $_SERVER['PHP_SELF'] . '?' . $qs->toString();
        
        $qs->update('date', $nextDate);
        $nextLink = $_SERVER['PHP_SELF'] . '?' . $qs->toString();
       
        $tmpl = "
        <table id=\"calendar\" cellpadding=\"0\" cellspacing=\"0\">
        <tbody>
        <caption><a href=\"$prevLink\" title=\"Bulan Sebelumnya\" class=\"nav\">&laquo;</a>
        " . $this->_monthTitle[date("n",strtotime($this->_date))-1] . " $year
        <a href=\"$nextLink\" title=\"Bulan Selanjutnya\" class=\"nav\">&raquo;</a></caption>
        
        <tr>\n 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[0] . "\" title=\"" . $this->_dayAlt[0] . "\">" . $this->_dayTitle[0] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[1] . "\" title=\"" . $this->_dayAlt[1] . "\">" . $this->_dayTitle[1] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[2] . "\" title=\"" . $this->_dayAlt[2] . "\">" . $this->_dayTitle[2] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[3] . "\" title=\"" . $this->_dayAlt[3] . "\">" . $this->_dayTitle[3] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[4] . "\" title=\"" . $this->_dayAlt[4] . "\">" . $this->_dayTitle[4] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[5] . "\" title=\"" . $this->_dayAlt[5] . "\">" . $this->_dayTitle[5] . "</th> 
            <th scope=\"col\" abbr=\"" . $this->_dayAlt[6] . "\" title=\"" . $this->_dayAlt[6] . "\">" . $this->_dayTitle[6] . "</th> 
        </tr>\n";

        return $tmpl.$cal;
    }
}