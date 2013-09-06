<?php
namespace Tests\ServeurTests\Traitement;

use AlaroxRestServeur\Serveur\Lib\ObjetReponse;
use AlaroxRestServeur\Serveur\Traitement\Ressource\AbstractRessource;
use Tests\MockArg;
use Tests\TestCase;

class AbstractRessourceTest extends TestCase
{
    public function testSetDatabase()
    {
        $abstractDatabase = $this->createMock('AbstractDatabase');
        $abstractRessource = $this->createMock('AbstractRessource');

        $abstractRessource->setConnectionDatabase($abstractDatabase);
        $this->assertEquals($abstractDatabase, $abstractRessource->getConnectionDatabase($abstractDatabase));
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetDatabaseEronnne()
    {
        $abstractRessource = $this->createMock('AbstractRessource');

        $abstractRessource->setConnectionDatabase(50);
    }

    public function testDoGetPourSingle()
    {
        $paramManager = $this->createMock('ParametresManager', new MockArg('isLazyLoad', false));

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('getOne', new ObjetReponse(200), array(1))
        );

        $this->assertEquals(200, $abstractRessource->doGet(array('resource', 1), $paramManager)->getStatusHttp());
    }

    public function testDoGetPourRecherche()
    {
        $paramManager = $this->getMock('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager');

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('getAll', new ObjetReponse(404), array($paramManager))
        );

        $this->assertEquals(404, $abstractRessource->doGet(array('resource', null), $paramManager)->getStatusHttp());
    }

    public function testDoPutCreateOrUpdate()
    {
        $paramManager = $this->getMock('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager');

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('createOrUpdateIdempotent', new ObjetReponse(201), array(4, $paramManager))
        );

        $this->assertEquals(201, $abstractRessource->doPut(array('resource', 4), $paramManager)->getStatusHttp());
    }

    public function testDoPutCollection()
    {
        $paramManager = $this->getMock('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager');

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('putCollection', new ObjetReponse(200), array(4, 'newCollection', $paramManager))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doPut(array('resource', 4, 'newCollection'), $paramManager)->getStatusHttp()
        );
    }

    public function testDoPutInCollection()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('putOneInCollection', new ObjetReponse(200), array(4, 'newCollection', 30))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doPut(array('resource', 4, 'newCollection', 30), null)->getStatusHttp()
        );
    }

    public function testDoPostUpdate()
    {
        $paramManager = $this->getMock('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager');

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('updateOne', new ObjetReponse(200), array(5, $paramManager))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doPost(array('resource', 5), $paramManager)->getStatusHttp()
        );
    }

    public function testDoPostCreate()
    {
        $paramManager = $this->getMock('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager');

        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('createOne', new ObjetReponse(201), array($paramManager))
        );

        $this->assertEquals(
            201,
            $abstractRessource->doPost(array('resource', null), $paramManager)->getStatusHttp()
        );
    }

    public function testDoDelete()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('deleteOne', new ObjetReponse(200), array(200500))
        );

        $this->assertEquals(200, $abstractRessource->doDelete(array('resource', 200500))->getStatusHttp());
    }

    public function testDoDeleteAll()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('deleteAll', new ObjetReponse(200))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doDelete(array('resource', null))->getStatusHttp()
        );
    }

    public function testDoDeleteCollection()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('deleteCollection', new ObjetReponse(200), array(3, 'nomCollection'))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doDelete(array('resource', 3, 'nomCollection'))->getStatusHttp()
        );
    }

    public function testDoDeleteInCollection()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock(
            'AbstractRessource',
            new MockArg('deleteInCollection', new ObjetReponse(200), array(3, 'nomCollection', 80))
        );

        $this->assertEquals(
            200,
            $abstractRessource->doDelete(array('resource', 3, 'nomCollection', 80))->getStatusHttp()
        );
    }

    public function testForbidden()
    {
        /** @var $abstractRessource AbstractRessource */
        $abstractRessource = $this->createMock('AbstractRessource');

        $class = new \ReflectionClass('AlaroxRestServeur\Serveur\Traitement\Ressource\AbstractRessource');
        $method = $class->getMethod('forbidden');
        $method->setAccessible(true);

        $this->assertEquals(403, $method->invoke($abstractRessource)->getStatusHttp());
    }
}