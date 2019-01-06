<?php
declare(strict_types=1);
/** ***********************************************************************************************
 * unit tests bootstrap file
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
spl_autoload_register(function($className)
{
    $sourceDirectoryPath        = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Src';
    $sourceDirectoryPathReal    = realpath($sourceDirectoryPath);
    $classPath                  = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $classFilePath              = $sourceDirectoryPathReal.DIRECTORY_SEPARATOR.$classPath.'.php';
    $classFile                  = new SplFileInfo($classFilePath);

    if ($classFile->isFile() && $classFile->getExtension() == 'php')
    {
        try
        {
            require $classFile->getPathname();
        }
        catch (Throwable $exception)
        {

        }
    }
});
spl_autoload_register(function($className)
{
    $classNameExplode   = explode('\\Tests\\', $className);
    $classNamePart      = $classNameExplode[1] ??'';
    $classNamePart      = str_replace('\\', DIRECTORY_SEPARATOR, $classNamePart);
    $classFilePath      = __DIR__.DIRECTORY_SEPARATOR.$classNamePart.'.php';
    $classFile          = new SplFileInfo($classFilePath);

    if ($classFile->isFile() && $classFile->getExtension() == 'php')
    {
        try
        {
            require $classFile->getPathname();
        }
        catch (Throwable $exception)
        {

        }
    }
});