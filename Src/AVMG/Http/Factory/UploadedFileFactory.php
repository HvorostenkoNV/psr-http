<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use InvalidArgumentException;
use Psr\Http\{
    Message\StreamInterface,
    Message\UploadedFileInterface,
    Message\UploadedFileFactoryInterface
};
use AVMG\Http\UploadedFile\UploadedFile;
/** ***********************************************************************************************
 * PSR-7 UploadedFileFactoryInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function createUploadedFile
    (
        StreamInterface $stream,
        ?int            $size               = null,
        ?int            $error              = UPLOAD_ERR_OK,
        ?string         $clientFilename     = null,
        ?string         $clientMediaType    = null
    ): UploadedFileInterface
    {
        try {
            return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), 0, $exception);
        }
    }
}