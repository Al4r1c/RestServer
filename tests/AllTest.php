<?php
	include_once('TestEnv.php');

	if (!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'AllTests::main');
	}

	class AllTests {
		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('Server Tests');

			$suite->addTest(Tests\ApplicationTests::suite());
			$suite->addTest(Tests\ConfigTests::suite());
			$suite->addTest(Tests\I18nTests::suite());
			$suite->addTest(Tests\LibTests::suite());
			$suite->addTest(Tests\RestTests::suite());

			return $suite;
		}
	}

	if(PHPUnit_MAIN_METHOD == 'AllTests::main') {
		AllTests::main();
	}