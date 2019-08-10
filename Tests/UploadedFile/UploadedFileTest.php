<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\UploadedFile;

use Throwable;
use InvalidArgumentException;
use RuntimeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use PHPUnit\Framework\TestCase;
use AVMG\Http\{
    Stream\Stream,
    UploadedFile\UploadedFile
};
use AVMG\Http\Tests\{
    Collection\File\Uploaded\Error as FileErrorCollection,
    Collection\CollectionMediator
};

use function rand;
use function uniqid;
use function str_repeat;
use function mb_strlen;
use function count;
use function in_array;
use function array_keys;
use function array_diff;
use function array_rand;
use function fopen;
use function is_file;
use function is_dir;
use function file_exists;
use function md5_file;
use function file_put_contents;
use function rename;
use function unlink;
use function rmdir;
use function mkdir;
use function sys_get_temp_dir;
use function tempnam;

use const DIRECTORY_SEPARATOR;
/** ***********************************************************************************************
 * PSR-7 UploadedFileInterface implementation test.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UploadedFileTest extends TestCase
{
    private static $uploadedFilesRegistered = [];
    private static $temporaryFiles          = [];
    private static $temporaryDirectories    = [];
    /** **********************************************************************
     * Test "UploadedFile::__construct" throws exception with invalid stream.
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
        new UploadedFile($stream);

        self::fail(
            "Method \"UploadedFile::__construct\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception with invalid stream.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getStream" provides expected stream object.
     *
     * @dataProvider    dataProviderStreamValuesValid
     *
     * @param           Stream $stream                  Stream.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testGetStream(Stream $stream): void
    {
        $fileOldPath        = $stream->getMetadata('uri');
        $fileOldHash        = md5_file($fileOldPath);
        $uploadedFile       = new UploadedFile($stream);
        $uploadedFileStream = $uploadedFile->getStream();
        $fileNewPath        = $uploadedFileStream->getMetadata('uri');
        $fileNewHash        = md5_file($fileNewPath);

        self::assertEquals(
            $fileOldPath,
            $fileNewPath,
            "Method \"UploadedFile::getStream\" provides unexpected stream.\n".
            "Expected stream file path is \"$fileOldPath\"\n".
            "Caught stream file path is \"$fileNewPath\".\n"
        );
        self::assertEquals(
            $fileOldHash,
            $fileNewHash,
            "Method \"UploadedFile::getStream\" provides unexpected stream.\n".
            "Expects stream file will has the same hash as was set\n".
            "Received stream file hash is not the same.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getStream" throws exception with no stream available.
     *
     * @expectedException   RuntimeException
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testGetStreamThrowsException(): void
    {
        $stream         = $this->getValidStream();
        $filePath       = $stream->getMetadata('uri');
        $uploadedFile   = new UploadedFile($stream);

        unlink($filePath);
        $uploadedFile->getStream();

        self::fail(
            "Method \"UploadedFile::getStream\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with non-exist file.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::moveTo" replace file.
     *
     * @dataProvider    dataProviderPathForReplaceValidValues
     *
     * @param           string $fileNewPath             File new path.
     *
     * @return          void
     * @throws          Throwable
     ************************************************************************/
    public function testMoveTo(string $fileNewPath): void
    {
        $stream         = $this->getValidStream();
        $uploadedFile   = new UploadedFile($stream);
        $fileOldPath    = $stream->getMetadata('uri');
        $fileOldHash    = md5_file($fileOldPath);

        $uploadedFile->moveTo($fileNewPath);

        self::assertFalse(
            file_exists($fileOldPath),
            "Method \"UploadedFile::moveTo\" showed unexpected behavior.\n".
            "Expects file will be replaced form \"$fileOldPath\" to \"$fileOldPath\"".
            " and will be not exists by old path.\n".
            "File is still exist by old path.\n"
        );
        self::assertTrue(
            file_exists($fileNewPath),
            "Method \"UploadedFile::moveTo\" showed unexpected behavior.\n".
            "Expects file will be replaced form \"$fileOldPath\" to \"$fileOldPath\"".
            " and will be exists by new path.\n".
            "File is not exist by new path.\n"
        );
        self::assertEquals(
            $fileOldHash,
            md5_file($fileNewPath),
            "Method \"UploadedFile::moveTo\" showed unexpected behavior.\n".
            "Expects replaced file will has the same hash\n".
            "Replaced file hash is not the same.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::moveTo" throws exception with invalid path for replacing.
     *
     * @dataProvider        dataProviderPathForReplaceInvalidValues
     * @expectedException   InvalidArgumentException
     *
     * @param               string $fileNewPath         File new path.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testMoveToThrowsExceptionWithInvalidPath(string $fileNewPath): void
    {
        $stream         = $this->getValidStream();
        $uploadedFile   = new UploadedFile($stream);

        $uploadedFile->moveTo($fileNewPath);

        self::fail(
            "Method \"UploadedFile::moveTo\" threw no expected exception.\n".
            "Expects \"InvalidArgumentException\" exception with invalid path for replacing.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::moveTo" throws exception with unreachable file.
     *
     * @expectedException   RuntimeException
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testMoveToThrowsExceptionWithUnreachableFile(): void
    {
        $stream         = $this->getValidStream();
        $fileNewPath    = $this->getValidPathForReplace();
        $fileOldPath    = $stream->getMetadata('uri');
        $uploadedFile   = new UploadedFile($stream);

        unlink($fileOldPath);
        $uploadedFile->moveTo($fileNewPath);

        self::fail(
            "Method \"UploadedFile::moveTo\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with unreachable file.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::moveTo" throws exception on calling method twice.
     *
     * @expectedException   RuntimeException
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testMoveToThrowsExceptionOnCallingTwice(): void
    {
        $stream         = $this->getValidStream();
        $fileNewPath    = $this->getValidPathForReplace();
        $uploadedFile   = new UploadedFile($stream);

        $uploadedFile->moveTo($fileNewPath);
        $uploadedFile->moveTo($fileNewPath);

        self::fail(
            "Method \"UploadedFile::moveTo\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception on calling this method twice.\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::moveTo" throws exception with any uploaded file error.
     *
     * @dataProvider        dataProviderUploadedFileErrorCriticalValues
     * @expectedException   RuntimeException
     *
     * @param               int $error                  Uploaded file error.
     *
     * @return              void
     * @throws              Throwable
     ************************************************************************/
    public function testMoveToThrowsExceptionWithFileError(int $error): void
    {
        $stream         = $this->getValidStream();
        $fileNewPath    = $this->getValidPathForReplace();
        $uploadedFile   = new UploadedFile($stream, null, $error);

        $uploadedFile->moveTo($fileNewPath);

        self::fail(
            "Method \"UploadedFile::moveTo\" threw no expected exception.\n".
            "Expects \"RuntimeException\" exception with file error \"$error\".\n".
            "Caught no exception.\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getSize" provides expected value.
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
    public function testGetSize(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = new UploadedFile($stream, $valueProvided);
        $valueCaught    = $uploadedFile->getSize();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFile::getSize\" returned unexpected result.\n".
            "Expected result after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught result is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getError" provides expected value.
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
    public function testGetError(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = new UploadedFile($stream, null, $valueProvided);
        $valueCaught    = $uploadedFile->getError();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFile::getError\" returned unexpected result.\n".
            "Expected result after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught result is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getClientFilename" provides expected value.
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
    public function testGetClientFilename(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = new UploadedFile($stream, null, null, $valueProvided);
        $valueCaught    = $uploadedFile->getClientFilename();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFile::getClientFilename\" returned unexpected result.\n".
            "Expected result after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught result is \"$valueCaught\".\n"
        );
    }
    /** **********************************************************************
     * Test "UploadedFile::getClientMediaType" provides expected value.
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
    public function testGetClientMediaType(Stream $stream, $valueProvided, $valueExpected): void
    {
        $uploadedFile   = new UploadedFile($stream, null, null, null, $valueProvided);
        $valueCaught    = $uploadedFile->getClientMediaType();

        self::assertEquals(
            $valueExpected,
            $valueCaught,
            "Method \"UploadedFile::getClientMediaType\" returned unexpected result.\n".
            "Expected result after setting value \"$valueProvided\" is \"$valueExpected\".\n".
            "Caught result is \"$valueCaught\".\n"
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
     * Data provider: path for replacing valid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPathForReplaceValidValues(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $newPath    = $this->getTemporaryDirectory().DIRECTORY_SEPARATOR.uniqid();
            $result[]   = [$newPath];
        }

        return $result;
    }
    /** **********************************************************************
     * Data provider: path for replacing invalid values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderPathForReplaceInvalidValues(): array
    {
        $result = [];

        for ($iterator = 1; $iterator <= 10; $iterator++) {
            $directory  = 'unknownDirectory-'.uniqid();
            $newPath    = $directory.DIRECTORY_SEPARATOR.uniqid();
            $result[]   = [$newPath];
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

        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');
        $stream     = new Stream($resource);
        $result[]   = [$stream, null, $valueDefault];

        return $result;
    }
    /** **********************************************************************
     * Data provider: uploaded file error critical values.
     *
     * @return  array                                   Data.
     ************************************************************************/
    public function dataProviderUploadedFileErrorCriticalValues(): array
    {
        $errorsSet      = CollectionMediator::get('file.uploaded.error');
        $valuesAll      = array_keys($errorsSet);
        $valuesCritical = array_diff($valuesAll, [FileErrorCollection::STATUS_OK]);
        $result         = [];

        foreach ($valuesCritical as $value) {
            $result[] = [$value];
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

        foreach (self::$temporaryDirectories as $directoryPath) {
            if (is_dir($directoryPath)) {
                self::deleteFilesFromDirectory($directoryPath);
                rmdir($directoryPath);
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
     * Get temporary directory path.
     *
     * @return  string                                  Directory path.
     ************************************************************************/
    private function getTemporaryDirectory(): string
    {
        $temporaryDirectory = sys_get_temp_dir();
        $directoryName      = uniqid();
        $directoryPath      = $temporaryDirectory.DIRECTORY_SEPARATOR.$directoryName;

        mkdir($directoryPath, 0777, TRUE);
        self::$temporaryDirectories[] = $directoryPath;

        return $directoryPath;
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
     * Get valid stream.
     *
     * @return  Stream                                  Valid stream.
     ************************************************************************/
    private function getValidStream(): Stream
    {
        $fileData   = $this->getUploadFileData();
        $resource   = fopen($fileData['tmp_name'], 'r');

        return new Stream($resource);
    }
    /** **********************************************************************
     * Get valid path for replace.
     *
     * @return  string                                  Valid path for replace.
     ************************************************************************/
    private function getValidPathForReplace(): string
    {
        return $this->getTemporaryDirectory().DIRECTORY_SEPARATOR.uniqid();
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
    /** **********************************************************************
     * Delete all files from directory.
     *
     * @param   string $directoryPath                   Directory path.
     *
     * @return  void
     * @throws  Throwable
     ************************************************************************/
    private static function deleteFilesFromDirectory(string $directoryPath): void
    {
        $iterator   = new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files      = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }
}