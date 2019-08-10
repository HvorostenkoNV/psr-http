<?php
declare(strict_types=1);

namespace AVMG\Http\Stream;

use TypeError;
use RuntimeException;
use Psr\Http\Message\StreamInterface;

use function gettype;
use function is_numeric;
use function is_resource;
use function fread;
use function fwrite;
use function fstat;
use function ftell;
use function fseek;
use function fclose;
use function stream_get_contents;
use function stream_get_meta_data;

use const SEEK_SET;
/** ***********************************************************************************************
 * PSR-7 StreamInterface implementation
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class Stream extends AbstractStream implements StreamInterface
{
    private $resource = null;
    /** **********************************************************************
     * Constructor.
     *
     * @param   resource $resource          Resource.
     ************************************************************************/
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            $argumentType   = gettype($resource);
            $methodName     = __METHOD__;
            $error          = "Argument 1 passed to $methodName() must be of the type resource, $argumentType given";

            throw new TypeError($error);
        }

        $this->resource = $resource;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function close(): void
    {
        $resource = $this->detach();

        if (is_resource($resource)) {
            fclose($resource);
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getSize(): ?int
    {
        $resourceStats = is_resource($this->resource)
            ? fstat($this->resource)
            : [];

        return isset($resourceStats['size']) && is_numeric($resourceStats['size'])
            ? (int) $resourceStats['size']
            : null;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function tell(): int
    {
        $cursorPosition = is_resource($this->resource)
            ? ftell($this->resource)
            : false;

        if ($cursorPosition === false) {
            throw new RuntimeException('stream reading error');
        }

        return $cursorPosition;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException('stream is not seekable');
        }

        $seekResult = fseek($this->resource, $offset, $whence);

        if ($seekResult === -1) {
            throw new RuntimeException('stream reading error');
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new RuntimeException('stream is not writable');
        }

        $writeResult = fwrite($this->resource, $string);
        if ($writeResult === false) {
            throw new RuntimeException('stream writing error');
        }

        return $writeResult;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('stream is not readable');
        }
        if ($length < 0) {
            throw new RuntimeException('length parameter cannot be negative');
        }
        if ($length === 0) {
            return '';
        }

        $readResult = fread($this->resource, $length);
        if ($readResult === false) {
            throw new RuntimeException('stream reading error');
        }

        return $readResult;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('stream is not readable');
        }

        $readResult = stream_get_contents($this->resource);

        if ($readResult === false) {
            throw new RuntimeException('stream reading error');
        }

        return $readResult;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function getMetadata(string $key = '')
    {
        $isResource = is_resource($this->resource);
        $metaData   = $isResource
            ? stream_get_meta_data($this->resource)
            : [
                'timed_out'     => true,
                'blocked'       => true,
                'eof'           => true,
                'unread_bytes'  => 0,
                'stream_type'   => '',
                'wrapper_type'  => '',
                'wrapper_data'  => '',
                'mode'          => '',
                'seekable'      => false,
                'uri'           => ''
            ];

        if ($key === '') {
            return $isResource ? $metaData : null;
        }

        return $metaData[$key] ?? null;
    }
}