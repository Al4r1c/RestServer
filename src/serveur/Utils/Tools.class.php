<?php
namespace Serveur\Utils;

class Tools
{
    /**
     * @param int $codeHttp
     * @return bool
     */
    public static function isValideHttpCode($codeHttp)
    {
        return array_key_exists($codeHttp, Constante::chargerConfig('httpcode'));
    }

    /**
     * @param string $header
     * @return bool
     */
    public static function isValideRequestHeader($header)
    {
        return in_array(strtolower($header), array_map('strtolower', Constante::chargerConfig('requestheaders')));
    }

    /**
     * @param string $header
     * @return bool
     */
    public static function isValideResponseHeader($header)
    {
        return in_array(strtolower($header), array_map('strtolower', Constante::chargerConfig('responseheaders')));
    }


    public static function isValideFormat($format)
    {
        return in_array(strtolower($format), array_map('strtolower', Constante::chargerConfig('mimes')));
    }
}