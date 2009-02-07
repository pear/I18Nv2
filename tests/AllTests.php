<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'I18Nv2_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once dirname(__FILE__) . '/I18Nv2_LocaleTest.php';


class I18Nv2_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('I18Nv2 package');

        $suite->addTestSuite('I18Nv2_LocaleTest');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'I18Nv2_AllTests::main') {
    I18Nv2_AllTests::main();
}
?>
