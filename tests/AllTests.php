<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

class AllTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Server Tests');

        $suite->addTest(\Tests\LoggingTests\I18nTests::suite());
        $suite->addTest(\Tests\ServeurTests\ApplicationTests::suite());
        $suite->addTest(\Tests\ServeurTests\GestionErreursTests::suite());
        $suite->addTest(\Tests\ServeurTests\LibTests::suite());
        $suite->addTest(\Tests\ServeurTests\RequeteTests::suite());
        $suite->addTest(\Tests\ServeurTests\TraitementTests::suite());
        $suite->addTest(\Tests\ServeurTests\ReponseTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}