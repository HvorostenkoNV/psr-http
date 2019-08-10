<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;

use function substr;
use function strtolower;
use function str_replace;
use function sort;
use function in_array;
use function array_keys;
use function array_map;
/** ***********************************************************************************************
 * Resource access mode class.
 *
 * @deprecated
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class ResourceAccessMode
{
    private const MODES         =
        [
            'r'     => ['read',          'from_begin'],
            'r+'    => ['read', 'write', 'from_begin'],
            'w'     => [        'write', 'from_begin',             'new', 'rewrite'],
            'w+'    => ['read', 'write', 'from_begin',             'new', 'rewrite'],
            'a'     => [        'write',               'from_end', 'new'],
            'a+'    => ['read', 'write',               'from_end', 'new'],
            'x'     => [        'write', 'from_begin',             'new'],
            'x+'    => ['read', 'write', 'from_begin',             'new'],
            'c'     => [        'write', 'from_begin',             'new',           'lock'],
            'c+'    => ['read', 'write', 'from_begin',             'new',           'lock']
        ];
    private const DEFAULT_MODE  = 'r';
    private const SPECIAL_FLAG  = 'b';
    /** **********************************************************************
     * Normalize mode value.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Normalized mode value.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $value): string
    {
        $valueClear = self::getValueClear($value);

        if (!isset(self::MODES[$valueClear]))
        {
            throw new NormalizingException("mode \"$value\" is unknown");
        }

        return self::getValueUpgraded($valueClear);
    }
    /** **********************************************************************
     * Get available resource mode versions.
     *
     * @return  string[]                    Available resource mode versions.
     ************************************************************************/
    public static function getAvailableValues(): array
    {
        $valuesRaw = array_keys(self::MODES);

        return array_map
        (
            function($value)
            {
                return self::getValueUpgraded($value);
            },
            $valuesRaw
        );
    }
    /** **********************************************************************
     * Get resource mode by params.
     *
     * @param   string  $readWrite          Read/write, available values:
     *                                      read|write|readWrite.
     * @param   string  $position           Start position, available values:
     *                                      begin|end.
     * @param   bool    $create             Try to create file.
     * @param   bool    $rewrite            File rewriting.
     * @param   bool    $lock               File locking while processing.
     *
     * @return  string                      Resource mode.
     ************************************************************************/
    public static function get
    (
        string  $readWrite  = 'read',
        string  $position   = 'begin',
        bool    $create     = false,
        bool    $rewrite    = false,
        bool    $lock       = false
    ): string
    {
        $needParams = [];

        switch (strtolower($readWrite))
        {
            case 'write':
                $needParams[] = 'write';
                break;
            case 'readwrite':
                $needParams[] = 'read';
                $needParams[] = 'write';
                break;
            case 'read':
            default:
                $needParams[] = 'read';
        }

        switch (strtolower($position))
        {
            case 'end':
                $needParams[] = 'from_end';
                break;
            case 'begin':
            default:
                $needParams[] = 'from_begin';
        }

        if ($create && in_array('write', $needParams))
        {
            $needParams[] = 'new';
        }

        if ($rewrite && in_array('write', $needParams))
        {
            $needParams[] = 'rewrite';
        }

        if ($lock)
        {
            $needParams[] = 'lock';
        }

        sort($needParams);
        foreach (self::MODES as $mode => $modeParams)
        {
            sort($modeParams);
            if ($modeParams == $needParams)
            {
                return self::getValueUpgraded($mode);
            }
        }

        return self::getValueUpgraded(self::DEFAULT_MODE);
    }
    /** **********************************************************************
     * Check mode value is readable.
     *
     * @param   string $value               Mode value.
     *
     * @return  bool                        Is readable.
     ************************************************************************/
    public static function isReadable(string $value): bool
    {
        $valueClear = self::getValueClear($value);
        $modeParams = self::MODES[$valueClear] ?? [];

        return in_array('read', $modeParams);
    }
    /** **********************************************************************
     * Check mode value is writable.
     *
     * @param   string $value               Mode value.
     *
     * @return  bool                        Is writable.
     ************************************************************************/
    public static function isWritable(string $value): bool
    {
        $valueClear = self::getValueClear($value);
        $modeParams = self::MODES[$valueClear] ?? [];

        return in_array('write', $modeParams);
    }
    /** **********************************************************************
     * Get resource mode without special flag.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Mode value without special flag.
     ************************************************************************/
    private static function getValueClear(string $value): string
    {
        $valueConverted = strtolower($value);

        return str_replace(self::SPECIAL_FLAG, '', $valueConverted);
    }
    /** **********************************************************************
     * Get resource mode with special flag.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Mode value with special flag.
     ************************************************************************/
    private static function getValueUpgraded(string $value): string
    {
        $valueConverted     = strtolower($value);
        $hasPlus            = substr($valueConverted, -1) == '+';
        $valueWithoutPlus   = $hasPlus
            ? substr($valueConverted, 0, -1)
            : $valueConverted;

        return $hasPlus
            ? $valueWithoutPlus.self::SPECIAL_FLAG.'+'
            : $valueWithoutPlus.self::SPECIAL_FLAG;
    }
}