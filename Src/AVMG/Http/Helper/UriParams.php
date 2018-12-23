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
        PORT_MIN_VALUE                  = 1,
        PORT_MAX_VALUE                  = 65535,
        SCHEMES_STANDARD_PORTS          =
            [
                'tcpmux'    => [1],
                'qotd'      => [17],
                'chargen'   => [19],
                'ftp'       => [20, 21],
                'ssh'       => [22],
                'telnet'    => [23],
                'smtp'      => [25],
                'whois'     => [43],
                'tftp'      => [69],
                'http'      => [80],
                'pop2'      => [109],
                'pop3'      => [110],
                'nntp'      => [119],
                'ntp'       => [123],
                'imap'      => [143],
                'snmp'      => [161],
                'irc'       => [194],
                'https'     => [443]
            ],
        SCHEME_ALLOWED_SPECIAL_CHARS    =
            [
                '+', '-', '.'
            ],
        HOST_ALLOWED_SPECIAL_CHARS      =
            [
                '-', '.'
            ],
        PATH_ALLOWED_SPECIAL_CHARS      =
            [
                '+', '-', '=',
                ',', ':', ';',
                '(', ')', '_',
                '!', '$', '&',
                '.', '~', '\'',
                '*', '@'
            ];
    private static
        $regularMasks = [];
    /** **********************************************************************
     * Normalize scheme.
     *
     * @param   string $value               Scheme.
     * @return  string                      Normalized scheme.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeScheme(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }

        $valueConverted = strtolower(trim($value));
        $schemeMask     = self::getMask('scheme');
        $matches        = [];

        preg_match($schemeMask, $valueConverted, $matches);

        if (!isset($matches[0]) || $matches[0] !== $valueConverted)
        {
            throw new NormalizingException;
        }

        return $valueConverted;
    }
    /** **********************************************************************
     * Normalize host.
     *
     * @param   string $value               Host.
     * @return  string                      Normalized host.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeHost(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }

        $valueConverted = strtolower(trim($value));
        $hostMask       = self::getMask('host');
        $matches        = [];

        preg_match($hostMask, $valueConverted, $matches);

        if (!isset($matches[0]) || $matches[0] !== $valueConverted)
        {
            throw new NormalizingException;
        }

        return $valueConverted;
    }
    /** **********************************************************************
     * Normalize port.
     *
     * @param   int     $value              Port.
     * @return  int                         Normalized port.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizePort(int $value) : int
    {
        if ($value < self::PORT_MIN_VALUE || $value > self::PORT_MAX_VALUE)
        {
            throw new NormalizingException;
        }

        return $value;
    }
    /** **********************************************************************
     * Check if port is standard for given scheme.
     *
     * @param   int     $port               Port.
     * @param   string  $scheme          preg_match   Scheme.
     * @return  bool                        Port is standard for given scheme.
     ************************************************************************/
    public static function isStandardPort(int $port, string $scheme) : bool
    {
        return
            array_key_exists($scheme, self::SCHEMES_STANDARD_PORTS) &&
            in_array($port, self::SCHEMES_STANDARD_PORTS[$scheme]);
    }
    /** **********************************************************************
     * Normalize user info.
     *
     * @param   string $value               User info.
     * @return  string                      Normalized string.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeUserInfo(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }

        $valueEncoded = rawurlencode(rawurldecode($value));

        return $valueEncoded;
    }
    /** **********************************************************************
     * Normalize path.
     *
     * @param   string $value               Path.
     * @return  string                      Normalized path.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizePath(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }

        $pathMask       = self::getMask('path');
        $pathExplode    = strpos($value, '/') !== false
            ? explode('/', $value)
            : [$value];
        $matches        = [];

        foreach ($pathExplode as $index => $part)
        {
            if (strlen($part) <= 0)
            {
                continue;
            }

            $partEncoded = rawurlencode(rawurldecode($part));

            preg_match($pathMask, $partEncoded, $matches);

            if (!isset($matches[0]) || $matches[0] !== $partEncoded)
            {
                throw new NormalizingException;
            }

            $pathExplode[$index] = $partEncoded;
        }

        return implode('/', $pathExplode);
    }
    /** **********************************************************************
     * Normalize query.
     *
     * @param   string $value               Query.
     * @return  string                      Normalized query.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeQuery(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }

        $queryMask      = self::getMask('query');
        $queryValidated = ltrim($value, '?');
        $queryExploded  = explode('&', $queryValidated);
        $matches        = [];

        foreach ($queryExploded as $index => $part)
        {
            $partExplode    = explode('=', $part, 2);
            $partExplode[1] = $partExplode[1] ?? '';

            foreach ($partExplode as $partIndex => $partValue)
            {
                preg_match($queryMask, $partValue, $matches);

                if (!isset($matches[0]) || $matches[0] !== $partValue)
                {
                    $partExplode[$partIndex] = $partValue;
                }
            }

            $queryExploded[$index] = strlen($partExplode[1]) > 0
                ? "{$partExplode[0]}={$partExplode[1]}"
                : $partExplode[0];
        }

        $queryExploded = array_filter($queryExploded, function($value)
        {
            return strlen($value) > 0;
        });

        return implode('&', $queryExploded);
    }
    /** **********************************************************************
     * Normalize fragment.
     *
     * @param   string $fragment            Fragment.
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
            case 'scheme':
                foreach (self::SCHEME_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/^[a-z]{1}[a-z0-9$mask]{1,}/";
                break;
            case 'host':
                foreach (self::HOST_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/^[a-z0-9]{1}[a-z0-9$mask]{1,}/";
                break;
            case 'path':
                foreach (self::PATH_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/[a-zA-Z0-9$mask\\:\\%]{1,}/";
                break;
            default:
        }

        self::$regularMasks[$type] = $mask;
        return self::$regularMasks[$type];
    }
}