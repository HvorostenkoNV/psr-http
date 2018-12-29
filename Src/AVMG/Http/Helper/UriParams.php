<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use
    RuntimeException,
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException;
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
            ],
        SCHEMES_STANDARD_PORTS              =
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
        SCHEME_ALLOWED_SPECIAL_CHARS        =
            [
                '+', '-', '.'
            ],
        DOMAIN_NAME_ALLOWED_SPECIAL_CHARS   =
            [
                '-', '.'
            ],
        IP_PART_V4_MIN_VALUE                = 0,
        IP_PART_V4_MAX_VALUE                = 255,
        IP_PARTS_V4_COUNT                   = 4,
        PORT_MIN_VALUE                      = 1,
        PORT_MAX_VALUE                      = 65535;
    private static
        $regularMasks = [];
    /** **********************************************************************
     * Normalize scheme.
     *
     * @param   string $scheme              Scheme.
     *
     * @return  string                      Normalized scheme.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeScheme(string $scheme) : string
    {
        try
        {
            $schemeConverted    = strtolower(trim($scheme));
            $schemeMask         = self::getMask('scheme');

            self::checkByRegularExpression($schemeMask, $schemeConverted);

            return $schemeConverted;
        }
        catch (InvalidArgumentException $exception)
        {
            throw new NormalizingException
            (
                "scheme \"$scheme\" validating impossible",
                0,
                $exception
            );
        }
        catch (RuntimeException $exception)
        {
            throw new NormalizingException
            (
                "scheme \"$scheme\" is invalid",
                0,
                $exception
            );
        }
    }
    /** **********************************************************************
     * Normalize host.
     *
     * @param   string $host                Host.
     *
     * @return  string                      Normalized host.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeHost(string $host) : string
    {
        try
        {
            return self::normalizeDomainName($host);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            return self::normalizeIpAddressV4($host);
        }
        catch (NormalizingException $exception)
        {

        }

        if
        (
            strlen($host) > 2   &&
            $host[0] == '['     &&
            $host[strlen($host) - 1] == ']'
        )
        {
            try
            {
                $hostPrepared   = trim($host, '[]');
                $hostNormalized = self::normalizeIpAddressV6($hostPrepared);

                return "[$hostNormalized]";
            }
            catch (NormalizingException $exception)
            {

            }
        }

        throw new NormalizingException("host \"$host\" is invalid");
    }
    /** **********************************************************************
     * Normalize domain name.
     *
     * @param   string $domainName          Domain name.
     *
     * @return  string                      Normalized domain name.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeDomainName(string $domainName) : string
    {
        try
        {
            $domainNameConverted    = strtolower(trim($domainName));
            $domainNameMask         = self::getMask('domain-name');

            self::checkByRegularExpression($domainNameMask, $domainNameConverted);

            return $domainNameConverted;
        }
        catch (RuntimeException $exception)
        {
            throw new NormalizingException
            (
                "domain name \"$domainName\" is invalid",
                0,
                $exception
            );
        }
        catch (InvalidArgumentException $exception)
        {
            throw new NormalizingException
            (
                'domain name validating impossible',
                0,
                $exception
            );
        }
    }
    /** **********************************************************************
     * Normalize ip address version 4.
     *
     * @param   string $ipAddress           Ip address.
     *
     * @return  string                      Normalized ip address.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeIpAddressV4(string $ipAddress) : string
    {
        $ipAddressExplode   = explode('.', $ipAddress);
        $exception          = new NormalizingException("ip address \"$ipAddress\" is invalid");

        if (count($ipAddressExplode) != self::IP_PARTS_V4_COUNT)
        {
            throw $exception;
        }

        foreach ($ipAddressExplode as $index => $part)
        {
            if (!is_numeric($part))
            {
                throw $exception;
            }

            $partNumeric = (int) $part;

            if
            (
                $partNumeric < self::IP_PART_V4_MIN_VALUE ||
                $partNumeric > self::IP_PART_V4_MAX_VALUE
            )
            {
                throw $exception;
            }

            $ipAddressExplode[$index] = $partNumeric;
        }

        return implode('.', $ipAddressExplode);
    }
    /** **********************************************************************
     * Normalize ip address version 6.
     *
     * @param   string $ipAddress           Ip address.
     *
     * @return  string                      Normalized ip address.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeIpAddressV6(string $ipAddress) : string
    {
        throw new NormalizingException;
    }
    /** **********************************************************************
     * Normalize port.
     *
     * @param   int     $port               Port.
     *
     * @return  int                         Normalized port.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizePort(int $port) : int
    {
        $minValue   = self::PORT_MIN_VALUE;
        $maxValue   = self::PORT_MAX_VALUE;

        if ($port < $minValue)
        {
            throw new NormalizingException("port \"$port\" is less then {$minValue}");
        }
        if ($port > $maxValue)
        {
            throw new NormalizingException("port \"$port\" is grater then {$minValue}");
        }

        return $port;
    }
    /** **********************************************************************
     * Check if port is standard for given scheme.
     *
     * @param   int     $port               Port.
     * @param   string  $scheme             Scheme.
     *
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
     * @param   string $userInfo            User info.
     *
     * @return  string                      Normalized string.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeUserInfo(string $userInfo) : string
    {
        if (strlen($userInfo) <= 0)
        {
            throw new NormalizingException;
        }

        return rawurlencode(rawurldecode($userInfo));
    }
    /** **********************************************************************
     * Normalize path.
     *
     * @param   string $path                Path.
     *
     * @return  string                      Normalized path.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizePath(string $path) : string
    {
        $pathMask       = self::getMask('path_part');
        $pathExplode    = strpos($path, '/') !== false ? explode('/', $path) : [$path];

        foreach ($pathExplode as $index => $value)
        {
            if (strlen($value) <= 0)
            {
                continue;
            }

            $valueEncoded   = rawurlencode(rawurldecode($value));
            $matches        = [];

            preg_match($pathMask, $valueEncoded, $matches);

            if (!isset($matches[0]) || $matches[0] !== $valueEncoded)
            {
                throw new NormalizingException;
            }

            $pathExplode[$index] = $valueEncoded;
        }

        return implode('/', $pathExplode);
    }
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
     * @throws  InvalidArgumentException    Unknown mask type.
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
            case 'domain-name':
                foreach (self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/^[a-z0-9]{1}[a-z0-9$mask]{0,}\.[a-z]{1,}/";
                break;
            case 'path_part':
            case 'query':
                foreach (self::URI_ALLOWED_SPECIAL_CHARS as $char)
                {
                    $mask .= "\\$char";
                }

                $mask = "/[a-zA-Z0-9$mask\\:\\%]{1,}/";
                break;
            default:
                throw new InvalidArgumentException("no mask available for type \"$type\"");
        }

        self::$regularMasks[$type] = $mask;
        return self::$regularMasks[$type];
    }
    /** **********************************************************************
     * Check value by regular expression with given mask.
     *
     * @param   string  $mask               Regular expression mask.
     * @param   string  $value              Value.
     *
     * @return  void
     * @throws  RuntimeException            Checking failed.
     ************************************************************************/
    private static function checkByRegularExpression(string $mask, string $value) : void
    {
        $matches = [];

        preg_match($mask, $value, $matches);

        if (!isset($matches[0]) || $matches[0] !== $value)
        {
            throw new RuntimeException("value \"$value\" does not matched the pattern \"$mask\"");
        }
    }
}