<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI port class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Port
{
    private const
        STANDARD_PORTS  =
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
        MIN_VALUE       = 1,
        MAX_VALUE       = 65535;
    /** **********************************************************************
     * Normalize the URI port.
     *
     * @param   int $port                   URI port.
     *
     * @return  int                         Normalized URI port.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(int $port) : int
    {
        $minValue   = self::MIN_VALUE;
        $maxValue   = self::MAX_VALUE;

        if ($port < $minValue)
        {
            throw new NormalizingException("value \"$port\" is less then $minValue");
        }
        if ($port > $maxValue)
        {
            throw new NormalizingException("value \"$port\" is grater then $minValue");
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
    public static function isStandard(int $port, string $scheme) : bool
    {
        return
            array_key_exists($scheme, self::STANDARD_PORTS) &&
            in_array($port, self::STANDARD_PORTS[$scheme]);
    }
}