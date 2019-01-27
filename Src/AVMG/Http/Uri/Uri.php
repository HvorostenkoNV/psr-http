<?php
declare(strict_types=1);

namespace AVMG\Http\Uri;

use
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException,
    Psr\Http\Message\UriInterface,
    AVMG\Http\Helper\Scheme,
    AVMG\Http\Helper\Host,
    AVMG\Http\Helper\Port,
    AVMG\Http\Helper\UriUserInfo,
    AVMG\Http\Helper\UriPath,
    AVMG\Http\Helper\UriQuery,
    AVMG\Http\Helper\UriFragment;
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
        $port       = 0,
        $userInfo   = '',
        $path       = '',
        $query      = '',
        $fragment   = '';
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
        $userInfo   = $this->getUserInfo();
        $host       = $this->getHost();
        $port       = $this->getPort();
        $result     = $host;

        if (strlen($result) <= 0)
        {
            return '';
        }
        if (strlen($userInfo) > 0)
        {
            $result = "$userInfo@$result";
        }
        if (!is_null($port))
        {
            $result = "$result:$port";
        }

        return $result;
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
        return $this->userInfo;
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

        return $port === 0 || Port::isStandard($port, $scheme)
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
     *
     * @return  UriInterface                Instance with the specified scheme.
     * @throws  InvalidArgumentException    Invalid or unsupported schemes.
     ************************************************************************/
    public function withScheme(string $scheme) : UriInterface
    {
        $newInstance = clone $this;

        if ($scheme === '')
        {
            $newInstance->scheme = '';
        }
        else
        {
            try
            {
                $newInstance->scheme = Scheme::normalize($scheme);
            }
            catch (NormalizingException $exception)
            {
                throw new InvalidArgumentException
                (
                    "scheme is invalid: {$exception->getMessage()}",
                    0,
                    $exception
                );
            }
        }

        return $newInstance;
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
     *
     * @return  UriInterface                Instance with the specified user information.
     ************************************************************************/
    public function withUserInfo(string $user, string $password = '') : UriInterface
    {
        $newInstance = clone $this;

        try
        {
            $newInstance->userInfo = UriUserInfo::normalizeFromParts($user, $password);
        }
        catch (NormalizingException $exception)
        {
            $newInstance->userInfo = '';
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
     *
     * @return  UriInterface                Instance with the specified host.
     * @throws  InvalidArgumentException    Invalid hostname.
     ************************************************************************/
    public function withHost(string $host) : UriInterface
    {
        $newInstance = clone $this;

        if ($host === '')
        {
            $newInstance->host = '';
        }
        else
        {
            try
            {
                $newInstance->host = Host::normalize($host);
            }
            catch (NormalizingException $exception)
            {
                throw new InvalidArgumentException("host is invalid: {$exception->getMessage()}");
            }
        }

        return $newInstance;
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
     *
     * @return  UriInterface                Instance with the specified port.
     * @throws  InvalidArgumentException    Invalid port.
     ************************************************************************/
    public function withPort(int $port = 0) : UriInterface
    {
        $newInstance = clone $this;

        if ($port === 0)
        {
            $newInstance->port = 0;
        }
        else
        {
            try
            {
                $newInstance->port = Port::normalize($port);
            }
            catch (NormalizingException $exception)
            {
                throw new InvalidArgumentException("port is invalid: {$exception->getMessage()}");
            }
        }

        return $newInstance;
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
     *
     * @return  UriInterface                Instance with the specified path.
     * @throws  InvalidArgumentException    Invalid paths.
     ************************************************************************/
    public function withPath(string $path) : UriInterface
    {
        try
        {
            $newInstance = clone $this;
            $newInstance->path = UriPath::normalize($path);

            return $newInstance;
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException("path is invalid: {$exception->getMessage()}");
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
     *
     * @return  UriInterface                Instance with the specified query string.
     * @throws  InvalidArgumentException    Invalid query string.
     ************************************************************************/
    public function withQuery(string $query) : UriInterface
    {
        $newInstance = clone $this;

        if ($query === '')
        {
            $newInstance->query = '';
        }
        else
        {
            try
            {
                $newInstance->query = UriQuery::normalize($query);
            }
            catch (NormalizingException $exception)
            {
                throw new InvalidArgumentException("query is invalid: {$exception->getMessage()}");
            }
        }

        return $newInstance;
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
     *
     * @return  UriInterface                Instance with the specified URI fragment.
     ************************************************************************/
    public function withFragment(string $fragment) : UriInterface
    {
        $newInstance = clone $this;

        try
        {
            $newInstance->fragment = UriFragment::normalize($fragment);
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
        $scheme     = $this->getScheme();
        $authority  = $this->getAuthority();
        $path       = $this->getPath();
        $query      = $this->getQuery();
        $fragment   = $this->getFragment();
        $result     = '';

        if (strlen($scheme) > 0)
        {
            $result = "$scheme:";
        }
        if (strlen($authority) > 0)
        {
            $result .= "//$authority";
        }
        if (strlen($path) > 0)
        {
            if (strlen($authority) > 0 && $path[0] != '/')
            {
                $result .= "/$path";
            }
            else
            {
                $result .= $path;
            }
        }
        if (strlen($query) > 0)
        {
            $result .= "?$query";
        }
        if (strlen($fragment) > 0)
        {
            $result .= "#$fragment";
        }

        return $result;
    }
}