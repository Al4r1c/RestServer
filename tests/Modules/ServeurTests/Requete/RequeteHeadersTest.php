<?php
namespace Tests\ServeurTests\Requete;

use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Requete\Headers\RequeteHeaders;
use Tests\TestCase;

class RequeteHeadersTest extends TestCase
{
    /**
     * @var RequeteHeaders
     */
    private $_headers;

    public function setUp()
    {
        $this->_headers = new RequeteHeaders();
    }

    public function testListeHeaders()
    {
        $this->_headers->setHeaders(array('Connection' => 'keep-alive'));

        $this->assertArrayHasKey('Connection', $this->_headers->getHeaders());
        $this->assertContains('keep-alive', $this->_headers->getHeaders());
        $this->assertCount(1, $this->_headers->getHeaders());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20200
     */
    public function testListeHeadersValide()
    {
        $this->_headers->setHeaders(array('do not exist' => ''));
    }
}