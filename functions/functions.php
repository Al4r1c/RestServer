<?php
	function  trigger_error_app($erreurNumber, $codeErreur, $arguments = array()) {
		call_user_func_array($GLOBALS['global_function_appli_error'], func_get_args());
	}

	function isNull(&$donnee) {
		return (!isset($donnee) || is_null($donnee) || ((is_string($donnee)) && trim($donnee) == '') || ((is_array($donnee) || is_object($donnee)) && empty($donnee)));
	}

	function startsWith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}

	function array_keys_exist(array $keys, array $array) {
		if (count(array_intersect($keys, array_keys($array))) == count($keys)) {
			return true;
		}

		return false;
	}

	function array_search_recursif($needle, array $haystack) {
		foreach($haystack as $key => $value) {
			$current_key = $key;
			if($needle === $value OR (is_array($value) && array_search_recursif($needle, $value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}

	function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues) {
		if (count($tabKey) == 1) {
			if(array_key_exists($tabKey[0], $arrayValues)) {
				return $arrayValues[$tabKey[0]];
			} else {
				return false;
			}
		} else {
			if(array_key_exists($tabKey[0], $arrayValues)) {
				$arrayValues = $arrayValues[$tabKey[0]];
				array_shift($tabKey);
				return rechercheValeurTableauMultidim($tabKey, $arrayValues);
			} else {
				return false;
			}
		}
	}