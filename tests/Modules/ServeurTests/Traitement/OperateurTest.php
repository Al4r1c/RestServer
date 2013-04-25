<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\DonneeRequete\Operateur;
use Tests\TestCase;

class OperateurTest extends TestCase
{
    /**
     * @var Operateur
     */
    private $_operateur;

    public function setUp()
    {
        $this->_operateur = new Operateur();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\Serveur\\Traitement\\DonneeRequete\\Operateur', $this->_operateur);
    }

    public function testType()
    {
        $this->_operateur->setType('like');

        $this->assertEquals('like', $this->_operateur->getType());
    }

    public function testTypeInitial()
    {
        $this->assertEquals('eq', $this->_operateur->getType());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30300
     */
    public function testTypeInvalide()
    {
        $this->_operateur->setType('fake');
    }
}
