<?php
declare(strict_types=1);

namespace AVMG\Http\Stream;

use InvalidArgumentException;
use RuntimeException;
use Psr\Http\Message\StreamInterface;
use AVMG\Http\{
    Normalizer\NormalizingException,
    Normalizer\NormalizerProxy,
    Collection\CollectionProxy
};

use function in_array;
/** ***********************************************************************************************
 * PSR-7 StreamInterface abstract implementation.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
abstract class AbstractStream implements StreamInterface
{
    /** **********************************************************************
     * Destructor.
     ************************************************************************/
    public function __destruct()
    {
        $this->close();
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (RuntimeException $exception) {
            return '';
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function eof(): bool
    {
        return $this->getMetadata('eof') === true;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function isSeekable(): bool
    {
        return $this->getMetadata('seekable') === true;
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function rewind(): void
    {
        try {
            $this->seek(0);
        } catch (RuntimeException $exception) {
            throw $exception;
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function isWritable(): bool
    {
        try {
            $value              = (string) $this->getMetadata('mode');
            $valueNormalized    = (string) NormalizerProxy::normalize('resource.accessMode', $value);
            $availableValues    = CollectionProxy::receive('resource.accessMode.writable');

            return in_array($valueNormalized, $availableValues);
        } catch (InvalidArgumentException | NormalizingException $exception) {
            return false;
        }
    }
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function isReadable(): bool
    {
        try {
            $value              = (string) $this->getMetadata('mode');
            $valueNormalized    = (string) NormalizerProxy::normalize('resource.accessMode', $value);
            $availableValues    = CollectionProxy::receive('resource.accessMode.readable');

            return in_array($valueNormalized, $availableValues);
        } catch (InvalidArgumentException | NormalizingException $exception) {
            return false;
        }
    }
}