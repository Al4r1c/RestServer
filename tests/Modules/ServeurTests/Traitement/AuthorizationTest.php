<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\Authorization\Authorization;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    /**
     * @var Authorization
     */
    private $_authorization;

    public function setUp()
    {
        $this->_authorization = new Authorization();
    }

    public function testUsername()
    {
        $this->_authorization->setEntityId('NomUtil');

        $this->assertEquals('NomUtil', $this->_authorization->getEntityId());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testUsernameString()
    {
        $this->_authorization->setEntityId(array());
    }

    public function testPrivateKey()
    {
        $this->_authorization->setClefPrivee('AKc4=');

        $this->assertEquals('AKc4=', $this->_authorization->getClefPrivee());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testPrivateKeyString()
    {
        $this->_authorization->setClefPrivee(5);
    }
}
