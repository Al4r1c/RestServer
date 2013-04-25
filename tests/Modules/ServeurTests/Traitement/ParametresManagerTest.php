<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\DonneeRequete\ParametresManager;
use Tests\TestCase;

class DonneeRequeteManagerTest extends TestCase
{
    /**
     * @var ParametresManager
     */
    private $_donneReqManager;

    public function setUp()
    {
        $this->_donneReqManager = new ParametresManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\Serveur\\Traitement\\DonneeRequete\\ParametresManager', $this->_donneReqManager);
    }

    public function testAjoutDonne()
    {
        $this->_donneReqManager->addDonneesRequete(
            $this->getMock('\\Serveur\\Traitement\\DonneeRequete\\ChampRequete')
        );

        $this->assertCount(1, $this->_donneReqManager->getChampsRequete());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testAjoutDonneType()
    {
        $this->_donneReqManager->addDonneesRequete(array());
    }

    public function testAjoutTri()
    {
        $this->_donneReqManager->addTri(
            $this->getMock('\\Serveur\\Traitement\\DonneeRequete\\ChampRequete')
        );

        $this->assertCount(1, $this->_donneReqManager->getTris());
    }

    public function testGetUnTri()
    {
        $tri = $this->getMock('\\Serveur\\Traitement\\DonneeRequete\\ChampRequete', array('getTypeTri'));

        $tri->expects($this->once())->method('getTypeTri')->will($this->returnValue('pageSize'));

        $this->_donneReqManager->addTri($tri);

        $this->assertSame($tri, $this->_donneReqManager->getUnTri('pageSize'));
    }

    public function testGetUnTriNotFound()
    {
        $this->assertNull($this->_donneReqManager->getUnTri('notFound'));
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testAjoutTriType()
    {
        $this->_donneReqManager->addTri(array());
    }

    public function testParseSansMotsClefs()
    {
        $this->_donneReqManager->parseTabParametres(array('param1' => 'value1'));

        $this->assertCount(1, $this->_donneReqManager->getChampsRequete());
        $this->assertEmpty($this->_donneReqManager->getTris());

        $this->assertEquals('param1', $this->_donneReqManager->getChampsRequete()[0]->getChamp());
    }

    public function testParseSansMotsClefsAvecMultiValeurs()
    {
        $this->_donneReqManager->parseTabParametres(array('param1' => 'value1|value2'));

        $this->assertCount(1, $this->_donneReqManager->getChampsRequete());
        $this->assertEmpty($this->_donneReqManager->getTris());

        $this->assertEquals(array('value1', 'value2'), $this->_donneReqManager->getChampsRequete()[0]->getValeurs());
    }

    public function testParseSansMotsClefsAvecConditions()
    {
        $this->_donneReqManager->parseTabParametres(array('param1!gt' => 'value1'));

        $this->assertCount(1, $this->_donneReqManager->getChampsRequete());
        $this->assertEmpty($this->_donneReqManager->getTris());

        $this->assertEquals('gt', $this->_donneReqManager->getChampsRequete()[0]->getOperateur()->getType());
    }

    public function testParseAvecMotsClefs()
    {
        $this->_donneReqManager->parseTabParametres(array('pageNum' => 3));

        $this->assertEmpty($this->_donneReqManager->getChampsRequete());
        $this->assertCount(1, $this->_donneReqManager->getTris());

        $this->assertEquals('pageNum', $this->_donneReqManager->getTris()[0]->getTypeTri());
        $this->assertEquals(3, $this->_donneReqManager->getTris()[0]->getValeur());
    }
}
