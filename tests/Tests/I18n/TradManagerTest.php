<?php
	namespace Tests\I18n;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;

	class TradManagerTest extends TestCase {

		/** @var \Serveur\I18n\TradManager */
		private $tradManager;

		public function setUp() {
			$this->tradManager = new \Serveur\I18n\TradManager();
		}

		public function testSetXmlObjet() {
			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true)
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$this->assertAttributeEquals(
				$xmlParser,
				'fichierTraductionDefaut',
				$this->tradManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\TradManagerException
		 * @expectedExceptionCode 40100
		 */
		public function testSetXmlObjetInvalide() {
			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', false)
			);

			$this->tradManager->setFichierTraduction($xmlParser);
		}

		public function testTransformeMessage() {
			$xmlElem1 = $this->createMock('XMLElement',
				array('getValeur', '', 'goA')
			);

			$xmlElem2 = $this->createMock('XMLElement',
				array('getValeur', '', 'MessParticulier')
			);

			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true),
				array('getConfigValeur', 'key.message[code=a]', array($xmlElem1)),
				array('getConfigValeur', 'section.message[code=3]', array($xmlElem2))
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$this->assertEquals("goA messagerie MessParticulier", $this->tradManager->recupererChaineTraduite("{key.a} messagerie {section.3}"));
		}

		public function testTransformeMessageRienModifie() {
			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true)
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$this->assertEquals("Message banal", $this->tradManager->recupererChaineTraduite("Message banal"));
		}

		public function testTransformeMessageNonTrouvee() {
			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true),
				array('getConfigValeur', 'fake.message[code=clef]', null)
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$this->assertEquals("Message avec {fake.clef}.", $this->tradManager->recupererChaineTraduite("Message avec {fake.clef}."));
		}

		public function testGetTraduction() {
			$xmlElem1 = $this->createMock('XMLElement',
				array('getValeur', '', 'goC')
			);

			$xmlElem2 = $this->createMock('XMLElement',
				array('getValeur', '', 'Mess2')
			);

			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true),
				array('getConfigValeur', 'maClef.message[code=code]', array($xmlElem1)),
				array('getConfigValeur', 'section.message[code=2]', array($xmlElem2))
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$class = new \ReflectionClass('Serveur\I18n\TradManager');
			$method = $class->getMethod('getTraduction');
			$method->setAccessible(true);

			$this->assertEquals('goC', $method->invokeArgs($this->tradManager, array('maClef', 'code')));
			$this->assertEquals('Mess2', $method->invokeArgs($this->tradManager, array('section', '2')));
		}

		public function testGetTraductionNonTrouvee() {
			$xmlParser = $this->createMock('XMLParser',
				array('isValide', '', true),
				array('getConfigValeur', 'existe.message[code=pas]', null)
			);

			$this->tradManager->setFichierTraduction($xmlParser);

			$class = new \ReflectionClass('Serveur\I18n\TradManager');
			$method = $class->getMethod('getTraduction');
			$method->setAccessible(true);

			$this->assertEquals('{existe.pas}', $method->invokeArgs($this->tradManager, array('existe', 'pas')));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\TradManagerException
		 * @expectedExceptionCode 40102
		 */
		public function testGetTraductionAucunXml() {
			$this->tradManager->recupererChaineTraduite("NO XML");
		}
	}