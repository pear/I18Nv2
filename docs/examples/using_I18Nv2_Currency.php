<?php

/**
* Using I18Nv2_Currency
* =====================
*
* I18Nv2 provides translated lists of currency names.
* 
* $Id$
*/

require_once 'I18Nv2/Currency.php';

$c = &new I18Nv2_Currency('de', 'iso-8859-1');

echo "German name for US Dollars:      ",
    $c->getName('usd'), "\n";

echo "German name for English Pounds:  ",
    $c->getName('GBP'), "\n";


$c->setLanguage('it');

echo "Italian name for US Dollars:     ",
    $c->getName('usd'), "\n";

echo "Italian name for English Pounds: ",
    $c->getName('GBP'), "\n";

?>