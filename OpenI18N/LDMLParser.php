<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: OpenI18N :: LDMLParser                             |
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
* I18Nv2::OpenI18N::LDMLParser
*
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'XML/Parser.php';

/** 
* I18Nv2_OpenI18N_LDMLParser
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_OpenI18N_LDMLParser extends XML_Parser
{
    /**#@+
    * @access private
    */
    var $_cnt;
    var $_cur = '';
    var $_get = false;
    var $_pos = null;
    var $_data = array();
    /**#@-**/
    
    /**
    * Constructor
    *
    * @access   protected
    * @return   object
    */
    function I18Nv2_OpenI18N_LDMLParser($encoding = 'UTF-8')
    {
        I18Nv2_OpenI18N_LDMLParser::__construct($encoding);
    }

    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($encoding = 'UTF-8')
    {
        parent::__construct('UTF-8', 'event', $encoding);
    }

    /**
    * Get locale specific data
    *
    * @access   public
    * @return   mixed
    * @param    string  $locale
    */
    function getLocale($locale)
    {
        if (is_file('@DATA_DIR@' . '/I18Nv2_OpenI18N/LDML/' . $locale . '.xml')) {
            $file = '@DATA_DIR@' . '/I18Nv2_OpenI18N/LDML/' . $locale . '.xml';
        } elseif (is_file($locale . '.xml')) {
            $file = $locale . '.xml';
        } elseif (is_file($locale)) {
            $file = $locale;
        } else {
            include_once 'PEAR.php';
            return PEAR::raiseError("File for locale '$locale' doesn't exist");
        }
        $this->_cnt = array(
            'months'    => 0,
            'abbrMonths'=> 0,
            'days'      => 0,
            'abbrDays'  => 0,
        );
        $this->setInputFile($file);
        if (is_a($err = $this->parse(), 'PEAR_Error')) {
            return $err;
        }
        return $this->_data;
    }
    
    /**
    * Get full locale data
    * 
    * Values that don't exist in the loaded locale will be merged from en.
    *
    * @access   public
    * @return   array
    * @param    string  $locale
    */
    function getFullLocale($locale)
    {
        if (is_a($en = $this->getLocale('en'), 'PEAR_Error')) {
            return $en;
        }

        $lang = substr($locale, 0,2);
        if ($locale === 'en' || is_a($langdata = $this->getLocale($lang), 'PEAR_Error')) {
            return $en;
        }

        $result = array();
        if ($lang == $locale || is_a($localedata = $this->getLocale($locale), 'PEAR_Error')) {
            foreach ($en as $key => $default) {
                $result[$key] = array_merge($default, $langdata[$key]);
            }
        } else {
            foreach ($en as $key => $default) {
                $result[$key] = array_merge($default, $langdata[$key], $localedata[$key]);
            }
        }
        return $result;
    }
    
    /**
    * startHandler
    *
    * @access   public
    * @return   void
    */
    function startHandler($p, $e, $a)
    {
        switch (strToUpper($e))
        {
            case 'LANGUAGE': 
                $this->_get = true;
                $this->_pos = &$this->_data['languages'][$a['TYPE']];
            break;
            
            case 'TERRITORY':
                $this->_get = true;
                $this->_pos = &$this->_data['countries'][$a['TYPE']];
            break;
            
            case 'MONTHNAMES':
                $this->_cur = 'months';
            break;
            
            case 'MONTHABBR':
                $this->_cur = 'abbrMonths';
            break;
            
            case 'DAYNAMES':
                $this->_cur = 'days';
            break;
            
            case 'DAYABBR':
                $this->_cur = 'abbrDays';
            break;
            
            case 'DAY':
            case 'MONTH':
                $this->_get = true;
                $pos = $this->_cnt[$this->_cur]++;
                $this->_pos = &$this->_data[$this->_cur][$pos];
            break;
        }
    }
    
    /**
    * endHandler
    *
    * @access   public
    * @return   void
    */
    function endHandler()
    {
        $this->_get = false;
    }
    
    /**
    * cdataHandler
    *
    * @access   public
    * @return   void
    */
    function cdataHandler($p, $cdata)
    {
        if($this->_get) {
            $this->_pos .= $cdata;
        }
    }
    
}
?>