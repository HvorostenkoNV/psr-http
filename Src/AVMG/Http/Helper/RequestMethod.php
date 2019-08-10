<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * HTTP request method class.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class RequestMethod
{
    private const AVAILABLE_METHODS =
        [
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'CONNECT',
            'OPTIONS',
            'TRACE',
            'PATCH'
        ];
    /** **********************************************************************
     * Normalize the HTTP request method.
     *
     * @param   string $method              HTTP request method.
     *
     * @return  string                      Normalized HTTP request method.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $method): string
    {
        $method = strtoupper($method);

        if (!in_array($method, self::AVAILABLE_METHODS))
        {
            throw new NormalizingException;
        }

        return $method;
    }
    /** **********************************************************************
     * Get available HTTP request methods.
     *
     * @return  string[]                    Available HTTP request methods.
     ************************************************************************/
    public static function getAvailableValues(): array
    {
        return self::AVAILABLE_METHODS;
    }
}