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
    /**#@+
    * @access private
    */
    var $_codes = array();
    var $_language = '';
    var $_encoding = 'UTF-8';
    /**#@-**/
    
    /**
    * Constructor
    * 
    * @access   public
    * @return   object  I18Nv2_Country
    * @param    string  $language
    */
    function I18Nv2_Country($language = null, $encoding = null)
    {
        I18Nv2_Country::__construct($language, $encoding);
    }

    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($language = null, $encoding = null)
    {
        $this->setEncoding($encoding);
        
        if (!$this->setLanguage($language)) {
            if (class_exists('I18Nv2')) {
                $locale = I18Nv2::lastLocale(0, true);
                if (isset($locale)) {
                    $this->_language = $locale['language'];
                    if (!@include 'I18Nv2/Country/' . $locale['language'] . '.php') {
                        $this->setLanguage('en');
                    }
                }
            } else {
                $this->setLanguage('en');
            }
        }
    }

    /**
    * Set active language
    * 
    * Note that each time you set a different language the corresponding
    * language file has to be loaded again, too.
    *
    * @access   public
    * @return   bool
    * @param    string  $language
    */
    function setLanguage($language)
    {
        if (!isset($language)) {
            return false;
        }
        $language = strToLower($language);
        if (!strcmp($language, $this->_language)) {
            return true;
        }
        if (@include "I18Nv2/Country/$language.php") {
            $this->_language = $language;
            return true;
        }
        return false;
    }
    
    /**
    * Set active encoding
    *
    * @access   public
    * @return   bool
    * @param    string  $encoding
    */
    function setEncoding($encoding)
    {
        if (isset($encoding)) {
            $this->_encoding = strToUpper($encoding);
            return true;
        }
        return false;
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
        $code = strToUpper($code);
        if (!isset($this->_codes[$code])) {
            return '';
        }
        if (strcmp('UTF-8', $this->_encoding)) {
            return iconv('UTF-8', $this->_encoding, $this->_codes[$code]);
        }
        return $this->_codes[$code];
    }

    /**
    * Return all the codes
    *
    * @access   public
    * @return   array   all country codes as associative array
    */
    function getAllCodes()
    {
        if (strcmp('UTF-8', $this->_encoding)) {
            $codes = $this->_codes;
            array_walk($codes, array(&$this, '_iconv'));
            return $codes;
        }
        return $this->_codes;
    }
    
    /**
    * @access   private
    * @return   void
    */
    function _iconv(&$code, $key)
    {
        $code = iconv('UTF-8', $this->_encoding, $code);
    }
}
?>
