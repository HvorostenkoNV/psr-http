<?php
declare(strict_types=1);

namespace AVMG\Http\Uri;

use
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException,
    Psr\Http\Message\UriInterface,
    AVMG\Http\Helper\UriParams;
/** ***********************************************************************************************
 * PSR-7 UriInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Uri implements UriInterface
{
    private
        $scheme     = '',
        $host       = '',
        $port       = null,
        $user       = '',
        $password   = '',
        $path       = '',
        $query      = '',
        $fragment   = '';
    /** **********************************************************************
     * Constructor.
     *
     * @param   string $uri                 Uri.
     ************************************************************************/
    public function __construct(string $uri = '')
    {
        $uriData = $this->parseUrl($uri);

        try
        {
            $scheme         = (string) ($uriData['scheme'] ?? '');
            $this->scheme   = UriParams::normalizeScheme($scheme);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $host       = (string) ($uriData['host'] ?? '');
            $this->host = UriParams::normalizeHost($host);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $port       = (int) ($uriData['port'] ?? 0);
            $this->port = UriParams::normalizePort($port);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $userName   = (string) ($uriData['user'] ?? '');
            $this->user = UriParams::normalizeUserInfo($userName);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $userPass       = (string) ($uriData['pass'] ?? '');
            $this->password = UriParams::normalizeUserInfo($userPass);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $path       = (string) ($uriData['path'] ?? '');
            $this->path = UriParams::normalizePath($path);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $query          = (string) ($uriData['query'] ?? '');
            $this->query    = UriParams::normalizeQuery($query);
        }
        catch (NormalizingException $exception)
        {

        }

        try
        {
            $fragment       = (string) ($uriData['fragment'] ?? '');
            $this->fragment = UriParams::normalizeFragment($fragment);
        }
        catch (NormalizingException $exception)
        {

        }
    }
    /** **********************************************************************
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return  string                      URI scheme.
     ************************************************************************/
    public function getScheme() : string
    {
        return $this->scheme;
    }
    /** **********************************************************************
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return  string                      URI authority, in "[user-info@]host[:port]" format.
     ************************************************************************/
    public function getAuthority() : string
    {
        $userInfo       = $this->getUserInfo();
        $host           = $this->getHost();
        $port           = $this->getPort();
        $userInfoExist  = strlen($userInfo) > 0;
        $hostExist      = strlen($host) > 0;
        $portExist      = !is_null($port);

        if ($userInfoExist && $hostExist && $portExist)
        {
            return "$userInfo@$host:$port";
        }
        if ($userInfoExist && $hostExist && !$portExist)
        {
            return "$userInfo@$host";
        }
        if (!$userInfoExist && $hostExist && $portExist)
        {
            return "$host:$port";
        }
        if ($userInfoExist && !$hostExist && $portExist)
        {
            return $userInfo;
        }

        return $host;
    }
    /** **********************************************************************
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return  string                      URI user information, in "username[:password]" format.
     ************************************************************************/
    public function getUserInfo() : string
    {
        return strlen($this->user) > 0 && strlen($this->password) > 0
            ? "{$this->user}:{$this->password}"
            : $this->user;
    }
    /** **********************************************************************
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return  string                      URI host.
     ************************************************************************/
    public function getHost() : string
    {
        return $this->host;
    }
    /** **********************************************************************
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return  null|int                    URI port.
     ************************************************************************/
    public function getPort() : ?int
    {
        $port   = $this->port;
        $scheme = $this->getScheme();

        return is_null($port) || UriParams::isStandardPort($port, $scheme)
            ? null
            : $port;
    }
    /** **********************************************************************
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntax.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return  string                      URI path.
     ************************************************************************/
    public function getPath() : string
    {
        return $this->path;
    }
    /** **********************************************************************
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return  string                      URI query string.
     ************************************************************************/
    public function getQuery() : string
    {
        return $this->query;
    }
    /** **********************************************************************
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return  string                      URI fragment.
     ************************************************************************/
    public function getFragment() : string
    {
        return $this->fragment;
    }
    /** **********************************************************************
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param   string $scheme              Scheme to use with the new instance.
     * @return  UriInterface                Instance with the specified scheme.
     * @throws  InvalidArgumentException    Invalid or unsupported schemes.
     ************************************************************************/
    public function withScheme(string $scheme) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->scheme = UriParams::normalizeScheme($scheme);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException('scheme is invalid or unsupported');
        }
    }
    /** **********************************************************************
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param   string  $user               User name to use for authority.
     * @param   string  $password           Password associated with $user.
     * @return  UriInterface                Instance with the specified user information.
     ************************************************************************/
    public function withUserInfo(string $user, string $password = '') : UriInterface
    {
        $newInstance = clone $this;

        try
        {
            $newInstance->user = UriParams::normalizeUserInfo($user);
        }
        catch (NormalizingException $exception)
        {
            $newInstance->user = '';
        }

        try
        {
            $newInstance->password = UriParams::normalizeUserInfo($password);
        }
        catch (NormalizingException $exception)
        {
            $newInstance->password = '';
        }

        return $newInstance;
    }
    /** **********************************************************************
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param   string $host                Hostname to use with the new instance.
     * @return  UriInterface                Instance with the specified host.
     * @throws  InvalidArgumentException    Invalid hostname.
     ************************************************************************/
    public function withHost(string $host) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->host = UriParams::normalizeHost($host);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException('hostname is invalid');
        }
    }
    /** **********************************************************************
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param   int $port                   The port to use with the new instance;
     *                                      a zero value removes the port information.
     * @return  UriInterface                Instance with the specified port.
     * @throws  InvalidArgumentException    Invalid port.
     ************************************************************************/
    public function withPort(int $port = 0) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->port = UriParams::normalizePort($port);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException('port is invalid');
        }
    }
    /** **********************************************************************
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntax.
     *
     * If an HTTP path is intended to be host-relative rather than path-relative
     * then it must begin with a slash ("/"). HTTP paths not starting with a slash
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param   string $path                Path to use with the new instance.
     * @return  UriInterface                Instance with the specified path.
     * @throws  InvalidArgumentException    Invalid paths.
     ************************************************************************/
    public function withPath(string $path) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->path = UriParams::normalizePath($path);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException('path is invalid');
        }
    }
    /** **********************************************************************
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param   string $query               Query string to use with the new instance.
     * @return  UriInterface                Instance with the specified query string.
     * @throws  InvalidArgumentException    Invalid query string.
     ************************************************************************/
    public function withQuery(string $query) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->query = UriParams::normalizeQuery($query);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException('query string is invalid');
        }
    }
    /** **********************************************************************
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param   string $fragment            Fragment to use with the new instance.
     * @return  UriInterface                Instance with the specified URI fragment.
     ************************************************************************/
    public function withFragment(string $fragment) : UriInterface
    {
        $newInstance = clone $this;

        try
        {
            $newInstance->fragment = UriParams::normalizeFragment($fragment);
        }
        catch (NormalizingException $exception)
        {
            $newInstance->fragment = '';
        }

        return $newInstance;
    }
    /** **********************************************************************
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return  string                      String representation as a URI reference.
     ************************************************************************/
    public function __toString() : string
    {
        $result = '';

        if (strlen($this->scheme) > 0)
        {
            $result .= "{$this->scheme}://";
        }
        $result .= $this->getAuthority().$this->path;
        if (strlen($this->query) > 0)
        {
            $result .= "?{$this->query}";
        }
        if (strlen($this->fragment) > 0)
        {
            $result .= "#{$this->fragment}";
        }

        return $result;
    }
    /** **********************************************************************
     * Parse URL and get array data.
     *
     * @param   string $uri                 Uri.
     * @return  array                       URL parsed data.
     ************************************************************************/
    private function parseUrl(string $uri = '')
    {
        $uriFiltered    = preg_replace('/\:{1,}\/{2}/', '://', $uri);
        $uriData        =
            [
                'scheme'    => '',
                'host'      => '',
                'port'      => '',
                'user'      => '',
                'pass'      => '',
                'path'      => '',
                'query'     => '',
                'fragment'  => ''
            ];

        if (strpos($uriFiltered, '://') !== false)
        {
            $explode            = explode('://', $uriFiltered);
            $uriData['scheme']  = array_shift($explode);
            $uriFiltered        = implode('://', $explode);
        }
        if (strpos($uriFiltered, '#') !== false)
        {
            $explode                = explode('#', $uriFiltered);
            $uriData['fragment']    = array_pop($explode);
            $uriFiltered            = implode('#', $explode);
        }
        if (strpos($uriFiltered, '?') !== false)
        {
            $explode            = explode('?', $uriFiltered);
            $uriData['query']   = array_pop($explode);
            $uriFiltered        = implode('?', $explode);
        }
        if (strpos($uriFiltered, '@') !== false)
        {
            $explode            = explode('@', $uriFiltered);
            $userInfo           = array_shift($explode);
            $userInfoExplode    = explode(':', $userInfo);
            $uriData['user']    = $userInfoExplode[0];
            $uriData['pass']    = $userInfoExplode[1] ?? '';
            $uriFiltered        = implode('@', $explode);
        }
        if (strpos($uriFiltered, '/') !== false)
        {
            $explode            = explode('/', $uriFiltered);
            $uriFiltered        = array_shift($explode);
            $uriData['path']    = '/'.implode('/', $explode);
        }
        if (strpos($uriFiltered, ':') !== false)
        {
            $explode            = explode(':', $uriFiltered);
            $uriData['port']    = array_pop($explode);
            $uriFiltered        = implode(':', $explode);
        }

        $uriData['host'] = $uriFiltered;

        return $uriData;
    }
}