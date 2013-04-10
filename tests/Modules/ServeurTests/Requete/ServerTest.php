<?php
namespace Tests\ServeurTests\Requete;

use Serveur\Requete\Server\Server;
use Tests\TestCase;

class ServerTest extends TestCase
{
    /** @var Server */
    private $_server;

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
        'PHP_INPUT' => '',
        'REDIRECT_HTTP_AUTHORIZATION' => '');

    public function setUp()
    {
        $this->_server = new Server();
    }

    public function testSetServeurVariable()
    {
        $this->_server->setServeurVariables(self::$donneesServer);

        $this->assertEquals(self::$donneesServer, $this->_server->getServeurVariables());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20100
     */
    public function testWhiteList()
    {
        $donnees = self::$donneesServer;
        unset($donnees['REQUEST_METHOD']);
        $this->_server->setServeurVariables($donnees);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetServeurDonneesErronee()
    {
        $this->_server->setVarServeur(null);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetServeurVariableErronee()
    {
        $this->_server->setServeurVariables(null);
    }

    public function testGetUneVariableServeur() {
        $this->_server->setVarServeur(self::$donneesServer);

        $this->assertEquals('server.com', $this->_server->getUneVariableServeur('HTTP_HOST'));
    }

    public function testGetUneVariableServeurNonTrouveRenvoiNull() {
        $this->_server->setVarServeur(self::$donneesServer);

        $this->assertNull($this->_server->getUneVariableServeur('NO_NO_NO'));
    }
}