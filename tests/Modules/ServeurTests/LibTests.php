<?php
    namespace Modules\ServeurTests;

    include_once(__DIR__ . '/../../TestEnv.php');

    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'LibTests::main');
    }

    class LibTests {

        public static function main() {
            \PHPUnit_TextUI_TestRunner::run(self::suite());
        }

        public static function suite() {
            $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

            $suite->addTestSuite('Modules\ServeurTests\Lib\FichierTest');
            $suite->addTestSuite('Modules\ServeurTests\Lib\FileSystemTest');
            $suite->addTestSuite('Modules\ServeurTests\Lib\FichierChargementTest');
            $suite->addTestSuite('Modules\ServeurTests\Lib\TypeDetectorTest');
            $suite->addTestSuite('Modules\ServeurTests\XMLParser\XMLParserTest');

            return $suite;
        }
    }

    if (PHPUnit_MAIN_METHOD == 'LibTests::main') {
        ConfigTests::main();
    }