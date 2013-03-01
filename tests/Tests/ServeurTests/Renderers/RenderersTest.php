<?php
	namespace Tests\ServeurTests\Renderers;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;

	class RenderersTest extends TestCase {

		private static $donnee = array('param1' => 1, 'param2' => array('one' => 'onevar2'), array('yosh', 'yosh2'));

		public function testRenderHtml() {
			$renderer = new \Serveur\Renderers\Html();

			$this->assertEquals(
				"<!DOCTYPE html>\r\n<html>\r\n<head>\r\n\t<title>Data</title>\r\n\t<meta http-equiv=\"Content-Type\" content=\"text/html\" />\r\n\t<style>\r\n\t\tbody {\r\n\t\t\tfont-family: Helvetica, Arial, sans-serif;\r\n\t\t\tfont-size: 14px;\r\n\t\t\tcolor: #000;\r\n\t\t\tpadding: 5px;\r\n\t    }\r\n\t    ul {\r\n\t\t\tpadding-bottom: 15px;\r\n\t\t\tpadding-left: 20px;\r\n\t    }\r\n    </style>\r\n</head>\r\n<body>\r\n<ul>\n\t<li><strong>param1:</strong> 1</li>\n\t<li><strong>param2:</strong> <ul>\n\t<li><strong>one:</strong> onevar2</li>\n</ul>\n</li>\n\t<li><strong>0:</strong> <ul>\n\t<li><strong>0:</strong> yosh</li>\n\t<li><strong>1:</strong> yosh2</li>\n</ul>\n</li>\n</ul>\n\r\n</body>\r\n</html>",
				$renderer->render(self::$donnee)
			);
		}

		public function testJson() {
			$renderer = new \Serveur\Renderers\Json();

			$this->assertEquals('{"param1":1,"param2":{"one":"onevar2"},"0":["yosh","yosh2"]}', $renderer->render(self::$donnee));
		}

		public function testPlain() {
			$renderer = new \Serveur\Renderers\Plain();

			$this->assertEquals("param1 => 1\nparam2 => \n\tone => onevar2\n0 => \n\t0 => yosh\n\t1 => yosh2\n", $renderer->render(self::$donnee));
		}

		public function testXml() {
			$renderer = new \Serveur\Renderers\Xml();

			$this->assertEquals(
				'<?xml version="1.0"?>' . "\n" . '<root><param1>1</param1><param2><one>onevar2</one></param2><0>yosh</0><1>yosh2</1></root>' . "\n",
				$renderer->render(self::$donnee)
			);
		}
	}