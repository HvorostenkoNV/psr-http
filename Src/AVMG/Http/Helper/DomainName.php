<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI domain name class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class DomainName
{
    private const
        ALLOWED_SPECIAL_CHARS =
            [
                '-', '.'
            ];
    private static
        $mask           = '',
        $maskPrepared   = false;
    /** **********************************************************************
     * Normalize the URI domain name.
     *
     * @param   string $domainName          URI domain name.
     *
     * @return  string                      Normalized URI domain name.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $domainName) : string
    {
        $domainNameConverted    = strtolower($domainName);
        $domainNameMask         = self::getMask();
        $matches                = [];

        preg_match($domainNameMask, $domainNameConverted, $matches);

        if (!isset($matches[0]) || $matches[0] !== $domainNameConverted)
        {
            throw new NormalizingException
            (
                "domain name \"$domainName\" does not matched the pattern \"$domainNameMask\""
            );
        }

        return $domainNameConverted;
    }
    /** **********************************************************************
     * Get the URI domain name regular expression mask.
     *
     * @return  string                      URI domain name regular expression mask.
     ************************************************************************/
    private static function getMask() : string
    {
        if (!self::$maskPrepared)
        {
            $specialCharsMask = '';

            foreach (self::ALLOWED_SPECIAL_CHARS as $char)
            {
                $specialCharsMask .= "\\$char";
            }

            self::$mask         = "/(^[a-z0-9]+)([a-z]+)([a-z0-9$specialCharsMask]{0,})/";
            self::$maskPrepared = true;
        }

        return self::$mask;
    }
}