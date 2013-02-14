<?php
	namespace ClassLoader;

	class ClassLoader {
		private $namespaces = array();

		public function ajouterNamespace($namespace, $includePath, $extension = '.class.php') {
			$this->namespaces[strtolower($namespace)] = array('path' => $includePath, 'extension' => $extension);
		}

		public function register() {
			spl_autoload_register(array($this, 'loaderFunction'));
		}

		public function unregister($namespace = '') {
			if (!isNull($namespace)) {
				if(array_key_exists(strtolower($namespace), $this->namespaces)) {
					unset($this->namespaces[strtolower($namespace)]);
				} else {
					echo 'Namespace '.$namespace.' not found';
				}
			} else {
				spl_autoload_unregister(array($this, 'loaderFunction'));
			}
		}

		public function loaderFunction($className) {
			foreach($this->namespaces as $unNamespace => $configNamespace) {
				if(!isNull($unNamespace) && substr_count(strtolower($className), $unNamespace) > 0) {
					include_once($configNamespace['path'] . DIRECTORY_SEPARATOR . $className . $configNamespace['extension']);

					return true;
				}
			}

			return false;
		}
	}