<?php
    namespace Modules\ServeurTests;

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

            $suite->addTestSuite('Modules\ServeurTests\Reponse\ConfigTest');
            $suite->addTestSuite('Modules\ServeurTests\Reponse\RenderersTest');
            $suite->addTestSuite('Modules\ServeurTests\Reponse\HeaderManagerTest');
            $suite->addTestSuite('Modules\ServeurTests\Reponse\RestReponseTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'ReponseTests::main') {
        ReponseTests::main();
    }