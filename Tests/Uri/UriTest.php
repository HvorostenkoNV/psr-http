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
                '`', '~', '!', '@', '"', '#', '№',
                '$', ';', '%', '^', ':', '&', '?',
                '*', '(', ')', '-', '_', '=', '+',
                '[', '{', ']', '}', '\\', '|',
                ',', '<', '.', '>', '/'
            ],
        URI_SEPARATING_CHARS                =
            [
                '/', '?', '&', ':', '@', '#'
            ],
        URI_UNCODED_CHARS                   =
            [
                '-', '_', '.', '~'
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
            ];
    /** **********************************************************************
     * Testing method "UriTest::getScheme".
     *
     * @test
     * @dataProvider    getSchemeDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedScheme     Expected scheme.
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
     * Testing method "UriTest::getHost".
     *
     * @test
     * @dataProvider    getHostDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedHost       Expected host.
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
     * Testing method "UriTest::getPort".
     *
     * @test
     * @dataProvider    getPortDataProvider
     *
     * @param           string  $uri                URI.
     * @param           mixed   $expectedPort       Expected port.
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
     * Testing method "UriTest::getUserInfo".
     *
     * @test
     * @dataProvider    getUserInfoDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedUserInfo   Expected user info.
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
     * Testing method "UriTest::getAuthority".
     *
     * @test
     * @dataProvider    getAuthorityDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedAuthority  Expected authority.
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
     * Testing method "UriTest::getPath".
     *
     * @test
     * @dataProvider    getPathDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedPath       Expected path.
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
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
            "Expected path form uri \"$uri\" is \"$expectedPath\".\n".
            "Caught path is \"$caughtPath\".\n"
        );
    }
    /** **********************************************************************
     * Testing method "UriTest::getQuery".
     *
     * @test
     * @dataProvider    getQueryDataProvider
     *
     * @param           string  $uri                URI.
     * @param           string  $expectedQuery      Expected query.
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
            "Method \"Uri::getAuthority\" returned unexpected result.\n".
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
        $schemeValidValues      = $this->getSchemeValidValues();
        $schemeInvalidValues    = $this->getSchemeInvalidValues();
        $result                 = [];

        foreach ($schemeValidValues as $scheme => $schemeExpected)
        {
            $result[] = ["$scheme://site.com", $schemeExpected];
        }
        foreach ($schemeInvalidValues as $scheme)
        {
            $result[] = ["$scheme://site.com", ''];
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
        $hostValidValues    = $this->getHostValidValues();
        $hostInvalidValues  = $this->getHostInvalidValues();
        $result             = [];

        foreach ($hostValidValues as $host => $hostExpected)
        {
            $result[] = ["http://$host", $hostExpected];
        }
        foreach ($hostInvalidValues as $host)
        {
            $result[] = ["http://$host", ''];
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
        $portValidValues    = $this->getPortValidValues();
        $portInvalidValues  = $this->getPortInvalidValues();
        $result             = [];

        foreach ($portValidValues as $port)
        {
            $result[] = ["http://site.com:$port", $port];
        }
        foreach ($portInvalidValues as $port)
        {
            $result[] = ["http://site.com:$port", null];
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
        $loginValidValues       = $this->getUserLoginValidValues();
        $loginInvalidValues     = $this->getUserLoginInvalidValues();
        $passwordValidValues    = $this->getUserPasswordValidValues();
        $passwordInvalidValues  = $this->getUserPasswordInvalidValues();
        $result                 = [];

        foreach ($loginValidValues as $login => $loginExpected)
        {
            $result[] = ["$login@site.com", $loginExpected];
        }
        foreach ($loginInvalidValues as $login)
        {
            $result[] = ["$login@site.com", ''];
        }
        foreach ($passwordValidValues as $password => $passwordExpected)
        {
            $result[] = ["user:$password@site.com", "user:$passwordExpected"];
        }
        foreach ($passwordInvalidValues as $password)
        {
            $result[] = ["user:$password@site.com", 'user'];
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
        $hostValidValues        = $this->getHostValidValues();
        $hostInvalidValues      = $this->getHostInvalidValues();
        $portValidValues        = $this->getPortValidValues();
        $portInvalidValues      = $this->getPortInvalidValues();
        $loginValidValues       = $this->getUserLoginValidValues();
        $loginInvalidValues     = $this->getUserLoginInvalidValues();
        $passwordValidValues    = $this->getUserPasswordValidValues();
        $passwordInvalidValues  = $this->getUserPasswordInvalidValues();
        $result                 = [];

        foreach ($hostValidValues as $host => $hostExpected)
        {
            $result[] =
                [
                    "http://user:password@$host:123/path/",
                    "user:password@$hostExpected:123"
                ];
        }
        foreach ($hostInvalidValues as $host)
        {
            $result[] =
                [
                    "http://user:password@$host:123/path/",
                    'user:password'
                ];
        }

        foreach ($portValidValues as $port)
        {
            $result[] =
                [
                    "http://user:password@site.com:$port/path/",
                    "user:password@site.com:$port"
                ];
        }
        foreach ($portInvalidValues as $port)
        {
            $result[] =
                [
                    "http://user:password@site.com:$port/path/",
                    'user:password@site.com'
                ];
        }

        foreach ($loginValidValues as $login => $loginExpected)
        {
            $result[] =
                [
                    "http://$login:password@site.com:123/path/",
                    "$loginExpected:password@site.com:123"
                ];
        }
        foreach ($loginInvalidValues as $login)
        {
            $result[] =
                [
                    "http://$login:password@site.com:123/path/",
                    'site.com:123'
                ];
        }
        foreach ($passwordValidValues as $password => $passwordExpected)
        {
            $result[] =
                [
                    "http://user:$password@site.com:123/path/",
                    "user:$passwordExpected@site.com:123"
                ];
        }
        foreach ($passwordInvalidValues as $password)
        {
            $result[] =
                [
                    "http://user:$password@site.com:123/path/",
                    'user@site.com:123'
                ];
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
        $pathValidValues    = $this->getPathValidValues();
        $pathInvalidValues  = $this->getPathInvalidValues();
        $result             = [];

        foreach ($pathValidValues as $path => $pathExpected)
        {
            $result[] = ["http://site.com$path", $pathExpected];
        }
        foreach ($pathInvalidValues as $path)
        {
            $result[] = ["http://site.com$path", ''];
        }

        $result[] = ['http://site.com//path',       '//path'];
        $result[] = ['http://site.com/path/',       '/path/'];
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
        $queryValidValues   = $this->getQueryValidValues();
        $queryInvalidValues = $this->getQueryInvalidValues();
        $result             = [];

        foreach ($queryValidValues as $query => $queryExpected)
        {
            $result[] = ["http://site.com?$query", $queryExpected];
        }
        foreach ($queryInvalidValues as $query)
        {
            $result[] = ["http://site.com?$query", ''];
        }

        $result[] = ['http://site.com?',        ''];
        $result[] = ['http://site.com??key',    'key'];
        $result[] = ['http://site.com????key',  'key'];

        return $result;
    }
    /** **********************************************************************
     * Get scheme valid values set.
     *
     * @return  array                               Scheme valid values set.
     ************************************************************************/
    private function getSchemeValidValues() : array
    {
        $result =
            [
                'http'      => 'http',
                'Http'      => 'http',
                'HTTP'      => 'http',
                'https'     => 'https',
                'ftp'       => 'ftp',
                'scheme'    => 'scheme',
                'scheme10'  => 'scheme10',
                'scheme '   => 'scheme',
                ' scheme'   => 'scheme'
            ];

        foreach (self::SCHEME_ALLOWED_SPECIAL_CHARS as $char)
        {
            $schemes["scheme{$char}"] = "scheme{$char}";
        }

        return $result;
    }
    /** **********************************************************************
     * Get scheme invalid values set.
     *
     * @return  array                               Scheme invalid values set.
     ************************************************************************/
    private function getSchemeInvalidValues() : array
    {
        $result =
            [
                '',
                '10scheme',
                's c h e m e'
            ];

        foreach (self::SCHEME_ALLOWED_SPECIAL_CHARS as $char)
        {
            $result[] = "{$char}scheme";
        }
        foreach (self::SPECIAL_CHARS as $char)
        {
            if
            (
                !in_array($char, self::URI_SEPARATING_CHARS) &&
                !in_array($char, self::SCHEME_ALLOWED_SPECIAL_CHARS)
            )
            {
                $result[] = "scheme{$char}";
                $result[] = "{$char}scheme";
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get host valid values set.
     *
     * @return  array                               Host valid values set.
     ************************************************************************/
    private function getHostValidValues() : array
    {
        $result =
            [
                'site'          => 'site',
                'Site'          => 'site',
                'SITE'          => 'site',
                'site '         => 'site',
                ' site'         => 'site',
                'site.com'      => 'site.com',
                'www.site.com'  => 'www.site.com',
                'site10.com'    => 'site10.com',
                '10site.com'    => '10site.com',
                '10.10.1.1'     => '10.10.1.1',
                '10.10.1'       => '10.10.1',
                '10.10'         => '10.10',
                '10'            => '10'
            ];

        foreach (self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS as $char)
        {
            $result["site{$char}.com"] = "site{$char}.com";
        }

        return $result;
    }
    /** **********************************************************************
     * Get host invalid values set.
     *
     * @return  array                               Host invalid values set.
     ************************************************************************/
    private function getHostInvalidValues() : array
    {
        $result =
            [
                's i t e'
            ];

        foreach (self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS as $char)
        {
            $result[] = "{$char}site.com";
        }
        foreach (self::SPECIAL_CHARS as $char)
        {
            if
            (
                !in_array($char, self::URI_SEPARATING_CHARS) &&
                !in_array($char, self::DOMAIN_NAME_ALLOWED_SPECIAL_CHARS)
            )
            {
                $result[] = "site{$char}.com";
                $result[] = "{$char}site.com";
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get user login valid values set.
     *
     * @return  array                               User login valid values set.
     ************************************************************************/
    private function getUserLoginValidValues() : array
    {
        $result =
            [
                'user'      => 'user',
                'User'      => 'User',
                'USER'      => 'USER',
                'user '     => 'user%20',
                ' user'     => '%20user',
                'u s e r'   => 'u%20s%20e%20r',
                'user10'    => 'user10',
                '10user'    => '10user'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (!in_array($char, self::URI_SEPARATING_CHARS))
            {
                $charEncoded = urlencode($char);

                if (in_array($char, self::URI_UNCODED_CHARS))
                {
                    $result["user{$char}"]          = "user{$char}";
                    $result["user{$charEncoded}"]   = "user{$char}";
                }
                else
                {
                    $result["user{$char}"]          = "user{$charEncoded}";
                    $result["user{$charEncoded}"]   = "user{$charEncoded}";
                }
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get user password invalid values set.
     *
     * @return  array                               User login invalid values set.
     ************************************************************************/
    private function getUserLoginInvalidValues() : array
    {
        return [];
    }
    /** **********************************************************************
     * Get user login valid values set.
     *
     * @return  array                               User password valid values set.
     ************************************************************************/
    private function getUserPasswordValidValues() : array
    {
        $result =
            [
                'password'          => 'password',
                'Password'          => 'Password',
                'PASSWORD'          => 'PASSWORD',
                'password '         => 'password%20',
                ' password'         => '%20password',
                'p a s s w o r d'   => 'p%20a%20s%20s%20w%20o%20r%20d',
                'password10'        => 'password10',
                '10password'        => '10password'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (!in_array($char, self::URI_SEPARATING_CHARS))
            {
                $charEncoded = urlencode($char);

                if (in_array($char, self::URI_UNCODED_CHARS))
                {
                    $result["password{$char}"]          = "password{$char}";
                    $result["password{$charEncoded}"]   = "password{$char}";
                }
                else
                {
                    $result["password{$char}"]          = "password{$charEncoded}";
                    $result["password{$charEncoded}"]   = "password{$charEncoded}";
                }
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get user password invalid values set.
     *
     * @return  array                               User password invalid values set.
     ************************************************************************/
    private function getUserPasswordInvalidValues() : array
    {
        return [''];
    }
    /** **********************************************************************
     * Get port valid values set.
     *
     * @return  array                               Port valid values set.
     ************************************************************************/
    private function getPortValidValues() : array
    {
        $result =
            [
                self::PORT_MIN_VALUE,
                self::PORT_MAX_VALUE
            ];

        for ($index = 10; $index > 0; $index--)
        {
            $result[] = rand(self::PORT_MIN_VALUE, self::PORT_MAX_VALUE);
        }

        return $result;
    }
    /** **********************************************************************
     * Get port invalid values set.
     *
     * @return  array                               Port invalid values set.
     ************************************************************************/
    private function getPortInvalidValues() : array
    {
        $result =
            [
                self::PORT_MIN_VALUE - 1,
                self::PORT_MAX_VALUE + 1,
                'someString'
            ];

        for ($index = 5; $index > 0; $index--)
        {
            $result[] = rand(self::PORT_MIN_VALUE - 100, self::PORT_MIN_VALUE - 1);
        }
        for ($index = 5; $index > 0; $index--)
        {
            $result[] = rand(self::PORT_MAX_VALUE + 1, self::PORT_MAX_VALUE + 100);
        }

        return $result;
    }
    /** **********************************************************************
     * Get path valid values set.
     *
     * @return  array                               Path valid values set.
     ************************************************************************/
    private function getPathValidValues() : array
    {
        $result =
            [
                '/path/'                    => '/path/',
                '/Path/'                    => '/Path/',
                '/PATH/'                    => '/PATH/',
                '/path /'                   => '/path%20/',
                '/ path/'                   => '/%20path/',
                '/p a t h/'                 => '/p%20a%20t%20h/',
                '/path1/path2/'             => '/path1/path2/',
                '/path1/path2/path3-path4'  => '/path1/path2/path3-path4'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (!in_array($char, self::URI_SEPARATING_CHARS))
            {
                $charEncoded = urlencode($char);

                if (in_array($char, self::URI_UNCODED_CHARS))
                {
                    $result["/path{$char}/"]        = "/path{$char}/";
                    $result["/path{$charEncoded}/"] = "/path{$char}/";
                }
                else
                {
                    $result["/path{$char}/"]        = "/path{$charEncoded}/";
                    $result["/path{$charEncoded}/"] = "/path{$charEncoded}/";
                }
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get path invalid values set.
     *
     * @return  array                               Path invalid values set.
     ************************************************************************/
    private function getPathInvalidValues() : array
    {
        return [];
    }
    /** **********************************************************************
     * Get query valid values set.
     *
     * @return  array                               Query valid values set.
     ************************************************************************/
    private function getQueryValidValues() : array
    {
        $result =
            [
                'key'                       => 'key',
                'Key'                       => 'Key',
                'KEY'                       => 'KEY',
                'key='                      => 'key',
                'key=value'                 => 'key=value',
                'Key=Value'                 => 'Key=Value',
                'key1=value2&'              => 'key1=value2',
                'key1=value2&key2'          => 'key1=value2&key2',
                'key1=value2&key2='         => 'key1=value2&key2',
                'key1=value2&key2=value2'   => 'key1=value2&key2=value2',
                'key1=value2&=value2'       => 'key1=value2'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            if (!in_array($char, self::URI_SEPARATING_CHARS))
            {
                $charEncoded = urlencode($char);

                if (in_array($char, self::URI_UNCODED_CHARS))
                {
                    $result["key{$char}=value{$char}"]                  = "key{$char}=value{$char}";
                    $result["key{$charEncoded}=value{$charEncoded}"]    = "key{$char}=value{$char}";
                }
                else
                {
                    $result["key{$char}=value{$char}"]                  = "key{$charEncoded}=value{$charEncoded}";
                    $result["key{$charEncoded}=value{$charEncoded}"]    = "key{$charEncoded}=value{$charEncoded}";
                }
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get query invalid values set.
     *
     * @return  array                               Query invalid values set.
     ************************************************************************/
    private function getQueryInvalidValues() : array
    {
        return [];
    }
}