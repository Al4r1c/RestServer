<?php
    namespace Tests\LoggingTests;

    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'I18nTests::main');
    }

    class I18nTests
    {

        public static function main()
        {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite()
        {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Tests\LoggingTests\I18n\TradManagerTest');
            $suite->addTestSuite('Tests\LoggingTests\I18n\I18nManagerTest');
            $suite->addTestSuite('Tests\LoggingTests\Factory\LoggingFactoryTest');
            $suite->addTestSuite('Tests\LoggingTests\Displayer\LoggerTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'I18nTests::main') {
        I18nTests::main();
    }