<?php
declare(strict_types=1);

namespace AVMG\Http\Stream;

use
    RuntimeException,
    InvalidArgumentException,
    Psr\Http\Message\StreamInterface;
/** ***********************************************************************************************
 * PSR-7 StreamInterface implementation
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Stream extends AbstractStream implements StreamInterface
{
    private $resource = null;
    /** **********************************************************************
     * Constructor.
     *
     * @param   resource $resource          Resource.
     *
     * @throws  InvalidArgumentException    Argument is not resource.
     ************************************************************************/
    public function __construct($resource)
    {
        if (!is_resource($resource))
        {
            $argumentType = gettype($resource);

            throw new InvalidArgumentException
            (
                "argument must be resource, \"$argumentType\" caught"
            );
        }

        $this->resource = $resource;
    }
    /** **********************************************************************
     * Destructor.
     ************************************************************************/
    public function __destruct()
    {
        $this->close();
    }
    /** **********************************************************************
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null                Underlying PHP stream, if any.
     ************************************************************************/
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }
    /** **********************************************************************
     * Get the size of the stream if known.
     *
     * @return int|null                     Size in bytes if known, or null if unknown.
     ************************************************************************/
    public function getSize() : ?int
    {
        $resourceStats = is_resource($this->resource)
            ? fstat($this->resource)
            : [];

        return isset($resourceStats['size']) && is_numeric($resourceStats['size'])
            ? (int) $resourceStats['size']
            : null;
    }
    /** **********************************************************************
     * Returns the current position of the file read/write pointer.
     *
     * @return  int                         Position of the file pointer.
     * @throws  RuntimeException            Error.
     ************************************************************************/
    public function tell() : int
    {
        $cursorPosition = is_resource($this->resource)
            ? ftell($this->resource)
            : false;

        if ($cursorPosition === false)
        {
            throw new RuntimeException('stream reading error');
        }

        return $cursorPosition;
    }
    /** **********************************************************************
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool                         Stream is at the end of the stream.
     ************************************************************************/
    public function eof() : bool
    {
        return is_resource($this->resource)
            ? feof($this->resource)
            : true;
    }
    /** **********************************************************************
     * Seek to a position in the stream.
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @param   int $offset                 Stream offset.
     * @param   int $whence                 Specifies how the cursor position
     *                                      will be calculated based on the seek offset.
     *                                      Valid values are identical to the built-in
     *                                      PHP $whence values for `fseek()`.
     *                                      SEEK_SET: Set position equal to offset bytes
     *                                      SEEK_CUR: Set position to current location plus offset
     *                                      SEEK_END: Set position to end-of-stream plus offset.
     *
     * @return  void
     * @throws  RuntimeException            Failure.
     ************************************************************************/
    public function seek(int $offset, int $whence = SEEK_SET) : void
    {
        if (!$this->isSeekable())
        {
            throw new RuntimeException('stream is not seekable');
        }

        $seekResult = fseek($this->resource, $offset, $whence);
        if ($seekResult === -1)
        {
            throw new RuntimeException('stream reading error');
        }
    }
    /** **********************************************************************
     * Write data to the stream.
     *
     * @param   string $string              String that is to be written.
     *
     * @return  int                         Number of bytes written to the stream.
     * @throws  RuntimeException            Failure.
     ************************************************************************/
    public function write(string $string) : int
    {
        if (!$this->isWritable())
        {
            throw new RuntimeException('stream is not writable');
        }

        $writeResult = fwrite($this->resource, $string);
        if ($writeResult === false)
        {
            throw new RuntimeException('stream writing error');
        }

        return $writeResult;
    }
    /** **********************************************************************
     * Read data from the stream.
     *
     * @param   int $length                 Read up to $length bytes from the object
     *                                      and return them. Fewer than $length bytes
     *                                      may be returned if underlying stream
     *                                      call returns fewer bytes.
     *
     * @return  string                      Data read from the stream,
     *                                      or an empty string if no bytes are available.
     * @throws  RuntimeException            Error occurs.
     ************************************************************************/
    public function read(int $length) : string
    {
        if (!$this->isReadable())
        {
            throw new RuntimeException('stream is not readable');
        }
        if ($length < 0)
        {
            throw new RuntimeException('length parameter cannot be negative');
        }
        if ($length == 0)
        {
            return '';
        }

        $readResult = fread($this->resource, $length);
        if ($readResult === false)
        {
            throw new RuntimeException('stream reading error');
        }

        return $readResult;
    }
    /** **********************************************************************
     * Returns the remaining contents in a string
     *
     * @return  string                      Remaining contents.
     * @throws  RuntimeException            Unable to read or occurs while reading.
     ************************************************************************/
    public function getContents() : string
    {
        if (!$this->isReadable())
        {
            throw new RuntimeException('stream is not readable');
        }

        $readResult = stream_get_contents($this->resource);
        if ($readResult === false)
        {
            throw new RuntimeException('stream reading error');
        }

        return $readResult;
    }
    /** **********************************************************************
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param   string $key                 Specific metadata to retrieve.
     *
     * @return  array|mixed|null            Returns an associative array
     *                                      if no key is provided. Returns a specific
     *                                      key value if a key is provided and the
     *                                      value is found, or null if the key is not found.
     ************************************************************************/
    public function getMetadata(string $key = '')
    {
        if (!is_resource($this->resource))
        {
            return null;
        }

        $metaData = stream_get_meta_data($this->resource);

        if (is_null($key))
        {
            return $metaData;
        }

        return $metaData[$key] ?? null;
    }
}