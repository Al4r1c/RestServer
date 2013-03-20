<?php
/**
 * @param int $erreurNumber
 * @param int $codeErreur
 * @param string $argumentsMessage
 */
function trigger_error_app($erreurNumber, $codeErreur, $argumentsMessage)
{
    call_user_func_array($GLOBALS['global_function_ajouterErreur'], func_get_args());
}

/**
 * @param mixed $donnee
 * @return bool
 */
function isNull(&$donnee)
{
    return (!isset($donnee) || is_null($donnee) || ((is_string($donnee)) && trim($donnee) == '') ||
        ((is_array($donnee) || is_object($donnee)) && empty($donnee)));
}

/**
 * @param string $string
 * @param string $stringRecherche
 * @return bool
 */
function startsWith($string, $stringRecherche)
{
    return !strncmp($string, $stringRecherche, strlen($stringRecherche));
}

/**
 * @param array $keys
 * @param array $array
 * @return bool
 */
function array_keys_exist(array $keys, array $array)
{
    if (count(array_intersect($keys, array_keys($array))) == count($keys)) {
        return true;
    }

    return false;
}

/**
 * @param string $needle
 * @param array $haystack
 * @return bool|int|string
 */
function array_search_recursif($needle, array $haystack)
{
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if ($needle === $value OR (is_array($value) && array_search_recursif($needle, $value) !== false)) {
            return $current_key;
        }
    }

    return false;
}

/**
 * @param array $tabKey
 * @param array $arrayValues
 * @return bool
 */
function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues)
{
    if (count($tabKey) == 1) {
        if (array_key_exists($tabKey[0], $arrayValues)) {
            return $arrayValues[$tabKey[0]];
        } else {
            return false;
        }
    } else {
        if (array_key_exists($tabKey[0], $arrayValues)) {
            $arrayValues = $arrayValues[$tabKey[0]];
            array_shift($tabKey);

            return rechercheValeurTableauMultidim($tabKey, $arrayValues);
        } else {
            return false;
        }
    }
}