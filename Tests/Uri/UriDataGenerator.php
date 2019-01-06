<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;
/** ***********************************************************************************************
 * URI data generator class.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UriDataGenerator
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
        PORT_MAX_VALUE                      = 65535,
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
     * Get scheme values map.
     *
     * @return  array                               Scheme values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    public static function getSchemeValues() : array
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
    public static function getHostValues() : array
    {
        $ipAddressesV6ValuesRaw = self::getIpAddressesV6Values();
        $ipAddressesV6Values    = [];

        foreach ($ipAddressesV6ValuesRaw as $key => $value)
        {
            $ipAddressesV6Values["[$key]"] = !is_null($value)
                ? "[$value]"
                : null;
        }

        return array_merge
        (
            self::getDomainNamesValues(),
            self::getIpAddressesV4Values(),
            $ipAddressesV6Values
        );
    }
    /** **********************************************************************
     * Get domain names values map.
     *
     * @return  array                               Domain names values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    public static function getDomainNamesValues() : array
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
    public static function getIpAddressesV4Values() : array
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
    public static function getIpAddressesV6Values() : array
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
    public static function getPortValues() : array
    {
        $maxAvailableValue  = self::PORT_MAX_VALUE;
        $result             =
            [
                0                       => 0,
                -1                      => null,
                $maxAvailableValue      => $maxAvailableValue,
                $maxAvailableValue + 1  => null
            ];

        for ($index = 5; $index > 0; $index--)
        {
            $value          = rand(0, $maxAvailableValue);
            $result[$value] = $value;
        }
        for ($index = 5; $index > 0; $index--)
        {
            $value          = rand(-100, -1);
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
    public static function getUserInfoValues() : array
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
    public static function getPathValues() : array
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
    public static function getQueryValues() : array
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
    /** **********************************************************************
     * Get fragment values map.
     *
     * @return  array                               Fragment values map, where key is raw value
     *                                              and value is value expected.
     ************************************************************************/
    public static function getFragmentValues() : array
    {
        $result =
            [
                'fragment'          => 'fragment',
                'Fragment'          => 'Fragment',
                'FRAGMENT'          => 'FRAGMENT',
                'fRaGmEnT'          => 'fRaGmEnT',

                'fragment '         => 'fragment%20',
                ' fragment'         => '%20fragment',
                'f r a g m e n t'   => 'f%20r%20a%20g%20m%20e%20n%20t',

                'fragment10'        => 'fragment10',
                '10fragment'        => '10fragment'
            ];

        foreach (self::SPECIAL_CHARS as $char)
        {
            $charEncoded                                    = urlencode($char);
            $result["{$char}fragment{$char}"]               = "{$char}fragment{$char}";
            $result["{$charEncoded}fragment{$charEncoded}"] = "{$charEncoded}fragment{$charEncoded}";
        }

        return $result;
    }
}