<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Stream;

use Throwable;
use InvalidArgumentException;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use AVMG\Http\Tests\Collection\CollectionMediator;
use AVMG\Http\Factory\StreamFactory;

use function is_file;
use function rand;
use function chr;
use function str_repeat;
use function range;
use function in_array;
use function array_diff;
use function fopen;
use function fwrite;
use function feof;
use function fgetc;
use function file_put_contents;
use function unlink;
use function sys_get_temp_dir;
use function tempnam;
/** ***********************************************************************************************
 * PSR-7 StreamFactoryInterface implementation test.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class StreamFactoryTest extends TestCase
{
    private static $temporaryFiles = [];
    /** **********************************************************************
     * Test "StreamFactory::createStream" creates a new stream with expected condition.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testCreateStream(): void
    {
        $expectedContent    = $this->getRandomText();
        $stream             = (new StreamFactory())->createStream($expectedContent);
        $providedContent    = $stream->getContents();

        self::assertEquals(
            $expectedContent,
            $providedContent,
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::getContents\" is \"$expectedContent\".\n".
            "Caught value is \"$providedContent\".\n"
        );
        self::assertEquals(
            0,
            $stream->tell(),
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::tell\" is \"0\".\n".
            "Caught value is not \"0\".\n"
        );
        self::assertFalse(
            $stream->eof(),
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::eof\" is \"false\".\n".
            "Caught value is not \"false\".\n"
        );
        self::assertTrue(
            $stream->isSeekable(),
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isSeekable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertTrue(
            $stream->isReadable(),
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isReadable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertTrue(
            $stream->isWritable(),
            "Method \"StreamFactory::createStream\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isWritable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
    }
    /** **********************************************************************
     * Test "StreamFactory::createStreamFromFile" creates a new stream with expected condition.
     *
     * @dataProvider        dataProviderFilesWithFullParams
     *
     * @param               string  $filename           File path.
     * @param               string  $mode               Mode.
     * @param               string  $expectedContent    File content.
     * @param               bool    $isWritable         File mode is writable.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testCreateStreamFromFile(
        string  $filename,
        string  $mode,
        string  $expectedContent,
        bool    $isWritable
    ): void
    {
        $stream             = (new StreamFactory())->createStreamFromFile($filename, $mode);
        $providedContent    = $stream->getContents();

        self::assertEquals(
            $expectedContent,
            $providedContent,
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::getContents\" is \"$expectedContent\".\n".
            "Caught value is \"$providedContent\".\n"
        );
        self::assertEquals(
            0,
            $stream->tell(),
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::tell\" is \"0\".\n".
            "Caught value is not \"0\".\n"
        );
        self::assertFalse(
            $stream->eof(),
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::eof\" is \"false\".\n".
            "Caught value is not \"false\".\n"
        );
        self::assertTrue(
            $stream->isSeekable(),
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isSeekable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertTrue(
            $stream->isReadable(),
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isReadable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertEquals(
            $isWritable,
            $stream->isWritable(),
            "Method \"StreamFactory::createStreamFromFile\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isWritable\" is \"$isWritable\".\n".
            "Caught value is not \"$isWritable\".\n"
        );
    }
    /** **********************************************************************
     * Test "StreamFactory::createStreamFromFile" throws exception with invalid
     * file open mode value.
     *
     * @dataProvider        dataProviderFileOpenModesInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $mode                Mode.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testCreateStreamFromFileThrowsException1(string $mode)
    {
        $filePath = $this->getTemporaryFile();
        (new StreamFactory())->createStreamFromFile($filePath, $mode);

        self::fail(
            "Method \"StreamFactory::createStreamFromFile\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception with file open mode value \"$mode\"\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "StreamFactory::createStreamFromFile" throws exception with file
     * that can not be opened.
     *
     * @dataProvider        dataProviderFilesUnopened
     * @expectedException   RuntimeException
     *
     * @param               string  $filename           File path.
     * @param               string  $mode               Mode.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testCreateStreamFromFileThrowsException2(string $filename, string $mode)
    {
        (new StreamFactory())->createStreamFromFile($filename, $mode);

        self::fail(
            "Method \"StreamFactory::createStreamFromFile\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with file in unreachable state\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "StreamFactory::createStreamFromResource" creates a new stream with expected condition.
     *
     * @dataProvider    dataProviderResourcesWithFullParams
     *
     * @param           resource    $resource           Resource.
     * @param           string      $expectedContent    Resource content.
     * @param           bool        $isWritable         Resource is writable.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    public function testCreateStreamFromResource(
        $resource,
        string  $expectedContent,
        bool    $isWritable
    ): void
    {
        $stream             = (new StreamFactory())->createStreamFromResource($resource);
        $providedContent    = $stream->getContents();

        self::assertEquals(
            $expectedContent,
            $providedContent,
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::getContents\" is \"$expectedContent\".\n".
            "Caught value is \"$providedContent\".\n"
        );
        self::assertEquals(
            0,
            $stream->tell(),
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::tell\" is \"0\".\n".
            "Caught value is not \"0\".\n"
        );
        self::assertFalse(
            $stream->eof(),
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::eof\" is \"false\".\n".
            "Caught value is not \"false\".\n"
        );
        self::assertTrue(
            $stream->isSeekable(),
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isSeekable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertTrue(
            $stream->isReadable(),
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isReadable\" is \"true\".\n".
            "Caught value is not \"true\".\n"
        );
        self::assertEquals(
            $isWritable,
            $stream->isWritable(),
            "Method \"StreamFactory::createStreamFromResource\" returned stream in unexpected statement.\n".
            "Expected value with method \"Stream::isWritable\" is \"$isWritable\".\n".
            "Caught value is not \"$isWritable\".\n"
        );
    }
    /** **********************************************************************
     * Data provider: files with full params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderFilesWithFullParams(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeReadableAndWritable    = array_diff(
            CollectionMediator::get('resource.accessMode.readableAndWritable'),
            CollectionMediator::get('resource.accessMode.nonSuitable'),
            CollectionMediator::get('resource.accessMode.rewrite')
        );
        $result                     = [];

        foreach ($modeReadableOnly as $mode) {
            $filePath   = $this->getTemporaryFile();
            $content    = $this->getRandomText();

            file_put_contents($filePath, $content);
            $result[] = [$filePath, $mode, $content, false];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $filePath   = $this->getTemporaryFile();
            $content    = $this->getRandomText();

            file_put_contents($filePath, $content);
            $result[] = [$filePath, $mode, $content, true];
        }

        $result[] = [$this->getTemporaryFile(), 'r', '', false];

        return $result;
    }
    /** **********************************************************************
     * Data provider: file open mode invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderFileOpenModesInvalidValues(): array
    {
        $allowedModes   = CollectionMediator::get('resource.accessMode.all');
        $rangeOfNumbers = range(0, 9);
        $rangeOfLetters = range(65, 112);
        $result         = [];

        foreach ($rangeOfNumbers as $number) {
            $result[] = [$number];
        }
        foreach ($rangeOfLetters as $number) {
            $character = chr($number);

            if (!in_array($character, $allowedModes)) {
                $result[] = [$character];
            }
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: files that can not be opened.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderFilesUnopened(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 5; $iterator++) {
            $result[] = ["incorrectFilePath-$iterator", 'r'];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: resources with full params.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderResourcesWithFullParams(): array
    {
        $modeReadableOnly           = array_diff(
            CollectionMediator::get('resource.accessMode.readableOnly'),
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
            $result[]   = [$resource, '', false];
        }
        foreach ($modeReadableAndWritable as $mode) {
            $file       = $this->getTemporaryFile();
            $resource   = fopen($file, $mode);
            $content    = $this->getRandomText();

            fwrite($resource, $content);
            while (!feof($resource)) {
                fgetc($resource);
            }

            $result[] = [$resource, $content, true];
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