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
class UriTest extends TestCase
{
    private const
        SPECIAL_CHARS                       =
            [
                '`'     => '%60',
                '\''    => '%27',
                '"'     => '%22',

                '['     => '%5B',
                ']'     => '%5D',
                '{'     => '%7B',
                '}'     => '%7D',
                '('     => '%28',
                ')'     => '%29',

                '\\'    => '%5C',
                '|'     => '%7C',
                '/'     => '%2F',

                '+'     => '%2B',
                '-'     => '-',
                '='     => '%3D',
                '*'     => '%2A',
                '%'     => '%25',

                '^'     => '%5E',
                '<'     => '%3C',
                '>'     => '%3E',

                ','     => '%2C',
                '.'     => '.',
                ':'     => '%3A',
                ';'     => '%3B',

                '~'     => '',
                '!'     => '%21',
                '@'     => '%40',
                '#'     => '%23',
                '№'     => '%E2%84%96',
                '$'     => '%24',
                '&'     => '%26',
                '?'     => '%3F',
                '_'     => '_'
            ],
        URI_SEPARATING_CHARS                =
            [
                '/', '?', '&', ':', '@', '#'
            ],
        SCHEME_ALLOWED_SPECIAL_CHARS        =
            [
                '+', '.', '-'
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
        PATH_ALLOWED_SPECIAL_CHARS          =
            [
                '-', '.', '_', '~',
                '!', '$', '&', '\'',
                '(', ')', '*', '+',
                ',', ';', '=', ':'
            ],
        QUERY_ALLOWED_SPECIAL_CHARS         =
            [
                '*', '-', '.', '_', '~'
            ];
    /** **********************************************************************
     * Testing method "UriInterface::getScheme".
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
     * Testing method "UriInterface::getHost".
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
     * Testing method "UriInterface::getPort".
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
     * Testing method "UriInterface::getUserInfo".
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
     * Testing method "UriInterface::getAuthority".
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
     * Testing method "UriInterface::getPath".
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
     * Testing method "UriInterface::getQuery".
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

        $result[] = [':site.com',       ''];
        $result[] = [':/site.com',      ''];
        $result[] = [':://site.com',    ''];
        $result[] = [':///site.com',    ''];

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
                $result[] = ["http://$host", $hostExpected];
            }
            else
            {
                $result[] = ["http://$host", ''];
            }
        }

        $result[] = ['://site.com',     'site.com'];
        $result[] = [':://site.com',    'site.com'];
        $result[] = [':///site.com',    ''];
        $result[] = [':::////site.com', ''];

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
                $result[] = ["http://site.com:$port", $portExpected];
            }
            else
            {
                $result[] = ["http://site.com:$port", null];
            }
        }

        foreach (self::SCHEMES_STANDARD_PORTS as $scheme => $port)
        {
            $result[] = ["$scheme://site.com:$port", null];
        }

        $result[] = ['http://site.com:',        null];
        $result[] = ['http://site.com:/path',   null];

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
                $result[]   = ["$value@site.com",         $valueExpected];
                $result[]   = ["user:$value@site.com",    "user:$valueExpected"];
            }
            else
            {
                $result[]   = ["$value@site.com",         ''];
                $result[]   = ["user:$value@site.com",    'user'];
            }
        }

        $result[] = ['user:password@site.com',  'user:password'];
        $result[] = ['user:@site.com',          'user'];
        $result[] = [':password@site.com',      ''];

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
                        "http://user:password@$host:123/path/",
                        "user:password@$hostExpected:123"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "http://user:password@$host:123/path/",
                        'user:password'
                    ];
            }
        }

        foreach ($portValues as $port => $portExpected)
        {
            if (!is_null($portExpected))
            {
                $result[] =
                    [
                        "http://user:password@site.com:$port/path/",
                        "user:password@site.com:$portExpected"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "http://user:password@site.com:$port/path/",
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
                        "http://$value:password@site.com:123/path/",
                        "$valueExpected:password@site.com:123"
                    ];
                $result[] =
                    [
                        "http://user:$value@site.com:123/path/",
                        "user:$valueExpected@site.com:123"
                    ];
            }
            else
            {
                $result[] =
                    [
                        "http://$value:password@site.com:123/path/",
                        'site.com:123'
                    ];
                $result[] =
                    [
                        "http://user:$value@site.com:123/path/",
                        'user@site.com:123'
                    ];
            }
        }

        $result[] =
            [
                'http://user:password@site.com:123/path/',
                'user:password@site.com:123'
            ];
        $result[] =
            [
                'http://user:@site.com:123/path/',
                'user@site.com:123'
            ];
        $result[] =
            [
                'http://:password@site.com:123/path/',
                'site.com:123'
            ];
        $result[] =
            [
                'http://user:password@site.com:/path/',
                'user:password@site.com'
            ];
        $result[] =
            [
                'http://user:@site.com:/path/',
                'user@site.com'
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
                $result[] = ["http://site.com$path", $pathExpected];
            }
            else
            {
                $result[] = ["http://site.com$path", ''];
            }
        }

        $result[] = ['http://site.com//path',       '//path'];
        $result[] = ['http://site.com',             ''];
        $result[] = ['http://site.com?key=value',   ''];
        $result[] = ['http://site.com/',            '/'];
        $result[] = ['http://site.com/?key=value',  '/'];

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
                $result[] = ["http://site.com?$query", $queryExpected];
            }
            else
            {
                $result[] = ["http://site.com?$query", ''];
            }
        }

        $result[] = ['http://site.com?',        ''];
        $result[] = ['http://site.com??key',    'key'];
        $result[] = ['http://site.com????key',  'key'];

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
        $allowedSpecialChars    = self::SCHEME_ALLOWED_SPECIAL_CHARS;
        $technicalSpecialChars  = self::URI_SEPARATING_CHARS;
        $incorrectSpecialChars  = array_keys(self::SPECIAL_CHARS);
        $incorrectSpecialChars  = array_filter
        (
            $incorrectSpecialChars,
            function($char) use ($allowedSpecialChars,  $technicalSpecialChars)
            {
                return
                    !in_array($char, $allowedSpecialChars) &&
                    !in_array($char, $technicalSpecialChars);
            }
        );
        $result                 =
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

                'scheme '       => 'scheme',
                ' scheme'       => 'scheme',
                's c h e m e'   => null
            ];

        foreach ($allowedSpecialChars as $char)
        {
            $schemes["scheme{$char}"]   = "scheme{$char}";
            $schemes["{$char}scheme"]   = null;
        }
        foreach ($incorrectSpecialChars as $char)
        {
            $schemes["scheme{$char}"]   = null;
            $schemes["{$char}scheme"]   = null;
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
            $key    = "[$key]";
            $value  = !is_null($value) ? "[$value]" : null;

            $ipAddressesV6Values[$key] = $value;
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
        $allowedSpecialChars    = self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS;
        $technicalSpecialChars  = self::URI_SEPARATING_CHARS;
        $incorrectSpecialChars  = array_keys(self::SPECIAL_CHARS);
        $incorrectSpecialChars  = array_filter
        (
            $incorrectSpecialChars,
            function($char) use ($allowedSpecialChars,  $technicalSpecialChars)
            {
                return
                    !in_array($char, $allowedSpecialChars) &&
                    !in_array($char, $technicalSpecialChars);
            }
        );
        $result                 =
            [
                'site.en'       => 'site.en',
                'site.com'      => 'site.com',
                'www.site.com'  => 'www.site.com',

                'Site.com'      => 'site.com',
                'SITE.com'      => 'site.com',
                'sItE.com'      => 'site.com',

                'site10.com'    => 'site10.com',
                '10site.com'    => '10site.com',
                '10.com'        => '10.com',

                'site.com '     => 'site.com',
                ' site.com'     => 'site.com',
                's i t e.com'   => null
            ];

        foreach ($allowedSpecialChars as $char)
        {
            $result["site{$char}.com"]  = "site{$char}.com";
            $result["{$char}site.com"]  = null;
        }
        foreach ($incorrectSpecialChars as $char)
        {
            $result["site{$char}.com"]  = null;
            $result["{$char}site.com"]  = null;
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
                '255.255.255.255'   => '255.255.255.255',

                '01.0.0.1'          => '1.0.0.1',
                '001.0.0.1'         => '1.0.0.1',
                '010.0.0.1'         => '10.0.0.1',

                '255.255.255.256'   => null,
                '255.255.255.999'   => null,
                '255.255.255.-1'    => null,

                '0.0.0.0.1'         => null,
                '0.0.0.0.0.1'       => null,
                '0.0.0.1.'          => null,
                '.0.0.0.1'          => null
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

                '1234:123::'                                => '1234:123::',
                '::1234:123'                                => '::1234:123',
                '1234:123::eeff:ABCD'                       => '1234:123::eeff:ABCD',
                '::'                                        => '::',
                '::10.10.1.10'                              => '::10.10.1.10',

                '01:5678:1357:2468:aabb:ccdd:eeff:ABCD'     => '1:5678:1357:2468:aabb:ccdd:eeff:ABCD',
                '1234:5678:1357:2468:aabb:ccdd:0:000'       => '1234:5678:1357:2468:aabb:ccdd::',
                '00:0000:1357:2468:aabb:ccdd:eeff:ABCD'     => '::1357:2468:aabb:ccdd:eeff:ABCD',
                '1234:5678:0:0:0:0:eeff:ABCD'               => '1234:5678::eeff:ABCD',

                'abcde::'                                   => null,
                '12345::'                                   => null,
                '12ab3::'                                   => null,

                '1111:2222:3333:aaaa:bbbb:cccc:1.0.0.1'     => '1111:2222:3333:aaaa:bbbb:cccc:1.0.0.1',
                '1111:2222:3333:aaaa::1.0.0.1'              => '1111:2222:3333:aaaa::1.0.0.1',
                '::3333:aaaa:bbbb:cccc:1.0.0.1'             => '::3333:aaaa:bbbb:cccc:1.0.0.1',
                '1111:2222::bbbb:cccc:1.0.0.1'              => '1111:2222::bbbb:cccc:1.0.0.1',

                '1111:2222:3333:aaaa:bbbb:cccc:1.0.0.256'   => null,
                '1111:2222:3333:aaaa:bbbb:cccc:1.0.0.1.2'   => null,
                '1111:2222:3333:aaaa:bbbb:gggg:1.0.0.1'     => null,
                '1111:2222:3333:aaaa:bbbb:cccc:::1.0.0.1'   => null,
                '1111:2222:3333:1.0.0.1:bbbb:cccc'          => null
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
        $technicalSpecialChars  = self::URI_SEPARATING_CHARS;
        $otherSpecialChars      = array_keys(self::SPECIAL_CHARS);
        $otherSpecialChars      = array_filter
        (
            $otherSpecialChars,
            function($char) use ($technicalSpecialChars)
            {
                return !in_array($char, $technicalSpecialChars);
            }
        );
        $result         =
            [
                'user'      => 'user',
                'login'     => 'login',
                'password'  => 'password',

                'Value'     => 'Value',
                'VALUE'     => 'VALUE',
                'vAlUe'     => 'vAlUe',

                'value10'   => 'value10',
                '10value'   => '10value',

                'value '    => 'value%20',
                ' value'    => '%20value',
                'v a l u e' => 'v%20a%20l%20u%20e'
            ];

        foreach ($otherSpecialChars as $char)
        {
            $charEncoded                    = self::SPECIAL_CHARS[$char];
            $result["value{$charEncoded}"]  = "value{$charEncoded}";
        }
        foreach ($otherSpecialChars as $char)
        {
            $charEncoded                    = self::SPECIAL_CHARS[$char];
            $result["value{$char}"]         = "value{$charEncoded}";
            $result["value{$charEncoded}"]  = "value{$charEncoded}";
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
        $allowedSpecialChars    = self::PATH_ALLOWED_SPECIAL_CHARS;
        $technicalSpecialChars  = self::URI_SEPARATING_CHARS;
        $otherSpecialChars      = array_keys(self::SPECIAL_CHARS);
        $otherSpecialChars      = array_filter
        (
            $otherSpecialChars,
            function($char) use ($allowedSpecialChars, $technicalSpecialChars)
            {
                return
                    !in_array($char, $allowedSpecialChars) &&
                    !in_array($char, $technicalSpecialChars);
            }
        );
        $result             =
            [
                '/path/'                    => '/path/',
                '/path1/path2/'             => '/path1/path2/',
                '/path1/path2/path3-path4'  => '/path1/path2/path3-path4',

                '/Path/'                    => '/Path/',
                '/PATH/'                    => '/PATH/',
                '/pAtH/'                    => '/pAtH/',

                '/path /'                   => '/path%20/',
                '/ path/'                   => '/%20path/',
                '/p a t h/'                 => '/p%20a%20t%20h/'
            ];

        foreach ($allowedSpecialChars as $char)
        {
            $result["/path{$char}/"] = "/path{$char}/";
        }
        foreach ($technicalSpecialChars as $char)
        {
            $charEncoded                    = urlencode($char);
            $result["/path{$charEncoded}/"] = "/path{$charEncoded}/";
        }
        foreach ($otherSpecialChars as $char)
        {
            $charEncoded                    = urlencode($char);
            $result["/path{$char}/"]        = "/path{$charEncoded}/";
            $result["/path{$charEncoded}/"] = "/path{$charEncoded}/";
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
        $allowedSpecialChars    = self::QUERY_ALLOWED_SPECIAL_CHARS;
        $technicalSpecialChars  = self::URI_SEPARATING_CHARS;
        $otherSpecialChars      = array_keys(self::SPECIAL_CHARS);
        $otherSpecialChars      = array_filter
        (
            $otherSpecialChars,
            function($char) use ($allowedSpecialChars, $technicalSpecialChars)
            {
                return
                    !in_array($char, $allowedSpecialChars) &&
                    !in_array($char, $technicalSpecialChars);
            }
        );
        $result             =
            [
                'key'                           => 'key',
                'value'                         => 'value',

                'Key'                           => 'Key',
                'KEY'                           => 'KEY',
                'kEy'                           => 'kEy',

                'key='                          => 'key',
                'key=value'                     => 'key=value',
                'Key=Value'                     => 'Key=Value',

                'key1=value2&key2'              => 'key1=value2&key2',
                'key1=value2&key2='             => 'key1=value2&key2',
                'key1=value2&key2=value2'       => 'key1=value2&key2=value2',

                'key1=value2&'                  => 'key1=value2',
                'key1=value2&=value2'           => 'key1=value2',
                'key1==value2&&key2===value2'   => 'key1=value2&key2=value2'
            ];

        foreach ($allowedSpecialChars as $char)
        {
            $result["{$char}={$char}"] = "{$char}={$char}";
        }
        foreach ($technicalSpecialChars as $char)
        {
            $charEncoded                                = urlencode($char);
            $result["{$charEncoded}={$charEncoded}"]    = "{$charEncoded}={$charEncoded}";
        }
        foreach ($otherSpecialChars as $char)
        {
            $charEncoded                                = urlencode($char);
            $result["{$char}={$char}"]                  = "{$charEncoded}={$charEncoded}";
            $result["{$charEncoded}={$charEncoded}"]    = "{$charEncoded}={$charEncoded}";
        }

        return $result;
    }
}