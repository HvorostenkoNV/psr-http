<?php
declare(strict_types=1);

namespace AVMG\Http\Request;

use
    InvalidArgumentException,
    Psr\Http\Message\UriInterface,
    Psr\Http\Message\UploadedFileInterface,
    Psr\Http\Message\ServerRequestInterface;
/** ***********************************************************************************************
 * PSR-7 ServerRequestInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class ServerRequest extends AbstractRequest implements ServerRequestInterface
{
    private $serverParams   = [];
    private $cookiesParams  = [];
    private $queryParams    = [];
    private $uploadedFiles  = [];
    private $parsedBody     = null;
    private $attributes     = [];
    /** **********************************************************************
     * Constructor.
     *
     * @param   string          $method         HTTP request method.
     * @param   UriInterface    $uri            URI for the request.
     * @param   array           $serverParams   Server parameters.
     ************************************************************************/
    public function __construct(string $method, UriInterface $uri, array $serverParams = [])
    {
        parent::__construct($method, $uri);

        $this->serverParams = $serverParams;
    }
    /** **********************************************************************
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return  array                           Server parameters.
     ************************************************************************/
    public function getServerParams(): array
    {
        return $this->serverParams;
    }
    /** **********************************************************************
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     * The data MUST be compatible with the structure of the $_COOKIE superglobal.
     *
     * @return  array                           Cookies.
     ************************************************************************/
    public function getCookieParams(): array
    {
        return $this->cookiesParams;
    }
    /** **********************************************************************
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param   array $cookies                  Array of key/value pairs representing cookies.
     *
     * @return  ServerRequestInterface          Instance with the specified cookies.
     ************************************************************************/
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $newInstance = clone $this;
        $newInstance->cookiesParams = $cookies;

        return $newInstance;
    }
    /** **********************************************************************
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return  array                           Query string arguments.
     ************************************************************************/
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }
    /** **********************************************************************
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param   array $query                    Array of query string arguments,
     *                                          typically from $_GET.
     *
     * @return  ServerRequestInterface          Instance with the specified query string arguments.
     ************************************************************************/
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $newInstance = clone $this;
        $newInstance->queryParams = $query;

        return $newInstance;
    }
    /** **********************************************************************
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return  array                           Array tree of UploadedFileInterface instances;
     *                                          an empty array MUST be returned if no data is present.
     ************************************************************************/
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }
    /** **********************************************************************
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param   array $uploadedFiles            Array tree of UploadedFileInterface instances.
     *
     * @return  ServerRequestInterface          Instance with the specified uploaded files.
     * @throws  InvalidArgumentException        Invalid structure is provided.
     ************************************************************************/
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        try
        {
            $this->checkUploadedFilesTree($uploadedFiles);
        }
        catch (InvalidArgumentException $exception)
        {
            throw new InvalidArgumentException('uploaded files structure is invalid');
        }

        $newInstance = clone $this;
        $newInstance->uploadedFiles = $uploadedFiles;

        return $newInstance;
    }
    /** **********************************************************************
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return  null|array|object               The deserialized body parameters, if any.
     *                                          These will typically be an array or object.
     ************************************************************************/
    public function getParsedBody()
    {
        return $this->parsedBody;
    }
    /** **********************************************************************
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param   null|array|object $data         The deserialized body data. This will
     *                                          typically be in an array or object.
     *
     * @return  ServerRequestInterface          Instance with the specified body parameters.
     * @throws  InvalidArgumentException        Unsupported argument type is provided.
     ************************************************************************/
    public function withParsedBody($data): ServerRequestInterface
    {
        $dataType = gettype($data);

        switch ($dataType)
        {
            case 'NULL':
            case 'array':
            case 'object':
                $newInstance = clone $this;
                $newInstance->parsedBody = $data;

                return $newInstance;
            default:
                throw new InvalidArgumentException("body data has to be null, array or object;\"$dataType\" caught");
        }
    }
    /** **********************************************************************
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return  mixed[]                         Attributes derived from the request.
     ************************************************************************/
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    /** **********************************************************************
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     *
     * @param   string  $name                   Attribute name.
     * @param   mixed   $default                Default value to return if the attribute
     *                                          does not exist.
     *
     * @return  mixed                           Derived request attribute.
     ************************************************************************/
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }
    /** **********************************************************************
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     *
     * @param   string  $name                   Attribute name.
     * @param   mixed   $value                  Value of the attribute.
     *
     * @return  ServerRequestInterface          Instance with the specified derived
     *                                          request attribute.
     ************************************************************************/
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $newInstance = clone $this;
        $newInstance->attributes[$name] = $value;

        return $newInstance;
    }
    /** **********************************************************************
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     *
     * @param   string $name                    Attribute name.
     *
     * @return  ServerRequestInterface          Instance that removes the specified
     *                                          derived request attribute.
     ************************************************************************/
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $newInstance = clone $this;
        unset($newInstance->attributes[$name]);

        return $newInstance;
    }
    /** **********************************************************************
     * Check uploaded files is valid.
     *
     * @param   array $files                Uploaded files.
     *
     * @return  void
     * @throws  InvalidArgumentException    Validating error.
     ************************************************************************/
    private function checkUploadedFilesTree(array $files): void
    {
        foreach ($files as $value)
        {
            if (is_array($value))
            {
                $this->checkUploadedFilesTree($value);
            }
            elseif (!$value instanceof UploadedFileInterface)
            {
                throw new InvalidArgumentException;
            }
        }
    }
}