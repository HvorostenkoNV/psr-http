<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Stream;

use Throwable;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use AVMG\Http\Tests\Collection\CollectionMediator;
use AVMG\Http\Stream\Stream;

use function is_file;
use function is_resource;
use function rand;
use function str_repeat;
use function mb_strlen;
use function substr;
use function array_diff;
use function fopen;
use function ftell;
use function fwrite;
use function fseek;
use function feof;
use function fgetc;
use function rewind;
use function stream_get_contents;
use function stream_get_meta_data;
use function unlink;
use function sys_get_temp_dir;
use function tempnam;

use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;
/** ***********************************************************************************************
 * PSR-7 StreamInterface implementation test.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class StreamTest extends TestCase
{
    private static $temporaryFiles = [];
    /** **********************************************************************
     * Test "Stream::__destruct" closes underlying resource.
     *
     * @dataProvider    dataProviderResourcesReadableAndWritable
     *
     * @param           resource $resource              Recourse.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testDestructorClosesResource($resource): void
    {
        $stream = new Stream($resource);

        unset($stream);

        self::assertFalse(
            is_resource($resource),
            "Stream object showed unexpected behavior.\n".
            "Expects underlying stream resource will be closed after stream object will be destroyed\n".
            "Underlying stream resource is not closed.\n"
        );
    }
    /** **********************************************************************
     * Test Stream object converts to string.
     *
     * @dataProvider    dataProviderResourcesWithData
     *
     * @param           resource    $resource           Recourse.
     * @param           string      $content            Recourse content.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testToStringConverting($resource, string $content): void
    {
        $stream = new Stream($resource);

        self::assertEquals(
            $content,
            (string) $stream,
            "Stream object converting to string returned unexpected result.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::close" closes underlying resource.
     *
     * @dataProvider    dataProviderResourcesReadableAndWritable
     *
     * @param           resource $resource              Recourse.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testClose($resource): void
    {
        $stream = new Stream($resource);

        $stream->close();

        self::assertFalse(
            is_resource($resource),
            "Method \"Stream::close\" showed unexpected behavior.\n".
            "Expects underlying stream resource will be closed after calling method \"Stream::close\".\n".
            "Underlying stream resource is not closed.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::close" DO NOT closes underlying resource, if stream is detached.
     *
     * @dataProvider    dataProviderResourcesReadableAndWritable
     *
     * @param           resource $resource              Recourse.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testCloseDoNothingWithDetachedResource($resource): void
    {
        $stream             = new Stream($resource);
        $resourceDetached   = $stream->detach();

        $stream->close();

        self::assertTrue(
            is_resource($resourceDetached),
            "Method \"Stream::close\" showed unexpected behavior.\n".
            "Expects underlying stream resource will be NOT closed after it was detached\n".
            "Underlying stream resource is closed.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::detach" provides recourse.
     *
     * @dataProvider    dataProviderResourcesReadableAndWritable
     *
     * @param           resource $resource              Recourse.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testDetach($resource): void
    {
        $stream = new Stream($resource);

        self::assertEquals(
            $resource,
            $stream->detach(),
            "Method \"Stream::detach\" returned unexpected result.\n".
            "Expects get the same resource as was set on stream constructing\n".
            "Caught resource is not the same.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getSize" provides recourse data size.
     *
     * @dataProvider    dataProviderResourcesWithDataSize
     *
     * @param           resource    $resource           Recourse.
     * @param           mixed       $expectedSize       Recourse content expected size.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetSize($resource, $expectedSize): void
    {
        $providedSize = (new Stream($resource))->getSize();

        self::assertEquals(
            $expectedSize,
            $providedSize,
            "Method \"Stream::getSize\" returned unexpected result.\n".
            "Expected result is \"$expectedSize\".\n".
            "Caught result is \"$providedSize\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::tell" provides recourse current position pointer.
     *
     * @dataProvider    dataProviderResourcesWithPointerValidValues
     *
     * @param           resource    $resource           Recourse.
     * @param           int         $expectedPosition   Recourse content expected
     *                                                  position pointer.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testTell($resource, int $expectedPosition): void
    {
        $providedPosition = (new Stream($resource))->tell();

        self::assertEquals(
            $expectedPosition,
            $providedPosition,
            "Method \"Stream::tell\" returned unexpected result.\n".
            "Expected result is \"$expectedPosition\".\n".
            "Caught result is \"$providedPosition\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::tell" throws exception with stream reading error.
     *
     * @dataProvider        dataProviderResourcesWithPointerInvalidValues
     * @expectedException   RuntimeException
     *
     * @param               resource $resource          Recourse.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testTellThrowsException($resource): void
    {
        (new Stream($resource))->tell();

        self::fail(
            "Method \"Stream::tell\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with resource cursor pointer in incorrect position.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::eof" provides true if the stream is at the end of the stream.
     *
     * @dataProvider    dataProviderResourcesWithPointerInTheEnd
     *
     * @param           resource    $resource           Recourse.
     * @param           bool        $isInTheEnd         Position pointer is in the end.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testEof($resource, bool $isInTheEnd): void
    {
        $providedValue = (new Stream($resource))->eof();

        self::assertEquals(
            $isInTheEnd,
            $providedValue,
            "Method \"Stream::eof\" returned unexpected result.\n".
            "Expected result is \"$isInTheEnd\".\n".
            "Caught result is \"$providedValue\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::isSeekable" provides true if the stream is seekable.
     *
     * @dataProvider    dataProviderResourcesWithSeekableState
     *
     * @param           resource    $resource           Recourse.
     * @param           bool        $isSeekable         Recourse is seekable.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testIsSeekable($resource, bool $isSeekable): void
    {
        $providedValue = (new Stream($resource))->isSeekable();

        self::assertEquals(
            $isSeekable,
            $providedValue,
            "Method \"Stream::isSeekable\" returned unexpected result.\n".
            "Expected result is \"$isSeekable\".\n".
            "Caught result is \"$providedValue\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::seek" works in expected way.
     *
     * @dataProvider    dataProviderResourcesWithSeekValidParams
     *
     * @param           resource    $resource           Recourse.
     * @param           int         $offset             Seek value.
     * @param           int         $whence             Seek value calculation type.
     * @param           int         $expectedPosition   Recourse cursor pointer expected position.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testSeek(
        $resource,
        int $offset,
        int $whence,
        int $expectedPosition
    ): void
    {
        $stream = new Stream($resource);
        $stream->seek($offset, $whence);
        $providedPosition = ftell($resource);

        self::assertEquals(
            $expectedPosition,
            $providedPosition,
            "Method \"Stream::seek\" showed unexpected behavior.\n".
            "Expected stream cursor position after seeking with offset \"$offset\"".
            " and whence value \"$whence\" is \"$expectedPosition\".\n".
            "Caught cursor position is \"$providedPosition\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::seek" throws exception with invalid arguments.
     *
     * @dataProvider        dataProviderResourcesWithSeekInvalidParams
     * @expectedException   RuntimeException
     *
     * @param               resource    $resource       Recourse.
     * @param               int         $offset         Seek value.
     * @param               int         $whence         Seek value calculation type.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testSeekThrowsException($resource, int $offset, int $whence): void
    {
        (new Stream($resource))->seek($offset, $whence);

        self::fail(
            "Method \"Stream::seek\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with seeking with".
            " offset \"$offset\" and whence value \"$whence\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::rewind" seeks the stream to the beginning.
     *
     * @dataProvider    dataProviderResourcesWithSeekableState
     *
     * @param           resource $resource              Recourse.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testRewind($resource): void
    {
        $stream = new Stream($resource);
        $stream->rewind();

        self::assertEquals(
            0,
            ftell($resource),
            "Method \"Stream::rewind\" showed unexpected behavior.\n".
            "Expects underlying resource will be seeked to the beginning.\n".
            "Underlying resource is not rewound.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::isWritable" provides true if the stream is writable.
     *
     * @dataProvider    dataProviderResourcesWithWritableState
     *
     * @param           resource    $resource           Recourse.
     * @param           bool        $isWritable         Recourse is writable.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testIsWritable($resource, bool $isWritable): void
    {
        $providedValue = (new Stream($resource))->isWritable();

        self::assertEquals(
            $isWritable,
            $providedValue,
            "Method \"Stream::isWritable\" returned unexpected result.\n".
            "Expected result is \"$isWritable\".\n".
            "Caught result is \"$providedValue\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::write" provides number of bytes written to the stream.
     *
     * @dataProvider    dataProviderResourcesWithDataToWriteValidValues
     *
     * @param           resource    $resource           Resource.
     * @param           string      $data               Data to write.
     * @param           int         $expectedSize       Expected number of bytes
     *                                                  written to the stream.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testWrite($resource, string $data, int $expectedSize): void
    {
        $providedSize = (new Stream($resource))->write($data);

        self::assertEquals(
            $expectedSize,
            $providedSize,
            "Method \"Stream::write\" returned unexpected result.\n".
            "Expected result with written data \"$data\" is \"$expectedSize\".\n".
            "Caught result is \"$providedSize\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::write" throws exception with data writing error.
     *
     * @dataProvider        dataProviderResourcesWithDataToWriteInvalidValues
     * @expectedException   RuntimeException
     *
     * @param               resource    $resource       Resource.
     * @param               string      $data           Data to write.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testWriteThrowsException($resource, string $data): void
    {
        (new Stream($resource))->write($data);

        self::fail(
            "Method \"Stream::write\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::write" add data to resource content.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testWriteAddData(): void
    {
        $resource   = $this->getTemporaryResource();
        $content1   = $this->getRandomText();
        $content2   = $this->getRandomText();
        $stream     = new Stream($resource);

        $stream->write($content1);
        $stream->write($content2);

        rewind($resource);

        self::assertEquals(
            $content1.$content2,
            stream_get_contents($resource),
            "Method \"Stream::write\" showed unexpected behavior.\n".
            "Expects data to write will be added to existed resource content.\n".
            "Caught resource content is not as expected.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::write" moves underlying resource cursor position after success.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testWriteMovesCursorPosition(): void
    {
        $resource       = $this->getTemporaryResource();
        $content        = $this->getRandomText();
        $contentSize    = mb_strlen($content);
        $stream         = new Stream($resource);

        $stream->write($content);

        self::assertEquals(
            $contentSize,
            ftell($resource),
            "Method \"Stream::write\" showed unexpected behavior.\n".
            "Expects underlying resource cursor position will be moved after success.\n".
            "Caught resource cursor position is not as expected.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::isReadable" provides true if the stream is readable.
     *
     * @dataProvider    dataProviderResourcesWithReadableState
     *
     * @param           resource    $resource           Recourse.
     * @param           bool        $isReadable         Recourse is readable.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testIsReadable($resource, bool $isReadable): void
    {
        $providedValue = (new Stream($resource))->isReadable();

        self::assertEquals(
            $isReadable,
            $providedValue,
            "Method \"Stream::isReadable\" returned unexpected result.\n".
            "Expected result is \"$isReadable\".\n".
            "Caught result is \"$providedValue\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::read" provides data from the stream.
     *
     * @dataProvider    dataProviderResourcesWithDataToReadValidValues
     *
     * @param           resource    $resource           Resource.
     * @param           int         $length             Read data length.
     * @param           string      $expectedData       Expected read data.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testRead($resource, int $length, string $expectedData): void
    {
        $providedData = (new Stream($resource))->read($length);

        self::assertEquals(
            $expectedData,
            $providedData,
            "Method \"Stream::read\" returned unexpected result.\n".
            "Expected result with read data length \"$length\" is \"$expectedData\".\n".
            "Caught result is \"$providedData\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::read" throws exception with data reading error.
     *
     * @dataProvider        dataProviderResourcesWithDataToReadInvalidValues
     * @expectedException   RuntimeException
     *
     * @param               resource    $resource       Resource.
     * @param               int         $length         Read data length.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testReadThrowsException($resource, int $length): void
    {
        (new Stream($resource))->read($length);

        self::fail(
            "Method \"Stream::read\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getContents" provides remaining contents in a string.
     *
     * @dataProvider    dataProviderResourcesWithContentValidValues
     *
     * @param           resource    $resource           Resource.
     * @param           string      $expectedContent    Expected content.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetContents($resource, string $expectedContent): void
    {
        $providedContent = (new Stream($resource))->getContents();

        self::assertEquals(
            $expectedContent,
            $providedContent,
            "Method \"Stream::getContents\" returned unexpected result.\n".
            "Expected result is \"$expectedContent\".\n".
            "Caught result is \"$providedContent\".\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getContents" throws exception with data reading error.
     *
     * @dataProvider        dataProviderResourcesWithContentInvalidValues
     * @expectedException   RuntimeException
     *
     * @param               resource $resource          Resource.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testGetContentsThrowsException($resource): void
    {
        (new Stream($resource))->getContents();

        self::fail(
            "Method \"Stream::getContents\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getContents" change stream current seek position.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testGetContentsChangeSeekPosition(): void
    {
        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        $stream     = new Stream($resource);

        $stream->write($content);

        $stream->rewind();
        self::assertEquals(
            $content,
            $stream->getContents(),
            "Method \"Stream::getContents\" returned unexpected result.\n".
            "Expects get set content after the stream was rewind.\n".
            "Caught result is not the same.\n"
        );
        self::assertEquals(
            '',
            $stream->getContents(),
            "Method \"Stream::getContents\" returned unexpected result.\n".
            "Expects get empty string after calling method twice.\n".
            "Caught result is not empty string.\n"
        );
        $stream->rewind();
        self::assertEquals(
            $content,
            $stream->getContents(),
            "Method \"Stream::getContents\" returned unexpected result.\n".
            "Expects get set content after the stream was rewind.\n".
            "Caught result is not the same.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getMetadata" provides stream metadata as an associative array.
     *
     * @dataProvider    dataProviderResourcesWithMetadata
     *
     * @param           resource    $resource           Resource.
     * @param           array       $expectedData       Expected metadata.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetMetadata($resource, array $expectedData): void
    {
        $providedData = (new Stream($resource))->getMetadata();

        self::assertEquals(
            $expectedData,
            $providedData,
            "Method \"Stream::getMetadata\" returned unexpected result.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getMetadata" provides metadata value by specific key.
     *
     * @dataProvider    dataProviderResourcesWithMetadataByKey
     *
     * @param           resource    $resource           Resource.
     * @param           string      $key                Specific key.
     * @param           mixed       $expectedValue      Expected metadata value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetMetadataByKey($resource, string $key, $expectedValue): void
    {
        $providedValue = (new Stream($resource))->getMetadata($key);

        self::assertEquals(
            $expectedValue,
            $providedValue,
            "Method \"Stream::getMetadata\" returned unexpected result.\n".
            "Expected result by key \"$key\" is \"$expectedValue\".\n".
            "Caught result is \"$providedValue\".\n"
        );
    }
    /** **********************************************************************
     * Data provider: resources, readable and writable.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesReadableAndWritable(): array
    {
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result                     = [];

        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithData(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeWritableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result                     = [];

        foreach ($modeReadableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, ''];
        }
        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $content    = $this->getRandomText();
            $resource   = fopen($file, $mode);
            fwrite($resource, $content);
            $result[]   = [$resource, ''];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $content    = $this->getRandomText();
            $resource   = fopen($file, $mode);
            fwrite($resource, $content);
            $result[]   = [$resource, $content];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data size.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataSize(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeWritableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result                     = [];

        foreach ($modeReadableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, 0];
        }
        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $content    = $this->getRandomText();
            $resource   = fopen($file, $mode);
            fwrite($resource, $content);
            $result[]   = [$resource, mb_strlen($content)];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $content    = $this->getRandomText();
            $resource   = fopen($file, $mode);
            fwrite($resource, $content);
            $result[]   = [$resource, mb_strlen($content)];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with cursor pointer in valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithPointerValidValues(): array
    {
        $result     = [];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, 0];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        $result[]   = [$resource, mb_strlen($content)];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        $seekValue  = (int) (mb_strlen($content) / 2);
        fwrite($resource, $content);
        fseek($resource, $seekValue);
        $result[]   = [$resource, $seekValue];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with cursor pointer in invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithPointerInvalidValues(): array
    {
        $result     = [];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        fseek($resource, -1);
        $result[]   = [$resource];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        fseek($resource, 1, SEEK_END);
        $result[]   = [$resource];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with cursor pointer in the end.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithPointerInTheEnd(): array
    {
        $result     = [];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, false];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        $result[]   = [$resource, false];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        while (!feof($resource)) {
            fgetc($resource);
        }
        $result[]   = [$resource, true];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with their seekable state.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithSeekableState(): array
    {
        $modeValues = array_diff(
            CollectionMediator::get('resource.accessMode.all'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result     = [];

        foreach ($modeValues as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with seek valid params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithSeekValidParams(): array
    {
        $result             = [];

        $resource           = $this->getTemporaryResource();
        $result[]           = [$resource, 0, SEEK_SET, 0];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        $seekValue          = (int) (mb_strlen($content) / 2);
        fwrite($resource, $content);
        $result[]           = [$resource, $seekValue, SEEK_SET, $seekValue];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        $seekValueFirst     = 10;
        $seekValueSecond    = 5;
        $seekValueTotal     = $seekValueFirst + $seekValueSecond;
        fwrite($resource, $content);
        fseek($resource, $seekValueFirst);
        $result[]           = [$resource, $seekValueSecond, SEEK_CUR, $seekValueTotal];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        $seekValue          = -10;
        $seekValueTotal     = mb_strlen($content) + $seekValue;
        fwrite($resource, $content);
        $result[]           = [$resource, $seekValue, SEEK_END, $seekValueTotal];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with seek invalid params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithSeekInvalidParams(): array
    {
        $result     = [];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, 1, SEEK_SET];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, -1, SEEK_SET];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        $result[]   = [$resource, mb_strlen($content) + 1, SEEK_SET];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        fseek($resource, mb_strlen($content));
        $result[]   = [$resource, 1, SEEK_CUR];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        $result[]   = [$resource, 1, SEEK_END];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, 0, 3];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, 0, -1];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with their writable state.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithWritableState(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeWritableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result                     = [];

        foreach ($modeReadableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, false];
        }
        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, true];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to write valid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToWriteValidValues(): array
    {
        $result     = [];

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, '', 0];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        $result[]   = [$resource, $content, mb_strlen($content)];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, 'someData');
        $result[]   = [$resource, $content, mb_strlen($content)];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to write invalid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToWriteInvalidValues(): array
    {
        $modeReadableOnly   = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result             = [];

        foreach ($modeReadableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $content    = $this->getRandomText();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, $content];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with their readable state.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithReadableState(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeWritableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result                     = [];

        foreach ($modeReadableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, true];
        }
        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, false];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to read valid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToReadValidValues(): array
    {
        $result             = [];

        $resource           = $this->getTemporaryResource();
        $result[]           = [$resource, 0, ''];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        fwrite($resource, $content);
        rewind($resource);
        $result[]           = [$resource, mb_strlen($content), $content];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        $readLength         = (int) (mb_strlen($content) / 2);
        $expectedContent    = substr($content, 0, $readLength);
        fwrite($resource, $content);
        rewind($resource);
        $result[]           = [$resource, $readLength, $expectedContent];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        fwrite($resource, $content);
        while (!feof($resource)) {
            fgetc($resource);
        }
        $result[]           = [$resource, mb_strlen($content), ''];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to read invalid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToReadInvalidValues(): array
    {
        $modeWritableOnly   = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result             = [];

        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, 1];
        }

        $resource   = $this->getTemporaryResource();
        $result[]   = [$resource, -1];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with content valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithContentValidValues(): array
    {
        $result             = [];

        $resource           = $this->getTemporaryResource();
        $result[]           = [$resource, ''];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        fwrite($resource, $content);
        $result[]           = [$resource, ''];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        fwrite($resource, $content);
        rewind($resource);
        $result[]           = [$resource, $content];

        $resource           = $this->getTemporaryResource();
        $content            = $this->getRandomText();
        $seekValue          = (int) (mb_strlen($content) / 2);
        $contentExpected    = substr($content, $seekValue);
        fwrite($resource, $content);
        fseek($resource, $seekValue);
        $result[]           = [$resource, $contentExpected];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with content invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithContentInvalidValues(): array
    {
        $modeWritableOnly   = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result             = [];

        foreach ($modeWritableOnly as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with metadata.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithMetadata(): array
    {
        $modeValues = array_diff(
            CollectionMediator::get('resource.accessMode.all'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result     = [];

        foreach ($modeValues as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $result[]   = [$resource, stream_get_meta_data($resource)];
        }

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        $result[]   = [$resource, stream_get_meta_data($resource)];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        rewind($resource);
        $result[]   = [$resource, stream_get_meta_data($resource)];

        $resource   = $this->getTemporaryResource();
        $content    = $this->getRandomText();
        fwrite($resource, $content);
        fseek($resource, (int) (mb_strlen($content) / 2));
        $result[]   = [$resource, stream_get_meta_data($resource)];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with metadata by key.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithMetadataByKey(): array
    {
        $availableKeys      = [
            'timed_out',    'blocked',
            'eof',          'unread_bytes',
            'stream_type',  'wrapper_type',
            'wrapper_data', 'mode',
            'seekable',     'uri'
        ];
        $unavailableKeys    = ['someKey1', 'someKey2', 'someKey3'];
        $result             = [];

        foreach ($availableKeys as $key) {
            $resourcesWithMetadata = $this->dataProviderResourcesWithMetadata();
            foreach ($resourcesWithMetadata as $resourceWithMetadata) {
                $result[] = [
                    $resourceWithMetadata[0],
                    $key,
                    $resourceWithMetadata[1][$key] ?? null
                ];
            }
        }
        foreach ($unavailableKeys as $key) {
            $resourcesWithMetadata = $this->dataProviderResourcesWithMetadata();
            foreach ($resourcesWithMetadata as $resourceWithMetadata) {
                $result[] = [
                    $resourceWithMetadata[0],
                    $key,
                    null
                ];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Get random text.
     *
     * @return  string                                  Random text.
     ************************************************************************/
    private function getRandomText(): string
    {
        return str_repeat("data-data\n", rand(5, 15));
    }
    /** **********************************************************************
     * Get temporary resource.
     *
     * @return  resource                                Temporary resource.
     ************************************************************************/
    private function getTemporaryResource()
    {
        return fopen('php://memory', 'r+');
    }
    /** **********************************************************************
     * Get temporary file path.
     *
     * @return  string                                  File path.
     ************************************************************************/
    private function getTemporaryFile(): string
    {
        $temporaryDirectory = sys_get_temp_dir();
        $temporaryFile      = tempnam($temporaryDirectory, 'unitTests');

        self::$temporaryFiles[] = $temporaryFile;
        return $temporaryFile;
    }
    /** **********************************************************************
     * After all test operations.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        foreach (self::$temporaryFiles as $filePath) {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }
}