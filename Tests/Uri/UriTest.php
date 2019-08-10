<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;

use Throwable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use AVMG\Http\Tests\{
    Collection\CollectionMediator,
    DataGenerator\GeneratorMediator as DataGenerator
};
use AVMG\Http\Uri\Uri;

use function is_null;
use function explode;
use function call_user_func_array;
/** ***********************************************************************************************
 * PSR-7 UriInterface implementation test.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UriTest extends TestCase
{
    /** **********************************************************************
     * Test "Uri::getScheme" provides valid normalized value.
     *
     * @dataProvider    dataProviderSchemeValidValues
     *
     * @param           string  $value                  Value.
     * @param           string  $valueExpected          Expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetScheme(string $value, string $valueExpected): void
    {
        $valueCaught = (new Uri())->withScheme($value)->getScheme();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"Uri::getScheme\" returned unexpected value.\n".
            "Expected value after setting \"$value\" is \"$valueExpected\".\n".
            "Caught value is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getHost" provides valid normalized value.
     *
     * @dataProvider    dataProviderHostValidValues
     *
     * @param           string  $value                  Value.
     * @param           string  $valueExpected          Expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetHost(string $value, string $valueExpected): void
    {
        $valueCaught = (new Uri())->withHost($value)->getHost();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"Uri::getHost\" returned unexpected value.\n".
            "Expected value after setting \"$value\" is \"$valueExpected\".\n".
            "Caught value is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getPort" provides valid normalized value.
     *
     * @dataProvider    dataProviderPortValidValues
     *
     * @param           int     $value                  Value.
     * @param           mixed   $valueExpected          Expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetPort(int $value, $valueExpected): void
    {
        $valueCaught = (new Uri())->withPort($value)->getPort();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"Uri::getPort\" returned unexpected value.\n".
            "Expected value after setting \"$value\" is \"$valueExpected\".\n".
            "Caught value is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getPort" provides null if port is standard for given scheme.
     *
     * @dataProvider    dataProviderSchemeStandardPorts
     *
     * @param           string  $scheme                 Scheme.
     * @param           int     $port                   Standard port fo this scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetPortWithStandardPort(string $scheme, int $port): void
    {
        $portCaught = (new Uri())
            ->withScheme($scheme)
            ->withPort($port)
            ->getPort();

        self::assertNull(
            $portCaught,
            "Method \"Uri::getPort\" returned unexpected value.\n".
            "Expected value after setting scheme \"$scheme\" and port \"$port\" is null.\n".
            "Caught result is \"$portCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "Uri::getUserInfo" provides valid normalized value.
     *
     * @dataProvider    dataProviderUserInfo
     *
     * @param           string  $login                  Login.
     * @param           string  $password               Password.
     * @param           mixed   $valueExpected          Expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetUserInfo(
        string  $login,
        string  $password,
        string  $valueExpected
    ): void
    {
        $valueCaught = (new Uri())->withUserInfo($login, $password)->getUserInfo();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"Uri::getUserInfo\" returned unexpected value.\n".
            "Expected value after setting login \"$login\" and password \"$password\"".
            " is \"$valueExpected\".\n".
            "Caught value is \"$valueCaught\".\n"
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
     * @param           mixed   $valueExpected          Expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetAuthority(
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $valueExpected
    ): void
    {
        $uri = (new Uri())->withUserInfo($login, $password);

        try {
            $uri = $uri->withHost($host);
        } catch (InvalidArgumentException $exception) {

        }

        try {
            $uri = $uri->withPort($port);
        } catch (InvalidArgumentException $exception) {

        }

        $valueCaught = $uri->getAuthority();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
            "Expected result after setting login \"$login\", password \"$password\", ".
            "host \"$host\" and port \"$port\" is \"$valueExpected\".\n".
            "Caught authority is \"$valueCaught\".\n"
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
    public function testGetPath(string $path, string $normalizedPath): void
    {
        $caughtPath = (new Uri())->withPath($path)->getPath();

        self::assertEquals(
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
    public function testGetQuery(string $query, string $normalizedQuery): void
    {
        $caughtQuery = (new Uri())->withQuery($query)->getQuery();

        self::assertEquals(
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
    public function testGetFragment(string $fragment, string $normalizedFragment): void
    {
        $caughtFragment = (new Uri())->withFragment($fragment)->getFragment();

        self::assertEquals(
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
    public function testToStringConverting(
        string  $scheme,
        string  $login,
        string  $password,
        string  $host,
        int     $port,
        string  $path,
        string  $query,
        string  $fragment,
        string  $normalizedUri
    ): void
    {
        $uri = new Uri();

        try {
            $uri = $uri->withScheme($scheme);
        } catch (InvalidArgumentException $exception) {

        }

        $uri = $uri->withUserInfo($login, $password);

        try {
            $uri = $uri->withHost($host);
        } catch (InvalidArgumentException $exception) {

        }

        try {
            $uri = $uri->withPort($port);
        } catch (InvalidArgumentException $exception) {

        }

        try {
            $uri = $uri->withPath($path);
        } catch (InvalidArgumentException $exception) {

        }

        try {
            $uri = $uri->withQuery($query);
        } catch (InvalidArgumentException $exception) {

        }

        $uri        = $uri->withFragment($fragment);
        $caughtUri  = (string) $uri;

        self::assertEquals(
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
     * Test URI setters methods return new instance.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testSettersMethodsReturnNewInstance(): void
    {
        $methods = [
            'withScheme'    => ['scheme'],
            'withUserInfo'  => ['user', 'password'],
            'withHost'      => ['site.com'],
            'withPort'      => [50],
            'withPath'      => ['/path/'],
            'withQuery'     => ['key=value'],
            'withFragment'  => ['fragment']
        ];

        foreach ($methods as $methodName => $methodArguments) {
            $uri    = new Uri();
            $uriNew = call_user_func_array([$uri, $methodName], $methodArguments);

            self::assertNotEquals(
                $uri,
                $uriNew,
                "Method \"Uri::$methodName\" returned unexpected result.\n".
                "Expected result must be new instance of Uri.\n".
                "Caught result is the same instance.\n"
            );
        }
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
    public function testWithHostThrowsException(string $host): void
    {
        (new Uri())->withHost($host);

        self::fail(
            "Method \"Uri::withHost\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting host \"$host\".\n".
            "Caught no exception.\n"
        );
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
    public function testWithSchemeThrowsException(string $scheme): void
    {
        (new Uri())->withScheme($scheme);

        self::fail(
            "Method \"Uri::withScheme\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting scheme \"$scheme\".\n".
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
    public function testWithPortThrowsException(int $port): void
    {
        (new Uri())->withPort($port);

        self::fail(
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
    public function testWithPathThrowsException(string $path): void
    {
        (new Uri())->withPath($path);

        self::fail(
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
    public function testWithQueryThrowsException(string $query): void
    {
        (new Uri())->withQuery($query);

        self::fail(
            "Method \"Uri::withQuery\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on setting query \"$query\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Data provider: scheme valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderSchemeValidValues(): array
    {
        $values = DataGenerator::generate('uri.scheme');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (!is_null($expectedValue)) {
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
    public function dataProviderSchemeInvalidValues(): array
    {
        $values = DataGenerator::generate('uri.scheme');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (is_null($expectedValue)) {
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
    public function dataProviderHostValidValues(): array
    {
        $values = DataGenerator::generate('uri.host');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (!is_null($expectedValue)) {
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
    public function dataProviderHostInvalidValues(): array
    {
        $values = DataGenerator::generate('uri.host');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (is_null($expectedValue)) {
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
    public function dataProviderPortValidValues(): array
    {
        $values = DataGenerator::generate('uri.port');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (!is_null($expectedValue) && $expectedValue !== 0) {
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
    public function dataProviderPortInvalidValues(): array
    {
        $values = DataGenerator::generate('uri.port');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (is_null($expectedValue)) {
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
    public function dataProviderSchemeStandardPorts(): array
    {
        $standardPorts  = CollectionMediator::get('uri.scheme.standardPorts');
        $result         = [];

        foreach ($standardPorts as $port => $scheme) {
            $result[] = [$scheme, $port];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: user info.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderUserInfo(): array
    {
        $values = DataGenerator::generate('uri.userInfo');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            $explode    = explode(':', $providedValue);
            $result[]   = [
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
    public function dataProviderAuthority(): array
    {
        $userInfoValues = DataGenerator::generate('uri.userInfo');
        $hostValues     = DataGenerator::generate('uri.host');
        $portValues     = DataGenerator::generate('uri.port');
        $result         = [];

        foreach ($userInfoValues as $providedValue => $expectedValue) {
            $explode    = explode(':', $providedValue);
            $login      = $explode[0];
            $password   = $explode[1] ?? '';
            $result[]   = [
                $login,
                $password,
                'site.com',
                123,
                !is_null($expectedValue)
                    ? "$expectedValue@site.com:123"
                    : 'site.com:123'
            ];
        }

        foreach ($hostValues as $providedValue => $expectedValue) {
            $result[] = [
                'user',
                'password',
                $providedValue,
                123,
                !is_null($expectedValue)
                    ? "user:password@$expectedValue:123"
                    : ''
            ];
        }

        foreach ($portValues as $providedValue => $expectedValue) {
            $result[] = [
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
    public function dataProviderPathValidValues(): array
    {
        $values = DataGenerator::generate('uri.path');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (!is_null($expectedValue)) {
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
    public function dataProviderPathInvalidValues(): array
    {
        $values = DataGenerator::generate('uri.path');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (is_null($expectedValue)) {
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
    public function dataProviderQueryValidValues(): array
    {
        $values = DataGenerator::generate('uri.query');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (!is_null($expectedValue)) {
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
    public function dataProviderQueryInvalidValues(): array
    {
        $values = DataGenerator::generate('uri.query');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
            if (is_null($expectedValue)) {
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
    public function dataProviderFragment(): array
    {
        $values = DataGenerator::generate('uri.fragment');
        $result = [];

        foreach ($values as $providedValue => $expectedValue) {
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
    public function dataProviderUriByParts(): array
    {
        return [
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