<?php

require_once 'PHPUnit.php';
require_once 'I18Nv2/Locale.php';

PEAR::setErrorHandling(PEAR_ERROR_PRINT, "PEAR_Error: %s\n");

/**
* TestCase for I18Nv2_LocaleTest class
* Generated by PHPEdit.XUnit Plugin
*/
class I18Nv2_LocaleTest extends PHPUnit_TestCase
{
    /**
    * Constructor
    * 
    * @param string $name The name of the test.
    * @access protected 
    */
    function I18Nv2_LocaleTest($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    /**
    * Called before the test functions will be executed this function is defined in PHPUnit_TestCase and overwritten here
    * 
    * @access protected 
    */
    function setUp()
    {
        $this->l = &new I18Nv2_Locale('en_US');
        $this->t = time();
    }

    /**
    * Called after the test functions are executed this function is defined in PHPUnit_TestCase and overwritten here
    * 
    * @access protected 
    */
    function tearDown()
    {
        unset($this->l);
        $this->l = null;
    }

    /**
    * Regression test for I18Nv2_Locale.setLocale method
    * 
    * @access public 
    */
    function testsetLocale()
    {
        $this->assertFalse(PEAR::isError($this->l->setLocale('en_US')));
    }

    /**
    * Regression test for I18Nv2_Locale.setCurrencyFormat method
    * 
    * @access public 
    */
    function testsetCurrencyFormat()
    {
        $this->assertFalse(PEAR::isError($this->l->setCurrencyFormat(I18Nv2_CURRENCY_LOCAL)));
    }

    /**
    * Regression test for I18Nv2_Locale.setNumberFormat method
    * 
    * @access public 
    */
    function testsetNumberFormat()
    {
        $this->assertFalse(PEAR::isError($this->l->setNumberFormat(I18Nv2_NUMBER_FLOAT)));
    }

    /**
    * Regression test for I18Nv2_Locale.setDateFormat method
    * 
    * @access public 
    */
    function testsetDateFormat()
    {
        $this->assertFalse(PEAR::isError($this->l->setDateFormat(I18Nv2_DATETIME_FULL)));
    }

    /**
    * Regression test for I18Nv2_Locale.setTimeFormat method
    * 
    * @access public 
    */
    function testsetTimeFormat()
    {
        $this->assertFalse(PEAR::isError($this->l->setTimeFormat(I18Nv2_DATETIME_FULL)));
    }

    /**
    * Regression test for I18Nv2_Locale.setCustomFormat method
    * 
    * @access public 
    */
    function testsetCustomFormat()
    {
        $tf = '%d. %B %Y';
        $this->l->setCustomFormat(I18Nv2_DATETIME, $tf);
        $this->assertEquals($tf, $this->l->_customFormats[I18Nv2_DATETIME]);
    }

    /**
    * Regression test for I18Nv2_Locale.formatCurrency method
    * 
    * @access public 
    */
    function testformatCurrency()
    {
        $this->assertEquals('USD 2,000.00', $this->l->formatCurrency(2000));
    }

    /**
    * Regression test for I18Nv2_Locale.formatNumber method
    * 
    * @access public 
    */
    function testformatNumber()
    {
        $this->assertEquals('2.13', $this->l->formatNumber(2.1331994));
    }

    /**
    * Regression test for I18Nv2_Locale.formatDate method
    * 
    * @access public 
    */
    function testformatDate()
    {
        $this->assertEquals(strftime('%d-%b-%Y', $this->t), $this->l->formatDate($this->t));
    }

    /**
    * Regression test for I18Nv2_Locale.formatTime method
    * 
    * @access public 
    */
    function testformatTime()
    {
        $this->assertEquals(strftime('%T', $this->t), $this->l->formatTime($this->t));
    }

    /**
    * Regression test for I18Nv2_Locale.time method
    * 
    * @access public 
    */
    function testtime()
    {
        $this->assertEquals(strftime('%x', $this->t), $this->l->time($this->t));
    }

    /**
    * Regression test for I18Nv2_Locale.date method
    * 
    * @access public 
    */
    function testdate()
    {
        $this->assertEquals(strftime('%X', $this->t), $this->l->date($this->t));
    }

    /**
    * Regression test for I18Nv2_Locale.dayName method
    * 
    * @access public 
    */
    function testdayName()
    {
        $dayNum = strftime('%w', $this->t);
        $this->assertEquals(strftime('%A', $this->t), $this->l->dayName($dayNum));
        $this->assertEquals(strftime('%a', $this->t), $this->l->dayName($dayNum, true));
    }

    /**
    * Regression test for I18Nv2_Locale.monthName method
    * 
    * @access public 
    */
    function testmonthName()
    {
        $monthNum = strftime('%m', $this->t) -1;
        $this->assertEquals(strftime('%B', $this->t), $this->l->monthName($monthNum));
        $this->assertEquals(strftime('%b', $this->t), $this->l->monthName($monthNum, true));
    }
}

$ts = &new PHPUnit_TestSuite('I18Nv2_LocaleTest');
$rs = PHPUnit::run($ts);
echo $rs->toString();
?>