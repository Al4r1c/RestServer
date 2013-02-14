<?php
	namespace Tests;

	include_once(__DIR__ . '/../TestEnv.php');

	if (!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'ApplicationTest::main');
	}

	class ApplicationTest {

		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('TestSuite');

			$suite->addTestSuite('Tests\Application\MainApplicationTest');

			return $suite;
		}
	}

	if (PHPUnit_MAIN_METHOD == 'ApplicationTest::main') {
		ApplicationTest::main();
	}
?>