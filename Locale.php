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
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
* I18Nv2::Locale
* 
* @package      I18Nv2
* @category     Internationalisation
*/

/**#@+ Format Constants **/
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
/**#@-**/

require_once 'PEAR.php';
require_once 'I18Nv2.php';

/** 
* I18Nv2_Locale
*
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
* @access       public
* @package      I18Nv2
*/
class I18Nv2_Locale
{
    /**
    * Locale Code
    * 
    * @access   protected
    * @var      string
    */
    var $locale = 'en_US';
    
    /**
    * Day/Month Names
    * 
    * @access   protected
    * @var      array
    */
    var $names = array(
        'days'   => array('short' => array(), 'long' => array()),
        'months' => array('short' => array(), 'long' => array()),
    );
    
    /**
    * Known Formats
    * 
    * @access   protected
    * @var      array
    */
    var $formats = array(
        'date'      => array(),
        'time'      => array(),
        'datetime'  => array(),
        'number'    => array(),
        'currency'  => array(),
    );
    
    /**
    * Current Formats
    * 
    * @access   protected
    * @var      array
    */
    var $current = array();
    
    /**
    * Custom Formats
    * 
    * @access   protected
    * @var      array
    */
    var $custom  = array();
    
    /**
    * Constructor
    *
    * @access   public
    * @return   object
    * @param    string  $locale
    */
    function I18Nv2_Locale($locale = null, $opts = null)
    {
        I18Nv2_Locale::__construct($locale, $opts);
    }

    /**
    * ZE2 Constructor
    * @ignore
    * @access   public
    * @return   object
    * @param    string  $locale
    */
    function __construct($locale = null, $opts = null)
    {
        $this->setOptions($opts);
        $this->setLocale($locale);
    }
    
    /**
    * Factory
    *
    * @access   public
    * @return   object  Returns an I18Nv2_Locale object.
    * @param    string  $locale locale code
    * @param    string  $type   implementation type (libc|icu|ldml)
    * @param    mixed   $opts   options
    */
    function &factory($locale = null, $type = 'libc', $opts = null)
    {
        $type = strToLower($type);
        
        if (!class_exists($class = 'I18Nv2_Locale_' . $type)) {
            require_once 'I18Nv2/Locale/' . $type . '.php';
        }

        return new $class($locale, $opts);
    }

    /**
    * Set options
    *
    * @access   public
    * @return   void
    * @param    array   $options
    */
    function setOptions($options = array())
    {
        foreach ((array) $options as $property => $value) {
            $this->$property = $value;
        }
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
        if (isset($locale) && !preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $locale)) {
            return PEAR::raiseError('Invalid locale supplied: ' . $locale);
        }
        return $this->init($locale);
    }
    
    /**
    * Initialize the locale
    *
    * @access   protected
    * @return   mixed
    * @param    mixed   $result The result that should be passed through.
    */
    function init($result = true)
    {
        if (!count($this->formats['datetime'])) {
            $this->formats['datetime'] = array(
                I18Nv2_DATETIME_SHORT   => 
                    $this->formats['date'][I18Nv2_DATETIME_SHORT]
                    . ', ' .
                    $this->formats['time'][I18Nv2_DATETIME_SHORT],
                I18Nv2_DATETIME_MEDIUM   => 
                    $this->formats['date'][I18Nv2_DATETIME_MEDIUM]
                    . ', ' .
                    $this->formats['time'][I18Nv2_DATETIME_MEDIUM],
                I18Nv2_DATETIME_DEFAULT   => 
                    $this->formats['date'][I18Nv2_DATETIME_DEFAULT]
                    . ', ' .
                    $this->formats['time'][I18Nv2_DATETIME_DEFAULT],
                I18Nv2_DATETIME_LONG   => 
                    $this->formats['date'][I18Nv2_DATETIME_LONG]
                    . ', ' .
                    $this->formats['time'][I18Nv2_DATETIME_LONG],
                I18Nv2_DATETIME_FULL   => 
                    $this->formats['date'][I18Nv2_DATETIME_FULL]
                    . ', ' .
                    $this->formats['time'][I18Nv2_DATETIME_FULL],
            );
        }

        $this->setDefaults();
        return $result;
    }
    
    /**
    * Set defaults
    *
    * @access   public
    * @return   void
    */
    function setDefaults()
    {
        $this->current = array(
            'date'      => I18Nv2_DATETIME_DEFAULT,
            'time'      => I18Nv2_DATETIME_DEFAULT,
            'datetime'  => I18Nv2_DATETIME_DEFAULT,
            'number'    => I18Nv2_NUMBER_FLOAT,
            'currency'  => I18Nv2_CURRENCY_INTERNATIONAL,
        );
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
            if (!isset($this->custom[$format])) {
                return PEAR::raiseError('Custom currency format "'.$format.'" doesn\'t exist.');
            }
            $this->current['currency'] = $this->custom[$format];
        } else {
            if (!isset($this->formats['currency'][$format])) {
                return PEAR::raiseError('Currency format "'.$format.'" doesn\'t exist.');
            }
            $this->current['currency'] = $this->formats['currency'][$format];
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
            if (!isset($this->custom[$format])) {
                return PEAR::raiseError('Custom number format "'.$format.'" doesn\'t exist.');
            }
            $this->current['number'] = $this->custom[$format];
        } else {
            if (!isset($this->formats['number'][$format])) {
                return PEAR::raiseError('Number format "'.$format.'" doesn\'t exist.');
            }
            $this->current['number'] = $this->formats['number'][$format];
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
            if (!isset($this->custom[$format])) {
                return PEAR::raiseError('Custom date fromat "'.$format.'" doesn\'t exist.');
            }
            $this->current['date'] = $this->custom[$format];
        } else {
            if (!isset($this->formats['date'][$format])) {
                return PEAR::raiseError('Date format "'.$format.'" doesn\'t exist.');
            }
            $this->current['date'] = $this->formats['date'][$format];
        }
        return true;
    }
    
    /**
    * Set time format
    *
    * @access   public
    * @return   mixed
    * @param    int     $format     a I18Nv2_DATETIME constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setTimeFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->custom[$format])) {
                return PEAR::raiseError('Custom time format "'.$format.'" doesn\'t exist.');
            }
            $this->current['time'] = $this->custom[$format];
        } else {
            if (!isset($this->formats['time'][$format])) {
                return PEAR::raiseError('Time format "'.$format.'" doesn\'t exist.');
            }
            $this->current['time'] = $this->formats['time'][$format];
        }
        return true;
    }
    
    /**
    * Set datetime format
    *
    * @access   public
    * @return   mixed
    * @param    int     $format     a I18Nv2_DATETIME constant
    * @param    bool    $custom     whether to use a defined custom format
    */
    function setDateTimeFormat($format, $custom = false)
    {
        if ($custom) {
            if (!isset($this->custom[$format])) {
                return PEAR::raiseError('Custom datetime format "'.$format.'" doesn\'t exist.');
            }
            $this->current['datetime'] = $this->custom[$format];
        } else {
            if (!isset($this->formats['datetime'][$format])) {
                return PEAR::raiseError('Datetime format "'.$format.'" doesn\'t exist.');
            }
            $this->current['datetime'] = $this->formats['datetime'][$format];
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
                $this->custom = array();
            } else {
                unset($this->custom[$type]);
            }
        } else {
            $this->custom[$type] = $format;
        }
    }
    
    /**
    * Format currency (incomplete)
    *
    * @access   public
    * @return   string
    * @param    numeric $value
    * @param    int     $overrideFormat
    */
    function formatCurrency($value, $overrideFormat = null)
    {
        return $this->format('currency', $value, $overrideFormat);
    }
    
    /**
    * Format a number
    *
    * @access   public
    * @return   string
    * @param    numeric $value
    * @param    int     $overrideFormat
    */
    function formatNumber($value, $overrideFormat = null)
    {
        return $this->format('number', $value, $overrideFormat);
    }
    
    /**
    * Format a date
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    * @param    int     $overrideFormat
    */
    function formatDate($timestamp = null, $overrideFormat = null)
    {
        return $this->format('date', $timestamp, $overrideFormat);
    }
    
    /**
    * Format a time
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    * @param    int     $overrideFormat
    */
    function formatTime($timestamp = null, $overrideFormat = null)
    {
        return $this->format('time', $timestamp, $overrideFormat);
    }

    /**
    * Format a datetime
    *
    * @access   public
    * @return   string
    * @param    int     $timestamp
    * @param    int     $overrideFormat
    */
    function formatDateTime($timestamp = null, $overrideFormat = null)
    {
        return $this->format('dateTime', $timestamp, $overrideFormat);
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
        return $this->names['days'][$short ? 'short' : 'long'][$weekday];
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
        return $this->names['months'][$short ? 'short' : 'long'][$month];
    }
    
    /**
    * Country name
    *
    * @access   public
    * @return   string
    * @param    string  ISO country code
    */
    function countryName($country)
    {
        return $this->names['countries'][$country];
    }
    
    /**
    * Language name
    *
    * @access   public
    * @return   string
    * @param    string  ISO langage code
    */
    function languageName($language)
    {
        return $this->names['languages'][$language];
    }
}
?>
