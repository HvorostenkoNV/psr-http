<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;

use
    Throwable,
    InvalidArgumentException,
    PHPUnit\Framework\TestCase,
    AVMG\Http\Uri\Uri;
/** ***********************************************************************************************
 * PSR-7 UriInterface implementation test.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UriTest extends TestCase
{
    private const SCHEMES_STANDARD_PORTS =
        [
            'http'  => 80,
            'https' => 443
        ];
    /** **********************************************************************
     * Testing Uri setters methods return new instance.
     *
     * @test
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function settersMethodsReturnNewInstance() : void
    {
        $methods =
            [
                'withScheme'    => ['scheme'],
                'withUserInfo'  => ['user', 'password'],
                'withHost'      => ['site.com'],
                'withPort'      => [50],
                'withPath'      => ['/path/'],
                'withQuery'     => ['key=value'],
                'withFragment'  => ['fragment']
            ];

        foreach ($methods as $methodName => $methodArguments)
        {
            $uri    = new Uri;
            $uriNew = call_user_func_array([$uri, $methodName], $methodArguments);

            self::assertNotEquals
            (
                $uri,
                $uriNew,
                "Method \"Uri::$methodName\" returned unexpected result.\n".
                "Expecting result must be new instance of Uri.\n".
                "Caught result is the same instance.\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::withScheme" throws exception with scheme invalid values.
     *
     * @test
     * @dataProvider    schemeInvalidValuesDataProvider
     *
     * @param           string $scheme              Scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function withSchemeExceptionThrowing(string $scheme) : void
    {
        try
        {
            (new Uri)->withScheme($scheme);

            self::fail
            (
                "Method \"Uri::withScheme\" threw no expected exception.\n".
                "Expecting \"InvalidArgumentException\" exception on setting scheme \"$scheme\".\n".
                "Caught no exception.\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::assertTrue(true);
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getScheme" provides valid normalized value.
     *
     * @test
     * @dataProvider    schemeValidValuesDataProvider
     *
     * @param           string  $providedScheme     Provided scheme.
     * @param           string  $expectedScheme     Expected scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getSchemeValueProviding(string $providedScheme, string $expectedScheme) : void
    {
        try
        {
            $caughtScheme = (new Uri)->withScheme($providedScheme)->getScheme();

            self::assertEquals
            (
                $expectedScheme,
                $caughtScheme,
                "Method \"Uri::getScheme\" returned unexpected result.\n".
                "Expecting result after setting scheme \"$providedScheme\" is \"$expectedScheme\".\n".
                "Caught result is \"$caughtScheme\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withScheme\" threw unexpected exception.\n".
                "Expecting no exception on setting scheme \"$providedScheme\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::withHost" throws exception with scheme invalid values.
     *
     * @test
     * @dataProvider    hostInvalidValuesDataProvider
     *
     * @param           string $host                Host.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function withHostExceptionThrowing(string $host) : void
    {
        try
        {
            (new Uri)->withHost($host);

            self::fail
            (
                "Method \"Uri::withHost\" threw no expected exception.\n".
                "Expecting \"InvalidArgumentException\" exception on setting host \"$host\".\n".
                "Caught no exception.\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::assertTrue(true);
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getHost" provides valid normalized value.
     *
     * @test
     * @dataProvider    hostValidValuesDataProvider
     *
     * @param           string  $providedHost       Provided host.
     * @param           string  $expectedHost       Expected host.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getHostValueProviding(string $providedHost, string $expectedHost) : void
    {
        try
        {
            $caughtHost = (new Uri)->withHost($providedHost)->getHost();

            self::assertEquals
            (
                $expectedHost,
                $caughtHost,
                "Method \"Uri::getHost\" returned unexpected result.\n".
                "Expecting result after setting host \"$providedHost\" is \"$expectedHost\".\n".
                "Caught result is \"$caughtHost\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withHost\" threw unexpected exception.\n".
                "Expecting no exception on setting host \"$providedHost\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::withPort" throws exception with scheme invalid values.
     *
     * @test
     * @dataProvider    portInvalidValuesDataProvider
     *
     * @param           int $port                   Port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function withPortExceptionThrowing(int $port) : void
    {
        try
        {
            (new Uri)->withPort($port);

            self::fail
            (
                "Method \"Uri::withPort\" threw no expected exception.\n".
                "Expecting \"InvalidArgumentException\" exception on setting port \"$port\".\n".
                "Caught no exception.\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::assertTrue(true);
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getPort" provides valid normalized value.
     *
     * @test
     * @dataProvider    portValidValuesDataProvider
     *
     * @param           int     $providedPort       Provided port.
     * @param           mixed   $expectedPort       Expected port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getPortValueProviding(int $providedPort, $expectedPort) : void
    {
        try
        {
            $caughtPort = (new Uri)->withPort($providedPort)->getPort();

            self::assertEquals
            (
                $expectedPort,
                $caughtPort,
                "Method \"Uri::getPort\" returned unexpected result.\n".
                "Expecting result after setting port \"$providedPort\" is \"$expectedPort\".\n".
                "Caught result is \"$caughtPort\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withPort\" threw unexpected exception.\n".
                "Expecting no exception on setting port \"$providedPort\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getPort" provides null if port is standard for given scheme.
     *
     * @test
     * @dataProvider    portStandardValuesDataProvider
     *
     * @param           string  $scheme             Scheme.
     * @param           int     $port               Port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getPortValueProvidingForStandardPort(string $scheme, int $port) : void
    {
        try
        {
            $caughtPort = (new Uri)
                ->withScheme($scheme)
                ->withPort($port)
                ->getPort();

            self::assertEquals
            (
                null,
                $caughtPort,
                "Method \"Uri::getPort\" returned unexpected result.\n".
                "Expecting result after setting scheme \"$scheme\" and port \"$port\" is null.\n".
                "Caught result is \"$caughtPort\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withScheme\" or \"Uri::withPort\" threw unexpected exception.\n".
                "Expecting no exception on setting scheme \"$scheme\" and port \"$port\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getUserInfo" provides valid normalized value.
     *
     * @test
     * @dataProvider    userInfoDataProvider
     *
     * @param           string  $login              Login.
     * @param           string  $password           Password.
     * @param           string  $expectedInfo       Expected user info.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getUserInfoValueProviding(string $login, string $password, string $expectedInfo) : void
    {
        $caughtInfo = (new Uri)->withUserInfo($login, $password)->getUserInfo();

        self::assertEquals
        (
            $expectedInfo,
            $caughtInfo,
            "Method \"Uri::getUserInfo\" returned unexpected result.\n".
            "Expecting result after setting login \"$login\" and password \"$password\" is \"$expectedInfo\".\n".
            "Caught result is \"$caughtInfo\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getAuthority" provides valid normalized value.
     *
     * @test
     * @dataProvider    authorityDataProvider
     *
     * @param           string  $login              Login.
     * @param           string  $password           Password.
     * @param           string  $host               Host.
     * @param           int     $port               Port.
     * @param           string  $expectedAuthority  Expected authority.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getAuthorityValueProviding
    (
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $expectedAuthority
    ) : void
    {
        $uri    = new Uri;
        $uri    = $uri->withUserInfo($login, $password);

        try
        {
            $uri = $uri->withHost($host);
        }
        catch (InvalidArgumentException $exception)
        {

        }
        try
        {
            $uri = $uri->withPort($port);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        $caughtAuthority = $uri->getAuthority();

        self::assertEquals
        (
            $expectedAuthority,
            $caughtAuthority,
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
            "Expecting result after setting login \"$login\", password \"$password\", ".
            "host \"$host\" and port \"$port\" is \"$expectedAuthority\".\n".
            "Caught authority is \"$caughtAuthority\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::withPath" throws exception with path invalid values.
     *
     * @test
     * @dataProvider    pathInvalidValuesDataProvider
     *
     * @param           string $path                Path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function withPathExceptionThrowing(string $path) : void
    {
        try
        {
            (new Uri)->withPath($path);

            self::fail
            (
                "Method \"Uri::withPath\" threw no expected exception.\n".
                "Expecting \"InvalidArgumentException\" exception on setting path \"$path\".\n".
                "Caught no exception.\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::assertTrue(true);
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getPath" provides valid normalized value.
     *
     * @test
     * @dataProvider    pathValidValuesDataProvider
     *
     * @param           string  $providedPath       Provided path.
     * @param           string  $expectedPath       Expected path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getPathValueProviding(string $providedPath, string $expectedPath) : void
    {
        try
        {
            $caughtPath = (new Uri)->withPath($providedPath)->getPath();

            self::assertEquals
            (
                $expectedPath,
                $caughtPath,
                "Method \"Uri::getPath\" returned unexpected result.\n".
                "Expecting result after setting path \"$providedPath\" is \"$expectedPath\".\n".
                "Caught result is \"$caughtPath\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withPath\" threw unexpected exception.\n".
                "Expecting no exception on setting path \"$providedPath\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::withQuery" throws exception with query invalid values.
     *
     * @test
     * @dataProvider    queryInvalidValuesDataProvider
     *
     * @param           string $query               Query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function withQueryExceptionThrowing(string $query) : void
    {
        try
        {
            (new Uri)->withQuery($query);

            self::fail
            (
                "Method \"Uri::withQuery\" threw no expected exception.\n".
                "Expecting \"InvalidArgumentException\" exception on setting query \"$query\".\n".
                "Caught no exception.\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::assertTrue(true);
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getQuery" provides valid normalized value.
     *
     * @test
     * @dataProvider    queryValidValuesDataProvider
     *
     * @param           string  $providedQuery      Provided query.
     * @param           string  $expectedQuery      Expected query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getQueryValueProviding(string $providedQuery, string $expectedQuery) : void
    {
        try
        {
            $caughtQuery = (new Uri)->withQuery($providedQuery)->getQuery();

            self::assertEquals
            (
                $expectedQuery,
                $caughtQuery,
                "Method \"Uri::getQuery\" returned unexpected result.\n".
                "Expecting result after setting query \"$providedQuery\" is \"$expectedQuery\".\n".
                "Caught result is \"$caughtQuery\".\n"
            );
        }
        catch (InvalidArgumentException $exception)
        {
            self::fail
            (
                "Method \"Uri::withQuery\" threw unexpected exception.\n".
                "Expecting no exception on setting query \"$providedQuery\".\n".
                "Caught exception with message \"{$exception->getMessage()}\".\n"
            );
        }
    }
    /** **********************************************************************
     * Testing method "Uri::getFragment" provides valid normalized value.
     *
     * @test
     * @dataProvider    fragmentDataProvider
     *
     * @param           string  $providedFragment   Provided fragment.
     * @param           string  $expectedFragment   Expected fragment.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getFragmentValueProviding(string $providedFragment, string $expectedFragment) : void
    {
        $caughtFragment = (new Uri)->withFragment($providedFragment)->getFragment();

        self::assertEquals
        (
            $expectedFragment,
            $caughtFragment,
            "Method \"Uri::getFragment\" returned unexpected result.\n".
            "Expecting result after setting fragment \"$providedFragment\" is \"$expectedFragment\".\n".
            "Caught result is \"$caughtFragment\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::__toString" provides valid normalized value.
     *
     * @test
     * @dataProvider    uriDataProvider
     *
     * @param           string  $scheme             Scheme.
     * @param           string  $login              Login.
     * @param           string  $password           Password.
     * @param           string  $host               Host.
     * @param           int     $port               Port.
     * @param           string  $path               Path.
     * @param           string  $query              Query.
     * @param           string  $fragment           Fragment.
     * @param           string  $expectedUri        Expected uri string.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function toStringValueProviding
    (
        string  $scheme,
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $path,
        string  $query,
        string  $fragment,
        string  $expectedUri
    ) : void
    {
        $uri = new Uri;

        try
        {
            $uri = $uri->withScheme($scheme);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        $uri = $uri->withUserInfo($login, $password);

        try
        {
            $uri = $uri->withHost($host);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        try
        {
            $uri = $uri->withPort($port);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        try
        {
            $uri = $uri->withPath($path);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        try
        {
            $uri = $uri->withQuery($query);
        }
        catch (InvalidArgumentException $exception)
        {

        }

        $uri        = $uri->withFragment($fragment);
        $caughtUri  = (string) $uri;

        self::assertEquals
        (
            $expectedUri,
            $caughtUri,
            "Method \"Uri::__toString\" returned unexpected result.\n".
            "Expecting result after setting scheme \"$scheme\", login \"$login\", ".
            "password \"$password\", host \"$host\", port \"$port\", path \"$path\", ".
            "query \"$query\", fragment \"$fragment\" is \"$expectedUri\".\n".
            "Caught result is \"$caughtUri\".\n"
        );
    }
    /** **********************************************************************
     * Scheme valid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function schemeValidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getSchemeValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = [$providedValue, $expectedValue];
            }
        }

        $result[] = ['', ''];

        return $result;
    }
    /** **********************************************************************
     * Scheme invalid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function schemeInvalidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getSchemeValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = [$providedValue];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Host valid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function hostValidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getHostValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = [$providedValue, $expectedValue];
            }
        }

        $result[] = ['', ''];

        return $result;
    }
    /** **********************************************************************
     * Host invalid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function hostInvalidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getHostValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = [$providedValue];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Port valid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function portValidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getPortValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue) && $expectedValue !== 0)
            {
                $result[] = [$providedValue, $expectedValue];
            }
        }

        $result[] = [0, null];

        return $result;
    }
    /** **********************************************************************
     * Port invalid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function portInvalidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getPortValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = [$providedValue];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Standard ports for scheme data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function portStandardValuesDataProvider() : array
    {
        $result = [];

        foreach (self::SCHEMES_STANDARD_PORTS as $scheme => $port)
        {
            $result[] = [$scheme, $port];
        }

        return $result;
    }
    /** **********************************************************************
     * User info data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function userInfoDataProvider() : array
    {
        $values = UriDataGenerator::getUserInfoValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            $explode    = explode(':', $providedValue);
            $result[]   =
                [
                    $explode[0],
                    $explode[1] ?? '',
                    !is_null($expectedValue) ? $expectedValue : ''
                ];
        }

        $result[] = ['', '', ''];

        return $result;
    }
    /** **********************************************************************
     * Authority data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function authorityDataProvider() : array
    {
        $userInfoValues = UriDataGenerator::getUserInfoValues();
        $hostValues     = UriDataGenerator::getHostValues();
        $portValues     = UriDataGenerator::getPortValues();
        $result         = [];

        foreach ($userInfoValues as $providedValue => $expectedValue)
        {
            $explode    = explode(':', $providedValue);
            $login      = $explode[0];
            $password   = $explode[1] ?? '';
            $result[]   =
                [
                    $login,
                    $password,
                    'site.com',
                    123,
                    !is_null($expectedValue)
                        ? "$expectedValue@site.com:123"
                        : 'site.com:123'
                ];
        }

        foreach ($hostValues as $providedValue => $expectedValue)
        {
            $result[] =
                [
                    'user',
                    'password',
                    $providedValue,
                    123,
                    !is_null($expectedValue)
                        ? "user:password@$expectedValue:123"
                        : ''
                ];
        }

        foreach ($portValues as $providedValue => $expectedValue)
        {
            $result[] =
                [
                    'user',
                    'password',
                    'site.com',
                    $providedValue,
                    !is_null($expectedValue) && $expectedValue !== 0
                        ? "user:password@site.com:$expectedValue"
                        : 'user:password@site.com'
                ];
        }

        return $result;
    }
    /** **********************************************************************
     * Path valid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function pathValidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getPathValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = [$providedValue, $expectedValue];
            }
        }

        $result[] = ['', ''];

        return $result;
    }
    /** **********************************************************************
     * Path invalid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function pathInvalidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getPathValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = [$providedValue];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Query valid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function queryValidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getQueryValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = [$providedValue, $expectedValue];
            }
        }

        $result[] = ['', ''];

        return $result;
    }
    /** **********************************************************************
     * Query invalid values data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function queryInvalidValuesDataProvider() : array
    {
        $values = UriDataGenerator::getQueryValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = [$providedValue];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Fragment data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function fragmentDataProvider() : array
    {
        $values = UriDataGenerator::getFragmentValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            $result[] = [$providedValue, !is_null($expectedValue) ? $expectedValue : ''];
        }

        $result[] = ['', ''];

        return $result;
    }
    /** **********************************************************************
     * Uri data provider.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function uriDataProvider() : array
    {
        return
            [
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    123,
                    'path',
                    'key=value',
                    'fragment',
                    'scheme://login:password@site.com:123/path?key=value#fragment'
                ],
                [
                    '',
                    'login',
                    'password',
                    'site.com',
                    123,
                    'path',
                    'key=value',
                    'fragment',
                    '//login:password@site.com:123/path?key=value#fragment'
                ],
                [
                    'scheme',
                    '',
                    'password',
                    'site.com',
                    123,
                    'path',
                    'key=value',
                    'fragment',
                    'scheme://site.com:123/path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    '',
                    'site.com',
                    123,
                    'path',
                    'key=value',
                    'fragment',
                    'scheme://login@site.com:123/path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    '',
                    123,
                    'path',
                    'key=value',
                    'fragment',
                    'scheme:path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    '',
                    123,
                    '/path',
                    'key=value',
                    'fragment',
                    'scheme:/path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    0,
                    'path',
                    'key=value',
                    'fragment',
                    'scheme://login:password@site.com/path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    123,
                    '',
                    'key=value',
                    'fragment',
                    'scheme://login:password@site.com:123?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    123,
                    '/path',
                    'key=value',
                    'fragment',
                    'scheme://login:password@site.com:123/path?key=value#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    123,
                    'path',
                    '',
                    'fragment',
                    'scheme://login:password@site.com:123/path#fragment'
                ],
                [
                    'scheme',
                    'login',
                    'password',
                    'site.com',
                    123,
                    'path',
                    'key=value',
                    '',
                    'scheme://login:password@site.com:123/path?key=value'
                ],
            ];
    }
}