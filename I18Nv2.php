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

/**
* I18Nv2
* 
* @package      I18Nv2
* @category     Internationalization
*/

define('I18Nv2_WIN', defined('OS_WINDOWS') ? OS_WINDOWS : (strToUpper(substr(PHP_OS, 0,3)) === 'WIN'));

/** 
* I18Nv2 - Internationalization v2
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
* @package  I18Nv2
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
    * @static
    * @access   public
    * @return   mixed   &type.string; used locale or false on failure
    * @param    string  $locale     a valid locale like en_US or de_DE
    * @param    int     $cat        the locale category - usually LC_ALL
    */
    function setLocale($locale = null, $cat = LC_ALL)
    {
        static $triedFallbacks;
        
        if (!isset($locale)) {
            return setLocale($cat, null);
        }
        
        $locales = I18Nv2::getStaticProperty('locales');
        
        // get complete standard locale code (en => en_US)
        if (isset($locales[$locale])) {
            $locale = $locales[$locale];
        }
        
        // get Win32 locale code (en_US => enu)
        if (I18Nv2_WIN) {
            $windows   = I18Nv2::getStaticProperty('windows');
            $setlocale = isset($windows[$locale]) ? $windows[$locale] : $locale;
        } else {
            $setlocale = $locale;
        }

        if (!isset($triedFallbacks[$locale])) {
            $triedFallbacks[$locale] = false;
        } else {
            $setlocale = $triedFallbacks[$locale];
        }
        
        $syslocale = setLocale($cat, $setlocale);
        
        // if the locale is not recognized by the system, check if there 
        // is a fallback locale and try that, otherwise return false
        if (!$syslocale) {
            if (!$triedFallbacks[$locale]) {
                $triedFallbacks[$locale] = $setlocale;
                $fallbacks = I18Nv2::getStaticProperty('fallbacks');
                if (isset($fallbacks[$locale])) {
                    return I18Nv2::setLocale($fallbacks[$locale], $cat);
                }
            }
            return false;
        }
        
        $language = substr($locale, 0,2);
        
        if (I18Nv2_WIN) {
            @putEnv('LANG='     . $language);
            @putEnv('LANGUAGE=' . $language);
        } else {
            @putEnv('LANG='     . $locale);
            @putEnv('LANGUAGE=' . $locale);
        }
        
        // unshift locale stack
        $last = &I18Nv2::getStaticProperty('last');
        array_unshift($last, 
            array(
                0           => $locale, 
                1           => $language, 
                2           => $syslocale,
                'locale'    => $locale,
                'language'  => $language,
                'syslocale' => $syslocale,
            )
        );
        
        // fetch locale specific information
        $info = &I18Nv2::getStaticProperty('info');
        $info = localeConv();
        
        return $syslocale;
    }
    
    /**
    * Get current/prior Locale
    *
    * This only works, if I18Nv2::setLocale() has already been called
    * 
    * @static
    * @access   public
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
    * @static
    * @access   public
    * @return   mixed
    * @param    string  $part
    */
    function getInfo($part = null)
    {
        $info = &I18Nv2::getStaticProperty('info');
        return isset($part, $info[$part]) ? $info[$part] : $info;
    }
    
    /**
    * Create a Locale object
    *
    * @static
    * @access   public
    * @return   object  I18Nv2_Locale
    * @param    string  $locale
    */
    function &createLocale($locale = null)
    {
        require_once 'I18Nv2/Locale.php';
        return new I18Nv2_Locale($locale);
    }
    
    /**
    * Create a Negotiator object
    *
    * @static
    * @access   public
    * @return   object  I18Nv2_Negotiator
    * @param    string  $defLang        default language
    * @param    string  $defCharset     default character set
    */
    function &createNegotiator($defLang = 'en', $defCharset = 'iso-8859-1')
    {
        require_once 'I18Nv2/Negotiator.php';
        return new I18Nv2_Negotiator($defLang, $defCharset);
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
    * @static
    * @access   public
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

        require_once 'PEAR.php';
        if (!PEAR::loadExtension('iconv')) {
            return PEAR::raiseError('Error: ext/iconv is not available');
        }
        
        iconv_set_encoding('internal_encoding', $ics);
        iconv_set_encoding('output_encoding', $ocs);
        iconv_set_encoding('input_encoding', $ocs);
        
        if (!ob_start('ob_iconv_handler')) {
            return PEAR::raiseError('Couldn\'t start output buffering');
        }
        
        return true;
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
        return array_map(array('I18Nv2','l2l'), (array) $locales);
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
        return array_map(array('I18Nv2','l2l'), (array) $languages);
    }
    
    /**
    * Locale to language or language to locale
    *
    * @static
    * @access   public
    * @return   string
    * @param    string  $localeOrLanguage
    */
    function l2l($localeOrLanguage)
    {
        return strtr($localeOrLanguage, '-_', '_-');
    }
    
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
        return preg_split('/[_-]/', $locale, 2, PREG_SPLIT_NO_EMPTY);
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
    * Get access to static property
    * 
    * @static
    * @access   public
    * @return   mixed   Returns a reference to a static property
    * @param    string  $property   the static property
    */
    function &getStaticProperty($property)
    {
        static $properties;
        return $properties[$property];
    }
    
    /**
    * This one gets called automatically
    *
    * @ignore
    * @static
    * @internal
    * @access   private
    * @return   void
    */
    function _main()
    {
        // initialize the locale stack
        $last = &I18Nv2::getStaticProperty('last');
        $last = array();
        
        // map of "fully qualified locale" codes
        $locales = &I18Nv2::getStaticProperty('locales');
        $locales = array(
            'af' => 'af_ZA',
            'de' => 'de_DE',
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'es' => 'es_ES',
            'pt' => 'pt_PT',
            'sv' => 'sv_SE',
            'nb' => 'nb_NO',
            'nn' => 'nn_NO',
            'no' => 'no_NO',
            'fi' => 'fi_FI',
            'is' => 'is_IS',
            'da' => 'da_DK',
            'nl' => 'nl_NL',
            'pl' => 'pl_PL',
            'sl' => 'sl_SI',
            'hu' => 'hu_HU',
            'ru' => 'ru_RU',
            'cs' => 'cs_CZ',
        );
        
        // define locale fallbacks
        $fallbacks = &I18Nv2::getStaticProperty('fallbacks');
        $fallbacks = array(
            'no_NO' => 'nb_NO',
            'nb_NO' => 'no_NO',
        );
        
        // include Win32 locale codes
        if (I18Nv2_WIN) {
            include_once 'I18Nv2/Locale/Windows.php';
        }
    }
}

I18Nv2::_main();

?>