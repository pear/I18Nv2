<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Locale :: ldml                                     |
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
* I18Nv2::Locale::ldml
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'PHP/Compat.php';
require_once 'PEAR.php';
require_once 'I18Nv2/Locale.php';
require_once 'I18Nv2/Util/LDML.php';

PHP_Compat::loadFunction('str_split');

/** 
* I18Nv2_Locale_ldml
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Locale_ldml extends I18Nv2_Locale
{
    var $datapath = '@DATA_DIR@/I18Nv2/LDML';
    var $encoding = 'UTF-8';
    
    var $patterns = array(
        'datetime'  => array(),
        'number'    => array(),
        '_phpdate'  => array(
            'day of week'   => 'w',
            'week of year'  => 'W',
            'full year'     => 'Y',
            'short year'    => 'y',
            'day of year'   => 'z',
            'month'         => 'n',
            'full hour'     => 'G',
            'short hour'    => 'g',
            'day of month'  => 'd',
            'am pm'         => 'a',
        ),
    );
    
    /**
    * Init
    *
    * @access   public
    * @return   mixed
    */
    function init($locale)
    {
        $ldml = &new I18Nv2_Util_LDML(
            array(  'datapath' => $this->datapath, 
                    'encoding' => $this->encoding   )
        );
        if (PEAR::isError($data = $ldml->getFullLocale($locale))) {
            return $data;
        }
        unset($ldml);

        $this->names['languages']       = $data['languages'];
        $this->names['countries']       = $data['countries'];
        $this->names['days']['long']    = $data['days'];
        $this->names['days']['short']   = $data['abbrDays'];
        $this->names['months']['long']  = $data['months'];
        $this->names['months']['short'] = $data['abbrMonths'];
        
        $this->formats['date'] = array(
            I18Nv2_DATETIME_SHORT   => $data['formats']['date']['short'],
            I18Nv2_DATETIME_MEDIUM  => $data['formats']['date']['medium'],
            I18Nv2_DATETIME_LONG    => $data['formats']['date']['long'],
            I18Nv2_DATETIME_FULL    => $data['formats']['date']['full'],
            I18Nv2_DATETIME_DEFAULT => $data['formats']['date'][$data['formats']['date']['default']]
        );
        
        $this->formats['time'] = array(
            I18Nv2_DATETIME_SHORT   => $data['formats']['time']['short'],
            I18Nv2_DATETIME_MEDIUM  => $data['formats']['time']['medium'],
            I18Nv2_DATETIME_LONG    => $data['formats']['time']['long'],
            I18Nv2_DATETIME_FULL    => $data['formats']['time']['full'],
            I18Nv2_DATETIME_DEFAULT => $data['formats']['time'][$data['formats']['time']['default']]
        );

        $this->formats['number'] = array(
            I18Nv2_NUMBER_FLOAT => array(
                2,
                $data['formats']['number']['decimal_point'],
                $data['formats']['number']['thousands_sep']
            ),
            I18Nv2_NUMBER_INTEGER => array(
                0,
                $data['formats']['number']['decimal_point'],
                $data['formats']['number']['thousands_sep']
            )
        );
        
        list(   $this->patterns['datetime']['era'],
                $this->patterns['datetime']['year'],
                $this->patterns['datetime']['month'],
                $this->patterns['datetime']['day of month'],
                $this->patterns['datetime']['hour24'],
                $this->patterns['datetime']['hour00'],
                $this->patterns['datetime']['minute'],
                $this->patterns['datetime']['second'],
                $this->patterns['datetime']['millisecond'],
                $this->patterns['datetime']['day of week'],
                $this->patterns['datetime']['day of year'],
                $this->patterns['datetime']['day of week in month'],
                $this->patterns['datetime']['week of year'],
                $this->patterns['datetime']['week of month'],
                $this->patterns['datetime']['am pm'],
                $this->patterns['datetime']['hour1'],
                $this->patterns['datetime']['hour0'],
                $this->patterns['datetime']['timezone'],
                $this->patterns['datetime']['year of week of year'], //WTF?
                $this->patterns['datetime']['timezone-e']
        ) = str_split($data['patternchars'], 1);
        
        print_r(($data['patternchars']));
        
        $this->locale = $locale;
        return parent::init(true);
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

        switch ($type) {
            case 'time':
                $datetime = &new I18Nv2_Locale_ldml_DateTime($frmt);
                $datetime->format($value);
            break;
        }
        
    }
    
}
class I18Nv2_Locale_ldml_DateTime
{
    /**
    * Constructor
    *
    * @access   public
    * @return   mixed
    */
    function I18Nv2_Locale_ldml_DateTime($format)
    {
        $this->format = $format;
    }
    
    /**
    * Format
    *
    * @access   public
    * @return   mixed
    */
    function format($value)
    {
        $literal_count = 0;
        preg_match_all('/(\'.*?\')/', $value, $literals);
        $value = preg_replace('/(\'.*?\')/e', '"__".$literal_count++."__"', $value);
        print_r($value);
    }
    
}
$l = new I18Nv2_Locale_ldml;
$l->setLocale('de');
$l->formatTime(123);
?>
