<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: CommonList                                         |
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
* I18Nv2::CommonList
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

/** 
* I18Nv2_CommonList
* 
* Base class for I18Nv2_Country and I18Nv2_Language that performs some basic
* work, so code doesn't get written twice or even more often in the future.
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_CommonList
{
    /**#@+
    * @access protected
    */
    var $codes = array();
    var $language;
    var $encoding;
    /**#@-**/
    
    /**
    * Constructor
    *
    * @access   public
    * @return   object
    */
    function I18Nv2_CommonList($language = null, $encoding = null)
    {
        I18Nv2_CommonList::__construct($language, $encoding);
    }

    /**
    * Constructor (ZE2)
    * @ignore
    */
    function __construct($language = null, $encoding = null)
    {
        if (!$this->setLanguage($language)) {
            if (class_exists('I18Nv2')) {
                $locale = I18Nv2::lastLocale(0, true);
                if (isset($locale)) {
                    $this->_language = $locale['language'];
                    if (!$this->setLanguage($locale['language'])) {
                        $this->setLanguage('en');
                    }
                }
            } else {
                $this->setLanguage('en');
            }
        }
        $this->setEncoding($encoding);
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
        if (!strcmp($language, $this->language)) {
            return true;
        }
        if ($this->_loadLanguage($language)) {
            $this->language = $language;
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
        if (!isset($encoding)) {
            return false;
        }
        $this->encoding = strToUpper($encoding);
        return true;
    }

    /**
    * Check if code is valid
    * 
    * @access   public
    * @return   bool
    * @param    string  $code   code
    */
    function isValidCode($code)
    {
        return isset($this->codes[strToUpper($code)]);
    }

    /**
    * Return corresponding name of code
    * 
    * @access   public
    * @return   string  name
    * @param    string  $code   code
    */
    function getName($code)
    {
        $code = strToUpper($code);
        if (!isset($this->codes[$code])) {
            return '';
        }
        if (strcmp('UTF-8', $this->encoding)) {
            return iconv('UTF-8', $this->encoding, $this->codes[$code]);
        }
        return $this->codes[$code];
    }

    /**
    * Return all the codes
    *
    * @access   public
    * @return   array   all codes as associative array
    */
    function getAllCodes()
    {
        if (strcmp('UTF-8', $this->encoding)) {
            $codes = $this->codes;
            array_walk($codes, array(&$this, '_iconv'));
            return $codes;
        }
        return $this->codes;
    }
    
    /**
    * @access   private
    * @return   void
    */
    function _iconv(&$code, $key)
    {
        $code = iconv('UTF-8', $this->encoding, $code);
    }
    
}
?>