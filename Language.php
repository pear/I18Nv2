<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Language                                           |
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
* I18Nv2::Language
* 
* @package      I18Nv2
* @category     Internationalisation
*/
 
/**
* I18Nv2_Language
* 
* List of ISO-639-1 two letter language code to language name mapping.
* 
* @author       Wolfram Kriesing <wk@visionp.de>
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
* @access       public
* @package      I18Nv2
*/
class I18Nv2_Language
{
    /**
    * @access   protected
    * @var      array
    */
    var $_codes = array();

    /**
    * Constructor
    * 
    * @access   public
    * @return   object  I18Nv2_Language
    * @param    string  $language
    */
    function I18Nv2_Language($language = null)
    {
        $this->__construct($language);
    }
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($language)
    {
        if (isset($language)) {
            @include 'I18Nv2/Language/' . strToLower($language) . '.php';
        } elseif (class_exists('I18Nv2')) {
            $locale = I18Nv2::lastLocale(0, true);
            if (isset($locale)) {
                @include 'I18Nv2/Language/' . $locale['language'] . '.php';
            }
        }
        if (!count($this->_codes)) {
            include 'I18Nv2/Language/en.php';
        }
    }

    /**
    * Check language code is valid
    * 
    * @access   public
    * @return   boolean
    * @param    string  $code   language code
    */
    function isValidCode($code)
    {
        return isset($this->_codes[strToLower($code)]);
    }

    /**
    * Return name of the language for language code
    * 
    * @access   public
    * @return   string  name of the language
    * @param    string  $code   language code
    */
    function getName($code)
    {
        return $this->_codes[strToLower($code)];
    }

    /**
    * Return all the codes
    *
    * @access   public
    * @return   array
    */
    function getAllCodes()
    {
        return $this->_codes;
    }
}
?>
