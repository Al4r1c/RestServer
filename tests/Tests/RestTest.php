<?php
	namespace Tests;

	include_once(__DIR__ . '/../TestEnv.php');

	if (!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'RestTest::main');
	}

	class RestTest {

		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('TestSuite');

			$suite->addTestSuite('Tests\Rest\RestRequeteTest');

			return $suite;
		}
	}

	if (PHPUnit_MAIN_METHOD == 'RestTest::main') {
		RestTest::main();
	}
?>