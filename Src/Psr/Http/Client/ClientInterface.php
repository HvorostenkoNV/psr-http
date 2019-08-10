<?php
declare(strict_types=1);

namespace Psr\Http\Client;

use Psr\Http\{
    Message\ResponseInterface,
    Message\RequestInterface
};
/** ***********************************************************************************************
 * Client interface.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
interface ClientInterface
{
    /** **********************************************************************
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param   RequestInterface $request   Request.
     *
     * @return  ResponseInterface           Response.
     * @throws  ClientExceptionInterface    Error happens while processing the request.
     ************************************************************************/
    public function sendRequest(RequestInterface $request): ResponseInterface;
}