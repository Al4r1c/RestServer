<?php
    namespace Tests\ServeurTests\Traitement;

    use Tests\TestCase;
    use Tests\MockArg;
    use Serveur\Traitement\Ressource\AbstractRessource;

    class AbstractRessourceTest extends TestCase
    {
        public function testDoGetPourSingle()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('getSingle', new \Serveur\Lib\ObjetReponse(200, array('my' => 'object')), array(1))
            );

            $this->assertEquals(200, $abstractRessource->doGet(1, null)->getStatusHttp());
        }

        public function testDoGetPourRecherche()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('search', new \Serveur\Lib\ObjetReponse(404, array('my' => 'object')), array(array('var1 => filter1')))
            );

            $this->assertEquals(404, $abstractRessource->doGet(null, array('var1 => filter1'))->getStatusHttp());
        }

        public function testDoGetPourColection()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('getAll', new \Serveur\Lib\ObjetReponse(200, array('my' => 'object')))
            );

            $this->assertEquals(200, $abstractRessource->doGet(null, null)->getStatusHttp());
        }

        public function testDoPut()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('update', new \Serveur\Lib\ObjetReponse(201), array(4, array('nom' => 'nouveauNom')))
            );

            $this->assertEquals(201, $abstractRessource->doPut(4, array('nom' => 'nouveauNom'))->getStatusHttp());
        }

        public function testDoPutMissingArgument()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $this->assertEquals(400, $abstractRessource->doPut(null, array('nom' => 'nouveauNom'))->getStatusHttp());
        }

        public function testDoPost()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('create', new \Serveur\Lib\ObjetReponse(201, array(1)), array(array('adresse' => 'adresseddd')))
            );

            $this->assertEquals(201, $abstractRessource->doPost(array('adresse' => 'adresseddd'))->getStatusHttp());
        }

        public function testDoDelete()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('delete', new \Serveur\Lib\ObjetReponse(200), array(200500))
            );

            $this->assertEquals(200, $abstractRessource->doDelete(200500)->getStatusHttp());
        }

        public function testDoDeleteMissingArgument()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $this->assertEquals(400, $abstractRessource->doDelete(null)->getStatusHttp());
        }

        public function testForbidden()
        {
            /**
             * @var AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $class = new \ReflectionClass('Serveur\Traitement\Ressource\AbstractRessource');
            $method = $class->getMethod('forbidden');
            $method->setAccessible(true);

            $this->assertEquals(403, $method->invoke($abstractRessource)->getStatusHttp());
        }
    }