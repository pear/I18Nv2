<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2                                                       |
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

require_once('PEAR.php');

/** 
* I18Nv2 - Internationalization v2
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2
{
    /**
    * Set Locale
    * 
    * Example:
    * <code>
    * I18Nv2::setLocale('en_GB');
    * </code>
    * 
    * @access   static
    * @return   mixed   &type.string; used locale or false on failure
    * @param    string  $locale
    * @param    int     $cat
    */
    function setLocale($locale, $cat = LC_ALL)
    {
        if (!$locale) {
            return setLocale($cat, null);
        }
        
        $locales = &I18Nv2::getStaticProperty('locales');
        $llocale = isset($locales[$locale]) ? $locales[$locale] : $locale;
        $usedloc = setLocale($cat, $llocale);
        
        // this satisfies gettext
        $language = OS_WINDOWS ? substr($llocale, 0,2) : $llocale;
        putEnv('LANG=' . $language);
        putEnv('LANGUAGE=' . $language);
        
        // push locale stack
        $last = &I18Nv2::getStaticProperty('last');
        array_unshift($last, array($locale, $llocale, $language, $usedloc));
        
        // fetch locale specific information
        $info = &I18Nv2::getStaticProperty('info');
        $info = localeConv();
        
        return $usedloc;
    }
    
    /**
    * Get current/prior Locale
    *
    * This only works, if you I18Nv2::setLocale() has already been called
    * 
    * @access   static
    * @return   string  last locale
    * @param    int     $prior  if 0, the current otherwise n prior current
    * @param    bool    $full   wheter to return the array with locale, 
    *                           language and actually used system locale
    */
    function lastLocale($prior = 0, $full = false)
    {
        $last = I18Nv2::getStaticProperty('last');
        return $full ? @$last[$prior] : @$last[$prior][0];
    }
    
    /**
    * Get several locale specific information
    * 
    * @see      http://www.php.net/localeconv
    * 
    * <code>
    * $locale = I18Nv2::setLocale('en_US');
    * $dollar = I18Nv2::getInfo('currency_symbol');
    * $point  = I18Nv2::getInfo('decimal_point');
    * </code>
    * 
    * @access   static
    * @return   mixed
    * @param    string  $part
    */
    function getInfo($part = null)
    {
        $info = &I18Nv2::getStaticProperty('info');
        return $part ? $info[$part] : $info;
    }
    
    /**
    * Create a Locale object
    *
    * @access   static
    * @return   object  I18Nv2_Locale
    * @param    string  $locale
    */
    function &createLocale($locale = null)
    {
        require_once('I18Nv2/Locale.php');
        return new I18Nv2_Locale($locale);
    }
    
    /**
    * Create a Negotiator object
    *
    * @access   public
    * @return   object  I18Nv2_Negotiator
    * @param    string  $defLang        default language
    * @param    string  $defCharset     default character set
    */
    function &createNegotiator($defLang = 'en', $defCharset = 'iso-8859-1')
    {
        static $negotiator;
        if (!isset($negotiator)) {
            require_once('I18Nv2/Negotiator.php');
            $negotiator = &new I18Nv2_Negotiator($defLang, $defCharset);
        }
        return $negotiator;
    }
    
    /**
    * Automatically transform output between character sets
    *
    * This method utilizes ob_iconv_handler(), so you should call
    * it at the beginning of your script (prior to output)
    * 
    * <code>
    *   <?php
    *   require_once('I18Nv2.php');
    *   I18Nv2::autoConv('iso-8859-1', 'utf-8');
    *   print('...'); // some utf-8 stuff gets converted to iso-8859-1
    *   // ...
    *   ?>
    * </code>
    * 
    * @access   static
    * @return   mixed   Returns &true; on success or 
    *                   <classname>PEAR_Error</classname> on failure.
    * @param    string  $ocs    desired output character set
    * @param    string  $ics    current intput character set
    */
    function autoConv($ocs = 'UTF-8', $ics = 'ISO-8859-1')
    {
        if (!strcasecmp($ocs, $ics)) {
            return true;
        }

        if (!PEAR::loadExtension('iconv')) {
            return PEAR::raiseError('Error: ext/iconv is not available');
        }
        
        iconv_set_encoding('internal_encoding', $ics);
        iconv_set_encoding('output_encoding', $ocs);
        
        if (!ob_start('ob_iconv_handler')) {
            return PEAR::raiseError('Couldn\'t start output buffering');
        }
        
        return true;
    }
    
    /**
    * Mimics PEAR::getStaticProperty()
    * 
    * @access   static
    * @return   mixed   reference to static property
    * @param    string  $property   property
    */
    function &getStaticProperty($property)
    {
        static $properties;
        return $properties[$property];
    }
    
    /**
    * This one gets called automatically
    *
    * @access   internal
    * @return   void
    */
    function _main()
    {
        // initialize the locale stack
        $last = &I18Nv2::getStaticProperty('last');
        $last = array();
        
        if (OS_WINDOWS) {
            // include Win32 locales map
            include_once('I18Nv2/Locale/MapWindows.php');
        } else {
            // or some standard mappings for other systems (?)
            include_once('I18Nv2/Locale/Map.php');
        }
    }
}

I18Nv2::_main();

?>