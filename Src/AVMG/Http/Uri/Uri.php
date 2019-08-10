<?php
declare(strict_types=1);

namespace AVMG\Http\Uri;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use AVMG\Http\{
    Normalizer\NormalizingException,
    Normalizer\NormalizerProxy,
    Collection\CollectionProxy
};

use function is_null;
use function strlen;
/** ***********************************************************************************************
 * PSR-7 UriInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class Uri implements UriInterface
{
    private $scheme     = '';
    private $host       = '';
    private $port       = 0;
    private $userInfo   = '';
    private $path       = '';
    private $query      = '';
    private $fragment   = '';
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getScheme(): string
    {
        return $this->scheme;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getAuthority(): string
    {
        $userInfo   = $this->getUserInfo();
        $host       = $this->getHost();
        $port       = $this->getPort();
        $result     = $host;

        if (strlen($host) === 0) {
            return '';
        }
        if (strlen($userInfo) > 0) {
            $result = "$userInfo@$result";
        }
        if (!is_null($port)) {
            $result = "$result:$port";
        }

        return $result;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getHost(): string
    {
        return $this->host;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getPort(): ?int
    {
        $port = $this->port;

        try {
            $scheme         = $this->getScheme();
            $standardPorts  = CollectionProxy::receive('uri.standardPorts');
            $portIsStandard = isset($standardPorts[$port]) && $standardPorts[$port] === $scheme;
        } catch (InvalidArgumentException $exception) {
            $portIsStandard = false;
        }

        return $port === 0 || $portIsStandard ? null : $port;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getPath(): string
    {
        return $this->path;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getQuery(): string
    {
        return $this->query;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getFragment(): string
    {
        return $this->fragment;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withScheme(string $scheme): UriInterface
    {
        $newInstance = clone $this;

        if ($scheme === '') {
            $newInstance->scheme = '';
        } else {
            try {
                $newInstance->scheme = NormalizerProxy::normalize('uri.scheme', $scheme);
            } catch (InvalidArgumentException | NormalizingException $exception) {
                throw new InvalidArgumentException("scheme \"$scheme\" is invalid", 0, $exception);
            }
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withUserInfo(string $user, string $password = ''): UriInterface
    {
        $newInstance = clone $this;

        try {
            $newInstance->userInfo = NormalizerProxy::normalize('uri.userInfo', "$user:$password");
        } catch (InvalidArgumentException | NormalizingException $exception) {
            $newInstance->userInfo = '';
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withHost(string $host): UriInterface
    {
        $newInstance = clone $this;

        if ($host === '') {
            $newInstance->host = '';
        } else {
            try {
                $newInstance->host = NormalizerProxy::normalize('uri.host', $host);
            } catch (InvalidArgumentException | NormalizingException $exception) {
                throw new InvalidArgumentException("host \"$host\" is invalid", 0, $exception);
            }
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withPort(int $port = 0): UriInterface
    {
        $newInstance = clone $this;

        if ($port === 0) {
            $newInstance->port = 0;
        } else {
            try {
                $newInstance->port = NormalizerProxy::normalize('uri.port', $port);
            } catch (InvalidArgumentException | NormalizingException $exception) {
                throw new InvalidArgumentException("port \"$port\" is invalid", 0, $exception);
            }
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withPath(string $path): UriInterface
    {
        try {
            $newInstance = clone $this;
            $newInstance->path = NormalizerProxy::normalize('uri.path', $path);

            return $newInstance;
        } catch (InvalidArgumentException | NormalizingException $exception) {
            throw new InvalidArgumentException("path \"$path\" is invalid", 0, $exception);
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withQuery(string $query): UriInterface
    {
        $newInstance = clone $this;

        if ($query === '') {
            $newInstance->query = '';
        } else {
            try {
                $newInstance->query = NormalizerProxy::normalize('uri.query', $query);
            } catch (InvalidArgumentException | NormalizingException $exception) {
                throw new InvalidArgumentException("query \"$query\" is invalid", 0, $exception);
            }
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function withFragment(string $fragment): UriInterface
    {
        $newInstance = clone $this;

        try {
            $newInstance->fragment = NormalizerProxy::normalize('uri.fragment', $fragment);
        } catch (InvalidArgumentException | NormalizingException $exception) {
            $newInstance->fragment = '';
        }

        return $newInstance;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function __toString(): string
    {
        $scheme     = $this->getScheme();
        $authority  = $this->getAuthority();
        $path       = $this->getPath();
        $query      = $this->getQuery();
        $fragment   = $this->getFragment();
        $result     = '';

        if (strlen($scheme) > 0) {
            $result = "$scheme:";
        }
        if (strlen($authority) > 0) {
            $result .= "//$authority";
        }
        if (strlen($path) > 0) {
            $result .= strlen($authority) > 0 && $path[0] != '/'
                ? "/$path"
                : $path;
        }
        if (strlen($query) > 0) {
            $result .= "?$query";
        }
        if (strlen($fragment) > 0) {
            $result .= "#$fragment";
        }

        return $result;
    }
}