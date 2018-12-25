<?php
declare(strict_types=1);

namespace AVMG\Http\Stream;

use
    RuntimeException,
    Psr\Http\Message\StreamInterface;
/** ***********************************************************************************************
 * PSR-7 StreamInterface implementation
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class Stream extends AbstractStream implements StreamInterface
{
    private $stream = null;
    /** **********************************************************************
     * Constructor.
     *
     * @param   resource $stream            Stream.
     ************************************************************************/
    public function __construct($stream)
    {
        if (is_resource($stream))
        {
            $this->stream = $stream;
            rewind($this->stream);
        }
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
        $stream = $this->stream;

        $this->stream = null;

        return $stream;
    }
    /** **********************************************************************
     * Get the size of the stream if known.
     *
     * @return int|null                     Size in bytes if known, or null if unknown.
     ************************************************************************/
    public function getSize() : ?int
    {
        $streamStats = is_resource($this->stream)
            ? fstat($this->stream)
            : [];

        return isset($streamStats['size']) && is_numeric($streamStats['size'])
            ? (int) $streamStats['size']
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
        if (!is_resource($this->stream))
        {
            throw new RuntimeException('no resource available');
        }

        $cursorPosition = ftell($this->stream);
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
        return is_resource($this->stream)
            ? feof($this->stream)
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
        if (!is_resource($this->stream))
        {
            throw new RuntimeException('no resource available');
        }
        if (!$this->isSeekable())
        {
            throw new RuntimeException('stream is not seekable');
        }

        $seekResult = fseek($this->stream, $offset, $whence);
        if ($seekResult !== 0)
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
        if (!is_resource($this->stream))
        {
            throw new RuntimeException('no resource available');
        }
        if (!$this->isWritable())
        {
            throw new RuntimeException('stream is not writable');
        }

        $writeResult = fwrite($this->stream, $string);
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
        if (!is_resource($this->stream))
        {
            throw new RuntimeException('no resource available');
        }
        if (!$this->isReadable())
        {
            throw new RuntimeException('stream is not readable');
        }

        $readResult = fread($this->stream, $length);
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
        if (!is_resource($this->stream))
        {
            throw new RuntimeException('no resource available');
        }
        if (!$this->isReadable())
        {
            throw new RuntimeException('stream is not readable');
        }

        $readResult = stream_get_contents($this->stream);
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
        $streamMeta = is_resource($this->stream)
            ? stream_get_meta_data($this->stream)
            : [];

        if (is_null($key))
        {
            return $streamMeta;
        }

        return $streamMeta[$key] ?? null;
    }
}