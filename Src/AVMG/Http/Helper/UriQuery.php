<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI query class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriQuery
{
    /** **********************************************************************
     * Normalize the URI query.
     *
     * @param   string $query               URI query.
     *
     * @return  string                      Normalized URI query.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $query) : string
    {
        $queryExploded  = explode('&', $query);
        $queryExploded  = array_filter($queryExploded, function($value)
        {
            return strlen($value) > 0;
        });

        foreach ($queryExploded as $index => $part)
        {
            $partExploded   = explode('=', $part, 2);
            $key            = $partExploded[0] ?? '';
            $value          = $partExploded[1] ?? '';

            try
            {
                $key = self::normalizeValue($key);
            }
            catch (NormalizingException $exception)
            {
                unset($queryExploded[$index]);
                continue;
            }

            try
            {
                $value = self::normalizeValue($value);
            }
            catch (NormalizingException $exception)
            {
                $value = '';
            }

            $queryExploded[$index] = strlen($value) > 0 ? "$key=$value" : $key;
        }

        if (count($queryExploded) <= 0)
        {
            throw new NormalizingException("value \"$query\" is empty after normalization");
        }

        return implode('&', $queryExploded);
    }
    /** **********************************************************************
     * Normalize the URI query value.
     *
     * @param   string $value               URI query value.
     *
     * @return  string                      Normalized URI query value.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizeValue(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException('value is empty string');
        }

        return str_replace(' ', '%20', $value);
    }
}