<?php
	namespace Tests\Lib;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;

	class TradManagerTest extends TestCase {

		/** @var \Serveur\I18n\TradManager */
		private $tradManager;

		public function setUp() {
			$this->tradManager = new \Serveur\I18n\TradManager();
		}

		public function setXmlObjet() {
			$xmlData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<root>
				<section>
					<message code=\"1\">Mess1</message>
					<message code=\"2\">Mess2</message>
					<message code=\"3\">MessParticulier</message>
				</section>
				<key>
					<message code=\"a\">goA</message>
					<message code=\"b\">goB</message>
					<message code=\"c\">goC</message>
				</key>
			</root>";

			$this->tradManager->setFichierTraduction(new \Serveur\Lib\XMLParser\XMLParser($xmlData));
		}

		public function testSetXmlObjet() {
			$XMLParser = new \Serveur\Lib\XMLParser\XMLParser("<root></root>");

			$this->tradManager->setFichierTraduction($XMLParser);

			$this->assertAttributeEquals(
				$XMLParser,
				'fichierTraductionDefaut',
				$this->tradManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\TradManagerException
		 * @expectedExceptionCode 40100
		 */
		public function testSetXmlObjetInvalide() {
			$XMLParser = new \Serveur\Lib\XMLParser\XMLParser("<root></toor>");

			$this->tradManager->setFichierTraduction($XMLParser);
		}

		public function testTransformeMessage() {
			$this->setXmlObjet();

			$this->assertEquals("goA messagerie MessParticulier", $this->tradManager->recupererChaineTraduite("{key.a} messagerie {section.3}"));
		}

		public function testTransformeMessageRienModifie() {
			$this->setXmlObjet();

			$this->assertEquals("Message banal", $this->tradManager->recupererChaineTraduite("Message banal"));
		}

		public function testTransformeMessageNonTrouvee() {
			$this->setXmlObjet();

			$this->assertEquals("Message avec {fake.clef}.", $this->tradManager->recupererChaineTraduite("Message avec {fake.clef}."));
		}

		public function testGetTraduction() {
			$this->setXmlObjet();

			$class = new \ReflectionClass('Serveur\I18n\TradManager');
			$method = $class->getMethod('getTraduction');
			$method->setAccessible(true);

			$this->assertEquals('Mess2', $method->invokeArgs($this->tradManager, array('section', '2')));
			$this->assertEquals('goC', $method->invokeArgs($this->tradManager, array('key', 'c')));
		}

		public function testGetTraductionNonTrouvee() {
			$this->setXmlObjet();

			$class = new \ReflectionClass('Serveur\I18n\TradManager');
			$method = $class->getMethod('getTraduction');
			$method->setAccessible(true);

			$this->assertEquals('{key.z}', $method->invokeArgs($this->tradManager, array('key', 'z')));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\TradManagerException
		 * @expectedExceptionCode 40102
		 */
		public function testGetTraductionAucunXml() {
			$this->tradManager->recupererChaineTraduite("NO XML");
		}
	}