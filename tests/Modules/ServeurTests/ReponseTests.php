<?php
namespace Tests\ServeurTests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ReponseTests::main');
}

class ReponseTests
{

    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('Tests\ServeurTests\Reponse\ConfigTest');
        $suite->addTestSuite('Tests\ServeurTests\Reponse\RenderersTest');
        $suite->addTestSuite('Tests\ServeurTests\Reponse\HeaderManagerTest');
        $suite->addTestSuite('Tests\ServeurTests\Reponse\ReponseManagerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ReponseTests::main') {
    ReponseTests::main();
}