<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI scheme class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Scheme
{
    private const
        ALLOWED_SPECIAL_CHARS =
            [
                '+', '-', '.'
            ];
    private static
        $mask           = '',
        $maskPrepared   = false;
    /** **********************************************************************
     * Normalize the URI scheme.
     *
     * @param   string $scheme              URI scheme.
     *
     * @return  string                      Normalized URI scheme.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $scheme) : string
    {
        $schemeConverted    = strtolower($scheme);
        $schemeMask         = self::getMask();
        $matches            = [];

        preg_match($schemeMask, $schemeConverted, $matches);

        if (!isset($matches[0]) || $matches[0] !== $schemeConverted)
        {
            throw new NormalizingException
            (
                "scheme \"$scheme\" does not matched the pattern \"$schemeMask\""
            );
        }

        return $schemeConverted;
    }
    /** **********************************************************************
     * Get the URI scheme regular expression mask.
     *
     * @return  string                      URI scheme regular expression mask.
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

            self::$mask         = "/^[a-z]{1}[a-z0-9$specialCharsMask]{1,}$/";
            self::$maskPrepared = true;
        }

        return self::$mask;
    }
}