<?php

class I18Nv2_OpenI18N_ICUParser
{
    
    var $node;
    var $toks = '';
    var $tree;
    
    function I18Nv2_OpenI18N_ICUParser()
    {
        $this->tree = &new I18Nv2_OpenI18N_ICURootNode;
        $this->node = &$this->tree;
    }
    
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
                     $c = $this->node->addChild(
                        new I18Nv2_OpenI18N_ICUTreeNode($this->toks)
                     );
                     $this->node = &$this->node->childs[$c];
                break;
                
                case '}':
                    $this->node->setData($this->toks);
                    $this->node = &$this->node->parent;
                break;
                
                default:
                     $this->toks .= ($token);
                break;
            }
        }
    }
}

class I18Nv2_OpenI18N_ICURootNode
{
    var $childs = array();
    var $data = '';
    var $name = '__ROOT__';
    
    function setData(&$data)
    {
        $this->data = trim($data);
        $data = '';
    }
    
    function addChild(&$child)
    {
        if (!is_a($child, 'I18Nv2_OpenI18N_ICUTreeNode')) {
            return -1;
        }
        $count = count($this->childs);
        $this->childs[$count] = &$child;
        $child->setParent($this);
        return $count;
    }
    
    function &getParent()
    {
        return $this;
    }
    
    function isRootNode()
    {
        return (strToLower(get_class($this)) === 'i18nv2_openi18n_icurootnode');
    }
    
    function getChildren()
    {
        return $this->childs;
    }
    
    function toArray()
    {
        return $this->_getChilds($this->childs);
    }
    
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

class I18Nv2_OpenI18N_ICUTreeNode extends I18Nv2_OpenI18N_ICURootNode
{
    var $parent;
    
    function I18Nv2_OpenI18N_ICUTreeNode(&$name)
    {
        $this->name = trim($name);
        $name = '';
    }
    
    function setParent(&$parent)
    {
        if ($isRootNode = is_a($parent, 'I18Nv2_OpenI18N_ICURootNode')) {
            $this->parent = &$parent;
        }
        return $isRootNode;
    }
    
    function &getParent()
    {
        return $this->parent;
    }
}

/*
$t = &new I18Nv2_OpenI18N_ICUParser();
$t->parse(' ICU { de{ "DE", "Deutsch" } en { "EN", "Englisch" } es { territory {"ES", "Spanien"} currency {"ESP", "Spanischer Peso"} }}');
#var_dump($t->tree);
print_r($t->tree->toArray());
*/
?>