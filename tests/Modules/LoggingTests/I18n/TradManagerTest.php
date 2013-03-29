<?php
namespace Tests\LoggingTests\I18n;

use Logging\I18n\TradManager;
use Tests\TestCase;

class TradManagerTest extends TestCase
{

    /** @var TradManager */
    private $tradManager;

    public function setUp()
    {
        $this->tradManager = new TradManager();
    }

    public function testSetXmlObjet()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent("<root></root>");

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertAttributeEquals($xmlParser, '_fichierTraductionDefaut', $this->tradManager);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Traduction object is invalid.
     */
    public function testSetXmlObjetInvalide()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent("<root></toor>");

        $this->tradManager->setFichierTraduction($xmlParser);
    }

    public function testTransformeMessage()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent(
            "<root><maClef><message code=\"AbC\">goA</message></maClef><section><message code=\"3\">MessParticulier</message></section></root>"
        );

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals(
            "goA messagerie MessParticulier",
            $this->tradManager->recupererChaineTraduite("{maClef.AbC} messagerie {section.3}")
        );
    }

    public function testTransformeMessageRienModifie()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent("<root></root>");

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals("Message banal", $this->tradManager->recupererChaineTraduite("Message banal"));
    }

    public function testTransformeMessageNonTrouvee()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent("<root></root>");

        $this->tradManager->setFichierTraduction($xmlParser);

        $this->assertEquals(
            "Message avec {fake.clef}.", $this->tradManager->recupererChaineTraduite("Message avec {fake.clef}.")
        );
    }

    public function testGetTraduction()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent(
            "<root><maClef><message code=\"code\">goC</message></maClef><section><message code=\"2\">Mess2</message></section></root>"
        );

        $this->tradManager->setFichierTraduction($xmlParser);

        $class = new \ReflectionClass('Logging\I18n\TradManager');
        $method = $class->getMethod('getTraduction');
        $method->setAccessible(true);

        $this->assertEquals('goC', $method->invokeArgs($this->tradManager, array('maClef', 'code')));
        $this->assertEquals('Mess2', $method->invokeArgs($this->tradManager, array('section', '2')));
    }

    public function testGetTraductionNonTrouvee()
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent("<root><existe><message code=\"hello\">World</message></existe></root>");

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