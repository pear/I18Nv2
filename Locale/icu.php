<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Locale :: ICU                                      |
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
* I18Nv2::Locale::ICU
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'PEAR.php';
require_once 'I18Nv2/Locale.php';
require_once 'I18Nv2/Util/ICU.php';

/** 
* I18Nv2_Locale_icu
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Locale_icu extends I18Nv2_Locale
{
    var $datapath = '@DATA_DIR@/I18Nv2/ICU';
    var $encoding = 'UTF-8';
    
    /**
    * Init
    *
    * @access   public
    * @return   mixed
    */
    function init($locale)
    {
        $ICU = &new I18Nv2_Util_ICU(
            array(  'datapath' => $this->datapath, 
                    'encoding' => $this->encoding   )
        );
        if (PEAR::isError($data = $ICU->getFullLocale($locale))) {
            return $data;
        }
        unset($ICU);
        
        $this->names['days']['long']    = $data['DayNames'];
        $this->names['days']['short']   = $data['DayAbbreviations'];
        $this->names['months']['long']  = $data['MonthNames'];
        $this->names['months']['short'] = $data['MonthAbbreviations'];
        $this->names['languages']       = $data['Languages'];
        $this->names['countries']       = $data['Countries'];
/*
        $this->formats['currency'] = array(
            I18Nv2_CURRENCY_LOCAL           => $data['localCurrencyFormat'],
            I18Nv2_CURRENCY_INTERNATIONAL   => $data['intlCurrencyFormat'],
        );
        $this->formats['number'] = array(
            I18Nv2_NUMBER_FLOAT     => $data['floatNumber'],
            I18Nv2_NUMBER_INTEGER   => $data['intNumber'],
        );
*/
        $this->locale = $locale;
        return parent::init(true);
    }
    
}
?>
