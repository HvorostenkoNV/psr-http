<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use
    Psr\Http\Message\UriInterface,
    Psr\Http\Message\UriFactoryInterface,
    AVMG\Http\Uri\Uri;
/** ***********************************************************************************************
 * PSR-7 UriFactoryInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriFactory implements UriFactoryInterface
{
    /** **********************************************************************
     * Create a new URI.
     *
     * @param   string $uri                 The URI to parse.
     *
     * @return  UriInterface                URI.
     ************************************************************************/
    public function createUri(string $uri = '') : UriInterface
    {
        return new Uri($uri);
    }
}