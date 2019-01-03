<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI params class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriParams
{
    private const
        URI_ALLOWED_SPECIAL_CHARS           =
            [
                '-', '_', '.', '~'
            ];
    private static
        $regularMasks = [];
    /** **********************************************************************
     * Normalize query.
     *
     * @param   string $query               Query.
     *
     * @return  string                      Normalized query.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeQuery(string $query) : string
    {
        $queryMask      = self::getMask('query');
        $queryConverted = trim($query,'?&');
        $queryConverted = preg_replace('/\&{1,}/', '&', $queryConverted);
        $queryConverted = preg_replace('/\={1,}/', '=', $queryConverted);
        $queryExploded  = explode('&', $queryConverted);

        foreach ($queryExploded as $index => $part)
        {
            $partExploded = explode('=', $part);

            foreach ($partExploded as $partIndex => $partValue)
            {
                $partValueEncoded   = rawurlencode(rawurldecode($partValue));
                $matches            = [];

                preg_match($queryMask, $partValueEncoded, $matches);

                $partExploded[$partIndex] = isset($matches[0]) && $matches[0] === $partValueEncoded
                    ? $partValueEncoded
                    : '';
            }

            $key    = $partExploded[0] ?? '';
            $value  = $partExploded[1] ?? '';

            $queryExploded[$index] = strlen($key) > 0 && strlen($value) > 0
                ? "$key=$value"
                : $key;
        }

        $queryExploded = array_filter($queryExploded, function($value)
        {
            return strlen($value) > 0;
        });

        if (count($queryExploded) <= 0)
        {
            throw new NormalizingException;
        }

        return implode('&', $queryExploded);
    }
    /** **********************************************************************
     * Normalize fragment.
     *
     * @param   string $fragment            Fragment.
     *
     * @return  string                      Normalized fragment.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeFragment(string $fragment) : string
    {
        if (strlen($fragment) <= 0)
        {
            return $fragment;
        }

        try
        {
            $fragmentValidated  = ltrim($fragment, '#');
            $fragmentValidated  = self::normalizeQueryOrFragment($fragmentValidated);

            return $fragmentValidated;
        }
        catch (NormalizingException $exception)
        {
            throw $exception;
        }
    }
    /** **********************************************************************
     * Normalize query or fragment.
     *
     * @param   string $value               Query or fragment.
     *
     * @return  string                      Normalized value.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizeQueryOrFragment(string $value) : string
    {
        return preg_replace_callback
        (
            '/(?:[^a-zA-Z0-9_\-\.~\pL!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/u',
            function(array $matches)
            {
                rawurlencode($matches[0]);
            },
            $value
        );
    }
    /** **********************************************************************
     * Get regular expression mask by given type.
     *
     * @param   string $type                Type.
     *
     * @return  string                      Regular expression mask by given type.
     ************************************************************************/
    private static function getMask(string $type) : string
    {
        if (isset(self::$regularMasks[$type]))
        {
            return self::$regularMasks[$type];
        }

        $mask = '';

        switch ($type)
        {
            case 'path_part':
            case 'query':
                foreach (self::URI_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/[a-zA-Z0-9$mask\\:\\%]{1,}/";
                break;
            default:
                $mask = '/*{0,}/';
        }

        self::$regularMasks[$type] = $mask;
        return self::$regularMasks[$type];
    }
}