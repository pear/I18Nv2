<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Country                                            |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The PEAR Group                                    |
// +----------------------------------------------------------------------+
// | Authors:   Naoki Shima <murahachibu@php.net>                         |
// |            Wolfram Kriesing <wk@visionp.de>                          |
// |            Michael Wallner <mike@iworks.at>                          |
// +----------------------------------------------------------------------+
//
// $Id$

/**
* I18Nv2::Country
* 
* List of ISO-3166 two letter country code to country name mapping.
*
* @package      I18Nv2
* @category     Internationalisation
*/

/**
* I18Nv2_Country
* 
* @author       Naoki Shima <murahachibu@php.net>
* @author       Wolfram Kriesing <wk@visionp.de>
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
* @access       public
* @package      I18Nv2
*/
class I18Nv2_Country
{
    /**
    * @access private
    */
    var $_codes = array();
    
    /**
    * Constructor
    * 
    * @access   public
    * @return   object  I18Nv2_Country
    * @param    string  $language
    */
    function I18Nv2_Country($language = null)
    {
        $this->__construct($language);
    }

    /**
    * @access   public
    * @ignore
    */
    function __construct($language = null)
    {
        if (isset($language)) {
            @include 'I18Nv2/Country/' . strToLower($language) . '.php';
        } elseif (class_exists('I18Nv2')) {
            $locale = I18Nv2::lastLocale(0, true);
            if (isset($locale)) {
                @include 'I18Nv2/Country/' . $locale['language'] . '.php';
            }
        }
        if (!count($this->_codes)) {
            include 'I18Nv2/Country/en.php';
        }
    }

    /**
    * Check if Country Code is valid
    * 
    * @access   public
    * @return   bool
    * @param    string  $code   country code
    */
    function isValidCode($code)
    {
        return isset($this->_codes[strToUpper($code)]);
    }

    /**
    * Return name of the country for country code passed
    * 
    * @access   public
    * @return   string  name of the country
    * @param    string  $code   country code
    */
    function getName($code)
    {
        return @$this->_codes[strToUpper($code)];
    }

    /**
    * Return all the codes
    *
    * @access   public
    * @return   array   all country codes as associative array
    */
    function getAllCodes()
    {
        return $this->_codes;
    }
}
?>
