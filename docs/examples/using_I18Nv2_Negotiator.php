<?php

/**
* Using I18Nv2_Negotiator
* =======================
*
* I18Nv2 provides a language, charset and locale negotiator for HTTP.
* 
* $Id$
*/

require_once 'I18Nv2/Negotiator.php';

if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en-GB,en;q=0.5,de';
if(!isset($_SERVER['HTTP_ACCEPT_CHARSET']))
$_SERVER['HTTP_ACCEPT_CHARSET']  = 'utf-8,iso-8859-1;q=0.5';

$neg = &new I18Nv2_Negotiator;

echo "<pre>\nUser agents preferred language:                  ",
    $lang = $neg->getLanguageMatch(), "\n";

echo "User agents preferred country for language '$lang': ",
    $neg->getCountryMatch($lang), "\n";

echo "User agents preferred locale:                    ",
    $neg->getLocaleMatch(), "\n";

echo "User agents preferred charset:                   ",
    $neg->getCharsetMatch(), "\n";

?>