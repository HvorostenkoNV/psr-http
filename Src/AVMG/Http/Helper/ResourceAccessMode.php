<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * Resource access mode class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class ResourceAccessMode
{
    private const
        MODES           =
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
            ],
        DEFAULT_MODE    = 'r',
        SPECIAL_FLAG    = 'b';
    /** **********************************************************************
     * Normalize mode value.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Normalized mode value.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $value) : string
    {
        $valueClear = self::getValueClear($value);

        if (!array_key_exists($valueClear, self::MODES))
        {
            throw new NormalizingException;
        }

        return self::getValueUpgraded($valueClear);
    }
    /** **********************************************************************
     * Get available resource mode versions.
     *
     * @return  string[]                    Available resource mode versions.
     ************************************************************************/
    public static function getAvailableValues() : array
    {
        $valuesRaw  = array_keys(self::MODES);
        $result     = [];

        foreach ($valuesRaw as $value)
        {
            $result[] = self::getValueUpgraded($value);
        }

        return $result;
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
    ) : string
    {
        $needParams = [];

        switch (strtolower($readWrite))
        {
            case 'write':
                $needParams[] = 'write';
                break;
            case 'readWrite':
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
    public static function isReadable(string $value) : bool
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
    public static function isWritable(string $value) : bool
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
    private static function getValueClear(string $value) : string
    {
        $valueNormalized = self::getValueNormalized($value);

        return str_replace
        (
            self::SPECIAL_FLAG,
            '',
            $valueNormalized
        );
    }
    /** **********************************************************************
     * Get resource mode with special flag.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Mode value with special flag.
     ************************************************************************/
    private static function getValueUpgraded(string $value) : string
    {
        $valueNormalized    = self::getValueNormalized($value);
        $hasPlus            = substr($valueNormalized, -1) == '+';
        $valueWithoutPlus   = $hasPlus
            ? substr($valueNormalized, 0, -1)
            : $valueNormalized;

        return $hasPlus
            ? $valueWithoutPlus.self::SPECIAL_FLAG.'+'
            : $valueWithoutPlus.self::SPECIAL_FLAG;
    }
    /** **********************************************************************
     * Get resource mode normalized.
     *
     * @param   string $value               Mode value.
     *
     * @return  string                      Mode value normalized.
     ************************************************************************/
    private static function getValueNormalized(string $value) : string
    {
        return strtolower($value);
    }
}