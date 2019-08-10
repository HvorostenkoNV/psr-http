<?php
declare(strict_types=1);
/** ***********************************************************************************************
 * Unit tests bootstrap file.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *
 * First class autoloader (Library classes).
 *************************************************************************************************/
spl_autoload_register(function($className) {
    $sourceDirectoryPath        = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Src';
    $sourceDirectoryPathReal    = realpath($sourceDirectoryPath);
    $classPath                  = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $classFilePath              = $sourceDirectoryPathReal.DIRECTORY_SEPARATOR.$classPath.'.php';
    $classFile                  = new SplFileInfo($classFilePath);

    if ($classFile->isFile()) {
        require $classFile->getPathname();
    }
});
/** ***********************************************************************************************
 * Second class autoloader (Library tests classes).
 *************************************************************************************************/
spl_autoload_register(function($className) {
    $classNameExploded  = explode('\\Tests\\', $className);
    $classNamePart      = $classNameExploded[1] ?? '';
    $classNamePart      = str_replace('\\', DIRECTORY_SEPARATOR, $classNamePart);
    $classFilePath      = __DIR__.DIRECTORY_SEPARATOR.$classNamePart.'.php';
    $classFile          = new SplFileInfo($classFilePath);

    if ($classFile->isFile()) {
        require $classFile->getPathname();
    }
});