<?php

/**
* Decorating a List
* =================
*
* I18Nv2 provides decorated classes for country and language lists.
* There's just another way to use them like in using_I18Nv2_DecoratedList.php
* 
* $Id$
*/

require_once 'I18Nv2/Country.php';

$c = &new I18Nv2_Country('it', 'iso-8859-1');

// Filter
$f = &$c->toDecoratedList('Filter');
$f->elements = array('it', 'fr', 'de', 'ch', 'at', 'es', 'lu', 'nl', 'be', 'gb');

// HtmlSelect
$s = &$f->toDecoratedList('HtmlSelect');

// set some attributes
$s->attributes['select']['name'] = 'CountrySelect';
$s->attributes['select']['onchange'] = 'this.form.submit()';

// set a selected entry
$s->selected['DE'] = true;

// print a HTML select box
echo $s->getAllCodes();
?>