<?php
    namespace Tests\ServeurTests\Requete;

    use Tests\TestCase;
    use Serveur\Requete\Server\Server;

    class ServerTest extends TestCase
    {
        /** @var Server */
        private $server;
        private static $donneesServer = array('REDIRECT_HTTP_CONTENT_TYPE' => '',
            'REDIRECT_MIBDIRS' => 'C:/xampp/php/extras/mibs',
            'REDIRECT_MYSQL_HOME' => '\xampp\mysql\bin',
            'REDIRECT_OPENSSL_CONF' => 'C:/xampp/apache/bin/openssl.cnf',
            'REDIRECT_PHP_PEAR_SYSCONF_DIR' => '\xampp\php',
            'REDIRECT_PHPRC' => '\xampp\php',
            'REDIRECT_TMP' => '\xampp\tmp',
            'REDIRECT_STATUS' => '200',
            'MIBDIRS' => 'C:/xampp/php/extras/mibs',
            'MYSQL_HOME' => '\xampp\mysql\bin',
            'OPENSSL_CONF' => 'C:/xampp/apache/bin/openssl.cnf',
            'PHP_PEAR_SYSCONF_DIR' => '\xampp\php',
            'PHPRC' => '\xampp\php',
            'TMP' => '\xampp\tmp',
            'HTTP_HOST' => 'server.com',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'SystemRoot' => 'C:\Windows',
            'COMSPEC' => 'C:\Windows\system32\cmd.exe',
            'PATHEXT' => '.COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC',
            'WINDIR' => 'C:\Windows',
            'SERVER_SIGNATURE' => '<address>Apache/2.4.3 (Win32) OpenSSL/1.0.1c PHP/5.4.7 Server at server.com Port 80</address>',
            'SERVER_SOFTWARE' => 'Apache/2.4.3 (Win32) OpenSSL/1.0.1c PHP/5.4.7',
            'SERVER_NAME' => 'server.com',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '127.0.0.1',
            'DOCUMENT_ROOT' => 'C:\www\server',
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_PREFIX' => '',
            'CONTEXT_DOCUMENT_ROOT' => 'C:\www\server',
            'SERVER_ADMIN' => 'postmaster@localhost',
            'SCRIPT_FILENAME' => 'C:/www/server/rest-api.php',
            'REMOTE_PORT' => '54001',
            'REDIRECT_QUERY_' => 'param1=var1&param2=var2',
            'REDIRECT_URL' => '/',
            'GATEWAY_ERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => 'param1=var1&param2=var2',
            'REQUEST_URI' => '/?param1=var1&param2=var2',
            'SCRIPT_NAME' => '/rest-api.php',
            'PHP_SELF' => '/rest-api.php',
            'REQUEST_TIME_FLOAT' => 1361285069.293,
            'REQUEST_TIME' => 1361285069,
            'PHP_INPUT' => '');

        public function setUp()
        {
            $this->server = new Server();
        }

        public function testSetServeurVariable()
        {
            $this->server->setServeurVariable(self::$donneesServer);

            $this->assertAttributeEquals(self::$donneesServer, '_serveurVariable', $this->server);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 20100
         */
        public function testWhiteList()
        {
            $donnees = self::$donneesServer;
            unset($donnees['REQUEST_METHOD']);
            $this->server->setServeurVariable($donnees);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetServeurDonneesErronee()
        {
            $this->server->setVarServeur(null);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetServeurVariableErronee()
        {
            $this->server->setServeurVariable(null);
        }

        public function testSetServeurDonnees()
        {
            $this->server->setServeurVariable(self::$donneesServer);
            $this->server->setServeurDonnees('GET');

            $this->assertAttributeCount(2, '_serveurDonnees', $this->server);
            $this->assertAttributeEquals(
                array('param1' => 'var1', 'param2' => 'var2'),
                '_serveurDonnees',
                $this->server
            );
        }

        public function testSetServeurDonneesPutPostDelete()
        {
            $donnees = self::$donneesServer;
            $donnees['PHP_INPUT'] = 'numberOne=ParamOne&numberTwo=ParamTwo';
            $this->server->setServeurVariable($donnees);
            $this->server->setServeurDonnees('PUT');

            $this->assertCount(2, $this->server->getServeurDonnees());
            $this->assertEquals(
                array('numberOne' => 'ParamOne', 'numberTwo' => 'ParamTwo'),
                $this->server->getServeurDonnees()
            );
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 20101
         */
        public function testSetServeurDonneesMethodeInvalide()
        {
            $this->server->setServeurVariable(self::$donneesServer);
            $this->server->setServeurDonnees('FAKE');
        }

        public function testSetVarServeur()
        {
            $this->server->setVarServeur(self::$donneesServer);

            $this->assertAttributeEquals(self::$donneesServer, '_serveurVariable', $this->server);

            $this->assertAttributeEquals(
                array('param1' => 'var1', 'param2' => 'var2'),
                '_serveurDonnees',
                $this->server
            );
        }

        public function testGetServeurMethode()
        {
            $this->server->setVarServeur(self::$donneesServer);

            $this->assertEquals('GET', $this->server->getServeurMethode());
        }

        public function testGetServeurUri()
        {
            $this->server->setVarServeur(self::$donneesServer);
            $this->assertEquals('/?param1=var1&param2=var2', $this->server->getServeurUri());
        }

        public function testGetServeurHttpAccept()
        {
            $this->server->setVarServeur(self::$donneesServer);
            $this->assertEquals(
                'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                $this->server->getServeurHttpAccept()
            );
        }

        public function testGetRemoteIp()
        {
            $this->server->setVarServeur(self::$donneesServer);

            $this->assertEquals('127.0.0.1', $this->server->getRemoteIp());
        }

        public function testGetRequestTime()
        {
            $this->server->setVarServeur(self::$donneesServer);

            $this->assertEquals(1361285069, $this->server->getRequestTime());
        }
    }