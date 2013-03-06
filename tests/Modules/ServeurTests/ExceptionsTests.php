<?php
    namespace Modules\ServeurTests;

    include_once(__DIR__ . '/../../TestEnv.php');

    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'ExceptionTests::main');
    }

    class ExceptionTests {

        public static function main() {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite() {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Modules\ServeurTests\Exceptions\ErrorManagerTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'ExceptionTests::main') {
        ExceptionTests::main();
    }