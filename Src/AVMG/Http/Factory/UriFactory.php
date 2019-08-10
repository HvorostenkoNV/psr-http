<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\{
    UriInterface,
    UriFactoryInterface
};
use AVMG\Http\Uri\Uri;

use function strlen;
use function strpos;
use function substr;
use function strripos;
use function explode;
use function is_numeric;
/** ***********************************************************************************************
 * PSR-7 UriFactoryInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriFactory implements UriFactoryInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function createUri(string $uri = ''): UriInterface
    {
        if ($uri === '') {
            return new Uri();
        }

        try {
            $uriParsedData = $this->parseUriString($uri);

            return (new Uri())
                ->withScheme($uriParsedData['scheme'])
                ->withUserInfo($uriParsedData['user'], $uriParsedData['pass'])
                ->withHost($uriParsedData['host'])
                ->withPort($uriParsedData['port'])
                ->withPath($uriParsedData['path'])
                ->withQuery($uriParsedData['query'])
                ->withFragment($uriParsedData['fragment']);
        } catch (RuntimeException $exception) {
            throw new InvalidArgumentException("uri \"$uri\" cannot be parsed", 0, $exception);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException("uri \"$uri\" building error", 0, $exception);
        }
    }
    /** **********************************************************************
     * Parse URI string.
     *
     * @param   string $uri                 The URI to parse.
     *
     * @return  array                       URI data.
     * @throws  RuntimeException            Parsing error.
     ************************************************************************/
    private function parseUriString(string $uri): array
    {
        $scheme     = $this->parseSchemeFromUri($uri);
        $fragment   = $this->parseFragmentFromUri($uri);
        $query      = $this->parseQueryFromUri($uri);
        $path       = $uri;
        $authority  = $this->parseAuthorityFromPath($path);
        $userData   = $this->parseUserDataFromAuthority($authority);
        $port       = $this->parsePortFromAuthority($authority);
        $host       = $authority;

        if (strlen($scheme) === 0 && strlen($host) === 0 && strlen($path) === 0) {
            throw new RuntimeException("uri \"$uri\" has no scheme, no host and no path");
        }

        return [
            'scheme'    => $scheme,
            'user'      => $userData['login'],
            'pass'      => $userData['password'],
            'host'      => $host,
            'port'      => $port,
            'path'      => $path,
            'query'     => $query,
            'fragment'  => $fragment
        ];
    }
    /** **********************************************************************
     * Parse scheme from URI string.
     * URI string will be changed.
     *
     * @param   string $uri                 The URI string link.
     *
     * @return  string                      Scheme.
     ************************************************************************/
    private function parseSchemeFromUri(string &$uri): string
    {
        $colonFirstCharPosition     = strpos($uri, ':');
        $delimiterCharFirstPosition = strpos($uri, '/');
        $bracerCharFirstPosition    = strpos($uri, '[');
        $scheme                     = '';

        if (
            $colonFirstCharPosition !== false && (
                $delimiterCharFirstPosition === false ||
                $colonFirstCharPosition < $delimiterCharFirstPosition
            ) && (
                $bracerCharFirstPosition === false ||
                $colonFirstCharPosition < $bracerCharFirstPosition
            )
        ) {
            $explode    = explode(':', $uri, 2);
            $uri        = $explode[1];
            $scheme     = $explode[0];
        }

        return $scheme;
    }
    /** **********************************************************************
     * Parse authority from path string.
     * Path string will be changed.
     *
     * @param   string $path                The path string link.
     *
     * @return  string                      Authority.
     ************************************************************************/
    private function parseAuthorityFromPath(string &$path): string
    {
        $authority = '';

        if (strpos($path, '//') === 0) {
            $path = substr($path, 2);

            if (strpos($path, '/') !== false) {
                $explode    = explode('/', $path, 2);
                $path       = '/'.$explode[1];
                $authority  = $explode[0];
            } else {
                $authority  = $path;
                $path       = '';
            }
        }

        return $authority;
    }
    /** **********************************************************************
     * Parse user data from authority string.
     * Authority string will be changed.
     *
     * @param   string $authority           The authority string link.
     *
     * @return  array                       User data array, login and password.
     ************************************************************************/
    private function parseUserDataFromAuthority(string &$authority): array
    {
        $userData = [
            'login'     => '',
            'password'  => ''
        ];

        if (strpos($authority, '@') !== false) {
            $explode                = explode('@', $authority, 2);
            $authority              = $explode[1];
            $userDataString         = $explode[0];
            $userDataStringExploded = explode(':', $userDataString, 2);
            $userData['login']      = $userDataStringExploded[0];
            $userData['password']   = $userDataStringExploded[1] ?? '';
        }

        return $userData;
    }
    /** **********************************************************************
     * Parse port from authority string.
     * Authority string will be changed.
     *
     * @param   string $authority           The authority string link.
     *
     * @return  int                         Port.
     ************************************************************************/
    private function parsePortFromAuthority(string &$authority): int
    {
        $colonCharLastPosition  = strripos($authority, ':');
        $bracerCharLastPosition = strripos($authority, ']');
        $port                   = 0;

        if (
            $colonCharLastPosition !== false && (
                $bracerCharLastPosition === false ||
                $colonCharLastPosition > $bracerCharLastPosition
            )
        ) {
            $portValue          = substr($authority, $colonCharLastPosition + 1);
            $portValueIsValid   = is_numeric($portValue) && strpos($portValue, '.') === false;
            $portValueIsEmpty   = $portValue === '';

            if ($portValueIsValid) {
                $port = (int) $portValue;
            }
            if ($portValueIsValid || $portValueIsEmpty) {
                $authority = substr($authority, 0, $colonCharLastPosition);
            }
        }

        return $port;
    }
    /** **********************************************************************
     * Parse query from URI string.
     * URI string will be changed.
     *
     * @param   string $uri                 The URI string link.
     *
     * @return  string                      Query.
     ************************************************************************/
    private function parseQueryFromUri(string &$uri): string
    {
        $query = '';

        if (strpos($uri, '?') !== false) {
            $explode    = explode('?', $uri, 2);
            $uri        = $explode[0];
            $query      = $explode[1];
        }

        return $query;
    }
    /** **********************************************************************
     * Parse fragment from URI string.
     * URI string will be changed.
     *
     * @param   string $uri                 The URI string link.
     *
     * @return  string                      Fragment.
     ************************************************************************/
    private function parseFragmentFromUri(string &$uri): string
    {
        $fragment = '';

        if (strpos($uri, '#') !== false) {
            $explode    = explode('#', $uri, 2);
            $uri        = $explode[0];
            $fragment   = $explode[1];
        }

        return $fragment;
    }
}