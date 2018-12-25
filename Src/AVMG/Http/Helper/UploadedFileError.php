<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * Uploaded file error class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UploadedFileError
{
    private const
        AVAILABLE_ERRORS =
            [
                UPLOAD_ERR_OK           => 'There is no error, the file uploaded with success',
                UPLOAD_ERR_INI_SIZE     => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE    => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified'.
                                           ' in the HTML form',
                UPLOAD_ERR_PARTIAL      => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE      => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR   => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE   => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION    => 'A PHP extension stopped the file upload'
            ],
        ERROR_OK        = UPLOAD_ERR_OK;
    /** **********************************************************************
     * Normalize error.
     *
     * @param   int $error                  Error.
     *
     * @return  int                         Normalized Error.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(int $error) : int
    {
        if (!array_key_exists($error, self::AVAILABLE_ERRORS))
        {
            throw new NormalizingException;
        }

        return $error;
    }
    /** **********************************************************************
     * Check error is critical.
     *
     * @param   int $error                  Error.
     *
     * @return  bool                        Error is critical.
     ************************************************************************/
    public static function isCritical(int $error) : bool
    {
        return $error != self::ERROR_OK;
    }
    /** **********************************************************************
     * Get error ok.
     *
     * @return  int                         Error ok.
     ************************************************************************/
    public static function getOk() : int
    {
        return self::ERROR_OK;
    }
    /** **********************************************************************
     * Get error phrase.
     *
     * @param   int $error                  Error.
     *
     * @return  string                      Error phrase.
     ************************************************************************/
    public static function getPhrase(int $error) : string
    {
        return self::AVAILABLE_ERRORS[$error] ?? '';
    }
}