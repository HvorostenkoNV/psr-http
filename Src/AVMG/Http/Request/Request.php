<?php
declare(strict_types=1);

namespace AVMG\Http\Request;

use Psr\Http\Message\RequestInterface;
/** ***********************************************************************************************
 * PSR-7 RequestInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Request extends AbstractRequest implements RequestInterface
{
    /** **********************************************************************
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     * Represent the headers as a string:
     * foreach ($message->getHeaders() as $name => $values)
     * {
     *     echo $name . ': ' . implode(', ', $values);
     * }
     *
     * Emit headers iteratively:
     * foreach ($message->getHeaders() as $name => $values)
     * {
     *     foreach ($values as $value)
     *     {
     *         header(sprintf('%s: %s', $name, $value), false);
     *     }
     * }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return  string[][]                  Associative array of the message's headers.
     *                                      Each key MUST be a header name, each
     *                                      value MUST be an array of strings
     *                                      for that header.
     ************************************************************************/
    public function getHeaders() : array
    {
        $headers                    = parent::getHeaders();
        $hostHeaderValue            = '';
        $hostHeaderName             = self::HOST_HEADER_NAME;
        $hostHeaderNameLowercase    = strtolower($hostHeaderName);

        foreach ($headers as $headerName => $headerValues)
        {
            if (strtolower($headerName) == $hostHeaderNameLowercase)
            {
                $hostHeaderValue = (string) array_shift($headerValues);
                unset($headers[$headerName]);
                break;
            }
        }

        if (strlen($hostHeaderValue) <= 0)
        {
            $uri                = $this->getUri();
            $hostHeaderValue    = $this->getHostFromUri($uri);
        }

        return array_merge
        (
            [$hostHeaderName => [$hostHeaderValue]],
            $headers
        );
    }
    /** **********************************************************************
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param   string $name                Case-insensitive header field name.
     * @return  bool                        Any header names match the given header
     *                                      name using a case-insensitive string
     *                                      comparison. Returns false if no matching
     *                                      header name is found in the message.
     ************************************************************************/
    public function hasHeader(string $name) : bool
    {
        $hasHeader                  = parent::hasHeader($name);
        $headerNameLowercase        = strtolower($name);
        $hostHeaderNameLowercase    = strtolower(self::HOST_HEADER_NAME);

        if ($headerNameLowercase == $hostHeaderNameLowercase && !$hasHeader)
        {
            $uri                = $this->getUri();
            $hostHeaderValue    = $this->getHostFromUri($uri);
            $hasHeader          = strlen($hostHeaderValue) > 0;
        }

        return $hasHeader;
    }
    /** **********************************************************************
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty array.
     *
     * @param   string $name                Case-insensitive header field name.
     * @return  string[]                    Array of string values as provided
     *                                      for the given header. If the header
     *                                      does not appear in the message,
     *                                      this method MUST return an empty array.
     ************************************************************************/
    public function getHeader(string $name) : array
    {
        $headerValues               = parent::getHeader($name);
        $headerNameLowercase        = strtolower($name);
        $hostHeaderNameLowercase    = strtolower(self::HOST_HEADER_NAME);

        if ($headerNameLowercase == $hostHeaderNameLowercase && count($headerValues) <= 0)
        {
            $uri                = $this->getUri();
            $hostHeaderValue    = $this->getHostFromUri($uri);
            $headerValues       = strlen($hostHeaderValue) > 0
                ? [$hostHeaderValue]
                : [];
        }

        return $headerValues;
    }
}