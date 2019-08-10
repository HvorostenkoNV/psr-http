<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator;

use InvalidArgumentException;

use function explode;
use function implode;
use function ucfirst;
use function array_map;
use function class_exists;
use function call_user_func;
/** ***********************************************************************************************
 * Data generator mediator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class GeneratorMediator
{
    /** **********************************************************************
     * Get generated data.
     *
     * @param   string $type                Generator type.
     *
     * @return  array                       Generated data.
     ************************************************************************/
    public static function generate(string $type): array
    {
        try {
            $generatorClassName = self::getGeneratorClassName($type);
            $generatorData      = call_user_func([$generatorClassName, 'generate']);

            return $generatorData;
        } catch (InvalidArgumentException $exception) {
            return [];
        }
    }
    /** **********************************************************************
     * Get need generator class name by type.
     *
     * @param   string $type                Generator type.
     *
     * @return  string                      Generator class name.
     * @throws  InvalidArgumentException    Need generator class was not found.
     ************************************************************************/
    private static function getGeneratorClassName(string $type): string
    {
        $typeParts      = explode('.', $type);
        $typeParts      = array_map(function($value) {
            return ucfirst($value);
        }, $typeParts);
        $classNameShort = implode("\\", $typeParts);
        $classNameFull  = __NAMESPACE__.'\\'.$classNameShort;

        if (!class_exists($classNameFull)) {
            throw new InvalidArgumentException("class $classNameFull was not found");
        }

        return __NAMESPACE__.'\\'.$classNameShort;
    }
}