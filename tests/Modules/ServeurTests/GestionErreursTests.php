<?php
    namespace Modules\ServeurTests;

    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'GestionErreursTests::main');
    }

    class GestionErreursTests
    {

        public static function main()
        {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite()
        {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Modules\ServeurTests\GestionErreurs\ErrorManagerTest');
            $suite->addTestSuite('Modules\ServeurTests\GestionErreurs\ErreurHandlerTest');
            $suite->addTestSuite('Modules\ServeurTests\GestionErreurs\MainExceptionTest');
            $suite->addTestSuite('Modules\ServeurTests\GestionErreurs\ArgumentTypeExceptionTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'ExceptionTests::main') {
        GestionErreursTests::main();
    }