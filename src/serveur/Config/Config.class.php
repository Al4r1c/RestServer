<?php
    namespace Serveur\Config;

    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class Config
    {

        /**
         * @var array
         */
        private $_applicationConfiguration = array();

        /**
         * @var array
         */
        private static $_clefMinimales = array('config',
            'config.default_render',
            'render');

        /**
         * @param \Serveur\Lib\Fichier $fichierFramework
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function chargerConfiguration($fichierFramework)
        {
            if (!$fichierFramework instanceof \Serveur\Lib\Fichier) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\Fichier', $fichierFramework);
            }

            try {
                $this->_applicationConfiguration =
                    array_change_key_case($fichierFramework->chargerFichier(), CASE_UPPER);
            }
            catch (\Exception $fe) {
                throw new MainException(30000, 500, $fichierFramework->getCheminCompletFichier());
            }

            $this->validerFichierConfiguration();
        }

        /**
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        private function validerFichierConfiguration()
        {
            foreach (self::$_clefMinimales as $uneClefQuiDoitExister) {
                if (is_null($this->getConfigValeur($uneClefQuiDoitExister))) {
                    throw new MainException(30001, 500, $uneClefQuiDoitExister);
                }
            }
        }

        /**
         * @param string $clefConfig
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return array|bool|null
         */
        public function getConfigValeur($clefConfig)
        {
            if (!is_string($clefConfig)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $clefConfig);
            }

            if ($valeur =
                rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->_applicationConfiguration)
            ) {
                return $valeur;
            } else {
                trigger_error_app(E_USER_NOTICE, 30002, $clefConfig);

                return null;
            }
        }
    }