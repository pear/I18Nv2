<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Locale                                             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

require_once('I18Nv2.php');

define('I18Nv2_NUMBER',                   10);
define('I18Nv2_CURRENCY',                 20);
define('I18Nv2_DATETIME',                 30);

define('I18Nv2_NUMBER_FLOAT' ,            11);
define('I18Nv2_NUMBER_INTEGER' ,          12);

define('I18Nv2_CURRENCY_LOCAL',           21);
define('I18Nv2_CURRENCY_INTERNATIONAL',   22);

define('I18Nv2_DATETIME_SHORT',           31);
define('I18Nv2_DATETIME_DEFAULT',         32);
define('I18Nv2_DATETIME_MEDIUM',          33);
define('I18Nv2_DATETIME_LONG',            34);
define('I18Nv2_DATETIME_FULL',            35);

/** 
* I18Nv2_Locale
*
* @package      I18Nv2
* @category     Internationalisation
* 
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
* @access       public
*/
class I18Nv2_Locale
{
    var $_locale = 'en_US';
    
    var $_days;
    var $_months;
    var $_abbrDays;
    var $_abbrMonths;
    
    var $_dateFormats;
    var $_numberFormats;
    var $_currencyFormats;
    
    var $_currentTimeFormat;
    var $_currentDateFormat;
    var $_currentNumberFormat;
    var $_currentCurrencyFormat;
    
    var $_customFormats;

    /**
    * Constructor
    *
    * @access   public
    * @return   object
    * @param    string  $locale
    */
    function I18Nv2_Locale($locale = null)
    {
        $this->__construct($locale);
    }

    /**
    * @access   public
    * @return   object
    * @param    string  $locale
    */
    function __construct($locale = null)
    {
        $this->setLocale($locale);
    }
    
    /**
    * Set _locale
    * 
    * This automatically calls I18Nv2_Locale::initialize()
    *
    * @access   public
    * @return   mixed
    * @param    string  $_locale
    */
    function setLocale($locale)
    {
        if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?/$', $locale)) {
            return PEAR::raiseError('Invalid locale supplied: ' . $locale)
        }
        $this->_locale = $locale;
        $this->initialize();
        return true;
    }
    
    /**
    * Initialize
    *
    * @access   public
    * @return   void
    * @param    string  $_locale
    */
    function initialize()
    {
        I18N::setLocale($this->_locale);

        $this->_days = array(
            strftime('%A', 320000),
            strftime('%A', 406000),
            strftime('%A', 492800),
            strftime('%A', 579200),
            strftime('%A', 665600),
            strftime('%A', 752000),
            strftime('%A', 838400),
        );
        
        $this->_abbrDays = array(
            strftime('%a', 320000),
            strftime('%a', 406000),
            strftime('%a', 492800),
            strftime('%a', 579200),
            strftime('%a', 665600),
            strftime('%a', 752000),
            strftime('%a', 838400),
        );

        $this->_months = array(
            strftime('%B', 978307261),
            strftime('%B', 980985661),
            strftime('%B', 983404861),
            strftime('%B', 986079661),
            strftime('%B', 988671661),
            strftime('%B', 991350061),
            strftime('%B', 993942061),
            strftime('%B', 996620461),
            strftime('%B', 999298861),
            strftime('%B', 1001890861),
            strftime('%B', 1004572861),
            strftime('%B', 1007164861),
        );
        
        $this->_abbrMonths = array(
            strftime('%b', 978307261),
            strftime('%b', 980985661),
            strftime('%b', 983404861),
            strftime('%b', 986079661),
            strftime('%b', 988671661),
            strftime('%b', 991350061),
            strftime('%b', 993942061),
            strftime('%b', 996620461),
            strftime('%b', 999298861),
            strftime('%b', 1001890861),
            strftime('%b', 1004572861),
            strftime('%b', 1007164861),
        );
        
        $info = I18Nv2::getInfo();
        
        /**
        * The currency symbol is old shit on Win2k, though
        * ###
        * Some get extended/overwritten with other local conventions
        */
        $this->_currencyFormats = array(
            I18Nv2_CURRENCY_LOCAL => array(
                $info['currency_symbol'],
                $info['int_frac_digits'],
                $info['mon_decimal_point'],
                $info['mon_thousands_sep'],
                $info['negative_sign'],
                $info['positive_sign'],
                $info['n_cs_precedes'],
                $info['p_cs_precedes'],
                $info['n_sep_by_space'],
                $info['p_sep_by_space'],
            ),
            I18Nv2_CURRENCY_INTERNATIONAL => array(
                $info['int_curr_symbol'],
                $info['int_frac_digits'],
                $info['mon_decimal_point'],
                $info['mon_thousands_sep'],
                $info['negative_sign'],
                $info['positive_sign'],
                $info['n_cs_precedes'],
                $info['p_cs_precedes'],
                $info['n_sep_by_space'],
                $info['p_sep_by_space'],
            ),
        );
        
        $this->_numberFormats = array(
            I18Nv2_NUMBER_FLOAT => array(
                $info['frac_digits'],
                $info['decimal_point'],
                $info['thousands_sep']
            ),
            I18Nv2_NUMBER_INTEGER => array(
                '0',
                $info['decimal_point'],
                $info['thousands_sep']
            ),

        );
        
        $this->loadExtension();
        $this->setDefaults();
    }
    
    /**
    * Loads corresponding _locale extension
    *
    * @access   public
    * @return   bool
    */
    function loadExtension()
    {
        $locale = I18Nv2::lastLocale(0, true);
        foreach ($locale as $lc) {
            if (@include('I18Nv2/Locale/'. $lc .'.php')) {
                return true;
            }
        }
        return false;
    }
    
    /**
    * Set defaults
    *
    * @access   public
    * @return   void
    */
    function setDefaults()
    {
        $this->_currentTimeFormat = $this->_timeFormats[I18Nv2_DATETIME_DEFAULT];
        $this->_currentDateFormat = $this->_dateFormats[I18Nv2_DATETIME_DEFAULT];
        $this->_currentNumberFormat = $this->_numberFormats[I18Nv2_NUMBER_FLOAT];
        $this->_currentCurrencyFormat = $this->_currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL];
    }
    
    /**
    * Set currency format
    *
    * @access   public
    * @return   mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
    * @param    int     $format     a I18Nv2_CURRENCY constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setCurrencyFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->_customFormats[$format])) {
                return PEAR::raiseError('Custom currency format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentCurrencyFormat = $this->_customFormats[$format];
        } else {
            if (!isset($this->_currencyFormats[$format])) {
                return PEAR::raiseError('Currency format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentCurrencyFormat = $this->_currencyFormats[$format];
        }
        return true;
    }
    
    /**
    * Set number format
    *
    * @access   public
    * @return   mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
    * @param    int     $format     a I18Nv2_NUMBER constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setNumberFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->_customFormats[$format])) {
                return PEAR::raiseError('Custom number format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentNumberFormat = $this->_customFormats[$format];
        } else {
            if (!isset($this->_numberFormats[$format])) {
                return PEAR::raiseError('Number format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentNumberFormat = $this->_numberFormats[$format];
        }
        return true;
    }
    
    /**
    * Set date format
    *
    * @access   public
    * @return   mixed   Returns &true; on success or <classname>PEAR_Error</classname> on failure.
    * @param    int     $format     a I18Nv2_DATETIME constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setDateFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->_customFormats[$format])) {
                return PEAR::raiseError('Custom date fromat "'.$format.'" doesn\'t exist.');
            }
            $this->_currentDateFormat = $this->_customFormats[$format];
        } else {
            if (!isset($this->_dateFormats[$format])) {
                return PEAR::raiseError('Date format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentDateFormat = $this->_dateFormats[$format];
        }
        return true;
    }
    
    /**
    * Set time format
    *
    * @access   public
    * @return   void
    * @param    int     $format     a I18Nv2_DATETIME constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setTimeFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->_customFormats[$format])) {
                return PEAR::raiseError('Custom time format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentTimeFormat = $this->_customFormats[$format];
        } else {
            if (!isset($this->_timeFormats[$format])) {
                return PEAR::raiseError('Time format "'.$format.'" doesn\'t exist.');
            }
            $this->_currentTimeFormat = $this->_timeFormats[$format];
        }
        return true;
    }
    
    /**
    * Set custom format
    *
    * If <var>$format</var> is omitted, the custom format for <var>$type</var>
    * will be dsicarded - if both vars are omitted all custom formats will
    * be discarded.
    * 
    * @access   public
    * @return   void
    * @param    mixed   $type
    * @param    mixed   $format
    */
    function setCustomFormat($type = null, $format = null)
    {
        if (is_null($format)) {
            if (is_null($type)) {
                $this->_customFormats = array();
            } else {
                unset($this->_customFormats[$type]);
            }
        } else {
            $this->_customFormats[$type] = $format;
        }
    }
    
    /**
    * Format currency (incomplete)
    *
    * @access   public
    * @return   string
    * @param    numeric $value
    */
    function formatCurrency($value)
    {
        list($sym, $dig, $dec, $sep, $nsign, $psign, $npre, $ppre, $nsep, $psep) 
            = $this->_currentCurrencyFormat;

        if ($value < 0) {
            if ($npre) {
                if ($nsep) {
                    $fString = $sym . ' ' . $nsign;
                } else {
                    $fString = $sym . $nsign;
                }
            } else {
                if ($nsep) {
                    $fString = $nsign . ' ' . $sym;
                } else {
                    $fString = $nsign . $sym;
                }
            }
        } else {
            if ($ppre) {
                if ($psep) {
                    $fString = $sym . ' ' . $psign;
                } else {
                    $fString = $sym . $psign;
                }
            } else {
                if ($psep) {
                    $fString = $psign . ' ' . $sym;
                } else {
                    $fString = $psign . $sym;
                }
            }
        }

        return $fString . ' ' . number_format($value, $dig, $dec, $sep);

    }
    
    /**
    * Format a number
    *
    * @access   public
    * @return   string
    * @param    numeric $value
    */
    function formatNumber($value)
    {
        list($dig, $dec, $sep) = $this->_currentNumberFormat;
        return number_format($value, $dig, $dec, $sep);
    }
    
    /**
    * Format a date
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function formatDate($timestamp = null)
    {
        return strftime($this->_currentDateFormat, 
            is_null($timestamp) ? time() : $timestamp);
    }
    
    /**
    * Format a time
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function formatTime($timestamp = null)
    {
        return strftime($this->_currentTimeFormat, 
             is_null($timestamp) ? time() : $timestamp);
    }

    /**
    * Locale time
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function time($timestamp = null)
    {
        return strftime('%x', is_null($timestamp) ? time() : $timestamp);
    }
    
    /**
    * Locale date
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function date($timestamp = null)
    {
        return strftime('%X', is_null($timestamp) ? time() : $timestamp);
    }
    
    /**
    * Day name
    *
    * @access   public
    * @return   mixed   Returns &type.string; name of weekday on success or
    *                   <classname>PEAR_Error</classname> on failure.
    * @param    int     $weekday    numerical representation of weekday
    *                               (0 = Sunday, 1 = Monday, ...)
    * @param    bool    $short  whether to return the abbreviation
    */
    function dayName($weekday, $short = false)
    {
        if ($short) {
            if (!isset($this->_abbrDays[$weekday])) {
                return PEAR::raiseError('Weekday "'.$weekday.'" is out of range.');
            }
            return $this->_abbrDays[$weekday];
        } else {
            if (!isset($this->_days[$weekday])) {
                return PEAR::raiseError('Weekday "'.$weekday.'" is out of range.');
            }
            return $this->_days[$weekday];
        }
    }
    
    /**
    * Month name
    *
    * @access   public
    * @return   mixed   Returns &type.string; name of month on success or
    *                   <classname>PEAR_Error</classname> on failure.
    * @param    int     $month  numerical representation of month
    *                           (0 = January, 1 = February, ...)
    * @param    bool    $short  whether to return the abbreviation
    */
    function monthName($month, $short = false)
    {
        if ($short) {
            if (!isset($this->_abbrMonths[$month])) {
                return PEAR::raiseError('Month "'.$month.'" is out of range.');
            }
            return $this->_abbrMonths[$month];
        } else {
            if (!isset($this->_months[$month])) {
                return PEAR::raiseError('Month "'.$month.'" is out of range.');
            }
            return $this->_months[$month];
        }
    }
}
?>