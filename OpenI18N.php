<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: OpenI18N                                           |
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
* I18Nv2::OpenI18N
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18Nv2
* @category     Internationalization
*/

require_once 'I18Nv2/OpenI18N/LDMLParser.php';

/** 
* I18Nv2_OpenI18N          
*
* @see      http://www.OpenI18N.org/
* @author   Michael Wallner <mike@php.net>
* @version  $Revision$
* @access   public
*/
class I18Nv2_OpenI18N
{
    var $_data = array();
    
    /**
    * Constructor
    *
    * @access   protected
    * @return   object
    */
    function I18Nv2_OpenI18N($locale, $encoding = 'UTF-8')
    {
        I18Nv2_OpenI18N::__construct($locale, $encoding);
    }

    /**
    * Constructor (ZE2)
    *
    * @access   protected
    * @return   object
    */
    function __construct($locale, $encoding = 'UTF-8')
    {
        $this->parser = &new I18Nv2_OpenI18N_LDMLParser($encoding);
        $this->setLocale($locale, $encoding);
    }

    /**
    * Set locale
    *
    * @access   public
    * @return   mixed
    * @param    string  $locale
    */
    function setLocale($locale, $encoding = 'UTF-8')
    {
        $this->parser->tgtenc = $encoding;
        if (is_a($data = $this->parser->getFullLocale($locale), 'PEAR_Error')) {
            return $data;
        }
        $this->_data = $data;
        return true;
    }
    
    /**
    * Get all languages
    *
    * @access   public
    * @return   array
    */
    function getLanguages()
    {
        return $this->_data['languages'];
    }
    
    /**
    * Get language
    *
    * @access   public
    * @return   string
    */
    function getLanguage($language)
    {
        return @$this->_data['languages'][$language];
    }
    
    /**
    * Get all countries
    *
    * @access   public
    * @return   array
    */
    function getCountries()
    {
        return $this->_data['countries'];
    }
    
    /**
    * Get country
    *
    * @access   public
    * @return   string
    */
    function getCountry($country)
    {
        return @$this->_data['countries'][$country];
    }
    
    /**
    * Get all month names
    *
    * @access   public
    * @return   array
    */
    function getMonths()
    {
        return $this->_data['months'];
    }
    
    /**
    * Get all months abbreviations
    *
    * @access   public
    * @return   array
    */
    function getAbbrMonths()
    {
        return $this->_data['abbrMonths'];
    }
    
    /**
    * Get all day names
    *
    * @access   public
    * @return   array
    */
    function getDays()
    {
        return $this->_data['days'];
    }
    
    /**
    * Get all days abbreviations
    *
    * @access   public
    * @return   array
    */
    function getAbbrDays()
    {
        return $this->_data['abbrDays'];
    }
    
    /**
    * Get day name
    *
    * @access   public
    * @return   string
    */
    function getDay($num)
    {
        return @$this->_data['days'][$num];
    }
    
    /**
    * Get day abbreviation
    *
    * @access   public
    * @return   string
    */
    function getAbbrDay($num)
    {
        return @$this->_data['abbrDays'][$num];
    }
    
    /**
    * Get month name
    *
    * @access   public
    * @return   string
    */
    function getMonth($num)
    {
        return @$this->_data['months'][$num];
    }
    
    /**
    * Get month abbreviation
    *
    * @access   public
    * @return   string
    */
    function getAbbrMonth($num)
    {
        return @$this->_data['abbrMonths'][$num];
    }
    
    /**
    * Get all available data
    *
    * @access   public
    * @return   array
    */
    function getAll()
    {
        return $this->_data;
    }
}
?>