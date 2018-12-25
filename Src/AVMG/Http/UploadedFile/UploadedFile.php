<?php
declare(strict_types=1);

namespace AVMG\Http\UploadedFile;

use
    InvalidArgumentException,
    RuntimeException,
    AVMG\Http\Exception\NormalizingException,
    SplFileInfo,
    Psr\Http\Message\StreamInterface,
    Psr\Http\Message\UploadedFileInterface,
    AVMG\Http\Helper\UploadedFileError,
    AVMG\Http\Helper\ResourceAccessMode;
/** ***********************************************************************************************
 * PSR-7 UploadedFileInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UploadedFile implements UploadedFileInterface
{
    private
        $stream             = null,
        $size               = null,
        $error              = null,
        $clientFilename     = '',
        $clientMediaType    = '',
        $isMoved            = false;
    /** **********************************************************************
     * Constructor.
     *
     * @param   StreamInterface $stream             The underlying stream representing
     *                                              the uploaded file content.
     * @param   int             $size               The size of the file in bytes.
     * @param   int             $error              The PHP file upload error.
     * @param   string          $clientFilename     The filename as provided by
     *                                              the client, if any.
     * @param   string          $clientMediaType    The media type as provided by
     *                                              the client, if any.
     *
     * @throws  InvalidArgumentException            File resource is not readable.
     ************************************************************************/
    public function __construct
    (
        StreamInterface $stream,
        int             $size               = null,
        int             $error              = UPLOAD_ERR_OK,
        string          $clientFilename     = null,
        string          $clientMediaType    = null
    )
    {
        if (!$stream->isReadable())
        {
            throw new InvalidArgumentException('resource is not readable');
        }

        $this->stream   = $stream;
        $this->size     = $size;

        try
        {
            $this->error = UploadedFileError::normalize($error);
        }
        catch (NormalizingException $exception)
        {

        }

        $this->clientFilename   = $clientFilename;
        $this->clientMediaType  = $clientMediaType;
    }
    /** **********************************************************************
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return  StreamInterface             Stream representation of the uploaded file.
     * @throws  RuntimeException            No stream is available or no stream can be created.
     ************************************************************************/
    public function getStream() : StreamInterface
    {
        $error = $this->getError();

        if (UploadedFileError::isCritical($error))
        {
            $errorMessage = UploadedFileError::getPhrase($error);
            throw new RuntimeException("cannot retrieve stream due to upload error \"$errorMessage\"");
        }
        if ($this->isMoved)
        {
            throw new RuntimeException('cannot retrieve stream after it has been already moved');
        }

        return $this->stream;
    }
    /** **********************************************************************
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     *
     * @param   string $targetPath          Path to which to move the uploaded file.
     *
     * @return  void
     * @throws  InvalidArgumentException    $targetPath specified is invalid.
     * @throws  RuntimeException            On any error during the move operation or
     *                                      on the second or subsequent call to the method.
     ************************************************************************/
    public function moveTo(string $targetPath) : void
    {
        $targetFile         = new SplFileInfo($targetPath);
        $targetDirectory    = new SplFileInfo($targetFile->getPath());
        $error              = $this->getError();

        if (UploadedFileError::isCritical($error))
        {
            $errorMessage = UploadedFileError::getPhrase($error);
            throw new RuntimeException("cannot retrieve stream due to upload error \"$errorMessage\"");
        }
        if ($this->isMoved)
        {
            throw new RuntimeException('cannot move file; already moved!');
        }
        if (!$targetDirectory->isDir())
        {
            $path = $targetDirectory->getPathname();
            throw new InvalidArgumentException("target directory \"$path\" does not exist");
        }
        if (!$targetDirectory->isWritable())
        {
            $path = $targetDirectory->getPathname();
            throw new InvalidArgumentException("target directory \"$path\" is not writable");
        }

        try
        {
            $this->writeStreamToFile($targetFile);
        }
        catch (RuntimeException $exception)
        {
            throw $exception;
        }
    }
    /** **********************************************************************
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null                     File size in bytes or null if unknown.
     ************************************************************************/
    public function getSize() : ?int
    {
        if (!is_null($this->size))
        {
            return $this->size;
        }

        try
        {
            return $this->getStream()->getSize();
        }
        catch (RuntimeException $exception)
        {
            return null;
        }
    }
    /** **********************************************************************
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     *
     * @return int                          One of PHP's UPLOAD_ERR_XXX constants.
     ************************************************************************/
    public function getError() : int
    {
        return !is_null($this->error)
            ? $this->error
            : UploadedFileError::getOk();
    }
    /** **********************************************************************
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null                  Filename sent by the client or null
     *                                      if none was provided.
     ************************************************************************/
    public function getClientFilename() : ?string
    {
        return $this->clientFilename;
    }
    /** **********************************************************************
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null                  Media type sent by the client or null
     *                                      if none was provided.
     ************************************************************************/
    public function getClientMediaType() : ?string
    {
        return $this->clientMediaType;
    }
    /** **********************************************************************
     * Write instance stream to file
     *
     * @param   SplFileInfo $file           File.
     *
     * @return  void
     * @throws  RuntimeException            File writing error.
     ************************************************************************/
    private function writeStreamToFile(SplFileInfo $file) : void
    {
        $filePath           = $file->getPathname();
        $needStreamMode     = ResourceAccessMode::get('readWrite', 'begin', true, true);
        $newFileStream      = fopen($filePath, $needStreamMode);

        if ($newFileStream === false)
        {
            throw new RuntimeException("unable to write file to \"$filePath\"");
        }

        try
        {
            $currentFileStream = $this->getStream();
            $currentFileStream->rewind();
            while (!$currentFileStream->eof())
            {
                fwrite($newFileStream, $currentFileStream->read(4096));
            }

        }
        catch (RuntimeException $exception)
        {
            throw $exception;
        }

        fclose($newFileStream);
    }
}