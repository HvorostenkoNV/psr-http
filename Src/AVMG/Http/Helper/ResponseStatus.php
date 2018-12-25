<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * HTTP response status class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class ResponseStatus
{
    private const
        AVAILABLE_STATUSES  =
            [
                // INFORMATIONAL CODES
                100 => 'Continue',
                101 => 'Switching Protocols',
                102 => 'Processing',
                103 => 'Early Hints',
                // SUCCESS CODES
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                207 => 'Multi-Status',
                208 => 'Already Reported',
                226 => 'IM Used',
                // REDIRECTION CODES
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => 'Switch Proxy',
                307 => 'Temporary Redirect',
                308 => 'Permanent Redirect',
                // CLIENT ERROR
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Payload Too Large',
                414 => 'URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Range Not Satisfiable',
                417 => 'Expectation Failed',
                418 => 'I\'m a teapot',
                421 => 'Misdirected Request',
                422 => 'Unprocessable Entity',
                423 => 'Locked',
                424 => 'Failed Dependency',
                425 => 'Too Early',
                426 => 'Upgrade Required',
                428 => 'Precondition Required',
                429 => 'Too Many Requests',
                431 => 'Request Header Fields Too Large',
                444 => 'Connection Closed Without Response',
                451 => 'Unavailable For Legal Reasons',
                // SERVER ERROR
                499 => 'Client Closed Request',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                506 => 'Variant Also Negotiates',
                507 => 'Insufficient Storage',
                508 => 'Loop Detected',
                510 => 'Not Extended',
                511 => 'Network Authentication Required',
                599 => 'Network Connect Timeout Error'
            ],
        STATUS_OK           = 200;
    /** **********************************************************************
     * Normalize the HTTP response status.
     *
     * @param   int $status                 HTTP response status.
     *
     * @return  int                         Normalized HTTP response status.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(int $status) : int
    {
        if (!array_key_exists($status, self::AVAILABLE_STATUSES))
        {
            throw new NormalizingException;
        }

        return $status;
    }
    /** **********************************************************************
     * Get available HTTP response statuses.
     *
     * @return  int[]                       Available HTTP response statuses.
     ************************************************************************/
    public static function getAvailableValues() : array
    {
        return array_keys(self::AVAILABLE_STATUSES);
    }
    /** **********************************************************************
     * Get status OK
     *
     * @return  int                         Status OK.
     ************************************************************************/
    public static function getStatusOk() : int
    {
        return self::STATUS_OK;
    }
    /** **********************************************************************
     * Get status phrase.
     *
     * @param   int $status                 HTTP response status.
     *
     * @return  string                      Status phrase.
     ************************************************************************/
    public static function getPhrase(int $status) : string
    {
        return self::AVAILABLE_STATUSES[$status] ?? '';
    }
}