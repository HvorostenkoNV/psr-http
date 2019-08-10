<?php
declare(strict_types=1);

namespace AVMG\Http\Factory;

use InvalidArgumentException;
use RuntimeException;
use Psr\Http\{
    Message\StreamInterface,
    Message\StreamFactoryInterface
};
use AVMG\Http\{
    Normalizer\NormalizingException,
    Normalizer\NormalizerProxy,
    Collection\CollectionProxy,
    Stream\Stream
};

use function strlen;
use function fopen;
use function array_shift;
use function file_exists;
/** ***********************************************************************************************
 * PSR-7 StreamFactoryInterface implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class StreamFactory implements StreamFactoryInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function createStream(string $content = ''): StreamInterface
    {
        try {
            $modesSet   = CollectionProxy::receive('resource.accessMode.readableAndWritable');
            $mode       = array_shift($modesSet);
        } catch (InvalidArgumentException $exception) {
            $mode       = '';
        }

        $resource   = fopen('php://temp', $mode);
        $stream     = $this->createStreamFromResource($resource);

        if (strlen($content) > 0) {
            try {
                $stream->write($content);
                $stream->rewind();
            } catch (RuntimeException $exception) {

            }
        }

        return $stream;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (!file_exists($filename)) {
            throw new RuntimeException("file \"$filename\" is not exist");
        }

        try {
            $modeNormalized = NormalizerProxy::normalize('resource.accessMode', $mode);
            $resource       = fopen($filename, $modeNormalized);
        } catch (InvalidArgumentException | NormalizingException $exception) {
            throw new InvalidArgumentException("mode \"$mode\" is invalid", 0, $exception);
        }

        if ($resource === false) {
            throw new RuntimeException("file \"$filename\" cannot be opened");
        }

        return $this->createStreamFromResource($resource);
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function createStreamFromResource($resource): StreamInterface
    {
        $stream = new Stream($resource);

        try {
            $stream->rewind();
        } catch (RuntimeException $exception) {

        }

        return $stream;
    }
}