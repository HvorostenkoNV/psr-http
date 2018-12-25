<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use
    RuntimeException,
    InvalidArgumentException,
    AVMG\Http\Exception\NormalizingException,
    SplFileInfo,
    Psr\Http\Message\StreamInterface,
    Psr\Http\Message\StreamFactoryInterface,
    AVMG\Http\Helper\ResourceAccessMode,
    AVMG\Http\Stream\Stream;
/** ***********************************************************************************************
 * PSR-7 StreamFactoryInterface implementation.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class StreamFactory implements StreamFactoryInterface
{
    /** **********************************************************************
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param   string $content             String content with which to populate the stream.
     *
     * @return  StreamInterface             New stream.
     ************************************************************************/
    public function createStream(string $content = '') : StreamInterface
    {
        $mode       = ResourceAccessMode::get('readWrite');
        $resource   = fopen('php://temp', $mode);

        if ($resource !== false && strlen($content) > 0)
        {
            fwrite($resource, $content);
        }

        return $this->createStreamFromResource($resource);
    }
    /** **********************************************************************
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param   string  $filename           The filename or stream URI to use
     *                                      as basis of stream.
     * @param   string  $mode               The mode with which to open
     *                                      the underlying filename/stream.
     *
     * @return  StreamInterface             Stream.
     * @throws  RuntimeException            File cannot be opened.
     * @throws  InvalidArgumentException    Mode is invalid.
     ************************************************************************/
    public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
    {
        $file = new SplFileInfo($filename);

        if (!$file->isFile())
        {
            throw new RuntimeException("file \"$filename\" not found");
        }
        if (!$file->isReadable())
        {
            throw new RuntimeException("file \"$filename\" is not readable");
        }

        try
        {
            $modeNormalized = ResourceAccessMode::normalize($mode);
            $resource       = fopen($file->getPathname(), $modeNormalized);

            return $this->createStreamFromResource($resource);
        }
        catch (NormalizingException $exception)
        {
            throw new InvalidArgumentException("mode \"$mode\" is invalid");
        }
    }
    /** **********************************************************************
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param   resource $resource          The PHP resource to use as
     *                                      the basis for the stream.
     *
     * @return  StreamInterface             Stream.
     ************************************************************************/
    public function createStreamFromResource($resource) : StreamInterface
    {
        return new Stream($resource);
    }
}