<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * HTTP protocol class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Protocol
{
    private const
        AVAILABLE_PROTOCOL_VERSIONS =
            [
                '0.9',
                '1.0',
                '1.1',
                '2.0'
            ],
        DEFAULT_PROTOCOL_VERSIONS   = '1.1';
    /** **********************************************************************
     * Normalize the HTTP protocol version.
     *
     * @param   string $version             HTTP protocol version.
     * @return  string                      Normalized HTTP protocol version.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $version) : string
    {
        if (!in_array($version, self::AVAILABLE_PROTOCOL_VERSIONS))
        {
            throw new NormalizingException;
        }

        return $version;
    }
    /** **********************************************************************
     * Get available HTTP protocol versions.
     *
     * @return  string[]                    Available HTTP protocol versions.
     ************************************************************************/
    public static function getAvailableValues() : array
    {
        return self::AVAILABLE_PROTOCOL_VERSIONS;
    }
    /** **********************************************************************
     * Get default HTTP protocol version.
     *
     * @return  string                      Default HTTP protocol version.
     ************************************************************************/
    public static function getDefault() : string
    {
        return self::DEFAULT_PROTOCOL_VERSIONS;
    }
}