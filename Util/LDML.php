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
        if (is_a($en = $this->getLocale('en'), 'PEAR_Error')) {
            return $en;
        }

        $lang = substr($locale, 0,2);
        if ($locale === 'en' || is_a($langdata = $this->getLocale($lang), 'PEAR_Error')) {
            return $en;
        }

        if ($lang == $locale || is_a($localedata = $this->getLocale($locale), 'PEAR_Error')) {
            return $this->mergeLocales($en, $langdata);
        } else {
            $data = $this->mergeLocales($this->mergeLocales($en, $langdata), $localedata);
            if (isset($this->_supp[$this->tgtenc]['currencies'][$c = substr($locale, -2)])) {
                $c = $this->_supp[$this->tgtenc]['currencies'][$c];
                $data['formats']['currency']['name'] = $c;
                if (isset($this->_supp[$this->tgtenc]['fracdigits'][$c])) {
                    $data['formats']['currency']['fracdigits'] =
                        $this->_supp[$this->tgtenc]['fracdigits'][$c];
                } else {
                    $data['formats']['currency']['fracdigits'] =
                        $this->_supp[$this->tgtenc]['fracdigits']['DEFAULT'];
                }
            }
            return $data;
        }
    }
    
    /**
    * Merge locale data arrays
    *
    * @access   public
    * @return   array
    */
    function mergeLocales($a1, $a2)
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
        if (preg_match('/(DATE|TIME)FORMATS$/', strToUpper($e))) {
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
        if($this->_get) {
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

$p = &new I18Nv2_Util_LDML;
$p->setOptions(array(
    'encoding' => 'Iso-8859-1', 
    'datapath' => '/www/mike/pear/i18nv2/data/ldml/')
);
print_r($p->getFullLocale('fr_FR'));
?>