<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI path class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriPath
{
    /** **********************************************************************
     * Normalize the URI path.
     *
     * @param   string $path                URI path.
     *
     * @return  string                      Normalized URI path.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $path) : string
    {
        $pathExplode = explode('/', $path);

        foreach ($pathExplode as $index => $part)
        {
            if (strlen($part) > 0)
            {
                try
                {
                    $pathExplode[$index] = self::normalizePart($part);
                }
                catch (NormalizingException $exception)
                {
                    throw $exception;
                }
            }
        }

        return implode('/', $pathExplode);
    }
    /** **********************************************************************
     * Normalize the URI path part.
     *
     * @param   string $part                URI path part.
     *
     * @return  string                      Normalized URI path part.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizePart(string $part) : string
    {
        throw new NormalizingException;
    }
}