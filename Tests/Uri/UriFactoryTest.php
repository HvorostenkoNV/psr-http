<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;

use
    Throwable,
    InvalidArgumentException,
    PHPUnit\Framework\TestCase,
    AVMG\Http\Factory\UriFactory;
/** ***********************************************************************************************
 * PSR-7 UriFactoryInterface implementation test.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UriFactoryTest extends TestCase
{
    /** **********************************************************************
     * Test "UriFactory::createUri" throws exception with invalid argument.
     *
     * @dataProvider        dataProviderUriInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $uri             URI string.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testCreateUriThrowsException(string $uri) : void
    {
        (new UriFactory)->createUri($uri);

        self::fail
        (
            "Method \"UriFactory::createUri\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception on parsing uri \"$uri\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses scheme from URI string.
     *
     * @dataProvider    dataProviderUriWithScheme
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedScheme   Normalized scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesScheme(string $uri, string $normalizedScheme) : void
    {
        $caughtScheme = (new UriFactory)
            ->createUri($uri)
            ->getScheme();

        self::assertEquals
        (
            $normalizedScheme,
            $caughtScheme,
            "Method \"Uri::getScheme\" returned unexpected result.\n".
            "Expected scheme from uri \"$uri\" is \"$normalizedScheme\".\n".
            "Caught scheme is \"$caughtScheme\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses user info from URI string.
     *
     * @dataProvider    dataProviderUriWithUserInfo
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedUserInfo Normalized user info.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesUserInfo(string $uri, string $normalizedUserInfo) : void
    {
        $caughtUserInfo = (new UriFactory)
            ->createUri($uri)
            ->getUserInfo();

        self::assertEquals
        (
            $normalizedUserInfo,
            $caughtUserInfo,
            "Method \"Uri::getUserInfo\" returned unexpected result.\n".
            "Expected user info from uri \"$uri\" is \"$normalizedUserInfo\".\n".
            "Caught user info is \"$caughtUserInfo\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses host from URI string.
     *
     * @dataProvider    dataProviderUriWithHost
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedHost     Normalized host.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesHost(string $uri, string $normalizedHost) : void
    {
        $caughtHost = (new UriFactory)
            ->createUri($uri)
            ->getHost();

        self::assertEquals
        (
            $normalizedHost,
            $caughtHost,
            "Method \"Uri::getHost\" returned unexpected result.\n".
            "Expected host from uri \"$uri\" is \"$normalizedHost\".\n".
            "Caught host is \"$caughtHost\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses port from URI string.
     *
     * @dataProvider    dataProviderUriWithPort
     *
     * @param           string  $uri                URI string.
     * @param           int     $normalizedPort     Normalized port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesPort(string $uri, int $normalizedPort) : void
    {
        $caughtPort = (new UriFactory)
            ->createUri($uri)
            ->getPort();

        if ($normalizedPort === 0)
        {
            $normalizedPort = null;
        }

        self::assertEquals
        (
            $normalizedPort,
            $caughtPort,
            "Method \"Uri::getPort\" returned unexpected result.\n".
            "Expected port from uri \"$uri\" is \"$normalizedPort\".\n".
            "Caught port is \"$caughtPort\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses path from URI string.
     *
     * @dataProvider    dataProviderUriWithPath
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedPath     Normalized path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesPath(string $uri, string $normalizedPath) : void
    {
        $caughtPath = (new UriFactory)
            ->createUri($uri)
            ->getPath();

        self::assertEquals
        (
            $normalizedPath,
            $caughtPath,
            "Method \"Uri::getPath\" returned unexpected result.\n".
            "Expected path from uri \"$uri\" is \"$normalizedPath\".\n".
            "Caught path is \"$caughtPath\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses query from URI string.
     *
     * @dataProvider    dataProviderUriWithQuery
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedQuery    Normalized query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesQuery(string $uri, string $normalizedQuery) : void
    {
        $caughtQuery = (new UriFactory)
            ->createUri($uri)
            ->getQuery();

        self::assertEquals
        (
            $normalizedQuery,
            $caughtQuery,
            "Method \"Uri::getQuery\" returned unexpected result.\n".
            "Expected query from uri \"$uri\" is \"$normalizedQuery\".\n".
            "Caught query is \"$caughtQuery\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" parses fragment from URI string.
     *
     * @dataProvider    dataProviderUriWithFragment
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedFragment Normalized fragment.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCreateUriParsesFragment(string $uri, string $normalizedFragment) : void
    {
        $caughtFragment = (new UriFactory)
            ->createUri($uri)
            ->getFragment();

        self::assertEquals
        (
            $normalizedFragment,
            $caughtFragment,
            "Method \"Uri::getFragment\" returned unexpected result.\n".
            "Expected fragment from uri \"$uri\" is \"$normalizedFragment\".\n".
            "Caught fragment is \"$caughtFragment\".\n"
        );
    }
    /** **********************************************************************
     * Test "UriFactory::createUri" normalizes URI in expected way.
     *
     * @dataProvider    dataProviderUriNormalization
     *
     * @param           string  $uri                URI string.
     * @param           string  $normalizedUri      Normalized URI.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUriNormalizes(string $uri, string $normalizedUri) : void
    {
        $caughtUriString = (string) (new UriFactory)->createUri($uri);

        self::assertEquals
        (
            $normalizedUri,
            $caughtUriString,
            "Method \"Uri::__toString\" returned unexpected result.\n".
            "Expected normalized uri string from \"$uri\" is \"$normalizedUri\".\n".
            "Caught uri string is \"$caughtUriString\".\n"
        );
    }
    /** **********************************************************************
     * Data provider: URI invalid values.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriInvalidValues() : array
    {
        $schemeValues   = UriDataGenerator::getSchemeValues();
        $hostValues     = UriDataGenerator::getHostValues();
        $portValues     = UriDataGenerator::getPortValues();
        $pathValues     = UriDataGenerator::getPathValues();
        $queryValues    = UriDataGenerator::getQueryValues();
        $result         = [];

        foreach ($schemeValues as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = ["$providedValue://site.com"];
            }
        }
        foreach ($hostValues as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = ["scheme://$providedValue"];
            }
        }
        foreach ($portValues as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = ["scheme://host:$providedValue"];
            }
        }
        foreach ($pathValues as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = ["scheme:$providedValue"];
            }
        }
        foreach ($queryValues as $providedValue => $expectedValue)
        {
            if (is_null($expectedValue))
            {
                $result[] = ["scheme:path?$providedValue"];
            }
        }

        $result[]   = ['//host:port'];
        $result[]   = ['//host:10.5'];
        $result[]   = ['10.10.0.1:10'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with scheme combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithScheme() : array
    {
        $values = UriDataGenerator::getSchemeValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["$providedValue://site.com", $expectedValue];
            }
        }

        $result[]   = ['scheme://host',     'scheme'];
        $result[]   = ['scheme://host:10',  'scheme'];
        $result[]   = ['//host',            ''];

        $result[]   = ['scheme:path',       'scheme'];
        $result[]   = ['scheme:path:path',  'scheme'];
        $result[]   = ['path',              ''];

        $result[]   = ['host:10',           'host'];
        $result[]   = ['host:port',         'host'];
        $result[]   = ['host:10:20',        'host'];
        $result[]   = ['host:10:20/path',   'host'];
        $result[]   = ['host:path/path',    'host'];

        $result[]   = ['/scheme://host',    ''];
        $result[]   = ['/scheme:path',      ''];

        $result[]   = ['scheme:10.10.0.1',  'scheme'];
        $result[]   = ['scheme:[1::2]',     'scheme'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with user info combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithUserInfo() : array
    {
        $values = UriDataGenerator::getUserInfoValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//$providedValue@site.com", $expectedValue];
            }
        }

        $result[]   = ['scheme://@site.com',        ''];
        $result[]   = ['scheme://user@10.10.0.1',   'user'];
        $result[]   = ['scheme://user@[1::2]',      'user'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with host combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithHost() : array
    {
        $values = UriDataGenerator::getHostValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//$providedValue", $expectedValue];
            }
        }

        $result[]   = ['scheme://host',         'host'];
        $result[]   = ['scheme://host:10',      'host'];
        $result[]   = ['//host',                'host'];
        $result[]   = ['//host/path',           'host'];
        $result[]   = ['//host?query',          'host'];
        $result[]   = ['//host#fragment',       'host'];

        $result[]   = ['scheme:host',           ''];
        $result[]   = ['scheme:host:host',      ''];
        $result[]   = ['host',                  ''];

        $result[]   = ['host:10',               ''];
        $result[]   = ['host:10:20',            ''];
        $result[]   = ['host:10/path',          ''];
        $result[]   = ['//host:10/path',        'host'];

        $result[]   = ['scheme:host',           ''];
        $result[]   = ['//user@host',           'host'];
        $result[]   = ['//:pass@host:10',       'host'];
        $result[]   = ['///path',               ''];

        $result[]   = ['//10.10.0.1',           '10.10.0.1'];
        $result[]   = ['//[1::2]',              '[1::2]'];
        $result[]   = ['10.10.0.1',             ''];
        $result[]   = ['[1::2]',                ''];
        $result[]   = ['scheme://10.10.0.1:10', '10.10.0.1'];
        $result[]   = ['scheme://[1::2]:10',    '[1::2]'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with port combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithPort() : array
    {
        $values = UriDataGenerator::getPortValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//host:$providedValue", $expectedValue];
            }
        }

        $result[]   = ['scheme://host:',    0];
        $result[]   = ['scheme://host:10',  10];
        $result[]   = ['//host:10',         10];
        $result[]   = ['//host:10/path',    10];

        $result[]   = ['scheme:host',       0];
        $result[]   = ['scheme:host:10',    0];

        $result[]   = ['host:10',           0];
        $result[]   = ['host:10:20',        0];
        $result[]   = ['host:10/path',      0];

        $result[]   = ['scheme://host/:10', 0];
        $result[]   = ['//user@host:10',    10];
        $result[]   = ['//:pass@host:10',   10];
        $result[]   = ['///host:10',        0];

        $result[]   = ['//10.10.0.1:10',    10];
        $result[]   = ['//[1::2]:10',       10];
        $result[]   = ['//[1::2]:10/20',    10];
        $result[]   = ['[1::2]:10',         0];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with path combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithPath() : array
    {
        $values = UriDataGenerator::getPathValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["scheme://host/$providedValue", "/$expectedValue"];
            }
        }

        $result[]   = ['scheme://host/path',                    '/path'];
        $result[]   = ['scheme://host:10/path/path',            '/path/path'];
        $result[]   = ['scheme://host:10/path?query#fragment',  '/path'];
        $result[]   = ['//host/path',                           '/path'];
        $result[]   = ['scheme:path',                           'path'];
        $result[]   = ['path',                                  'path'];

        $result[]   = ['scheme:path?path?query',                'path'];
        $result[]   = ['scheme:path#path#fragment',             'path'];
        $result[]   = ['scheme:path:path',                      'path:path'];

        $result[]   = ['scheme:host:10',                        'host:10'];
        $result[]   = ['host:port',                             'port'];
        $result[]   = ['scheme:10.10.0.1:10',                   '10.10.0.1:10'];

        $result[]   = ['host:10:20',                            '10:20'];
        $result[]   = ['host:10/path',                          '10/path'];
        $result[]   = ['///path',                               '/path'];
        $result[]   = ['//path',                                ''];

        $result[]   = ['10.10.0.1',                             '10.10.0.1'];
        $result[]   = ['[1::2]',                                '[1::2]'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with query combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithQuery() : array
    {
        $values = UriDataGenerator::getQueryValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["scheme://host?$providedValue", $expectedValue];
            }
        }

        $result[]   = ['scheme://host/path?query#fragment', 'query'];
        $result[]   = ['scheme://host/path?query',          'query'];
        $result[]   = ['scheme:path?query',                 'query'];
        $result[]   = ['path?query',                        'query'];
        $result[]   = ['//host/path?query',                 'query'];

        $result[]   = ['//host/path?query?query',           'query?query'];
        $result[]   = ['//host/path???',                    '??'];
        $result[]   = ['//host/path?query#query#fragment',  'query'];
        $result[]   = ['path?path?path?path',               'path?path?path'];

        $result[]   = ['10.10.0.1?query',                   'query'];
        $result[]   = ['[1::2]?query',                      'query'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: URI with fragment combinations.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriWithFragment() : array
    {
        $values = UriDataGenerator::getFragmentValues();
        $result = [];

        foreach ($values as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["scheme://host#$providedValue", $expectedValue];
            }
        }

        $result[]   = ['scheme://host/path?query#fragment', 'fragment'];
        $result[]   = ['scheme://host/path#fragment',       'fragment'];
        $result[]   = ['scheme:path#fragment',              'fragment'];
        $result[]   = ['path#fragment',                     'fragment'];
        $result[]   = ['//host/path#fragment',              'fragment'];

        $result[]   = ['//host/path#fragment#fragment',     'fragment#fragment'];
        $result[]   = ['//host/path###',                    '##'];
        $result[]   = ['path#path#path#path',               'path#path#path'];

        $result[]   = ['10.10.0.1#fragment',                'fragment'];
        $result[]   = ['[1::2]#fragment',                   'fragment'];

        return $result;
    }
    /** **********************************************************************
     * Data provider: normalized URI.
     *
     * @return  array                               Data.
     ************************************************************************/
    public function dataProviderUriNormalization() : array
    {
        $schemeValues   = UriDataGenerator::getSchemeValues();
        $userInfoValues = UriDataGenerator::getUserInfoValues();
        $hostValues     = UriDataGenerator::getHostValues();
        $portValues     = UriDataGenerator::getPortValues();
        $pathValues     = UriDataGenerator::getPathValues();
        $queryValues    = UriDataGenerator::getQueryValues();
        $fragmentValues = UriDataGenerator::getFragmentValues();
        $result         = [];

        foreach ($schemeValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["$providedValue://site.com", "$expectedValue://site.com"];
            }
        }
        foreach ($userInfoValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//$providedValue@site.com", "//$expectedValue@site.com"];
            }
        }
        foreach ($hostValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["scheme://$providedValue", "scheme://$expectedValue"];
            }
        }
        foreach ($portValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue) && $expectedValue !== 0)
            {
                $result[] = ["//host:$providedValue", "//host:$expectedValue"];
            }
        }
        foreach ($pathValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//host/$providedValue",   "//host/$expectedValue"];
                $result[] = ["scheme:$providedValue",   "scheme:$expectedValue"];
            }
        }
        foreach ($queryValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//host?$providedValue", "//host?$expectedValue"];
            }
        }
        foreach ($fragmentValues as $providedValue => $expectedValue)
        {
            if (!is_null($expectedValue))
            {
                $result[] = ["//host#$providedValue", "//host#$expectedValue"];
            }
        }

        $result[]   = ['scheme://@host:',       'scheme://host'];
        $result[]   = ['scheme:path?',          'scheme:path'];
        $result[]   = ['scheme:path#',          'scheme:path'];
        $result[]   = ['scheme://1.0.0.1:80',   'scheme://1.0.0.1:80'];
        $result[]   = ['scheme://[::]:80',      'scheme://[::]:80'];
        $result[]   = ['path?#',                'path'];

        return $result;
    }
}