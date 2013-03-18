<?php
    namespace Serveur\Traitement\Data;

    abstract class AbstractDatabase implements IDatabaseActions
    {
        /**
         * @var resource|object
         */
        private $_connection;

        /**
         * @return resource|object
         */
        public function getConnection()
        {
            return $this->_connection;
        }

        /**
         * @param resource|object $connection
         */
        public function setConnection($connection)
        {
            $this->_connection = $connection;
        }

        public function __destruct()
        {
            $this->fermerConnection($this->_connection);
        }
    }