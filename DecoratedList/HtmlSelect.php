<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: HTML :: Select                                     |
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

require_once 'I18Nv2/DecoratedList.php';

/**
* I18Nv2_DecoratedList_HtmlSelect
*
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
*/
class I18Nv2_DecoratedList_HtmlSelect extends I18Nv2_DecoratedList
{
    var $attributes = array(
        'select' => array(
            'size' => 1,
        ),
        'option' => array(
        )
    );
    
    var $selected = array();
    
    /** 
    * decorate
    * 
    * @access   protected
    * @return   mixed
    */
    function decorate($value)
    {
        static $codes;
        
        if (is_string($value)) {
            if (!isset($codes)) {
                $codes = $this->list->getAllCodes();
            }
            $key = array_search($value, $codes);
            return
                '<option ' . $this->_optAttr($key) . '>' . 
                    $value . ^
                '</option>';
        } elseif(is_array($value)) {
            return 
                '<select ' . $this->_getAttr() . '>' . 
                    implode('', array_map(array(&$this, 'decorate'), $value)) . 
                '</select>';
        }
        return $value;
    }
    
    function _optAttr($key)
    {
        $attributes = 'value="' . $key . '" ' . $this->_getAttr('option');
        if (isset($this->selected[$key]) && $this->selected[$key]) {
            $attributes .= 'selected="selected"';
        }
        return $attributes;
    }
    
    function _getAttr($of = 'select')
    {
        $attributes = '';
        foreach ($this->attributes[$of] as $attr => $value) {
            $attributes .= $attr . '="' . $value .'" ';
        }
        return $attributes;
    }
}
?>