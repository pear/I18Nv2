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
    var $locale = 'en_US';
    
    var $days;
    var $months;
    var $abbrDays;
    var $abbrMonths;
    
    var $dateFormats;
    var $numberFormats;
    var $currencyFormats;
    
    var $currentTimeFormat;
    var $currentDateFormat;
    var $currentNumberFormat;
    var $currentCurrencyFormat;
    
    var $customFormats;

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
    * Set locale
    * 
    * This automatically calls I18Nv2_Locale::initialize()
    *
    * @access   public
    * @return   mixed
    * @param    string  $locale
    */
    function setLocale($locale)
    {
        if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?/$', $locale)) {
            return PEAR::raiseError('Invalid locale supplied: ' . $locale)
        }
        $this->locale = $locale;
        $this->initialize();
        return true;
    }
    
    /**
    * Initialize
    *
    * @access   public
    * @return   void
    * @param    string  $locale
    */
    function initialize()
    {
        I18N::setLocale($this->locale);

        $this->days = array(
            strftime('%A', 320000),
            strftime('%A', 406000),
            strftime('%A', 492800),
            strftime('%A', 579200),
            strftime('%A', 665600),
            strftime('%A', 752000),
            strftime('%A', 838400),
        );
        
        $this->abbrDays = array(
            strftime('%a', 320000),
            strftime('%a', 406000),
            strftime('%a', 492800),
            strftime('%a', 579200),
            strftime('%a', 665600),
            strftime('%a', 752000),
            strftime('%a', 838400),
        );

        $this->months = array(
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
        
        $this->abbrMonths = array(
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
        $this->currencyFormats = array(
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
        
        $this->numberFormats = array(
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
    * Loads corresponding locale extension
    *
    * @access   public
    * @return   bool
    */
    function loadExtension()
    {
        $locale = I18Nv2::lastLocale(0, true);
        foreach ($locale as $lc) {
            if (@include("I18Nv2/Locale/{$lc}.php")) {
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
        $this->currentTimeFormat = $this->timeFormats[I18Nv2_DATETIME_DEFAULT];
        $this->currentDateFormat = $this->dateFormats[I18Nv2_DATETIME_DEFAULT];
        $this->currentNumberFormat = $this->numberFormats[I18Nv2_NUMBER_FLOAT];
        $this->currentCurrencyFormat = $this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL];
    }
    
    /**
    * Set currency format
    *
    * @access   public
    * @return   void
    * @param    int     $format     a I18Nv2_CURRENCY constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setCurrencyFormat($format, $custom = false)
    {
        $this->currentCurrencyFormat = 
            $custom ?
            @$this->customFormats[$format] :
            @$this->currencyFormats[$format];
    }
    
    /**
    * Set number format
    *
    * @access   public
    * @return   void
    * @param    int     $format     a I18Nv2_NUMBER constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setNumberFormat($format, $custom = false)
    {
        $this->currentNumberFormat = 
            $custom ?
                @$this->customFormats[$format] :
                @$this->numberFormats[$format];
    }
    
    /**
    * Set date format
    *
    * @access   public
    * @return   void
    * @param    int     $format     a I18Nv2_DATETIME constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setDateFormat($format, $custom = false)
    {
        $this->currentDateFormat = 
            $custom ? 
                @$this->customFormats[$format] :
                @$this->dateFormats[$format];
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
        $this->currentTimeFormat = 
            $custom ? 
                @$this->customFormats[$format] :
                @$this->timeFormats[$format];
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
                $this->customFormats = array();
            } else {
                unset($this->customFormats[$type]);
            }
        } else {
            $this->customFormats[$type] = $format;
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
            = $this->currentCurrencyFormat;

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
        list($dig, $dec, $sep) = $this->currentNumberFormat;
        return number_format($value, $dig, $dec, $sep);
    }
    
    /**
    * Format a date
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function formatDate($timestamp = 0)
    {
        return strftime($this->currentDateFormat, $timestamp ? $timestamp : time());
    }
    
    /**
    * Format a time
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function formatTime($timestamp = 0)
    {
        return strftime($this->currentTimeFormat, $timestamp ? $timestamp : time());
    }

    /**
    * Locale time
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function time($timestamp = 0)
    {
        return strftime('%x', $timestamp ? $timestamp : time());
    }
    
    /**
    * Locale date
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    */
    function date($timestamp = 0)
    {
        return strftime('%X', $timestamp ? $timestamp : time());
    }
    
    /**
    * Day name
    *
    * @access   public
    * @return   string
    * @param    int     $weekday    numerical representation of weekday
    *                               (0 = Sunday, 1 = Monday, ...)
    * @param    bool    $short  whether to return the abbreviation
    */
    function dayName($weekday, $short = false)
    {
        return $short ? $this->abbrDays[$weekday] : $this->days[$weekday];
    }
    
    /**
    * Month name
    *
    * @access   public
    * @return   string
    * @param    int     $month  numerical representation of month
    *                           (0 = January, 1 = February, ...)
    * @param    bool    $short  whether to return the abbreviation
    */
    function monthName($month, $short = false)
    {
        return $short ? $this->abbrMonths[$month] : $this->months[$month];
    }
}
?>