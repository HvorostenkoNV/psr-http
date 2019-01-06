<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;

use
    Throwable,
    PHPUnit\Framework\TestCase,
    AVMG\Http\Uri\Uri;
/** ***********************************************************************************************
 * PSR-7 UriInterface implementation test.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UriTestOld extends TestCase
{
    private const
        SPECIAL_CHARS                       =
            [
                '`',
                '\'',
                '"',

                '[',
                ']',
                '{',
                '}',
                '(',
                ')',

                '\\',
                '|',
                '/',

                '+',
                '-',
                '=',
                '*',
                '%',

                '^',
                '<',
                '>',

                ',',
                '.',
                ':',
                ';',

                '~',
                '!',
                '@',
                '#',
                '№',
                '$',
                '&',
                '?',
                '_'
            ],
        URI_RESERVED_CHARS                  =
            [
                ':', '/', '?', '#',
                '[', ']', '@'
            ],
        URI_UNRESERVED_CHARS                =
            [
                '-', '.', '_', '~'
            ],
        SCHEME_ALLOWED_SPECIAL_CHARS        =
            [
                '+', '-', '.'
            ],
        DOMAIN_NAME_ALLOWED_SPECIAL_CHARS   =
            [
                '-', '.'
            ],
        PORT_MIN_VALUE                      = 1,
        PORT_MAX_VALUE                      = 65535,
        SCHEMES_STANDARD_PORTS              =
            [
                'http'  => 80,
                'https' => 443
            ],
        PATH_UNCODED_SPECIAL_CHARS          =
            [
                '\'',
                '[', ']', '(', ')',
                '+', '-', '=', '*', '%',
                ',', '.', ':',
                '~', '!', '@',
                '$', '&', '_'
            ];
    /** **********************************************************************
     * Testing method "Uri::getScheme".
     *
     * @test
     * @dataProvider    getSchemeDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedScheme     Expected scheme.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getScheme(string $uri, string $expectedScheme) : void
    {
        $caughtScheme = (new Uri($uri))->getScheme();

        self::assertEquals
        (
            $expectedScheme,
            $caughtScheme,
            "Method \"Uri::getScheme\" returned unexpected result.\n".
            "Expected scheme form uri \"$uri\" is \"$expectedScheme\".\n".
            "Caught scheme is \"$caughtScheme\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getHost".
     *
     * @test
     * @dataProvider    getHostDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedHost       Expected host.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getHost(string $uri, string $expectedHost) : void
    {
        $caughtHost = (new Uri($uri))->getHost();

        self::assertEquals
        (
            $expectedHost,
            $caughtHost,
            "Method \"Uri::getHost\" returned unexpected result.\n".
            "Expected host form uri \"$uri\" is \"$expectedHost\".\n".
            "Caught host is \"$caughtHost\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getPort".
     *
     * @test
     * @dataProvider    getPortDataProvider
     *
     * @param           string  $uri                URI.
     * @param           mixed   $expectedPort       Expected port.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getPort(string $uri, $expectedPort) : void
    {
        $caughtPort = (new Uri($uri))->getPort();

        self::assertEquals
        (
            $expectedPort,
            $caughtPort,
            "Method \"Uri::getPort\" returned unexpected result.\n".
            "Expected port form uri \"$uri\" is \"$expectedPort\".\n".
            "Caught port is \"$caughtPort\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getUserInfo".
     *
     * @test
     * @dataProvider    getUserInfoDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedUserInfo   Expected user info.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getUserInfo(string $uri, string $expectedUserInfo) : void
    {
        $caughtUserInfo = (new Uri($uri))->getUserInfo();

        self::assertEquals
        (
            $expectedUserInfo,
            $caughtUserInfo,
            "Method \"Uri::getUserInfo\" returned unexpected result.\n".
            "Expected user info form uri \"$uri\" is \"$expectedUserInfo\".\n".
            "Caught user info is \"$caughtUserInfo\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getAuthority".
     *
     * @test
     * @dataProvider    getAuthorityDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedAuthority  Expected authority.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getAuthority(string $uri, string $expectedAuthority) : void
    {
        $caughtAuthority = (new Uri($uri))->getAuthority();

        self::assertEquals
        (
            $expectedAuthority,
            $caughtAuthority,
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
            "Expected authority form uri \"$uri\" is \"$expectedAuthority\".\n".
            "Caught authority is \"$caughtAuthority\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getPath".
     *
     * @test
     * @dataProvider    getPathDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedPath       Expected path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getPath(string $uri, string $expectedPath) : void
    {
        $caughtPath = (new Uri($uri))->getPath();

        self::assertEquals
        (
            $expectedPath,
            $caughtPath,
            "Method \"Uri::getPath\" returned unexpected result.\n".
            "Expected path form uri \"$uri\" is \"$expectedPath\".\n".
            "Caught path is \"$caughtPath\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getQuery".
     *
     * @test
     * @dataProvider    getQueryDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedQuery      Expected query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getQuery(string $uri, string $expectedQuery) : void
    {
        $caughtQuery = (new Uri($uri))->getQuery();

        self::assertEquals
        (
            $expectedQuery,
            $caughtQuery,
            "Method \"Uri::getQuery\" returned unexpected result.\n".
            "Expected query form uri \"$uri\" is \"$expectedQuery\".\n".
            "Caught query is \"$caughtQuery\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "Uri::getFragment".
     *
     * @test
     * @dataProvider    getFragmentDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedFragment   Expected query.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function getFragment(string $uri, string $expectedFragment) : void
    {
        $caughtFragment = (new Uri($uri))->getFragment();

        self::assertEquals
        (
            $expectedFragment,
            $caughtFragment,
            "Method \"Uri::getFragment\" returned unexpected result.\n".
            "Expected fragment form uri \"$uri\" is \"$caughtFragment\".\n".
            "Caught fragment is \"$caughtFragment\".\n"
        );
    }
    /** **********************************************************************
     * Data provider for method "getScheme".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getSchemeDataProvider() : array
    {
        $schemeValues   = $this->getSchemeValues();
        $result         = [];

        foreach ($schemeValues as $scheme => $schemeExpected)
        {
            if (!is_null($schemeExpected))
            {
                $result[] = ["$scheme://site.com", $schemeExpected];
            }
            else
            {
                $result[] = ["$scheme://site.com", ''];
            }
        }

        $result[]   = ['scheme:path',         'scheme'];
        $result[]   = ['scheme:/path',        'scheme'];
        $result[]   = ['scheme://site.com',   'scheme'];
        $result[]   = ['scheme:',             'scheme'];
        $result[]   = ['scheme' ,             ''];
        $result[]   = [':path',               ''];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getHost".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getHostDataProvider() : array
    {
        $hostValues = $this->getHostValues();
        $result     = [];

        foreach ($hostValues as $host => $hostExpected)
        {
            if (!is_null($hostExpected))
            {
                $result[] = ["scheme://$host", $hostExpected];
            }
            else
            {
                $result[] = ["scheme://$host", ''];
            }
        }

        $result[]   = ['scheme://site.com',     'site.com'];
        $result[]   = ['scheme:site.com',       ''];
        $result[]   = ['://site.com',           ''];
        $result[]   = ['site.com',              ''];
        $result[]   = ['scheme:///path',        ''];
        $result[]   = ['scheme:://site.com',    ''];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getPort".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getPortDataProvider() : array
    {
        $portValues = $this->getPortValues();
        $result     = [];

        foreach ($portValues as $port => $portExpected)
        {
            if (!is_null($portExpected))
            {
                $result[] = ["scheme://site.com:$port", $portExpected];
            }
            else
            {
                $result[] = ["scheme://site.com:$port", null];
            }
        }

        foreach (self::SCHEMES_STANDARD_PORTS as $scheme => $port)
        {
            $result[] = ["$scheme://site.com:$port", null];
        }

        $result[]   = ['scheme://site.com:',        null];
        $result[]   = ['scheme://site.com:/path',   null];
        $result[]   = ['://site.com:10',            null];
        $result[]   = ['scheme://user@site.com:10', 10];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getUserInfo".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getUserInfoDataProvider() : array
    {
        $userInfoValues = $this->getUserInfoValues();
        $result         = [];

        foreach ($userInfoValues as $value => $valueExpected)
        {
            if (!is_null($valueExpected))
            {
                $result[] = ["scheme://$value@site.com", $valueExpected];
            }
            else
            {
                $result[] = ["scheme://$value@site.com", ''];
            }
        }

        $result[]   = ['scheme://@site.com',    ''];
        $result[]   = ['://user@site.com',      ''];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getAuthority".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getAuthorityDataProvider() : array
    {
        $hostValues     = $this->getHostValues();
        $portValues     = $this->getPortValues();
        $userInfoValues = $this->getUserInfoValues();
        $result         = [];

        foreach ($hostValues as $host => $hostExpected)
        {
            if (!is_null($hostExpected))
            {
                $result[] =
                    [
                        "scheme://user:password@$host:123/path/",
                        "user:password@$hostExpected:123"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "scheme://user:password@$host:123/path/",
                        ''
                    ];
            }
        }

        foreach ($portValues as $port => $portExpected)
        {
            if (!is_null($portExpected))
            {
                $result[] =
                    [
                        "scheme://user:password@site.com:$port/path/",
                        "user:password@site.com:$portExpected"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "scheme://user:password@site.com:$port/path/",
                        'user:password@site.com'
                    ];
            }
        }

        foreach ($userInfoValues as $value => $valueExpected)
        {
            if (!is_null($valueExpected))
            {
                $result[] =
                    [
                        "scheme://$value@site.com:123/path/",
                        "$valueExpected@site.com:123"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "scheme://$value@site.com:123/path/",
                        'site.com:123'
                    ];
            }
        }

        $result[] =
            [
                'scheme://@site.com:123/path/',
                'site.com:123'
            ];
        $result[] =
            [
                'scheme://user:password@site.com:/path/',
                'user:password@site.com'
            ];
        $result[] =
            [
                'scheme://@site.com:/path/',
                'site.com'
            ];
        $result[] =
            [
                'scheme:path/path/',
                ''
            ];
        $result[] =
            [
                '://site.com/path/',
                ''
            ];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getPath".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getPathDataProvider() : array
    {
        $pathValues = $this->getPathValues();
        $result     = [];

        foreach ($pathValues as $path => $pathExpected)
        {
            if (!is_null($pathExpected))
            {
                $result[] = ["scheme://site.com/$path", "/$pathExpected"];
                $result[] = ["scheme:$path",            $pathExpected];
            }
            else
            {
                $result[] = ["scheme://site.com$path", ''];
            }
        }

        $result[]   = ['scheme://site.com',             ''];
        $result[]   = ['scheme://site.com/',            '/'];
        $result[]   = ['scheme://site.com//path',       '//path'];
        $result[]   = ['scheme:path',                   'path'];
        $result[]   = ['scheme:///',                    '/'];
        $result[]   = ['scheme://site.com?key=value',   ''];
        $result[]   = ['scheme://site.com/?key=value',  '/'];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getQuery".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getQueryDataProvider() : array
    {
        $queryValues    = $this->getQueryValues();
        $result         = [];

        foreach ($queryValues as $query => $queryExpected)
        {
            if (!is_null($queryExpected))
            {
                $result[] = ["scheme://site.com?$query", $queryExpected];
            }
            else
            {
                $result[] = ["scheme://site.com?$query", ''];
            }
        }

        $result[]   = ['scheme://site.com?',        ''];
        $result[]   = ['scheme://site.com?&',       ''];
        $result[]   = ['scheme://site.com??key',    '?key'];

        return $result;
    }
    /** **********************************************************************
     * Data provider for method "getFragment".
     *
     * @return  array                               Data.
     ************************************************************************/
    public function getFragmentDataProvider() : array
    {
        $fragmentValues = $this->getFragmentValues();
        $result         = [];

        foreach ($fragmentValues as $fragment => $fragmentExpected)
        {
            if (!is_null($fragmentExpected))
            {
                $result[] = ["scheme://site.com#$fragment", $fragmentExpected];
            }
            else
            {
                $result[] = ["scheme://site.com#$fragment", ''];
            }
        }

        $result[]   = ['scheme://site.com?',        ''];
        $result[]   = ['scheme://site.com?&',       ''];
        $result[]   = ['scheme://site.com??key',    '?key'];

        return $result;
    }
    /** **********************************************************************
     * Get scheme values map.
     *
     * @return  array                               Scheme values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getSchemeValues() : array
    {
        $result =
            [
                'http'          => 'http',
                'https'         => 'https',
                'ftp'           => 'ftp',
                'scheme'        => 'scheme',

                'Http'          => 'http',
                'HTTP'          => 'http',
                'hTtP'          => 'http',

                'scheme10'      => 'scheme10',
                '10scheme'      => null,

                'scheme '       => null,
                ' scheme'       => null,
                's c h e m e'   => null
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (in_array($char, self::URI_RESERVED_CHARS))
            {
                continue;
            }

            if (in_array($char, self::SCHEME_ALLOWED_SPECIAL_CHARS))
            {
                $schemes["scheme{$char}"]   = "scheme{$char}";
                $schemes["{$char}scheme"]   = null;
            }
            else
            {
                $schemes["scheme{$char}"]   = null;
                $schemes["{$char}scheme"]   = null;
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get host values map.
     *
     * @return  array                               Host values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getHostValues() : array
    {
        $ipAddressesV6ValuesRaw = $this->getIpAddressesV6Values();
        $ipAddressesV6Values    = [];

        foreach ($ipAddressesV6ValuesRaw as $key => $value)
        {
            $ipAddressesV6Values["[$key]"] = !is_null($value)
                ? "[$value]"
                : null;
        }

        return array_merge
        (
            $this->getDomainNamesValues(),
            $this->getIpAddressesV4Values(),
            $ipAddressesV6Values
        );
    }
    /** **********************************************************************
     * Get domain names values map.
     *
     * @return  array                               Domain names values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getDomainNamesValues() : array
    {
        $result =
            [
                'site.com'      => 'site.com',
                'www.site.com'  => 'www.site.com',
                'localhost'     => 'localhost',

                'Site.com'      => 'site.com',
                'SITE.com'      => 'site.com',
                'sItE.com'      => 'site.com',

                'site10'        => 'site10',
                '10site'        => '10site',

                'site.com '     => null,
                ' site.com'     => null,
                's i t e.com'   => null
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (in_array($char, self::URI_RESERVED_CHARS))
            {
                continue;
            }

            if (in_array($char, self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS))
            {
                $result["site{$char}.com"]  = "site{$char}.com";
                $result["{$char}site.com"]  = null;
            }
            else
            {
                $result["site{$char}.com"]  = null;
                $result["{$char}site.com"]  = null;
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get ip addresses v4 values map.
     *
     * @return  array                               Ip addresses v4 values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getIpAddressesV4Values() : array
    {
        return
            [
                '127.0.0.1'         => '127.0.0.1',
                '1.0.0.1'           => '1.0.0.1',
                '10.0.0.10'         => '10.0.0.10',

                '255.255.255.255'   => '255.255.255.255',
                '255.255.255.256'   => null,
                '255.255.255.-1'    => null,

                '01.0.0.1'          => '1.0.0.1',
                '001.0.0.1'         => '1.0.0.1',
                '010.0.0.1'         => '10.0.0.1',

                '10.20'             => '10.0.0.20',
                '10.20.30'          => '10.20.0.30',
                '10.20.30.40.50'    => null,

                '10.20.30.40.'      => '10.20.30.40',
                '.10.20.30.40'      => '10.20.30.40',
                '.10.20.30.40.'     => '10.20.30.40',
                '.10.20.30.40.50.'  => null
            ];
    }
    /** **********************************************************************
     * Get ip addresses v6 values map.
     *
     * @return  array                               Ip addresses v6 values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getIpAddressesV6Values() : array
    {
        return
            [
                '1234:5678:1357:2468:aabb:ccdd:eeff:ABCD'   => '1234:5678:1357:2468:aabb:ccdd:eeff:ABCD',
                '1234:123:12:1:abcd:ABCD:AbCd:FF'           => '1234:123:12:1:abcd:ABCD:AbCd:FF',
                '1a:2b:3c:4d:5e:6f:7:8'                     => '1a:2b:3c:4d:5e:6f:7:8',

                '1:2:3:4:5:6:7:8'                           => '1:2:3:4:5:6:7:8',
                '1:2:3:4:5:6:7:g'                           => null,
                '1:2:3:4:5:6:7:G'                           => null,
                '1:2:3:4:5:6:7:-1'                          => null,

                '1:2:3:4:5:6:7:8:9'                         => null,
                '1:2:3:4:5:6:7'                             => null,

                '01:2:3:4:5:6:7:8'                          => '1:2:3:4:5:6:7:8',
                '001:2:3:4:5:6:7:8'                         => '1:2:3:4:5:6:7:8',
                '010:2:3:4:5:6:7:8'                         => '10:2:3:4:5:6:7:8',
                '00001:2:3:4:5:6:7:8'                       => null,

                '1:2:3:4:5:6:0:0'                           => '1:2:3:4:5:6::',
                '0:0:3:4:5:6:7:8'                           => '::3:4:5:6:7:8',
                '1:2:3:0:0:6:7:8'                           => '1:2:3::6:7:8',

                '1:0:00:4:000:0000:0:8'                     => '1:0:0:4::8',
                '1:0:0:4:5:0:0:8'                           => '1::4:5:0:0:8',
                '0:00:000:0000:0:00:000:0000'               => '::',

                '1:2::'                                     => '1:2::',
                '1:2:::'                                    => null,
                '::7:8'                                     => '::7:8',
                ':::7:8'                                    => null,
                '1:2::7:8'                                  => '1:2::7:8',
                '1:2:::7:8'                                 => null,
                '1::5::8'                                   => null,
                '::'                                        => '::',
                ':::'                                       => null,

                '1:2:3:4:5:6:7::'                           => null,
                '::2:3:4:5:6:7:8'                           => null,

                '1:2:3:4:5:6:1.0.0.1'                       => '1:2:3:4:5:6:1.0.0.1',
                '1:2:3:4:5:1.0.0.1'                         => null,
                '1:2:3:4:5:6:7:1.0.0.1'                     => null,
                '1:2:3:4:5:6:1.0.0.256'                     => null,
                '1:2:3:4:5:6:1.0.0.-1'                      => null,
                '1:2:3:4:5:6:1.0.0.0.1'                     => null,

                '1:2:3:4::1.0.0.1'                          => '1:2:3:4::1.0.0.1',
                '1:2:3:4:0:0:1.0.0.1'                       => '1:2:3:4::1.0.0.1',
                '1:2:3:4:::1.0.0.1'                         => null,
                '1:2:3:4:5::1.0.0.1'                        => null,

                '::3:4:5:6:1.0.0.1'                         => '::3:4:5:6:1.0.0.1',
                '::2:3:4:5:6:1.0.0.1'                       => null,
                '1:2::5:6:1.0.0.1'                          => '1:2::5:6:1.0.0.1',
                '01:002:0000:000:5:6:01.010.000.1'          => '1:2::5:6:1.10.0.1',

                '1:2:3:4:5:6:1.0.0.1:'                      => null,
                '1:2:3:4:5:6:1.0.0.1:0'                     => null,
                '1:2:3:4:5:6:7:'                            => null,
                ':2:3:4:5:6:7:8'                            => null,
                '1:2:3::6:7:'                               => null,
                ':2:3::6:7:8'                               => null
            ];
    }
    /** **********************************************************************
     * Get port values map.
     *
     * @return  array                               Port values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getPortValues() : array
    {
        $minAvailableValue  = self::PORT_MIN_VALUE;
        $maxAvailableValue  = self::PORT_MAX_VALUE;
        $result             =
            [
                $minAvailableValue      => $minAvailableValue,
                $maxAvailableValue      => $maxAvailableValue,

                $minAvailableValue - 1  => null,
                $maxAvailableValue + 1  => null,

                'somePort'              => null
            ];

        for ($index = 5; $index > 0; $index--)
        {
            $value          = rand($minAvailableValue, $maxAvailableValue);
            $result[$value] = $value;
        }
        for ($index = 5; $index > 0; $index--)
        {
            $value          = rand($minAvailableValue - 100, $minAvailableValue - 1);
            $result[$value] = null;
        }
        for ($index = 5; $index > 0; $index--)
        {
            $value          = rand($maxAvailableValue + 1, $maxAvailableValue + 100);
            $result[$value] = null;
        }

        return $result;
    }
    /** **********************************************************************
     * Get user info values map.
     *
     * @return  array                               User info values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getUserInfoValues() : array
    {
        $result =
            [
                'login:password'    => 'login:password',
                'login'             => 'login',
                ':password'         => null,

                'Login:Password'    => 'Login:Password',
                'LOGIN:PASSWORD'    => 'LOGIN:PASSWORD',
                'lOgIn:PasSwOrD'    => 'lOgIn:PasSwOrD',

                'login10'           => 'login10',
                '10login'           => '10login',

                'login '            => 'login%20',
                ' login'            => '%20login',
                'l o g i n'         => 'l%20o%20g%20i%20n'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            $charEncoded = urlencode($char);

            if (in_array($char, self::URI_RESERVED_CHARS))
            {
                $result["login{$charEncoded}"]  = "login{$charEncoded}";
            }
            elseif (in_array($char, self::URI_UNRESERVED_CHARS))
            {
                $result["login{$char}"]         = "login{$char}";
                $result["login{$charEncoded}"]  = "login{$char}";
            }
            else
            {
                $result["login{$char}"]         = "login{$charEncoded}";
                $result["login{$charEncoded}"]  = "login{$charEncoded}";
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get path values map.
     *
     * @return  array                               Path values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getPathValues() : array
    {
        $result =
            [
                'path'                      => 'path',
                'path1/path2'               => 'path1/path2',
                'path1/path2/path3-path4'   => 'path1/path2/path3-path4',

                'Path'                      => 'Path',
                'PATH'                      => 'PATH',
                'pAtH'                      => 'pAtH',

                'path '                     => 'path%20',
                ' path'                     => '%20path',
                'p a t h'                   => 'p%20a%20t%20h'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            $charEncoded = urlencode($char);

            if (in_array($char, self::PATH_UNCODED_SPECIAL_CHARS))
            {
                $result["path{$char}"]          = "path{$char}";
                $result["path{$charEncoded}"]   = "path{$char}";
            }
            elseif (in_array($char, self::URI_RESERVED_CHARS))
            {
                $result["path{$charEncoded}"]   = "path{$charEncoded}";
            }
            else
            {
                $result["path{$char}"]          = "path{$charEncoded}";
                $result["path{$charEncoded}"]   = "path{$charEncoded}";
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get query values map.
     *
     * @return  array                               Query values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    private function getQueryValues() : array
    {
        $result =
            [
                'key'                           => 'key',
                'value'                         => 'value',

                'Key'                           => 'Key',
                'KEY'                           => 'KEY',
                'kEy'                           => 'kEy',

                'key '                          => 'key%20',
                ' key'                          => '%20key',
                'k e y'                         => 'k%20e%20y',

                'key='                          => 'key',
                'key=='                         => 'key==',
                'key=&'                         => 'key',
                'key=?'                         => 'key=?',
                'key=value'                     => 'key=value',

                'key1=value2&key2'              => 'key1=value2&key2',
                'key1=value2&key2='             => 'key1=value2&key2',
                'key1=value2&key2=value2'       => 'key1=value2&key2=value2',

                'key1=value2&'                  => 'key1=value2',
                'key1=value2&=value2'           => 'key1=value2',
                'key1==value2&&key2===value2'   => 'key1==value2&key2===value2'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (in_array($char, ['=', '&', '?']))
            {
                continue;
            }

            $charEncoded                                = urlencode($char);
            $result["{$char}={$char}"]                  = "{$char}={$char}";
            $result["{$charEncoded}={$charEncoded}"]    = "{$charEncoded}={$charEncoded}";
        }

        return $result;
    }
}