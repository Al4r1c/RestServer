<?php
namespace Tests\ServeurTests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'TraitementTests::main');
}

class TraitementTests
{

    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('Tests\ServeurTests\Traitement\AbstractRessourceTest');
        $suite->addTestSuite('Tests\ServeurTests\Traitement\AbstractDatabaseTest');
        $suite->addTestSuite('Tests\ServeurTests\Traitement\AuthorizationManagerTest');
        $suite->addTestSuite('Tests\ServeurTests\Traitement\AuthorizationTest');
        $suite->addTestSuite('Tests\ServeurTests\Traitement\TraitementManagerTest');
        $suite->addTestSuite('Tests\ServeurTests\Traitement\DatabaseConfigTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'TraitementTests::main') {
    TraitementTests::main();
}