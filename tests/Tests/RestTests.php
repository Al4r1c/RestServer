<?php
	namespace Tests;

	include_once(__DIR__ . '/../TestEnv.php');

	if(!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'RestTests::main');
	}

	class RestTests {

		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('TestSuite');

			$suite->addTestSuite('Tests\Renderers\RenderersTest');
			$suite->addTestSuite('Tests\Rest\HeaderManagerTest');
			$suite->addTestSuite('Tests\Rest\ServerTest');
			$suite->addTestSuite('Tests\Rest\RestRequeteTest');
			$suite->addTestSuite('Tests\Rest\RestReponseTest');
			$suite->addTestSuite('Tests\Rest\RestManagerTest');

			return $suite;
		}
	}

	if(PHPUnit_MAIN_METHOD == 'RestTests::main') {
		RestTests::main();
	}