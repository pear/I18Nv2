<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Util :: ICU                                        |
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
* I18Nv2::Util::ICU
* 
* @author       Michael Wallner <mike@php.net>
* @package      I18NV2
* @category     Internationalization
*/

require_once 'I18Nv2/Util.php';

/**
* I18Nv2_Util_ICU
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
class I18Nv2_Util_ICU
{
    /**
    * Path to ICU files
    * 
    * @var      string
    * @access   public
    */
    var $datapath = '@DATA_DIR@/I18Nv2/ICU';
    
    /**
    * Desired Character Set
    * 
    * @var      string
    * @access   public
    */
    var $encoding = 'UTF-8';
    
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
    * Initializes the tree with an ICU_RootNode.
    * 
    * @access   public
    * @return   object
    * @param    array   $options
    */
    function I18Nv2_Util_ICU($options = array())
    {
        I18Nv2_Util_ICU::__construct($options);
    }
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($options = array())
    {
        $this->setOptions($options);
        $this->init();
    }
    
    /**
    * Set Options
    *
    * @access   public
    * @return   void
    * @param    array   $options
    */
    function setOptions($options)
    {
        if (isset($options['datapath'])) {
            $this->datapath = $options['datapath'];
        }
        if (isset($options['encoding'])) {
            $this->encoding = $options['encoding'];
        }
    }
    
    /**
    * Init
    *
    * @access   public
    * @return   void
    */
    function init()
    {
        $this->tree = &new I18Nv2_Util_ICU_RootNode;
        $this->node = &$this->tree;
    }
    
    /**
    * Parse ICU string
    * 
    * @access   public
    * @return   int     string length (bytes)
    * @param    string  $str
    */
    function parse($str)
    {
        $count = 0;
        $length = $this->prepareRawData($str);
        while ($count < $length)
        {
            $token = $str{$count++};
            if ('{' === $token) {
                $this->node = &$this->node->addChild($this->buffer);
                $this->buffer = '';
            } elseif ('}' === $token) {
                $this->node->setData($this->prepareData($this->buffer));
                if (isset($this->node->parent)) {
                    $this->node = &$this->node->parent;
                }
                $this->buffer = '';
            } else {
                $this->buffer .= $token;
            }
        }
        return $length;
    }
    
    /**
    * Parse ICU file
    *
    * @access   public
    * @return   mixed
    * @param    string  path to ICU file
    */
    function parseFile($file)
    {
        if (!is_file($file)) {
            return false;
        }
        $this->init();
        return $this->parse(file_get_contents($file));
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
    
    /** 
    * Strip comments
    * 
    * @access   public
    * @return   int     cleaned strings length
    * @param    string  $string
    */
    function prepareRawData(&$string)
    {
        static $s = array('/\/\/.*$/m', '/\\\\u([0-9a-fA-F]{4})/e');
        static $r = array('', 'I18Nv2_Util::unichr("\\1")');
        $string = preg_replace($s, $r, $string);
        return strlen($string);
    }
    
    /**
    * Get locale data
    *
    * @access   public
    * @return   mixed
    * @param    sring   $locale
    */
    function getLocale($locale)
    {
        if (!is_file($file = $this->datapath . '/' . $locale . '.txt')) {
            include_once 'PEAR.php';
            return PEAR::raiseError("File for locale '$locale' doesn't exist.");
        }
        $this->parseFile($file);
        return array_shift($this->getTree());
    }
    
    /**
    * Get full locale data
    *
    * @access   public
    * @return   mixed
    */
    function getFullLocale($locale)
    {
        if (!is_array($en = $this->getLocale('en'))) {
            return $en;
        }
        
        @list($lang, $country) = I18Nv2_Util::splitLocale($locale);
        if ('en' === $locale || !is_array($langdata = $this->getLocale($lang))) {
            return $en;
        }
        
        if ($lang === $locale || !is_array($localedata = $this->getLocale($locale))) {
            return I18Nv2_Util::merge($en, $langdata);
        }
        
        return I18Nv2_Util::mergeMany($en, $langdata, $localedata);
    }
    
    /** 
    * Prepare data
    * 
    * @access   public
    * @return   mixed
    * @param    string  $data
    */
    function prepareData($data)
    {
        preg_match_all('/"(.*?)",?/', $data, $matches);
        
        $result = $matches[1];
        
        if (!$count = count($result)) {
            return iconv('UTF-8', $this->encoding, trim($data));
        } elseif ($count == 1) {
            return iconv('UTF-8', $this->encoding, array_shift($result));
        } else {
            return I18Nv2_Util::iconvArray($result, $this->encoding);
        }
    }
    
}

/**
* I18Nv2_Util_ICU_RootNode
* 
* @author   Michael Wallner <mike@php.net>
* @package  I18Nv2
* @version  $Revision$
* @access   protected
*/
class I18Nv2_Util_ICU_RootNode
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
    * @access   public
    * @return   void
    * @param    string  $data
    */
    function setData($data)
    {
        $this->data = $data;
    }
    
    /**
    * Add a ICU_TreeNode child
    * 
    * @access   public
    * @return   object
    * @param    object
    */
    function &addChild($name)
    {
        $child = &new I18Nv2_Util_ICU_TreeNode($name);
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
        return (strToLower(get_class($this)) === 'i18nv2_util_icu_rootnode');
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
* I18Nv2_Util_ICU_TreeNode
* 
* @author   Michael Wallner <mike@php.net>
* @package  I18Nv2
* @version  $Revision$
* @access   protected
*/
class I18Nv2_Util_ICU_TreeNode extends I18Nv2_Util_ICU_RootNode
{
    /**
    * Parent Node
    * @var  object
    */
    var $parent;
    
    /**
    * Constructor
    * 
    * @access   public
    * @return   object
    * @param    string  $name
    */
    function I18Nv2_Util_ICU_TreeNode($name)
    {
        I18Nv2_Util_ICU_TreeNode::__construct($name);
    }
    
    /**
    * ZE2 Constructor
    * @ignore
    */
    function __construct($name)
    {
        parent::__construct();
        $this->name = trim($name);
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
        if ($isRootNode = is_a($parent, 'I18Nv2_Util_ICU_RootNode')) {
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
?>
