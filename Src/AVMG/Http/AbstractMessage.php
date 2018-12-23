<?php
declare(strict_types=1);

namespace AVMG\Http;

use
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException,
    Psr\Http\Message\MessageInterface,
    Psr\Http\Message\StreamInterface,
    AVMG\Http\Factory\StreamFactory,
    AVMG\Http\Helper\MessageHeader,
    AVMG\Http\Helper\Protocol;
/** ***********************************************************************************************
 * PSR-7 MessageInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
abstract class AbstractMessage implements MessageInterface
{
    private
        $headers        = [],
        $headerNames    = [],
        $protocol       = '',
        $body           = null;
    /** **********************************************************************
     * Retrieves the HTTP protocol version as a string.
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return  string                      HTTP protocol version.
     ************************************************************************/
    public function getProtocolVersion() : string
    {
        return strlen($this->protocol) > 0
            ? $this->protocol
            : Protocol::getDefault();
    }
    /** **********************************************************************
     * Return an instance with the specified HTTP protocol version.
     * The version string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the immutability
     * of the message, and MUST return an instance that has the new protocol version.
     *
     * @param   string $version             HTTP protocol version.
     * @return  MessageInterface            Instance with the specified HTTP protocol version.
     ************************************************************************/
    public function withProtocolVersion(string $version) : MessageInterface
    {
        $newInstance = clone $this;

        try
        {
            $newInstance->protocol = Protocol::normalize($version);
        }
        catch (NormalizingException $exception)
        {
            $newInstance->protocol = '';
        }

        return $newInstance;
    }
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
        $result = [];

        foreach ($this->headers as $headerName => $headerValues)
        {
            $headerNameTrue = $this->headerNames[$headerName] ?? $headerName;
            $result[$headerNameTrue] = $headerValues;
        }

        return $result;
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
        $headerNameLowercase = strtolower($name);

        return array_key_exists($headerNameLowercase, $this->headers);
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
        $headerNameLowercase = strtolower($name);

        return $this->headers[$headerNameLowercase] ?? [];
    }
    /** **********************************************************************
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given case-insensitive
     * header name as a string concatenated together using a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param   string $name                Case-insensitive header field name.
     * @return  string                      String of values as provided for
     *                                      the given header concatenated together
     *                                      using a comma. If the header does not
     *                                      appear in the message, this method MUST
     *                                      return an empty string.
     ************************************************************************/
    public function getHeaderLine(string $name) : string
    {
        $values = $this->getHeader($name);

        return implode(', ', $values);
    }
    /** **********************************************************************
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param   string          $name       Case-insensitive header field name.
     * @param   string|string[] $value      Header value(s).
     * @return  MessageInterface            Instance with the provided value
     *                                      replacing the specified header.
     * @throws  InvalidArgumentException    Invalid header names or values.
     ************************************************************************/
    public function withHeader(string $name, $value) : MessageInterface
    {
        $headerName             = null;
        $headerNameLowercase    = null;
        $headerValuesRaw        = (array) $value;
        $headerValues           = [];
        $newInstance            = clone $this;

        try
        {
            $headerName             = MessageHeader::normalizeHeaderName($name);
            $headerNameLowercase    = strtolower($headerName);
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException("header name \"$name\" is invalid");
        }

        foreach ($headerValuesRaw as $value)
        {
            try
            {
                $headerValues[] = MessageHeader::normalizeHeaderValue($value);
            }
            catch (NormalizingException $exception)
            {
                throw new InvalidArgumentException("header value \"$value\" is invalid");
            }
        }

        $newInstance->headers[$headerNameLowercase]        = $headerValues;
        $newInstance->headerNames[$headerNameLowercase]    = $headerName;

        return $newInstance;
    }
    /** **********************************************************************
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param   string          $name       Case-insensitive header field name to add.
     * @param   string|string[] $value      Header value(s).
     * @return  MessageInterface            Instance with the specified header
     *                                      appended with the given value.
     * @throws  InvalidArgumentException    Invalid header names or values.
     ************************************************************************/
    public function withAddedHeader(string $name, $value) : MessageInterface
    {
        $headerOldValues    = $this->getHeader($name);
        $headerNewValues    = (array) $value;
        $headerFullValues   = array_merge($headerOldValues, $headerNewValues);

        try
        {
            return $this->withHeader($name, $headerFullValues);
        }
        catch (InvalidArgumentException $exception)
        {
            throw $exception;
        }
    }
    /** **********************************************************************
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param   string $name                Case-insensitive header field name to remove.
     * @return  MessageInterface            Instance without the specified header.
     ************************************************************************/
    public function withoutHeader(string $name) : MessageInterface
    {
        $headerNameLowercase    = strtolower($name);
        $newInstance            = clone $this;

        unset
        (
            $newInstance->headerNames[$headerNameLowercase],
            $newInstance->headers[$headerNameLowercase]
        );

        return $newInstance;
    }
    /** **********************************************************************
     * Gets the body of the message.
     *
     * @return  StreamInterface             Body as a stream.
     ************************************************************************/
    public function getBody() : StreamInterface
    {
        return $this->body instanceof StreamInterface
            ? $this->body
            : (new StreamFactory)->createStream('');
    }
    /** **********************************************************************
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param   StreamInterface $body       Body.
     * @return  MessageInterface            Instance with the specified message body.
     * @throws  InvalidArgumentException    Body is not valid.
     ************************************************************************/
    public function withBody(StreamInterface $body) : MessageInterface
    {
        $newInstance = clone $this;
        $newInstance->body = $body;

        return $newInstance;
    }
}