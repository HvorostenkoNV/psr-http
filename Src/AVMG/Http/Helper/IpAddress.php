<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use
    RuntimeException,
    AVMG\Http\Exception\NormalizingException;
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
        V6_DUAL_PARTS_COUNT = 6,
        V6_PART_MASK        = '/^[0-9a-zA-Z]{1,4}$/';
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
                $error = $exception->getMessage();
                throw new NormalizingException
                (
                    "ip address v4 segment validation error: $error",
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
            $ipAddressConverted = implode(':', $ipAddressExplode);
            $isDual             = true;
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
            throw new NormalizingException("$segment is not numeric value");
        }

        $segmentNumeric = (int) $segment;
        $minValue       = self::V4_PART_MIN_VALUE;
        $maxValue       = self::V4_PART_MAX_VALUE;

        if ($segmentNumeric < self::V4_PART_MIN_VALUE)
        {
            throw new NormalizingException("$segmentNumeric less than $minValue");
        }
        if ($segmentNumeric > self::V4_PART_MAX_VALUE)
        {
            throw new NormalizingException("$segmentNumeric grater than $maxValue");
        }

        return (string) $segmentNumeric;
    }
    /** **********************************************************************
     * Convert v6 IP address to full format.
     *
     * @param   string  $ipAddress          V6 IP address.
     * @param   bool    $isDual             Provided v6 IP address is dual.
     *
     * @return  string                      Converted v6 IP address in full format.
     ************************************************************************/
    private static function convertV6ToFullFormat(string $ipAddress, bool $isDual) : string
    {
        $ipAddressExplode       = explode(':', $ipAddress);
        $ipAddressNormalParts   = array_filter($ipAddressExplode, function($value)
        {
            return strlen($value) > 0;
        });
        $currentPartsCount      = count($ipAddressNormalParts);
        $needPartsCount         = $isDual ? self::V6_DUAL_PARTS_COUNT : self::V6_PARTS_COUNT;
        $isShortened            = (int) preg_match_all('/[\:]{2}/', $ipAddress) == 1;
        $ipAddressPrepared      = $ipAddress;

        if ($isShortened == 1)
        {
            $repeatCount        = $needPartsCount - $currentPartsCount;
            $repeatString       = str_repeat(':0:', $repeatCount);
            $ipAddressPrepared  = str_replace('::', $repeatString, $ipAddressPrepared);
            $ipAddressPrepared  = str_replace('::', ':', $ipAddressPrepared);
            $ipAddressPrepared  = trim($ipAddressPrepared, ':');
        }

        return $ipAddressPrepared;
    }
    /** **********************************************************************
     * Convert v6 IP address to short format.
     *
     * @param   string $ipAddress           V6 IP address.
     *
     * @return  string                      Converted v6 IP address in short format.
     ************************************************************************/
    private static function convertV6ToShortFormat(string $ipAddress) : string
    {
        $ipAddressExplode   = explode(':', $ipAddress);
        $ipAddressParts     = array_walk($ipAddressExplode, function(&$value)
        {
            $value  = ltrim($value, '0');
            $value  = strlen($value) > 0 ? $value : '0';
        });
        $ipAddressPrepared  = implode(':', $ipAddressParts);
        $matches            = [];

        preg_match_all('/(\:){0,1}(0\:0){1,}(\:){0,1}/', $ipAddressPrepared, $matches);
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

    }
}