<?php
declare(strict_types=1);

namespace AVMG\Http\Stream;

use
    RuntimeException,
    Psr\Http\Message\StreamInterface,
    AVMG\Http\Helper\ResourceAccessMode;
/** ***********************************************************************************************
 * PSR-7 StreamInterface abstract implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
abstract class AbstractStream implements StreamInterface
{
    /** **********************************************************************
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     *
     * @return  string                      All data from the stream.
     ************************************************************************/
    public function __toString() : string
    {
        try
        {
            $this->rewind();

            return $this->getContents();
        }
        catch (RuntimeException $exception)
        {
            return '';
        }
    }
    /** **********************************************************************
     * Closes the stream and any underlying resources.
     *
     * @return void
     ************************************************************************/
    public function close() : void
    {
        $resource = $this->detach();

        if (is_resource($resource))
        {
            fclose($resource);
        }
    }
    /** **********************************************************************
     * Returns whether or not the stream is seekable.
     *
     * @return bool                         Stream is seekable.
     ************************************************************************/
    public function isSeekable() : bool
    {
        return $this->getMetadata('seekable') === true;
    }
    /** **********************************************************************
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @return  void
     * @throws  RuntimeException            Failure.
     ************************************************************************/
    public function rewind() : void
    {
        try
        {
            $this->seek(0);
        }
        catch (RuntimeException $exception)
        {
            throw $exception;
        }
    }
    /** **********************************************************************
     * Returns whether or not the stream is writable.
     *
     * @return bool                         Stream is writable.
     ************************************************************************/
    public function isWritable() : bool
    {
        $mode = (string) $this->getMetadata('mode');

        return ResourceAccessMode::isWritable($mode);
    }
    /** **********************************************************************
     * Returns whether or not the stream is readable.
     *
     * @return bool                         Stream is readable.
     ************************************************************************/
    public function isReadable() : bool
    {
        $mode = (string) $this->getMetadata('mode');

        return ResourceAccessMode::isReadable($mode);
    }
}