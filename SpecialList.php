<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: SpecialList                                        |
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
 * I18Nv2::SpecialList
 * 
 * @author      Michael Wallner <mike@php.net>
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/CommonList.php';
 
/** 
 * I18Nv2_SpecialList
 * 
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @access      public
 */
class I18Nv2_SpecialList extends I18Nv2_CommonList
{
    /**
     * Key
     * 
     * @access  protected
     * @var     string
     */
    var $key = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string  $language
     * @param   string  $encoding
     */
    function I18Nv2_SpecialList($key, $language = null, $encoding = null)
    {
        $this->key = $this->changeKeyCase($key);
        parent::I18Nv2_CommonList($language, $encoding);
    }
}
?>
