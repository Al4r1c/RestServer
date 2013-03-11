<?php
    namespace Modules\ServeurTests;

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

            $suite->addTestSuite('Modules\ServeurTests\Requete\ServerTest');
            $suite->addTestSuite('Modules\ServeurTests\Requete\RequeteManagerTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'RequeteTests::main') {
        RequeteTests::main();
    }