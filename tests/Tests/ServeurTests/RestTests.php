<?php
	namespace Tests\ServeurTests;

	include_once(__DIR__ . '/../../TestEnv.php');

	if(!defined('PHPUnit_MAIN_METHOD')) {
		define('PHPUnit_MAIN_METHOD', 'RestTests::main');
	}

	class RestTests {

		public static function main() {
			\PHPUnit_TextUI_TestRunner::run(self::suite());
		}

		public static function suite() {
			$suite = new \PHPUnit_Framework_TestSuite('TestSuite');

			$suite->addTestSuite('Tests\ServeurTests\Renderers\RenderersTest');
			$suite->addTestSuite('Tests\ServeurTests\Rest\HeaderManagerTest');
			$suite->addTestSuite('Tests\ServeurTests\Rest\ServerTest');
			$suite->addTestSuite('Tests\ServeurTests\Rest\RestRequeteTest');
			$suite->addTestSuite('Tests\ServeurTests\Rest\RestReponseTest');
			$suite->addTestSuite('Tests\ServeurTests\Rest\RestManagerTest');

			return $suite;
		}
	}

	if(PHPUnit_MAIN_METHOD == 'RestTests::main') {
		RestTests::main();
	}