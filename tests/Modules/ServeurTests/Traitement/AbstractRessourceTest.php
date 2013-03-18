<?php
    namespace Tests\ServeurTests\Traitement;

    use Serveur\Lib\ObjetReponse;
    use Serveur\Traitement\Ressource\AbstractRessource;
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
         * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function testSetDatabaseEronnne()
        {
            $abstractRessource = $this->createMock('AbstractRessource');

            $abstractRessource->setConnectionDatabase(50);
        }

        public function testDoGetPourSingle()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('getOne', new ObjetReponse(200), array(1)));

            $this->assertEquals(200, $abstractRessource->doGet(1, null)->getStatusHttp());
        }

        public function testDoGetPourRecherche()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('getAll', new ObjetReponse(404), array(array('var1 => filter1'))));

            $this->assertEquals(404, $abstractRessource->doGet(null, array('var1 => filter1'))->getStatusHttp());
        }

        public function testDoPut()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('createOrUpdateIdempotent', new ObjetReponse(201), array(4, array('nom' => 'nouveauNom'))));

            $this->assertEquals(201, $abstractRessource->doPut(4, array('nom' => 'nouveauNom'))->getStatusHttp());
        }

        public function testDoPostUpdate()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('updateOne', new ObjetReponse(200), array(5, array('adresse' => 'adresseddd'))));

            $this->assertEquals(200, $abstractRessource->doPost(5, array('adresse' => 'adresseddd'))->getStatusHttp());
        }

        public function testDoPostCreate()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('createOne', new ObjetReponse(201), array(array('nom' => 'named'))));

            $this->assertEquals(201, $abstractRessource->doPost(null, array('nom' => 'named'))->getStatusHttp());
        }

        public function testDoDelete()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('deleteOne', new ObjetReponse(200), array(200500)));

            $this->assertEquals(200, $abstractRessource->doDelete(200500, array())->getStatusHttp());
        }

        public function testDoDeleteAll()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource',
                new MockArg('deleteAll', new ObjetReponse(200), array(array('champs1' => 'valeur1'))));

            $this->assertEquals(200,
                $abstractRessource->doDelete(null, array('champs1' => 'valeur1'))->getStatusHttp());
        }

        public function testForbidden()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $class = new \ReflectionClass('Serveur\Traitement\Ressource\AbstractRessource');
            $method = $class->getMethod('forbidden');
            $method->setAccessible(true);

            $this->assertEquals(403, $method->invoke($abstractRessource)->getStatusHttp());
        }
    }