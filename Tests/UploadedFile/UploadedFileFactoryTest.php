<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\UploadedFile;

use Throwable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use AVMG\Http\{
    Stream\Stream,
    Factory\UploadedFileFactory
};
use AVMG\Http\Tests\{
    Collection\File\Uploaded\Error as FileErrorCollection,
    Collection\CollectionMediator
};

use function rand;
use function str_repeat;
use function mb_strlen;
use function count;
use function in_array;
use function array_keys;
use function array_diff;
use function array_rand;
use function fopen;
use function is_file;
use function md5_file;
use function file_put_contents;
use function rename;
use function unlink;
use function sys_get_temp_dir;
use function tempnam;
/** ***********************************************************************************************
 * PSR-7 UploadedFileFactoryInterface implementation test.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UploadedFileFactoryTest extends TestCase
{
    private static $uploadedFilesRegistered = [];
    private static $temporaryFiles          = [];
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" throws exception with invalid stream.
     *
     * @dataProvider        dataProviderStreamValuesInvalid
     * @expectedException   InvalidArgumentException
     *
     * @param               Stream $stream              Stream.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testConstructorThrowsException(Stream $stream): void
    {
        (new UploadedFileFactory())->createUploadedFile($stream);

        self::fail(
            "Method \"UploadedFileFactoryInterface::createUploadedFile\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception with invalid stream.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" provides uploaded file
     * with stream in expected state.
     *
     * @dataProvider    dataProviderStreamValuesValid
     *
     * @param           Stream $stream                  Stream.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUploadedFileStream(Stream $stream): void
    {
        $fileOldPath        = $stream->getMetadata('uri');
        $fileOldHash        = md5_file($fileOldPath);
        $uploadedFile       = (new UploadedFileFactory())->createUploadedFile($stream);
        $uploadedFileStream = $uploadedFile->getStream();
        $fileNewPath        = $uploadedFileStream->getMetadata('uri');
        $fileNewHash        = md5_file($fileNewPath);

        self::assertEquals(
            $fileOldPath,
            $fileNewPath,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expected uploaded file stream file path is \"$fileOldPath\"\n".
            "Caught stream file path is \"$fileNewPath\".\n"
        );
        self::assertEquals(
            $fileOldHash,
            $fileNewHash,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expects uploaded file stream file will has the same hash as was set\n".
            "Received stream file hash is not the same.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" provides uploaded file
     * with expected size.
     *
     * @dataProvider    dataProviderStreamWithUploadedFileSize
     *
     * @param           Stream  $stream                 Stream.
     * @param           mixed   $valueProvided          Value provided value.
     * @param           mixed   $valueExpected          Value expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUploadedFileSize(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = (new UploadedFileFactory())->createUploadedFile($stream, $valueProvided);
        $valueCaught    = $uploadedFile->getSize();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expects uploaded file size after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught uploaded file size is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" provides uploaded file
     * with expected error.
     *
     * @dataProvider    dataProviderStreamWithUploadedFileError
     *
     * @param           Stream  $stream                 Stream.
     * @param           mixed   $valueProvided          Value provided value.
     * @param           mixed   $valueExpected          Value expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUploadedFileError(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = (new UploadedFileFactory())->createUploadedFile($stream, null, $valueProvided);
        $valueCaught    = $uploadedFile->getError();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expects uploaded file error after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught uploaded file error is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" provides uploaded file
     * with expected client file name.
     *
     * @dataProvider    dataProviderStreamWithUploadedFileClientName
     *
     * @param           Stream  $stream                 Stream.
     * @param           mixed   $valueProvided          Value provided value.
     * @param           mixed   $valueExpected          Value expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUploadedFileClientFilename(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = (new UploadedFileFactory())->createUploadedFile($stream, null, 0, $valueProvided);
        $valueCaught    = $uploadedFile->getClientFilename();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expects uploaded file client file name after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught uploaded file client file name is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFileFactoryInterface::createUploadedFile" provides uploaded file
     * with expected client media type.
     *
     * @dataProvider    dataProviderStreamWithUploadedFileClientMediaType
     *
     * @param           Stream  $stream                 Stream.
     * @param           mixed   $valueProvided          Value provided value.
     * @param           mixed   $valueExpected          Value expected value.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testUploadedFileClientMediaType(Stream $stream, $valueProvided, $valueExpected): void
    {
        $factory        = new UploadedFileFactory();
        $uploadedFile   = $factory->createUploadedFile($stream, null, 0, null, $valueProvided);
        $valueCaught    = $uploadedFile->getClientMediaType();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFileFactoryInterface::createUploadedFile\"".
            " provides uploaded file in unexpected state.\n".
            "Expects uploaded file client media type after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught uploaded file client media type is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Data provider: stream invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamValuesInvalid(): array
    {
        $modeValidValues    = array_diff(
            CollectionMediator::get('resource.accessMode.all'),
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $modeInvalidValues  = array_diff(
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result             = [];

        foreach ($modeInvalidValues as $mode) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], $mode);
            $stream     = new Stream($resource);
            $result[]   = [$stream];
        }
        foreach ($modeValidValues as $mode) {
            $filePath   = $this->getTemporaryFile();
            $resource   = fopen($filePath, $mode);
            $stream     = new Stream($resource);
            $result[]   = [$stream];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: stream valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamValuesValid(): array
    {
        $modeValidValues    = array_diff(
            CollectionMediator::get('resource.accessMode.all'),
            CollectionMediator::get('resource.accessMode.writableOnly'),
            CollectionMediator::get('resource.accessMode.nonSuitable')
        );
        $result             = [];

        foreach ($modeValidValues as $mode) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], $mode);
            $stream     = new Stream($resource);
            $result[]   = [$stream];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: stream with uploaded file size values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamWithUploadedFileSize(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $fileSize   = $fileData['size'];
            $result[]   = [$stream, $fileSize, $fileSize];
        }

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $fileSize   = rand(1, 999999);
        $result[]   = [$stream, $fileSize, $fileSize];

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, null, null];

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, 0, null];

        return $result;
    }
    /** **********************************************************************
     * Data provider: stream with uploaded file error values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamWithUploadedFileError(): array
    {
        $errorsSet      = CollectionMediator::get('file.uploaded.error');
        $valuesValid    = array_keys($errorsSet);
        $valuesInvalid  = [];
        $valueDefault   = FileErrorCollection::STATUS_OK;
        $result         = [];

        while (count($valuesInvalid) < 10) {
            $value = rand(-100, 100);

            if (!in_array($value, $valuesValid)) {
                $valuesInvalid[] = $value;
            }
        }

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $fileError  = $fileData['error'];
            $result[]   = [$stream, $fileError, $fileError];
        }

        foreach ($valuesValid as $value) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $result[]   = [$stream, $value, $value];
        }
        foreach ($valuesInvalid as $value) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $result[]   = [$stream, $value, $valueDefault];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: stream with client file name values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamWithUploadedFileClientName(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $fileName   = $fileData['name'];
            $result[]   = [$stream, $fileName, $fileName];
        }

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, null, null];

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, '', null];

        return $result;
    }
    /** **********************************************************************
     * Data provider: stream with client media type values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderStreamWithUploadedFileClientMediaType(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $fileData   = $this->getUploadFileData();
            $resource   = fopen($fileData['tmp_name'], 'r');
            $stream     = new Stream($resource);
            $fileType   = $fileData['type'];
            $result[]   = [$stream, $fileType, $fileType];
        }

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, null, null];

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, '', null];

        return $result;
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
     * Get uploaded file data.
     *
     * @return  array                                   Uploaded file data.
     ************************************************************************/
    private function getUploadFileData(): array
    {
        $availableFileTypes     = [
            'txt'   => 'text/plain',
            'jpg'   => 'image/jpeg',
            'pdf'   => 'application/pdf'
        ];
        $fileExtension          = array_rand($availableFileTypes);
        $fileMimeType           = $availableFileTypes[$fileExtension];

        $fileData               = $this->getRandomText();
        $fileSize               = mb_strlen($fileData);

        $temporaryDirectory     = sys_get_temp_dir();
        $file                   = tempnam($temporaryDirectory, 'unitTests');
        $fileRandomNumber       = rand(1, 99);
        $fileShortName          = "randomFileName-$fileRandomNumber.$fileExtension";
        $fileFullName           = "$file.$fileExtension";

        rename($file, $fileFullName);
        file_put_contents($fileFullName, $fileData);

        self::$temporaryFiles[] = $file;
        $this->registerUploadedFile($fileFullName);

        return [
            'name'      => $fileShortName,
            'type'      => $fileMimeType,
            'tmp_name'  => $fileFullName,
            'error'     => FileErrorCollection::STATUS_OK,
            'size'      => $fileSize
        ];
    }
    /** **********************************************************************
     * Register uploaded file.
     *
     * @param   string $filePath                        File path.
     *
     * @return  void
     ************************************************************************/
    private function registerUploadedFile(string $filePath): void
    {
        self::$uploadedFilesRegistered[] = $filePath;
    }
}