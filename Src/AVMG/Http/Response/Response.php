<?php
declare(strict_types=1);

namespace AVMG\Http\Response;

use
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException,
    Psr\Http\Message\ResponseInterface,
    AVMG\Http\AbstractMessage,
    AVMG\Http\Helper\ResponseStatus;
/** ***********************************************************************************************
 * PSR-7 ResponseInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class Response extends AbstractMessage implements ResponseInterface
{
    private $status         = 0;
    private $reasonPhrase   = '';
    /** **********************************************************************
     * Constructor.
     *
     * @param   int     $code               Status.
     * @param   string  $reasonPhrase       Reason phrase.
     ************************************************************************/
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        try
        {
            $this->status = ResponseStatus::normalize($code);
        }
        catch (NormalizingException $exception)
        {

        }

        $this->reasonPhrase = $reasonPhrase;
    }
    /** **********************************************************************
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int                          Status code.
     ************************************************************************/
    public function getStatusCode(): int
    {
        return $this->status > 0
            ? $this->status
            : ResponseStatus::getStatusOk();
    }
    /** **********************************************************************
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param   int     $code               3-digit integer result code to set.
     * @param   string  $reasonPhrase       The reason phrase to use with the
     *                                      provided status code; if none is provided,
     *                                      implementations MAY use the defaults
     *                                      as suggested in the HTTP specification.
     *
     * @return  ResponseInterface           Instance with the specified status code
     *                                      and, optionally, reason phrase.
     * @throws  InvalidArgumentException    Invalid status code arguments.
     ************************************************************************/
    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        try
        {
            $newInstance = clone $this;

            $newInstance->status        = ResponseStatus::normalize($code);
            $newInstance->reasonPhrase  = $reasonPhrase;

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException("status \"$code\" is invalid");
        }
    }
    /** **********************************************************************
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be empty. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string                       Reason phrase; must return an empty
     *                                      string if none present.
     ************************************************************************/
    public function getReasonPhrase(): string
    {
        return strlen($this->reasonPhrase) > 0
            ? $this->reasonPhrase
            : ResponseStatus::getPhrase($this->status);
    }
}