<?php
namespace Tests\ServeurTests\Traitement;

use AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager;
use Tests\TestCase;

class ParametresManagerTest extends TestCase
{
    /**
     * @var ParametresManager
     */
    private $_parametresManager;

    public function setUp()
    {
        $this->_parametresManager = new ParametresManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
             '\\AlaroxRestServeur\Serveur\\Traitement\\DonneeRequete\\ParametresManager',
                 $this->_parametresManager
        );
    }

    public function testAjoutDonne()
    {
        $this->_parametresManager->addChampRequete(
                                 $this->getMock('\\AlaroxRestServeur\Serveur\\Traitement\\DonneeRequete\\ChampRequete')
        );

        $this->assertCount(1, $this->_parametresManager->getChampsRequete());
    }

    public function testGetUnChamp()
    {
        $champRequete =
            $this->getMock('\\AlaroxRestServeur\Serveur\\Traitement\\DonneeRequete\\ChampRequete', array('getChamp'));

        $champRequete->expects($this->once())->method('getChamp')->will($this->returnValue('champs'));

        $this->_parametresManager->addChampRequete($champRequete);

        $this->assertSame($champRequete, $this->_parametresManager->getUnChampsRequete('champs'));
    }

    public function testGetUnChampNotFound()
    {
        $this->assertNull($this->_parametresManager->getUnChampsRequete('bug'));
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testAjoutDonneType()
    {
        $this->_parametresManager->addChampRequete(array());
    }

    public function testAjoutTri()
    {
        $this->_parametresManager->addTri(
                                 $this->getMock('\\AlaroxRestServeur\Serveur\\Traitement\\DonneeRequete\\Tri')
        );

        $this->assertCount(1, $this->_parametresManager->getTris());
    }

    public function testGetUnTri()
    {
        $tri = $this->getMock('\\AlaroxRestServeur\Serveur\\Traitement\\DonneeRequete\\Tri', array('getTypeTri'));

        $tri->expects($this->once())->method('getTypeTri')->will($this->returnValue('pageSize'));

        $this->_parametresManager->addTri($tri);

        $this->assertSame($tri, $this->_parametresManager->getUnTri('pageSize'));
    }

    public function testGetUnTriNotFound()
    {
        $this->assertNull($this->_parametresManager->getUnTri('notFound'));
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testAjoutTriType()
    {
        $this->_parametresManager->addTri(array());
    }

    public function testParseSansMotsClefs()
    {
        $this->_parametresManager->parseTabParametres(array('param1' => 'value1'));

        $this->assertCount(1, $this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());

        $this->assertEquals('param1', $this->_parametresManager->getChampsRequete()[0]->getChamp());
    }

    public function testParseSansMotsClefsAvecMultiValeurs()
    {
        $this->_parametresManager->parseTabParametres(array('param1' => 'value1|value2'));

        $this->assertCount(1, $this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());

        $this->assertEquals(array('value1', 'value2'), $this->_parametresManager->getChampsRequete()[0]->getValeurs());
    }

    public function testParseSansMotsClefsAvecConditions()
    {
        $this->_parametresManager->parseTabParametres(array('param1!gt' => 'value1'));

        $this->assertCount(1, $this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());

        $this->assertEquals('gt', $this->_parametresManager->getChampsRequete()[0]->getOperateur()->getType());
    }

    public function testParseAvecMotsClefs()
    {
        $this->_parametresManager->parseTabParametres(array('pageNum' => 3));

        $this->assertEmpty($this->_parametresManager->getChampsRequete());
        $this->assertCount(1, $this->_parametresManager->getTris());

        $this->assertEquals('pageNum', $this->_parametresManager->getTris()[0]->getTypeTri());
        $this->assertEquals(3, $this->_parametresManager->getTris()[0]->getValeur());
    }

    public function testLazyLoad()
    {
        $this->assertFalse($this->_parametresManager->getLazyLoad());
    }

    public function testParseLazyLoad()
    {
        $this->_parametresManager->parseTabParametres(array('lazyLoad' => true));

        $this->assertEmpty($this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());
        $this->assertTrue($this->_parametresManager->getLazyLoad());
    }

    public function testParseLazyLoadInt()
    {
        $this->_parametresManager->parseTabParametres(array('lazyLoad' => 1));

        $this->assertEmpty($this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());
        $this->assertTrue($this->_parametresManager->getLazyLoad());
    }

    public function testParseLazyLoadValue()
    {
        $this->_parametresManager->parseTabParametres(array('lazyLoad' => 'hello'));

        $this->assertEmpty($this->_parametresManager->getChampsRequete());
        $this->assertEmpty($this->_parametresManager->getTris());
        $this->assertEquals('hello', $this->_parametresManager->getLazyLoad());
    }
}
