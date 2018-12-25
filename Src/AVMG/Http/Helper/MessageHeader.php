<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * HTTP message header class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class MessageHeader
{
    /** **********************************************************************
     * Normalize header name.
     *
     * @param   string $name                Header name.
     *
     * @return  string                      Normalized header name.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeHeaderName(string $name) : string
    {
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $name))
        {
            throw new NormalizingException;
        }

        return $name;
    }
    /** **********************************************************************
     * Normalize header value.
     *
     * @param   mixed $value                Header value.
     *
     * @return  string                      Normalized header value.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeHeaderValue($value) : string
    {
        if (!is_string($value) && !is_numeric($value))
        {
            throw new NormalizingException;
        }
        if (strlen($value) <= 0)
        {
            throw new NormalizingException;
        }
        if (!preg_match('#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#', $value))
        {
            throw new NormalizingException;
        }
        if (!preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $value))
        {
            throw new NormalizingException;
        }

        return (string) $value;
    }
}