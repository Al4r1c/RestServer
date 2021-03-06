<?php
namespace Tests\ServeurTests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'LibTests::main');
}

class LibTests
{

    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('Tests\ServeurTests\Lib\ObjetReponseTest');
        $suite->addTestSuite('Tests\ServeurTests\Lib\TypeDetectorTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'LibTests::main') {
    LibTests::main();
}