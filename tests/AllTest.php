<?php
	include_once('TestEnv.php');

	if(!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'AllTests::main');
	}

	class AllTests {
		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('Most Tests');

			$suite->addTest(Tests\ApplicationTest::suite());

			return $suite;
		}
	}

	if(PHPUnit_MAIN_METHOD == 'AllTests::main') {
		AllTests::main();
	}
?>