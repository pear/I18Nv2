<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Locale :: libc                                     |
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
* I18Nv2::Locale::libc
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'PEAR.php';
require_once 'I18Nv2.php';
require_once 'I18Nv2/Locale.php';
require_once 'I18Nv2/Country.php';
require_once 'I18Nv2/Language.php';

/** 
* I18Nv2_Locale_libc
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Locale_libc extends I18Nv2_Locale
{
    /**
    * Init the locale
    *
    * @access   protected
    * @return   bool    true
    * @param    string  $locale
    */
    function init($locale)
    {
        I18Nv2::setLocale($locale);
        
        $lang = I18Nv2_Util::languageOf($locale);
        $isoc = &new I18Nv2_Country($lang, 'iso-8859-1');
        $this->names['countries'] = $isoc->getAllCodes();
        $isol = &new I18Nv2_Language($lang, 'iso-8859-1');
        $this->names['languages'] = $isol->getAllCodes();
        unset($lang, $isoc, $isol);

        $this->names['days']['long'] = array(
            strftime('%A', 320000),
            strftime('%A', 406000),
            strftime('%A', 492800),
            strftime('%A', 579200),
            strftime('%A', 665600),
            strftime('%A', 752000),
            strftime('%A', 838400),
        );
        
        $this->names['days']['short'] = array(
            strftime('%a', 320000),
            strftime('%a', 406000),
            strftime('%a', 492800),
            strftime('%a', 579200),
            strftime('%a', 665600),
            strftime('%a', 752000),
            strftime('%a', 838400),
        );

        $this->names['months']['long'] = array(
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
        
        $this->names['months']['short'] = array(
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
        * Some get extended/overwritten with other local conventions
        */
        $this->formats['currency'] = array(
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
        
        $this->formats['number'] = array(
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

        $this->locale = $locale;
        $this->loadExtension();
        return parent::init(true);
    }
    
    /**
    * Loads corresponding locale extension
    *
    * @access   public
    * @return   void
    */
    function loadExtension()
    {
        $locale = I18Nv2::lastLocale(0, true);
        if (isset($locale)) {
            $dir = dirname(__FILE__);
            foreach (array($locale['language'], $locale['locale']) as $lc) {
                if (is_file($dir . '/libc/' . $lc . '.php')) {
                    include $dir . '/libc/' . $lc . '.php';
                }
            }
        }
    }
    
    /**
    * Format
    *
    * @access   public
    * @return   mixed
    */
    function format($type, $value, $override)
    {
        $type = strToLower($type);
        $frmt = isset($override) ? 
            $this->formats[$type][$override] : 
            $this->current[$type];
        
        switch ($type)
        {
            case 'time':
            case 'date':
            case 'datetime':
                return strftime($frmt, isset($value) ? $value : time());
            break;
            
            case 'number':
                list($dig, $dec, $sep) = $frmt;
                return number_format($value, $dig, $dec, $sep);
            break;
            
            case 'currency':
                list($sym, $dig, $dec, $sep, $nsign, $psign, $npre, $ppre, 
                    $nsep, $psep) = $frmt;
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
            break;

        }
    }
    
    /**
    * time
    *
    * @access   public
    * @return   string
    */
    function time($value)
    {
        return strftime('%x', $value);
    }
    
    /**
    * date
    *
    * @access   public
    * @return   string
    */
    function date($value)
    {
        return strftime('%X', $value);
    }
    
}
?>