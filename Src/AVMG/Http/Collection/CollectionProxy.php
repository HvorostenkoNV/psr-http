<?php
declare(strict_types=1);

namespace AVMG\Http\Collection;

use InvalidArgumentException;

use function explode;
use function implode;
use function ucfirst;
use function array_map;
use function call_user_func;
use function class_exists;
/** ***********************************************************************************************
 * Collection proxy.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class CollectionProxy
{
    /** **********************************************************************
     * Receive specific collection data.
     *
     * @param   string $type                Collection type.
     *
     * @return  array                       Collection data.
     * @throws  InvalidArgumentException    Collection type incorrect.
     ************************************************************************/
    public static function receive(string $type)
    {
        try {
            $collectionClassName    = self::getCollectionClassName($type);
            $collectionData         = call_user_func([$collectionClassName, 'get']);

            return $collectionData;
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException("collection type \"$type\" is unhandled", 0, $exception);
        }
    }
    /** **********************************************************************
     * Get need collection class name by type.
     *
     * @param   string $type                Collection type.
     *
     * @return  string                      Collection class name.
     * @throws  InvalidArgumentException    Need collection class was not found.
     ************************************************************************/
    private static function getCollectionClassName(string $type): string
    {
        $typeParts      = explode('.', $type);
        $typeParts      = array_map(function($value) {
            return ucfirst($value);
        }, $typeParts);
        $classNameShort = implode("\\", $typeParts);
        $classNameFull  = __NAMESPACE__."\\".$classNameShort;

        if (!class_exists($classNameFull)) {
            throw new InvalidArgumentException("class $classNameFull was not found");
        }

        return $classNameFull;
    }
}