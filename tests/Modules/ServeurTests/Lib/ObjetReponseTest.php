<?php
namespace Tests\ServeurTests\Lib;

use Serveur\Lib\ObjetReponse;
use Tests\TestCase;

class ObjetReponseTest extends TestCase
{
    /** @var ObjetReponse */
    private $_objetReponse;

    public function setUp()
    {
        $this->_objetReponse = new ObjetReponse();
    }

    public function testSetCodeHttp()
    {
        $this->_objetReponse->setStatusHttp(500);

        $this->assertEquals(500, $this->_objetReponse->getStatusHttp());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testCodeHttpNonInt()
    {
        $this->_objetReponse->setStatusHttp('500');
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 10300
     */
    public function testCodeHttpInvalide()
    {
        $this->_objetReponse->setStatusHttp(999);
    }

    public function testSetContenu()
    {
        $this->_objetReponse->setDonneesReponse(array('param' => 'variable', 'param2' => 'var2'));
        $this->assertCount(2, $this->_objetReponse->getDonneesReponse());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testContenuInvalide()
    {
        $this->_objetReponse->setDonneesReponse('INVALID');
    }

    public function testSetErreurHttp()
    {
        $this->_objetReponse->setErreurHttp(500);

        $this->assertEquals(500, $this->_objetReponse->getStatusHttp());
        $this->assertArrayHasKey('Code', $this->_objetReponse->getDonneesReponse());
        $this->assertArrayHasKey('Status', $this->_objetReponse->getDonneesReponse());
        $this->assertArrayHasKey('Message', $this->_objetReponse->getDonneesReponse());
    }

    public function testFormat()
    {
        $this->_objetReponse->setFormat('application/json');

        $this->assertEquals('application/json', $this->_objetReponse->getFormat());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 10301
     */
    public function testFormatErrone()
    {
        $this->_objetReponse->setFormat('application/fake');
    }
}