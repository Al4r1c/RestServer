<?php
namespace Tests\ServeurTests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'RestTests::main');
}

class RequeteTests
{

    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('Tests\ServeurTests\Requete\RequeteHeadersTest');
        $suite->addTestSuite('Tests\ServeurTests\Requete\ServerTest');
        $suite->addTestSuite('Tests\ServeurTests\Requete\RequeteManagerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'RequeteTests::main') {
    RequeteTests::main();
}