<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Util :: LDML                                       |
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
* I18Nv2::Util::LDML
*
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'XML/Parser.php';
require_once 'I18Nv2/Util.php';

/** 
* I18Nv2_Util_LDML
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Util_LDML extends XML_Parser
{
    /**#@+
    * @access private
    */
    var $_cnt;
    var $_cur = '';
    var $_fmt = false;
    var $_get = false;
    var $_pos = null;
    var $_data = array();
    var $_supp = array();
    /**#@-**/
    
    var $datapath = '@DATA_DIR@/I18Nv2/LDML';
    
    /**
    * Constructor
    *
    * @access   protected
    * @return   object
    * @param    array   $options
    */
    function I18Nv2_Util_LDML($options = array())
    {
        I18Nv2_Util_LDML::__construct($options);
    }

    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($options = array())
    {
        $this->setOptions($options);
        parent::__construct('UTF-8', 'event', 'UTF-8');
    }

    /**
    * Set options
    *
    * @access   public
    * @return   void
    * @param    array   $options
    *                       o string encoding
    *                       o string datapath
    */
    function setOptions($options)
    {
        if (isset($options['encoding'])) {
            $this->tgtenc = $options['encoding'];
        }
        if (isset($options['datapath']) && is_dir($options['datapath'])) {
            $this->datapath = $options['datapath'];
        }
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
        if (!isset($this->_supp[$this->tgtenc])) {
            $this->_supp[$this->tgtenc] = 
                I18Nv2_Util_LDML_Supplemental::getStatic(
                    $this->datapath, $this->tgtenc);
        }
        
        if (!is_file($file = $this->datapath . '/' . $locale . '.xml')) {
            include_once 'PEAR.php';
            return PEAR::raiseError("File for locale '$locale' doesn't exist");
        }
        
        $this->_get = false;
        $this->_cnt = array(
            'months'    => 0,
            'abbrMonths'=> 0,
            'days'      => 0,
            'abbrDays'  => 0,
        );
        $this->_data = array();
        
        $this->setInputFile($file);
        if (is_a($err = $this->parse(), 'PEAR_Error')) {
            return $err;
        }
        
        $this->_data['currency']['symbol'] = null;
        
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
        if (is_a($root = $this->getLocale('root'), 'PEAR_Error') ||
            is_a($en = $this->getLocale('en'), 'PEAR_Error')) {
            //
            return $root;
        }

        @list($lang, $country) = I18Nv2_Util::splitLocale($locale);
        if ($locale === 'en' || !is_array($langdata = $this->getLocale($lang))) {
            return I18Nv2_Util::merge($root, $en);
        }

        if ($lang === $locale || !is_array($localedata = $this->getLocale($locale))) {
            return I18Nv2_Util::mergeMany($root, $en, $langdata);
        } else {
            
            $data = I18Nv2_Util::mergeMany($root, $en, $langdata, $localedata);
            
            // check for the currency symbol and fracdigits in supplemental data
            if (isset($this->_supp[$this->tgtenc]['currencies'][$country])) {
                $currency = $this->_supp[$this->tgtenc]['currencies'][$country];
                $data['formats']['currency']['name'] = $currency;
                if (isset($this->_supp[$this->tgtenc]['fracdigits'][$currency])) {
                    $data['formats']['currency']['fracdigits'] =
                        $this->_supp[$this->tgtenc]['fracdigits'][$currency];
                } else {
                    $data['formats']['currency']['fracdigits'] =
                        $this->_supp[$this->tgtenc]['fracdigits']['DEFAULT'];
                }
            }
            
            return $data;
        }
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
                if (strlen($a['TYPE']) === 2) {
                    $this->_get = true;
                    $this->_pos = &$this->_data['languages'][$a['TYPE']];
                }
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
            
            case 'DATEFORMATS':
                $this->_fmt = 'date';
                $this->_pos = &$this->_data['formats']['date'];
            break;

            case 'TIMEFORMATS':
                $this->_fmt = 'time';
                $this->_pos = &$this->_data['formats']['time'];
            break;
            
            case 'DECIMALFORMATS':
                $this->_fmt = 'number';
            break;
            
            case 'CURRENCYFORMATS':
                $this->_fmt = 'currency';
            break;

            case 'DEFAULT':
                $this->_pos['default'] = $a['TYPE'];
            break;

            case 'DATEFORMATLENGTH':
            case 'TIMEFORMATLENGTH':
                $this->_pos = &$this->_data['formats'][$this->_fmt][$a['TYPE']];
            break;

            case 'PATTERN':
                if ($this->_fmt) {
                    $this->_get = true;
                }
            break;
            
            case 'DECIMAL':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['decimal_point'];
            break;
            
            case 'GROUP':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['thousands_sep'];
            break;

            case 'LIST':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['pattern_sep'];
            break;

            case 'PATTERNDIGIT':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['pattern_digit'];
            break;

            case 'PLUSSIGN':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['positive_sign'];
            break;

            case 'MINUSSIGN':
                $this->_get = true;
                $this->_pos = &$this->_data['formats']['number']['negative_sign'];
            break;

            case 'LOCALICEDPATTERNCHARS':
                $this->_get = true;
                $this->_pos = &$this->_data['patternchars'];
            break;
        }
    }
    
    /**
    * endHandler
    *
    * @access   public
    * @return   void
    */
    function endHandler($p, $e)
    {
        $this->_get = false;
        if (preg_match('/FORMATS$/i', $e)) {
            $this->_fmt = false;
        }
    }
    
    /**
    * cdataHandler
    *
    * @access   public
    * @return   void
    */
    function cdataHandler($p, $cdata)
    {
        if ($this->_get) {
            $this->_pos .= $cdata;
        }
    }
}

/** 
* I18Nv2_Util_LDML_Supplemental
*
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_Util_LDML_Supplemental extends XML_Parser
{
    var $_data;
    var $_pos;

    /**
    * Constructor
    *
    * @access   protected
    * @return   object
    */
    function I18Nv2_Util_LDML_Supplemental($datapath, $encoding)
    {
        I18Nv2_Util_LDML_Supplemental::__construct($datapath, $encoding);
    }

    /**
    * ZE2 Constructor
    *
    * @access   protected
    * @return   object
    */
    function __construct($datapath, $encoding)
    {
        parent::__construct('UTF-8', 'event', $encoding);
        $this->setInputFile($datapath . '/supplementalData.xml');
        $this->parse();
    }

    /**
    * startHandler
    *
    * @access   public
    * @return   mixed
    */
    function startHandler($p, $e, $a)
    {
        switch (strToUpper($e))
        {
            case 'INFO': 
                $this->_data['fracdigits'][$a['ISO4217']] = $a['DIGITS'];
            break;
            case 'REGION':
                $this->_pos = &$this->_data['currencies'][$a['ISO3166']];
            break;
            case 'CURRENCY':
                $this->_pos = $a['ISO4217'];
            break;
        }
    }
    
    /**
    * Get Data
    *
    * @access   public
    * @return   mixed
    */
    function getData()
    {
        return $this->_data;
    }
    
    /**
    * Get Data statically
    *
    * @static
    * @access   public
    * @return   mixed
    */
    function getStatic($datapath, $encoding)
    {
        $p = &new I18Nv2_Util_LDML_Supplemental($datapath, $encoding);
        return $p->getData();
    }
}
?>
