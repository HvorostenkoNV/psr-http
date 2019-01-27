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
     * Test URI setters methods return new instance.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testSettersMethodsReturnNewInstance() : void
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
                "Expected result must be new instance of Uri.\n".
                "Caught result is the same instance.\n"
            );
        }
    }
    /** **********************************************************************
     * Test "Uri::withScheme" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderSchemeInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $scheme              Scheme.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWithSchemeThrowsException(string $scheme) : void
    {
        (new Uri)->withScheme($scheme);

        self::fail
        (
            "Method \"Uri::withScheme\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting scheme \"$scheme\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::withHost" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderHostInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $host                Host.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWithHostThrowsException(string $host) : void
    {
        (new Uri)->withHost($host);

        self::fail
        (
            "Method \"Uri::withHost\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting host \"$host\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::withPort" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderPortInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               int $port                   Port.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWithPortThrowsException(int $port) : void
    {
        (new Uri)->withPort($port);

        self::fail
        (
            "Method \"Uri::withPort\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting port \"$port\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::withPath" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderPathInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $path                Path.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWithPathThrowsException(string $path) : void
    {
        (new Uri)->withPath($path);

        self::fail
        (
            "Method \"Uri::withPath\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting path \"$path\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::withQuery" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderQueryInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $query               Query.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWithQueryThrowsException(string $query) : void
    {
        (new Uri)->withQuery($query);

        self::fail
        (
            "Method \"Uri::withQuery\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting query \"$query\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getScheme" provides valid normalized value.
     *
     * @dataProvider    dataProviderSchemeValidValues
     *
     * @param           string  $scheme                 Scheme.
     * @param           string  $normalizedScheme       Normalized scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetScheme(string $scheme, string $normalizedScheme) : void
    {
        $caughtScheme = (new Uri)->withScheme($scheme)->getScheme();

        self::assertEquals
        (
            $normalizedScheme,
            $caughtScheme,
            "Method \"Uri::getScheme\" returned unexpected result.\n".
            "Expected result after setting scheme \"$scheme\" is \"$normalizedScheme\".\n".
            "Caught result is \"$caughtScheme\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getHost" provides valid normalized value.
     *
     * @dataProvider    dataProviderHostValidValues
     *
     * @param           string  $host                   Host.
     * @param           string  $normalizedHost         Normalized host.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetHost(string $host, string $normalizedHost) : void
    {
        $caughtHost = (new Uri)->withHost($host)->getHost();

        self::assertEquals
        (
            $normalizedHost,
            $caughtHost,
            "Method \"Uri::getHost\" returned unexpected result.\n".
            "Expected result after setting host \"$host\" is \"$normalizedHost\".\n".
            "Caught result is \"$caughtHost\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getPort" provides valid normalized value.
     *
     * @dataProvider    dataProviderPortValidValues
     *
     * @param           int     $port                   Port.
     * @param           mixed   $normalizedPort         Normalized port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetPort(int $port, $normalizedPort) : void
    {
        $caughtPort = (new Uri)->withPort($port)->getPort();

        self::assertEquals
        (
            $normalizedPort,
            $caughtPort,
            "Method \"Uri::getPort\" returned unexpected result.\n".
            "Expected result after setting port \"$port\" is \"$normalizedPort\".\n".
            "Caught result is \"$caughtPort\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getPort" provides null if port is standard for given scheme.
     *
     * @dataProvider    dataProviderSchemeStandardPorts
     *
     * @param           string  $scheme                 Scheme.
     * @param           int     $port                   Port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetPortWithStandardPort(string $scheme, int $port) : void
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
            "Expected result after setting scheme \"$scheme\" and port \"$port\" is null.\n".
            "Caught result is \"$caughtPort\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getUserInfo" provides valid normalized value.
     *
     * @dataProvider    dataProviderUserInfo
     *
     * @param           string  $login                  Login.
     * @param           string  $password               Password.
     * @param           string  $normalizedInfo         Normalized user info.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetUserInfo
    (
        string  $login,
        string  $password,
        string  $normalizedInfo
    ) : void
    {
        $caughtInfo = (new Uri)->withUserInfo($login, $password)->getUserInfo();

        self::assertEquals
        (
            $normalizedInfo,
            $caughtInfo,
            "Method \"Uri::getUserInfo\" returned unexpected result.\n".
            "Expected result after setting login \"$login\" and password \"$password\"".
            " is \"$normalizedInfo\".\n".
            "Caught result is \"$caughtInfo\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getAuthority" provides valid normalized value.
     *
     * @dataProvider    dataProviderAuthority
     *
     * @param           string  $login                  Login.
     * @param           string  $password               Password.
     * @param           string  $host                   Host.
     * @param           int     $port                   Port.
     * @param           string  $normalizedAuthority    Normalized authority.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetAuthority
    (
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $normalizedAuthority
    ) : void
    {
        $uri = (new Uri)->withUserInfo($login, $password);

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
            $normalizedAuthority,
            $caughtAuthority,
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
            "Expected result after setting login \"$login\", password \"$password\", ".
            "host \"$host\" and port \"$port\" is \"$normalizedAuthority\".\n".
            "Caught authority is \"$caughtAuthority\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getPath" provides valid normalized value.
     *
     * @dataProvider    dataProviderPathValidValues
     *
     * @param           string  $path                   Path.
     * @param           string  $normalizedPath         Normalized path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetPath(string $path, string $normalizedPath) : void
    {
        $caughtPath = (new Uri)
            ->withPath($path)
            ->getPath();

        self::assertEquals
        (
            $normalizedPath,
            $caughtPath,
            "Method \"Uri::getPath\" returned unexpected result.\n".
            "Expected result after setting path \"$path\" is \"$normalizedPath\".\n".
            "Caught result is \"$caughtPath\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getQuery" provides valid normalized value.
     *
     * @dataProvider    dataProviderQueryValidValues
     *
     * @param           string  $query                  Query.
     * @param           string  $normalizedQuery        Normalized query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetQuery(string $query, string $normalizedQuery) : void
    {
        $caughtQuery = (new Uri)->withQuery($query)->getQuery();

        self::assertEquals
        (
            $normalizedQuery,
            $caughtQuery,
            "Method \"Uri::getQuery\" returned unexpected result.\n".
            "Expected result after setting query \"$query\" is \"$normalizedQuery\".\n".
            "Caught result is \"$caughtQuery\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getFragment" provides valid normalized value.
     *
     * @dataProvider    dataProviderFragment
     *
     * @param           string  $fragment               Fragment.
     * @param           string  $normalizedFragment     Normalized fragment.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetFragment(string $fragment, string $normalizedFragment) : void
    {
        $caughtFragment = (new Uri)->withFragment($fragment)->getFragment();

        self::assertEquals
        (
            $normalizedFragment,
            $caughtFragment,
            "Method \"Uri::getFragment\" returned unexpected result.\n".
            "Expected result after setting fragment \"$fragment\" is \"$normalizedFragment\".\n".
            "Caught result is \"$caughtFragment\".\n"
        );
    }
    /** **********************************************************************
     * Test URI object converts to string.
     *
     * @dataProvider    dataProviderUriByParts
     *
     * @param           string  $scheme                 Scheme.
     * @param           string  $login                  Login.
     * @param           string  $password               Password.
     * @param           string  $host                   Host.
     * @param           int     $port                   Port.
     * @param           string  $path                   Path.
     * @param           string  $query                  Query.
     * @param           string  $fragment               Fragment.
     * @param           string  $normalizedUri          Normalized URI string.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testToStringConverting
    (
        string  $scheme,
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $path,
        string  $query,
        string  $fragment,
        string  $normalizedUri
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
            $normalizedUri,
            $caughtUri,
            "Method \"Uri::__toString\" returned unexpected result.\n".
            "Expected result after setting scheme \"$scheme\", login \"$login\", ".
            "password \"$password\", host \"$host\", port \"$port\", path \"$path\", ".
            "query \"$query\", fragment \"$fragment\" is \"$normalizedUri\".\n".
            "Caught result is \"$caughtUri\".\n"
        );
    }
    /** **********************************************************************
     * Data provider: scheme valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderSchemeValidValues() : array
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
     * Data provider: scheme invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderSchemeInvalidValues() : array
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
     * Data provider: host valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderHostValidValues() : array
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
     * Data provider: host invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderHostInvalidValues() : array
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
     * Data provider: port valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPortValidValues() : array
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
     * Data provider: port invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPortInvalidValues() : array
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
     * Data provider: schemes with standard ports.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderSchemeStandardPorts() : array
    {
        $result = [];

        foreach (self::SCHEMES_STANDARD_PORTS as $scheme => $port)
        {
            $result[] = [$scheme, $port];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: user info.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderUserInfo() : array
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
     * Data provider: authority.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderAuthority() : array
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
     * Data provider: path valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPathValidValues() : array
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
     * Data provider: path invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPathInvalidValues() : array
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
     * Data provider: query valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderQueryValidValues() : array
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
     * Data provider: query invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderQueryInvalidValues() : array
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
     * Data provider: fragment.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderFragment() : array
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
     * Data provider: URI by parts.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderUriByParts() : array
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