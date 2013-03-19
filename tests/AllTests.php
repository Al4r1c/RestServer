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

        $suite->addTestSuite('Modules\ClassLoader\ClassLoaderTest');
        $suite->addTest(Modules\LoggingTests\I18nTests::suite());
        $suite->addTest(Modules\ServeurTests\ApplicationTests::suite());
        $suite->addTest(Modules\ServeurTests\GestionErreursTests::suite());
        $suite->addTest(Modules\ServeurTests\LibTests::suite());
        $suite->addTest(Modules\ServeurTests\RequeteTests::suite());
        $suite->addTest(Modules\ServeurTests\TraitementTests::suite());
        $suite->addTest(Modules\ServeurTests\ReponseTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}