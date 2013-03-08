<?php
    namespace Modules\ServeurTests\Rest;

    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\Rest\RestRequete;

    class RestRequeteTest extends TestCase
    {

        /**
         * @var RestRequete $restRequete
         */
        private $restRequete;

        protected function setUp()
        {
            $this->restRequete = new RestRequete();
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\MainException
         * @expectedExceptionCode 20000
         */
        public function testRestMethodeValide()
        {
            $this->restRequete->setMethode('METHODE_ERREUR');
        }

        public function testRestMethodeDefaultGet()
        {
            $this->assertEquals('GET', $this->restRequete->getMethode());
        }

        public function testRestMethodeAcceptePost()
        {
            $this->restRequete->setMethode('post');

            $this->assertEquals('POST', $this->restRequete->getMethode());
        }

        public function testRestAcceptFormatJSON()
        {
            $this->restRequete->setFormat('application/json');

            $this->assertContains('json', $this->restRequete->getFormatsDemandes());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestAcceptFormatInvalide()
        {
            $this->restRequete->setFormat(5);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\MainException
         * @expectedExceptionCode 20001
         */
        public function testRestFormatValide()
        {
            $this->restRequete->setFormat('HTTP_ACCEPT_INVALIDE');
        }

        public function testRestUri()
        {
            $this->restRequete->setVariableUri('/mon/uri/');

            $this->assertInternalType('array', $this->restRequete->getUriVariables());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestUriErronee()
        {
            $this->restRequete->setVariableUri(20.2);
        }

        public function testRestUriVideNonGeree()
        {
            $this->restRequete->setVariableUri('/mon/uri/');
            $this->restRequete->setVariableUri('');

            $this->assertCount(2, $this->restRequete->getUriVariables());
        }

        public function testRestUriRecupererVariable()
        {
            $this->restRequete->setVariableUri('/variable1//var2////var3/');

            $this->assertEquals('variable1', $this->restRequete->getUriVariables()[0]);
            $this->assertEquals('var3', $this->restRequete->getUriVariables()[2]);
        }

        public function testRestRessourceEncode()
        {
            $this->restRequete->setVariableUri('/rés%s"ou#rce<////');

            $this->assertEquals(rawurlencode('rés%s"ou#rce<'), $this->restRequete->getUriVariables()[0]);
        }

        public function testRestRessourceNoVariable()
        {
            $this->restRequete->setVariableUri('/var1?id=3');

            $this->assertEquals(rawurlencode('var1'), $this->restRequete->getUriVariables()[0]);
        }

        public function testRestDonnee()
        {
            $this->restRequete->setParametres(array());

            $this->assertInternalType('array', $this->restRequete->getParametres());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestDonneeSeulementTableau()
        {
            $this->restRequete->setParametres('GO_GO_ERREUR');
        }

        public function testParametreSauvegardes()
        {
            $this->restRequete->setParametres(array("param1" => "valeur1", "data" => 1));
            $this->assertCount(2, $this->restRequete->getParametres());
        }

        public function testRecupererParametre()
        {
            $this->restRequete->setParametres(array("param1" => "valeur1", "data" => 1));
            $this->assertEquals('valeur1', $this->restRequete->getParametres()['param1']);
        }

        public function testRestSetServer()
        {
            $serveur = $this->createMock(
                'Server',
                new MockArg('getServeurMethode', 'PUT'),
                new MockArg('getServeurHttpAccept', 'text/html,application/xhtml+xml,application/xml;q=0.9'),
                new MockArg('getServeurUri', '/mon/uri/'),
                new MockArg('getServeurDonnees', array('param1' => 'var1', 'param2' => 'var2')),
                new MockArg('getRemoteIp', '127.0.0.1'),
                new MockArg('getRequestTime', 1362000000)
            );

            $this->restRequete->setServer($serveur);
            $this->assertEquals('PUT', $this->restRequete->getMethode());
            $this->assertContains('xml', $this->restRequete->getFormatsDemandes());
            $this->assertEquals('uri', $this->restRequete->getUriVariables()[1]);
            $this->assertEquals('var2', $this->restRequete->getParametres()['param2']);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestSetServerEronnee()
        {
            $this->restRequete->setServer(null);
        }

        public function testRestDateRequete()
        {
            $this->restRequete->setDateRequete(1362000000);

            $this->assertInstanceOf('DateTime', $this->restRequete->getDateRequete());
            $this->assertEquals($this->restRequete->getDateRequete()->getTimestamp(), 1362000000);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestDateRequeteErrone()
        {
            $this->restRequete->setDateRequete('oops');
        }

        public function testRestIp()
        {
            $this->restRequete->setIp('192.168.0.250');

            $this->assertEquals('192.168.0.250', $this->restRequete->getIp());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRestIpErrone()
        {
            $this->restRequete->setIp(500);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\MainException
         * @expectedExceptionCode 20002
         */
        public function testRestIpFake()
        {
            $this->restRequete->setIp('WRONG_IP');
        }

        public function testRestIpV6()
        {
            $this->restRequete->setIp('8000::123:4567:89AB:CDEF');

            $this->assertEquals('8000::123:4567:89AB:CDEF', $this->restRequete->getIp());
        }
    }