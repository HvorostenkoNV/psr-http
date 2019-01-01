<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * IP address class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class IpAddress
{
    private const
        V4_PART_MIN_VALUE   = 0,
        V4_PART_MAX_VALUE   = 255,
        V4_PARTS_COUNT      = 4,
        V6_PARTS_COUNT      = 8,
        V6_DUAL_PARTS_COUNT = 6;
    /** **********************************************************************
     * Normalize the v4 IP address.
     *
     * @param   string $ipAddress           V4 IP address.
     *
     * @return  string                      Normalized v4 IP address.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeV4(string $ipAddress) : string
    {
        $ipAddressPrepared  = trim($ipAddress);
        $ipAddressExplode   = explode('.', $ipAddressPrepared);
        $partsMaxCount      = self::V4_PARTS_COUNT;

        if (count($ipAddressExplode) != self::V4_PARTS_COUNT)
        {
            throw new NormalizingException
            (
                "ip address v4 \"$ipAddress\" contains more than $partsMaxCount parts"
            );
        }

        foreach ($ipAddressExplode as $index => $part)
        {
            try
            {
                $ipAddressExplode[$index] = self::normalizeV4Segment($part);
            }
            catch (NormalizingException $exception)
            {
                throw new NormalizingException
                (
                    "ip address v4 segment validation error: {$exception->getMessage()}",
                    0,
                    $exception
                );
            }
        }

        return implode('.', $ipAddressExplode);
    }
    /** **********************************************************************
     * Normalize the v6 IP address.
     *
     * @param   string $ipAddress           V6 IP address.
     *
     * @return  string                      Normalized v6 IP address.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeV6(string $ipAddress) : string
    {
        $ipAddressConverted = trim($ipAddress);
        $ipAddressV4Postfix = '';
        $isDual             = false;

        try
        {
            $ipAddressExplode   = explode(':', $ipAddressConverted);
            $lastPart           = array_pop($ipAddressExplode);
            $ipAddressV4Postfix = self::normalizeV4($lastPart);
            $isDual             = true;

            if (strlen($ipAddressExplode[count($ipAddressExplode) - 1]) <= 0)
            {
                $ipAddressExplode[] = '';
            }

            $ipAddressConverted = implode(':', $ipAddressExplode);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $ipAddressConverted = self::normalizeV6WithoutV4Part($ipAddressConverted, $isDual);
        }
        catch (NormalizingException $exception)
        {
            throw $exception;
        }

        return $isDual
            ? "$ipAddressConverted:$ipAddressV4Postfix"
            : $ipAddressConverted;
    }
    /** **********************************************************************
     * Normalize the v4 IP address segment.
     *
     * @param   string $segment             V4 IP address segment.
     *
     * @return  string                      Normalized v4 IP address segment.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizeV4Segment(string $segment) : string
    {
        if (!is_numeric($segment))
        {
            throw new NormalizingException("\"$segment\" is not numeric value");
        }

        $segmentNumeric = (int) $segment;
        $minValue       = self::V4_PART_MIN_VALUE;
        $maxValue       = self::V4_PART_MAX_VALUE;

        if ($segmentNumeric < self::V4_PART_MIN_VALUE)
        {
            throw new NormalizingException("\"$segmentNumeric\" less than $minValue");
        }
        if ($segmentNumeric > self::V4_PART_MAX_VALUE)
        {
            throw new NormalizingException("\"$segmentNumeric\" grater than $maxValue");
        }

        return (string) $segmentNumeric;
    }
    /** **********************************************************************
     * Normalize the v6 IP address without v4 IP address postfix.
     *
     * @param   string  $ipAddress          V6 IP address.
     * @param   bool    $isDual             V6 IP address was with v4 IP address postfix.
     *
     * @return  string                      Normalized v6 IP address.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizeV6WithoutV4Part(string $ipAddress, bool $isDual) : string
    {
        $ipAddressExplode       = explode(':', $ipAddress);
        $ipAddressNotEmptyParts = array_filter($ipAddressExplode, function($value)
        {
            return strlen($value) > 0;
        });
        $currentPartsCount      = count($ipAddressNotEmptyParts);
        $needPartsCount         = $isDual ? self::V6_DUAL_PARTS_COUNT : self::V6_PARTS_COUNT;
        $shortsCount            = (int) preg_match_all('/[\:]{2}/',     $ipAddress);
        $shortsIncorrectCount   = (int) preg_match_all('/[\:]{3,}/',    $ipAddress);
        $isShortened            = $shortsCount == 1;

        if ($shortsCount > 1 || $shortsIncorrectCount > 0)
        {
            throw new NormalizingException
            (
                "ip address v6 \"$ipAddress\" contains incorrect shortens"
            );
        }
        if
        (
            strlen($ipAddress) < 2 ||
            (
                $ipAddress[0] == ':' &&
                $ipAddress[1] != ':'
            )  ||
            (
                $ipAddress[strlen($ipAddress) - 1] == ':' &&
                $ipAddress[strlen($ipAddress) - 2] != ':'
            )
        )
        {
            throw new NormalizingException
            (
                "ip address v6 \"$ipAddress\" has incorrect format"
            );
        }
        if
        (
            !$isShortened   && $currentPartsCount != $needPartsCount ||
             $isShortened   && $currentPartsCount >  $needPartsCount - 2
        )
        {
            throw new NormalizingException
            (
                "ip address v6 \"$ipAddress\" contains incorrect segments count"
            );
        }

        foreach ($ipAddressExplode as $index => $part)
        {
            if ($isShortened && strlen($part) <= 0)
            {
                continue;
            }

            try
            {
                $ipAddressExplode[$index] = self::normalizeV6Segment($part);
            }
            catch (NormalizingException $exception)
            {
                throw new NormalizingException
                (
                    "ip address v6 segment validation error: {$exception->getMessage()}",
                    0,
                    $exception
                );
            }
        }

        $ipAddressConverted = implode(':', $ipAddressExplode);
        $ipAddressConverted = !$isShortened
            ? self::convertV6ToShortFormat($ipAddressConverted)
            : $ipAddressConverted;

        return $ipAddressConverted;
    }
    /** **********************************************************************
     * Convert v6 IP address to short format.
     *
     * @param   string $ipAddress           V6 IP address.
     *
     * @return  string                      Converted v6 IP address to short format.
     ************************************************************************/
    private static function convertV6ToShortFormat(string $ipAddress) : string
    {
        preg_match_all('/([\:]?0[\:]?){1,}/', $ipAddress, $matches);

        $ipAddressPrepared  = $ipAddress;
        $longestValue       = '';
        $foundMatches       = isset($matches[0]) && is_array($matches[0])
            ? $matches[0]
            : [];

        foreach ($foundMatches as $value)
        {
            if (strlen($value) > strlen($longestValue))
            {
                $longestValue = $value;
            }
        }

        if (strlen($longestValue) > 3)
        {
            $ipAddressPrepared = preg_replace
            (
                '/'.preg_quote($longestValue).'/',
                '::',
                $ipAddressPrepared,
                1
            );
        }

        return $ipAddressPrepared;
    }
    /** **********************************************************************
     * Normalize the v6 IP address segment.
     *
     * @param   string $segment             V6 IP address segment.
     *
     * @return  string                      Normalized v6 IP address segment.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizeV6Segment(string $segment) : string
    {
        $mask       = '/^[0-9a-fA-F]{1,4}$/';
        $matches    = [];

        preg_match($mask, $segment, $matches);

        if (!isset($matches[0]) || $matches[0] !== $segment)
        {
            throw new NormalizingException
            (
                "\"$segment\" does not matched the pattern \"$mask\""
            );
        }

        $segmentConverted   = ltrim($segment, '0');
        $segmentConverted   = strlen($segmentConverted) <= 0
            ? '0'
            : $segmentConverted;

        return $segmentConverted;
    }
}