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

require_once 'PEAR.php';
require_once 'I18Nv2/Locale.php';
require_once 'I18Nv2/Util/LDML.php';

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
                2,
                $data['formats']['number']['decimal_point'],
                $data['formats']['number']['thousands_sep']
            )
        );
        
        $this->locale = $locale;
        return parent::init(true);
    }
    
}
?>
