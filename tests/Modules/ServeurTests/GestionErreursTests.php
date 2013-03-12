<?php
    namespace Tests\ServeurTests;

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

            $suite->addTestSuite('Tests\ServeurTests\GestionErreurs\ErrorManagerTest');
            $suite->addTestSuite('Tests\ServeurTests\GestionErreurs\ErreurHandlerTest');
            $suite->addTestSuite('Tests\ServeurTests\GestionErreurs\MainExceptionTest');
            $suite->addTestSuite('Tests\ServeurTests\GestionErreurs\ArgumentTypeExceptionTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'ExceptionTests::main') {
        GestionErreursTests::main();
    }