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
    private const UNCODED_SPECIAL_CHARS =
        [
            '\''    => '%27',
            '['     => '%5B',
            ']'     => '%5D',
            '('     => '%28',
            ')'     => '%29',
            '+'     => '%2B',
            '='     => '%3D',
            '*'     => '%2A',
            '%'     => '%25',
            ','     => '%2C',
            ':'     => '%3A',
            '!'     => '%21',
            '@'     => '%40',
            '$'     => '%24',
            '&'     => '%26'
        ];
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
                    throw new NormalizingException
                    (
                        "path part validation error, \"{$exception->getMessage()}\"",
                        0,
                        $exception
                    );
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
        if (strlen($part) <= 0)
        {
            throw new NormalizingException('value is empty string');
        }

        $partNormalized = rawurlencode(rawurldecode($part));

        foreach (self::UNCODED_SPECIAL_CHARS as $char => $charEncoded)
        {
            $partNormalized = str_replace($charEncoded, $char, $partNormalized);
        }

        return $partNormalized;
    }
}