<?php
    namespace Modules\ServeurTests;

    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'RestTests::main');
    }

    class RestTests {

        public static function main() {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite() {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Modules\ServeurTests\Renderers\RenderersTest');
            $suite->addTestSuite('Modules\ServeurTests\Rest\HeaderManagerTest');
            $suite->addTestSuite('Modules\ServeurTests\Rest\ServerTest');
            $suite->addTestSuite('Modules\ServeurTests\Rest\RestRequeteTest');
            $suite->addTestSuite('Modules\ServeurTests\Rest\RestReponseTest');
            $suite->addTestSuite('Modules\ServeurTests\Rest\RestManagerTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'RestTests::main') {
        RestTests::main();
    }