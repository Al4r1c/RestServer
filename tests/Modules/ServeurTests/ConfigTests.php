<?php
    namespace Modules\ServeurTests;

    if (!defined('PHPUnit_MAIN_METHOD'))
    {
        define('PHPUnit_MAIN_METHOD', 'ConfigTests::main');
    }

    class ConfigTests
    {

        public static function main()
        {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite()
        {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Modules\ServeurTests\Config\ConfigTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'ConfigTests::main')
    {
        ConfigTests::main();
    }