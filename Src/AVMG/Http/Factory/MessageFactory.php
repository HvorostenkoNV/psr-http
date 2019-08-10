<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use
    Psr\Http\Message\UriInterface,
    Psr\Http\Message\RequestInterface,
    Psr\Http\Message\ServerRequestInterface,
    Psr\Http\Message\ResponseInterface,
    Psr\Http\Message\RequestFactoryInterface,
    Psr\Http\Message\ServerRequestFactoryInterface,
    Psr\Http\Message\ResponseFactoryInterface,
    AVMG\Http\Request\Request,
    AVMG\Http\Request\ServerRequest,
    AVMG\Http\Response\Response;
/** ***********************************************************************************************
 * PSR-7 RequestFactoryInterface, ServerRequestFactoryInterface,
 * ResponseFactoryInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class
    MessageFactory
implements
    RequestFactoryInterface,
    ServerRequestFactoryInterface,
    ResponseFactoryInterface
{
    /** **********************************************************************
     * Create a new request.
     *
     * @param   string              $method         The HTTP method associated with the request.
     * @param   UriInterface|string $uri            The URI associated with the request.
     *
     * @return  RequestInterface                    Request.
     ************************************************************************/
    public function createRequest(string $method, $uri): RequestInterface
    {
        $uriInstance = $uri;

        if (!$uriInstance instanceof UriInterface)
        {
            $uriString      = is_string($uri) ? $uri : '';
            $uriInstance    = (new UriFactory)->createUri($uriString);
        }

        return new Request($method, $uriInstance);
    }
    /** **********************************************************************
     * Create a new server request.
     *
     * Note that server parameters are taken precisely as given - no parsing/processing
     * of the given values is performed. In particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param   string              $method         The HTTP method associated
     *                                              with the request.
     * @param   UriInterface|string $uri            The URI associated with the request.
     * @param   array               $serverParams   An array of Server API (SAPI)
     *                                              parameters with which to seed
     *                                              the generated request instance.
     *
     * @return  ServerRequestInterface              New server request.
     ************************************************************************/
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $uriInstance = $uri;

        if (!$uriInstance instanceof UriInterface)
        {
            $uriString      = is_string($uri) ? $uri : '';
            $uriInstance    = (new UriFactory)->createUri($uriString);
        }

        return new ServerRequest($method, $uriInstance, $serverParams);
    }
    /** **********************************************************************
     * Create a new response.
     *
     * @param   int     $code               The HTTP status code. Defaults to 200.
     * @param   string  $reasonPhrase       The reason phrase to associate with
     *                                      the status code in the generated response.
     *                                      If none is provided, implementations MAY use
     *                                      the defaults as suggested in the HTTP specification.
     *
     * @return  ResponseInterface           Response.
     ************************************************************************/
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, $reasonPhrase);
    }
}