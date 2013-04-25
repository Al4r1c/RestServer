<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\DonneeRequete\ChampRequete;
use Tests\TestCase;

class ChampRequeteTest extends TestCase
{
    /**
     * @var ChampRequete
     */
    private $_filtreDonnee;

    public function setUp()
    {
        $this->_filtreDonnee = new ChampRequete();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\Serveur\\Traitement\\DonneeRequete\\ChampRequete', $this->_filtreDonnee);
    }

    public function testChamp()
    {
        $this->_filtreDonnee->setChamp('monChamp');

        $this->assertEquals('monChamp', $this->_filtreDonnee->getChamp());
    }

    public function testValeur()
    {
        $this->_filtreDonnee->setValeurs('value');

        $this->assertEquals('value', $this->_filtreDonnee->getValeurs());
    }

    public function testOperateur() {
        $this->_filtreDonnee->setOperateur($operateur = $this->getMock('\\Serveur\\Traitement\\DonneeRequete\\Operateur'));

        $this->assertSame($operateur, $this->_filtreDonnee->getOperateur());
    }

    public function testOperateurInitial() {
        $this->assertInstanceOf('\\Serveur\\Traitement\\DonneeRequete\\Operateur', $this->_filtreDonnee->getOperateur());
    }
}
