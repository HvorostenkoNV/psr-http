<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Stream;

use
    Throwable,
    InvalidArgumentException,
    RuntimeException,
    PHPUnit\Framework\TestCase,
    AVMG\Http\Stream\Stream;
/** ***********************************************************************************************
 * PSR-7 StreamInterface implementation test.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class StreamTest extends TestCase
{
    /** **********************************************************************
     * Test "Stream::__construct" throws exception with incorrect argument.
     *
     * @dataProvider        dataProviderConstructorIncorrectArgument
     * @expectedException   InvalidArgumentException
     *
     * @param               mixed $resource             Incorrect argument.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testConstructorThrowsException($resource) : void
    {
        new Stream($resource);
        $argumentType = gettype($resource);

        self::fail
        (
            "Method \"Stream::__construct\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception with argument type \"$argumentType\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::__destruct" closes underlying resource.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testDestructorClosesResource() : void
    {
        $resource   = fopen('php://memory', 'w+');
        $stream     = new Stream($resource);

        unset($stream);

        self::assertFalse
        (
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
    public function testToStringConverting($resource, string $content) : void
    {
        $stream = new Stream($resource);

        self::assertEquals
        (
            $content,
            (string) $stream,
            "Stream object converting to string returned unexpected result.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::close" closes underlying resource.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testClose() : void
    {
        $resource   = fopen('php://memory', 'w+');
        $stream     = new Stream($resource);

        $stream->close();

        self::assertFalse
        (
            is_resource($resource),
            "Method \"Stream::close\" showed unexpected behavior.\n".
            "Expects underlying stream resource will be closed after calling method \"Stream::close\".\n".
            "Underlying stream resource is not closed.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::close" DO NOT closes underlying resource, if stream is detached.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testCloseDoNothingWithDetachedResource() : void
    {
        $resource           = fopen('php://memory', 'w+');
        $stream             = new Stream($resource);
        $resourceDetached   = $stream->detach();

        $stream->close();

        self::assertTrue
        (
            is_resource($resourceDetached),
            "Method \"Stream::close\" showed unexpected behavior.\n".
            "Expects underlying stream resource will be NOT closed after it was detached\n".
            "Underlying stream resource is closed.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::detach" provides recourse.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testDetach() : void
    {
        $resource   = fopen('php://memory', 'w+');
        $stream     = new Stream($resource);

        self::assertEquals
        (
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
    public function testGetSize($resource, $expectedSize) : void
    {
        $providedSize = (new Stream($resource))->getSize();

        self::assertEquals
        (
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
    public function testTell($resource, int $expectedPosition) : void
    {
        $providedPosition = (new Stream($resource))->tell();

        self::assertEquals
        (
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
    public function testTellThrowsException($resource) : void
    {
        (new Stream($resource))->tell();

        self::fail
        (
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
    public function testEof($resource, bool $isInTheEnd) : void
    {
        $providedValue = (new Stream($resource))->eof();

        self::assertEquals
        (
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
    public function testIsSeekable($resource, bool $isSeekable) : void
    {
        $providedValue = (new Stream($resource))->isSeekable();

        self::assertEquals
        (
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
    public function testSeek
    (
        $resource,
        int $offset,
        int $whence,
        int $expectedPosition
    ) : void
    {
        $stream = new Stream($resource);
        $stream->seek($offset, $whence);
        $providedPosition = ftell($resource);

        self::assertEquals
        (
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
    public function testSeekThrowsException($resource, int $offset, int $whence) : void
    {
        (new Stream($resource))->seek($offset, $whence);

        self::fail
        (
            "Method \"Stream::seek\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with seeking with".
            " offset \"$offset\" and whence value \"$whence\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::rewind" seeks the stream to the beginning.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testRewind() : void
    {
        $resource = fopen('php://memory', 'w+');
        fwrite($resource, 'some data');
        fseek($resource, 2);

        $stream = new Stream($resource);
        $stream->rewind();

        self::assertEquals
        (
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
    public function testIsWritable($resource, bool $isWritable) : void
    {
        $providedValue = (new Stream($resource))->isWritable();

        self::assertEquals
        (
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
    public function testWrite($resource, string $data, int $expectedSize) : void
    {
        $providedSize = (new Stream($resource))->write($data);

        self::assertEquals
        (
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
    public function testWriteThrowsException($resource, string $data) : void
    {
        (new Stream($resource))->write($data);

        self::fail
        (
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
    public function testWriteAddData() : void
    {
        $resource   = fopen('php://memory', 'w');
        $content1   = $this->generateResourceRandomData();
        $content2   = $this->generateResourceRandomData();
        $stream     = new Stream($resource);

        $stream->write($content1);
        $stream->write($content2);

        rewind($resource);
        $resourceContent = stream_get_contents($resource);

        self::assertEquals
        (
            $content1.$content2,
            $resourceContent,
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
    public function testWriteMovesCursorPosition() : void
    {
        $resource       = fopen('php://memory', 'w');
        $content        = $this->generateResourceRandomData();
        $contentSize    = mb_strlen($content);
        $stream         = new Stream($resource);

        $stream->write($content);
        $resourcePointer = ftell($resource);

        self::assertEquals
        (
            $contentSize,
            $resourcePointer,
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
    public function testIsReadable($resource, bool $isReadable) : void
    {
        $providedValue = (new Stream($resource))->isReadable();

        self::assertEquals
        (
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
    public function testRead($resource, int $length, string $expectedData) : void
    {
        $providedData = (new Stream($resource))->read($length);

        self::assertEquals
        (
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
    public function testReadThrowsException($resource, int $length) : void
    {
        (new Stream($resource))->read($length);

        self::fail
        (
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
    public function testGetContents($resource, string $expectedContent) : void
    {
        $providedContent = (new Stream($resource))->getContents();

        self::assertEquals
        (
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
    public function testGetContentsThrowsException($resource) : void
    {
        (new Stream($resource))->getContents();

        self::fail
        (
            "Method \"Stream::getContents\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "Stream::getMetadata" provides stream metadata as an associative array.
     *
     * @dataProvider    dataProviderResourcesWithContentValidValues
     *
     * @param           resource    $resource           Resource.
     * @param           string      $expectedContent    Expected content.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetMetadata($resource, string $expectedContent) : void
    {
        $providedContent = (new Stream($resource))->getContents();

        self::assertEquals
        (
            $expectedContent,
            $providedContent,
            "Method \"Stream::getContents\" returned unexpected result.\n".
            "Expected result is \"$expectedContent\".\n".
            "Caught result is \"$providedContent\".\n"
        );
    }
    /** **********************************************************************
     * Test Stream object behavior in an unusable state.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testStreamInUnusableState() : void
    {
        foreach (['close', 'detach'] as $closeMethod)
        {
            $resource = fopen('php://memory', 'w+');
            fwrite($resource, 'data-data');

            $stream = new Stream($resource);
            $stream->$closeMethod();

            self::assertEquals
            (
                '',
                (string) $stream,
                "Method \"Stream::__toString\" returned unexpected result.\n".
                "Expects get empty string from stream in an unusable state\n".
                "Caught result is not empty string.\n"
            );

            foreach (['detach', 'getSize'] as $method)
            {
                self::assertNull
                (
                    $stream->$method(),
                    "Method \"Stream::$method\" returned unexpected result.\n".
                    "Expects get null from stream in an unusable state\n".
                    "Caught result is not null.\n"
                );
            }

            self::assertTrue
            (
                $stream->eof(),
                "Method \"Stream::eof\" returned unexpected result.\n".
                "Expects get true from stream in an unusable state\n".
                "Caught result is not true.\n"
            );

            foreach (['isSeekable', 'isWritable', 'isReadable'] as $method)
            {
                self::assertFalse
                (
                    $stream->$method(),
                    "Method \"Stream::$method\" returned unexpected result.\n".
                    "Expects get false from stream in an unusable state\n".
                    "Caught result is not true.\n"
                );
            }

            foreach
            (
                [
                    'tell'          => [],
                    'seek'          => [0],
                    'rewind'        => [],
                    'write'         => ['data'],
                    'read'          => [1],
                    'getContents'   => [],
                ]
                as $method => $arguments
            )
            {
                try
                {
                    call_user_func_array([$stream, $method], $arguments);
                    self::fail
                    (
                        "Method \"Stream::$method\" threw no expected exception.\n".
                        "Expects \"RuntimeException\" exception from stream in an unusable state.\n".
                        "Caught no exception.\n"
                    );
                }
                catch (RuntimeException $exception)
                {

                }
            }
        }
    }
    /** **********************************************************************
     * Data provider: incorrect arguments for stream constructor.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderConstructorIncorrectArgument() : array
    {
        $result =
            [
                ['someString'],
                [''],
                [10],
                [0],
                [-10],
                [0.5],
                [0.0],
                [-0.5],
                [true],
                [false],
                [['value', 'value', 'value']],
                [[]],
                [new InvalidArgumentException],
                [null]
            ];

        $resource = fopen('php://memory', 'w+');
        fclose($resource);
        $result[] = [$resource];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithData() : array
    {
        $resourcesReadableOnly          = $this->getResourcesReadableOnlySet();
        $resourcesWritableOnly          = $this->getResourcesWritableOnlySet();
        $resourcesReadableAndWritable   = $this->getResourcesReadableAndWritableSet();
        $result                         = [];

        $resource   = fopen(__FILE__, 'r');
        $content    = file_get_contents(__FILE__);
        $result[]   = [$resource, $content];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, ''];
        }
        foreach ($resourcesWritableOnly as $resource)
        {
            $content    = $this->generateResourceRandomData();
            fwrite($resource, $content);
            $result[]   = [$resource, ''];
        }
        foreach ($resourcesReadableAndWritable as $resource)
        {
            $content    = $this->generateResourceRandomData();
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
    public function dataProviderResourcesWithDataSize() : array
    {
        $resourcesReadableOnly          = $this->getResourcesReadableOnlySet();
        $resourcesWritableOnly          = $this->getResourcesWritableOnlySet();
        $resourcesReadableAndWritable   = $this->getResourcesReadableAndWritableSet();
        $result                         = [];

        $resource   = fopen(__FILE__, 'r');
        $result[]   = [$resource, filesize(__FILE__)];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, 0];
        }
        foreach ($resourcesWritableOnly as $resource)
        {
            $content    = $this->generateResourceRandomData();
            fwrite($resource, $content);
            $result[]   = [$resource, null];
        }
        foreach ($resourcesReadableAndWritable as $resource)
        {
            $content    = $this->generateResourceRandomData();
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
    public function dataProviderResourcesWithPointerValidValues() : array
    {
        $resourcesWritableOnly  = $this->getResourcesWritableOnlySet();
        $result                 = [];

        foreach ($resourcesWritableOnly as $resource)
        {
            $content    = $this->generateResourceRandomData();
            fwrite($resource, $content);
            $result[]   = [$resource, 0];
        }

        $resource   = fopen('php://memory', 'r');
        $result[]   = [$resource, 0];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        $result[]   = [$resource, mb_strlen($content)];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
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
    public function dataProviderResourcesWithPointerInvalidValues() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        fseek($resource, -1);
        $result[]   = [$resource];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
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
    public function dataProviderResourcesWithPointerInTheEnd() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, false];

        $resource   = fopen('php://memory', 'w+');
        fwrite($resource, $this->generateResourceRandomData());
        $result[]   = [$resource, false];

        $resource   = fopen('php://memory', 'w+');
        fwrite($resource, $this->generateResourceRandomData());
        while (!feof($resource))
        {
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
    public function dataProviderResourcesWithSeekableState() : array
    {
        $resourcesReadableOnly          = $this->getResourcesReadableOnlySet();
        $resourcesWritableOnly          = $this->getResourcesWritableOnlySet();
        $resourcesReadableAndWritable   = $this->getResourcesReadableAndWritableSet();
        $result                         = [];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, true];
        }
        foreach ($resourcesWritableOnly as $resource)
        {
            $result[] = [$resource, false];
        }
        foreach ($resourcesReadableAndWritable as $resource)
        {
            $result[] = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with seek valid params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithSeekValidParams() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, 0, SEEK_SET, 0];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        $seekValue  = (int) (mb_strlen($content) / 2);
        fwrite($resource, $content);
        $result[]   = [$resource, $seekValue, SEEK_SET, $seekValue];

        $resource           = fopen('php://memory', 'w+');
        $seekValueFirst     = 10;
        $seekValueSecond    = 5;
        $seekValueTotal     = $seekValueFirst + $seekValueSecond;
        fwrite($resource, $this->generateResourceRandomData());
        fseek($resource, $seekValueFirst);
        $result[]           = [$resource, $seekValueSecond, SEEK_CUR, $seekValueTotal];

        $resource       = fopen('php://memory', 'w+');
        $content        = $this->generateResourceRandomData();
        $seekValue      = -10;
        $seekValueTotal = mb_strlen($content) + $seekValue;
        fwrite($resource, $content);
        $result[]       = [$resource, $seekValue, SEEK_END, $seekValueTotal];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with seek invalid params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithSeekInvalidParams() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, 1, SEEK_SET];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, -1, SEEK_SET];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        $result[]   = [$resource, mb_strlen($content) + 1, SEEK_SET];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        fseek($resource, mb_strlen($content));
        $result[]   = [$resource, 1, SEEK_CUR];

        $resource   = fopen('php://memory', 'w+');
        fwrite($resource, $this->generateResourceRandomData());
        $result[]   = [$resource, 1, SEEK_END];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, 0, 3];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, 0, -1];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with their writable state.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithWritableState() : array
    {
        $resourcesReadableOnly          = $this->getResourcesReadableOnlySet();
        $resourcesWritableOnly          = $this->getResourcesWritableOnlySet();
        $resourcesReadableAndWritable   = $this->getResourcesReadableAndWritableSet();
        $result                         = [];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, false];
        }
        foreach ($resourcesWritableOnly as $resource)
        {
            $result[] = [$resource, true];
        }
        foreach ($resourcesReadableAndWritable as $resource)
        {
            $result[] = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to write valid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToWriteValidValues() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w');
        $result[]   = [$resource, '', 0];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        $result[]   = [$resource, $content, mb_strlen($content)];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, 'someData');
        $result[]   = [$resource, $content, mb_strlen($content)];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to write invalid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToWriteInvalidValues() : array
    {
        $resourcesReadableOnly  = $this->getResourcesReadableOnlySet();
        $result                 = [];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, $this->generateResourceRandomData()];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with their readable state.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithReadableState() : array
    {
        $resourcesReadableOnly          = $this->getResourcesReadableOnlySet();
        $resourcesWritableOnly          = $this->getResourcesWritableOnlySet();
        $resourcesReadableAndWritable   = $this->getResourcesReadableAndWritableSet();
        $result                         = [];

        foreach ($resourcesReadableOnly as $resource)
        {
            $result[] = [$resource, true];
        }
        foreach ($resourcesWritableOnly as $resource)
        {
            $result[] = [$resource, false];
        }
        foreach ($resourcesReadableAndWritable as $resource)
        {
            $result[] = [$resource, true];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to read valid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToReadValidValues() : array
    {
        $result = [];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, 0, ''];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        rewind($resource);
        $result[]   = [$resource, mb_strlen($content), $content];

        $resource           = fopen('php://memory', 'w+');
        $content            = $this->generateResourceRandomData();
        $readLength         = (int) (mb_strlen($content) / 2);
        $expectedContent    = substr($content, 0, $readLength);
        fwrite($resource, $content);
        rewind($resource);
        $result[]           = [$resource, $readLength, $expectedContent];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        while (!feof($resource))
        {
            fgetc($resource);
        }
        $result[]   = [$resource, mb_strlen($content), ''];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with data to read invalid value.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithDataToReadInvalidValues() : array
    {
        $resourcesWritableOnly  = $this->getResourcesWritableOnlySet();
        $result                 = [];

        foreach ($resourcesWritableOnly as $resource)
        {
            $result[]   = [$resource, 1];
        }

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, -1];

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with content valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithContentValidValues() : array
    {
        $result = [];

        $resource   = fopen(__FILE__, 'r');
        $content    = file_get_contents(__FILE__);
        $result[]   = [$resource, $content];

        $resource   = fopen('php://memory', 'w+');
        $result[]   = [$resource, ''];

        $resource   = fopen('php://memory', 'w+');
        fwrite($resource, $this->generateResourceRandomData());
        $result[]   = [$resource, ''];

        $resource   = fopen('php://memory', 'w+');
        $content    = $this->generateResourceRandomData();
        fwrite($resource, $content);
        rewind($resource);
        $result[]   = [$resource, $content];

        $resource           = fopen('php://memory', 'w+');
        $content            = $this->generateResourceRandomData();
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
    public function dataProviderResourcesWithContentInvalidValues() : array
    {
        $resourcesWritableOnly  = $this->getResourcesWritableOnlySet();
        $result                 = [];

        foreach ($resourcesWritableOnly as $resource)
        {
            $result[] = [$resource];
        }

        return $result;
    }
    /** **********************************************************************
     * Get resources readable only set.
     *
     * @return  resource[]                              Resources.
     ************************************************************************/
    private function getResourcesReadableOnlySet() : array
    {
        return
            [
                fopen('php://memory', 'r'),
                fopen('php://input', 'w+')
            ];
    }
    /** **********************************************************************
     * Get resources writable only set.
     *
     * @return  resource[]                              Resources.
     ************************************************************************/
    private function getResourcesWritableOnlySet() : array
    {
        return
            [
                fopen('php://output', 'w+')
            ];
    }
    /** **********************************************************************
     * Get resources readable and writable set.
     *
     * @return  resource[]                              Resources.
     ************************************************************************/
    private function getResourcesReadableAndWritableSet() : array
    {
        return
            [
                fopen('php://memory', 'r+'),
                fopen('php://memory', 'w+')
            ];
    }
    /** **********************************************************************
     * Generate resource random content data.
     *
     * @return  string                                  Data.
     ************************************************************************/
    private function generateResourceRandomData() : string
    {
        return str_repeat("data-data\n", rand(5, 15));
    }
}