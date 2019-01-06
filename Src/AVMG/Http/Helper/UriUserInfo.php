<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI user info class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriUserInfo
{
    /** **********************************************************************
     * Normalize the URI user info.
     *
     * @param   string $userInfo            URI user info.
     *
     * @return  string                      Normalized URI user info.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $userInfo) : string
    {
        $userInfoExplode    = explode(':', $userInfo);
        $login              = $userInfoExplode[0];
        $password           = $userInfoExplode[1] ?? '';

        try
        {
            $login = self::normalizePart($login);
        }
        catch (NormalizingException $exception)
        {
            throw new NormalizingException
            (
                "login validation error, \"{$exception->getMessage()}\"",
                0,
                $exception
            );
        }

        try
        {
            $password = self::normalizePart($password);
        }
        catch (NormalizingException $exception)
        {
            $password = '';
        }

        return strlen($password) > 0
            ? "$login:$password"
            : $login;
    }
    /** **********************************************************************
     * Normalize the URI user info from user login and password.
     *
     * @param   string  $login              URI user login.
     * @param   string  $password           URI user password.
     *
     * @return  string                      Normalized URI user info.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalizeFromParts(string $login, string $password) : string
    {
        try
        {
            return self::normalize("$login:$password");
        }
        catch (NormalizingException $exception)
        {
            throw $exception;
        }
    }
    /** **********************************************************************
     * Normalize the URI user info part.
     *
     * @param   string $value               URI user info part.
     *
     * @return  string                      Normalized URI user info part.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    private static function normalizePart(string $value) : string
    {
        if (strlen($value) <= 0)
        {
            throw new NormalizingException('value is empty string');
        }

        return rawurlencode(rawurldecode($value));
    }
}