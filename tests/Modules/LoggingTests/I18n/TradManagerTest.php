<?php
namespace Tests\LoggingTests\I18n;

use Tests\MockArg;
use Tests\TestCase;

class TradManagerTest extends TestCase
{

    /** @var \Logging\I18n\TradManager */
    private $tradManager;

    public function setUp()
    {
        $this->tradManager = new \Logging\I18n\TradManager();
    }

    public function testSetXmlObjet()
    {
        $xmlParser = $this->createMock('XMLParser', new MockArg('isValide', true));

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertAttributeEquals($xmlParser, '_fichierTraductionDefaut', $this->tradManager);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Traduction object is invalid.
     */
    public function testSetXmlObjetInvalide()
    {
        $xmlParser = $this->createMock('XMLParser', new MockArg('isValide', false));

        $this->tradManager->setFichierTraduction($xmlParser);
    }

    public function testTransformeMessage()
    {
        $xmlElem1 = $this->createMock('XMLElement', new MockArg('getValeur', 'goA'));

        $xmlElem2 = $this->createMock('XMLElement', new MockArg('getValeur', 'MessParticulier'));

        $xmlParser = $this->createMock(
            'XMLParser', new MockArg('isValide', true),
            new MockArg('getConfigValeur', array($xmlElem1), array('key.message[code=a]')),
            new MockArg('getConfigValeur', array($xmlElem2), array('section.message[code=3]')));

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals(
            "goA messagerie MessParticulier",
            $this->tradManager->recupererChaineTraduite("{key.a} messagerie {section.3}"));
    }

    public function testTransformeMessageRienModifie()
    {
        $xmlParser = $this->createMock('XMLParser', new MockArg('isValide', true));

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals("Message banal", $this->tradManager->recupererChaineTraduite("Message banal"));
    }

    public function testTransformeMessageNonTrouvee()
    {
        $xmlParser = $this->createMock(
            'XMLParser', new MockArg('isValide', true),
            new MockArg('getConfigValeur', null, array('fake.message[code=clef]')));

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals(
            "Message avec {fake.clef}.", $this->tradManager->recupererChaineTraduite("Message avec {fake.clef}."));
    }

    public function testGetTraduction()
    {
        $xmlElem1 = $this->createMock('XMLElement', new MockArg('getValeur', 'goC'));

        $xmlElem2 = $this->createMock('XMLElement', new MockArg('getValeur', 'Mess2'));

        $xmlParser = $this->createMock(
            'XMLParser', new MockArg('isValide', true),
            new MockArg('getConfigValeur', array($xmlElem1), array('maClef.message[code=code]')),
            new MockArg('getConfigValeur', array($xmlElem2), array('section.message[code=2]')));

        $this->tradManager->setFichierTraduction($xmlParser);

        $class = new \ReflectionClass('Logging\I18n\TradManager');
        $method = $class->getMethod('getTraduction');
        $method->setAccessible(true);

        $this->assertEquals('goC', $method->invokeArgs($this->tradManager, array('maClef', 'code')));
        $this->assertEquals('Mess2', $method->invokeArgs($this->tradManager, array('section', '2')));
    }

    public function testGetTraductionNonTrouvee()
    {
        $xmlParser = $this->createMock(
            'XMLParser', new MockArg('isValide', true),
            new MockArg('getConfigValeur', null, array('existe.message[code=pas]')));

        $this->tradManager->setFichierTraduction($xmlParser);

        $class = new \ReflectionClass('Logging\I18n\TradManager');
        $method = $class->getMethod('getTraduction');
        $method->setAccessible(true);

        $this->assertEquals('{existe.pas}', $method->invokeArgs($this->tradManager, array('existe', 'pas')));
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage No traduction object set.
     */
    public function testGetTraductionAucunXml()
    {
        $this->tradManager->recupererChaineTraduite("NO XML");
    }
}