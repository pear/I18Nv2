<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: OpenI18N :: ICUParser                              |
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
* I18Nv2_OpenI18N_ICUParser
* 
* @see  The OpenI18N Project    http://www.OpenI18N.org/
* @see  IBM ICU CVS             http://oss.software.ibm.com/cvs/icu/
* @see  IBM ICU Data            http://oss.software.ibm.com/cvs/icu/icu/source/data/locales/
* 
* @author   Michael Wallner <mike@php.net>
* @package  I18Nv2
* @version  $Revision$
* @access   public
*/
class I18Nv2_OpenI18N_ICUParser
{
    /**
    * Reference to the current node in the tree
    * 
    * @var      object
    * @access   protected
    */
    var $node;
    
    /**
    * ICU tree
    * 
    * @var      object
    * @access   protected
    */
    var $tree;
    
    /**
    * String buffer
    * 
    * @var      string
    * @access   protected
    */
    var $buffer = '';
    
    /**
    * Constructor
    * 
    * Initializes the tree with an ICURootNode.
    * 
    * @access   public
    * @return   object
    */
    function I18Nv2_OpenI18N_ICUParser()
    {
        I18Nv2_OpenI18N_ICUParser::__construct();
    }
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct()
    {
        $this->tree = &new I18Nv2_OpenI18N_ICURootNode;
        $this->node = &$this->tree;
    }
    
    /**
    * Parse ICU string
    * 
    * @access   public
    * @return   int     string length
    * @param    string  $str
    */
    function parse($str)
    {
        $count = 0;
        $length = strlen($str);
        while ($count < $length)
        {
            $token = $str{$count++};
            
            switch ($token)
            {
                case '{':
                     $this->node = &$this->node->addChild(
                        new I18Nv2_OpenI18N_ICUTreeNode($this->buffer)
                     );
                break;
                
                case '}':
                    $this->node->setData($this->buffer);
                    $this->node = &$this->node->parent;
                break;
                
                default:
                     $this->buffer .= ($token);
                break;
            }
        }
        return $length;
    }
    
    /**
    * Get the tree
    * 
    * @access   public
    * @return   array
    * @param    bool    $asArray
    */
    function getTree($asArray = true)
    {
        return $asArray ? $this->tree->toArray() :  $this->tree->getChildren();
    }
}

/**
* I18Nv2_OpenI18N_ICURootNode
* 
* @author   Michael Wallner <mike@php.net>
* @package  I18Nv2
* @version  $Revision$
* @access   protected
*/
class I18Nv2_OpenI18N_ICURootNode
{
    /**#@+
    * @access   protected
    */
    var $childs = array();
    var $data = '';
    var $name = '__ROOT__';
    /**#@-**/
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct() {}
    
    /**
    * Set payload of the treenode
    * 
    * Supplied data will be truncated.
    * 
    * @access   public
    * @return   void
    * @param    string  $data
    */
    function setData(&$data)
    {
        $this->data = trim($data);
        $data = '';
    }
    
    /**
    * Add a ICUTreeNode child
    * 
    * Returns the child supplied as parameter.
    * 
    * @access   public
    * @return   object
    * @param    object
    */
    function &addChild(&$child)
    {
        if (!is_a($child, 'I18Nv2_OpenI18N_ICUTreeNode')) {
            return null;
        }
        $child->setParent($this);
        $this->childs[] = &$child;
        return $child;
    }
    
    /**
    * Get parent tree node
    * 
    * The RootNode will return a reference to itself.
    * 
    * @access   public
    * @return   object
    */
    function &getParent()
    {
        return $this;
    }
    
    /**
    * Check wheter this node is a RootNode
    * 
    * @access   public
    * @return   bool
    */
    function isRootNode()
    {
        return (strToLower(get_class($this)) === 'i18nv2_openi18n_icurootnode');
    }
    
    /**
    * Get children
    * 
    * @access   public
    * @return   array
    */
    function getChildren()
    {
        return $this->childs;
    }
    
    /**
    * Get trees data as array
    * 
    * @access   public
    * @return   array
    */
    function toArray()
    {
        return $this->_getChilds($this->childs);
    }
    
    /**
    * Tree ==> Array
    * 
    * @access   private
    * @return   array
    * @param    array   $childs
    */
    function _getChilds($childs)
    {
        $result = array();
        foreach ($childs as $child){
            if (count($child->childs)) {
                $result[$child->name] = $this->_getChilds($child->childs);
            } else {
                $result[$child->name] = $child->data;
            }
        }
        return $result;
    }
}

/**
* I18Nv2_OpenI18N_ICUTreeNode
* 
* @author   Michael Wallner <mike@php.net>
* @package  I18Nv2
* @version  $Revision$
* @access   protected
*/
class I18Nv2_OpenI18N_ICUTreeNode extends I18Nv2_OpenI18N_ICURootNode
{
    /**
    * Parent Node
    * @var  object
    */
    var $parent;
    
    /**
    * Constructor
    * 
    * Supplied variable gets truncated.
    * 
    * @access   public
    * @return   object
    * @param    string  $name
    */
    function I18Nv2_OpenI18N_ICUTreeNode(&$name)
    {
        I18Nv2_OpenI18N_ICUTreeNode::__construct($name);
    }
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct(&$name)
    {
        parent::__construct();
        $this->name = trim($name);
        $name = '';
    }
    
    /**
    * Set parent node
    * 
    * @access   public
    * @return   bool
    * @param    object
    */
    function setParent(&$parent)
    {
        if ($isRootNode = is_a($parent, 'I18Nv2_OpenI18N_ICURootNode')) {
            $this->parent = &$parent;
        }
        return $isRootNode;
    }
    
    /**
    * Get parent node
    * 
    * @access   public
    * @return   object
    */
    function &getParent()
    {
        return $this->parent;
    }
}

$t = &new I18Nv2_OpenI18N_ICUParser();
$t->parse(' ICU { de{ "DE", "Deutsch" } en { "EN", "Englisch" } es { territory {"ES", "Spanien"} currency {"ESP", "Spanischer Peso"} }}');
#var_dump($t->tree);
print_r($t->tree->toArray());

?>