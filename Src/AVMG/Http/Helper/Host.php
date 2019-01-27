<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI host class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Host
{
    /** **********************************************************************
     * Normalize the URI host.
     *
     * @param   string $host                Host.
     *
     * @return  string                      Normalized host.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $host) : string
    {
        try
        {
            return IpAddress::normalizeV4($host);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $ipAddressPrepared      = trim($host, '[]');
            $ipAddressNormalized    = IpAddress::normalizeV6($ipAddressPrepared);

            return "[$ipAddressNormalized]";
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            return DomainName::normalize($host);
        }
        catch (NormalizingException $exception)
        {

        }

        throw new NormalizingException("value \"$host\" is invalid host");
    }
}