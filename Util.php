<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Util                                               |
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
* I18Nv2::Util
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

/** 
* I18Nv2_Util
* 
* Utility class offering some static methods
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Util
{
    /**
    * Split locale code
    * 
    * Splits locale codes into its language and country part
    *
    * @static
    * @access   public
    * @return   array
    * @param    string  $locale
    */
    function splitLocale($locale)
    {
        return preg_split('/[_-]/', $locale, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
    * Get language code of locale
    *
    * @static
    * @access   public
    * @return   string
    * @patram   string  $locale
    */
    function languageOf($locale)
    {
        return array_shift(I18Nv2_Util::splitLocale($locale));
    }
    
    /**
    * Get country code of locale
    *
    * @static
    * @access   public
    * @return   string
    * @param    string  $locale
    */
    function countryOf($locale)
    {
        return array_pop(I18NV2_Util::splitLocale($locale));
    }
    
    /**
    * Merge many locale data arrays
    *
    * @static
    * @access   public
    * @return   array
    */
    function mergeMany()
    {
        $arrays = func_get_args();
        $result = array_shift($arrays);
        while ($add = array_shift($arrays)) {
            $result = I18Nv2_Util::merge($result, $add);
        }
        return $result;
    }
    
    /**
    * Merge locale data arrays
    *
    * @static
    * @access   public
    * @return   array
    * @param    array
    * @param    array
    */
    function merge($a1, $a2)
    {
        if (!is_array($a1) || !is_array($a2)) {
            return false;
        }
        foreach($a2 as $key => $val) {
            if (isset($a1[$key]) && is_array($val) && is_array($a1[$key])) {
                $a1[$key] = I18Nv2_Util_LDML::mergeLocales($a1[$key], $val);
            } else {
                $a1[$key] = $val;
            }
        }
        return $a1;
    }
    
    /**
    * Traverse locales to languages
    * 
    * Returns en-US, de-DE from en_US and de_DE
    *
    * @static
    * @access   public
    * @return   array
    * @param    array   $locales
    */
    function locales2langs($locales)
    {
        return array_map(array('I18NV2_Util', 'l2l'), (array) $locales);
    }
    
    /**
    * Traverse languages to locales
    *
    * Returns en_US, de_DE from en-US and de-DE
    *
    * @static
    * @access   public
    * @return   array
    * @param    array   $languages
    */
    function langs2locales($languages)
    {
        return array_map(array('I18Nv2_Util', 'l2l'), (array) $languages);
    }

    /**
    * Traverse languages and locales
    * 
    * Returns en_US out of en-US.
    * Retruns en-US out of en_US.
    *
    * @static
    * @access   public
    * @return   string
    * @param    string  $l
    */
    function l2l($l)
    {
        return strtr($l, '-_', '_-');
    }
}
?>
