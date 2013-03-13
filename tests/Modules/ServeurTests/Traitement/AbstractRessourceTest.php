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
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('recuperer', new \Serveur\Lib\ObjetReponse(200, array('my' => 'object')), array(1, null))
            );

            $this->assertEquals(200, $abstractRessource->doGet(1, null, null)->getStatusHttp());
        }

        public function testDoGetChamps()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('recuperer', new \Serveur\Lib\ObjetReponse(404, array('my' => 'object')), array(1,
                    array('champs1', 'champs2')))
            );

            $this->assertEquals(404, $abstractRessource->doGet(1, null, 'champs1,champs2')->getStatusHttp());
        }

        public function testDoGetPourRecherche()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('rechercher', new \Serveur\Lib\ObjetReponse(404, array('my' => 'object')), array(array('var1 => filter1'),
                    null))
            );

            $this->assertEquals(404, $abstractRessource->doGet(null, array('var1 => filter1'), null)->getStatusHttp());
        }

        public function testDoGetPourColection()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('recupererCollection', new \Serveur\Lib\ObjetReponse(200, array('my' => 'object'), null))
            );

            $this->assertEquals(200, $abstractRessource->doGet(null, null, null)->getStatusHttp());
        }

        public function testDoPut()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('mettreAJour', new \Serveur\Lib\ObjetReponse(201), array(4, array('nom' => 'nouveauNom')))
            );

            $this->assertEquals(201, $abstractRessource->doPut(4, array('nom' => 'nouveauNom'))->getStatusHttp());
        }

        public function testDoPutMissingArgument()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $this->assertEquals(400, $abstractRessource->doPut(null, array('nom' => 'nouveauNom'))->getStatusHttp());
        }

        public function testDoPost()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('creer', new \Serveur\Lib\ObjetReponse(201, array(1)), array(array('adresse' => 'adresseddd')))
            );

            $this->assertEquals(201, $abstractRessource->doPost(array('adresse' => 'adresseddd'))->getStatusHttp());
        }

        public function testDoDelete()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('supprimer', new \Serveur\Lib\ObjetReponse(200), array(200500))
            );

            $this->assertEquals(200, $abstractRessource->doDelete(200500)->getStatusHttp());
        }

        public function testDoDeleteMissingArgument()
        {
            /**
             * @var $abstractRessource AbstractRessource
             */
            $abstractRessource = $this->createMock('AbstractRessource');

            $this->assertEquals(400, $abstractRessource->doDelete(null)->getStatusHttp());
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