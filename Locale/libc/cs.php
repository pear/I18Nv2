<?php
/**
* $Id$
* @author <mishal(@)centrum.cz>
*/

$this->formats['date'] = array(
    I18Nv2_DATETIME_SHORT     =>  '%d.%m.%y',
    I18Nv2_DATETIME_DEFAULT   =>  '%d.%m.%Y',
    I18Nv2_DATETIME_MEDIUM    =>  '%d %b %Y',
    I18Nv2_DATETIME_LONG      =>  '%d %B %Y',
    I18Nv2_DATETIME_FULL      =>  '%A, %d %B %Y'
);
$this->formats['time'] = array(
    I18Nv2_DATETIME_SHORT     =>  '%H:%M',
    I18Nv2_DATETIME_DEFAULT   =>  '%H:%M:%S',
    I18Nv2_DATETIME_MEDIUM    =>  '%H:%M:%S',
    I18Nv2_DATETIME_LONG      =>  '%H:%M:%S %Z',
    I18Nv2_DATETIME_FULL      =>  '%H:%M hod. %Z'
);

$this->formats['currency'][I18Nv2_CURRENCY_LOCAL][0] = 'Kè';
$this->formats['currency'][I18Nv2_CURRENCY_LOCAL][1] = '2';
$this->formats['currency'][I18Nv2_CURRENCY_LOCAL][2] = ',';
$this->formats['currency'][I18Nv2_CURRENCY_LOCAL][3] = '.';
$this->formats['currency'][I18Nv2_CURRENCY_INTERNATIONAL][0] = 'CZK';
$this->formats['currency'][I18Nv2_CURRENCY_INTERNATIONAL][1] = '2';
$this->formats['currency'][I18Nv2_CURRENCY_INTERNATIONAL][2] = '.';
$this->formats['currency'][I18Nv2_CURRENCY_INTERNATIONAL][3] = ',';

?>
