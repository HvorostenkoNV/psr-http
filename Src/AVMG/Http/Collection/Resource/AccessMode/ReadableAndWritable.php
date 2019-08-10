<?php
declare(strict_types=1);

namespace AVMG\Http\Collection\Resource\AccessMode;

use AVMG\Http\Collection\CollectionInterface;

use function array_values;
use function array_intersect;
/** ***********************************************************************************************
 * Resource access mode readable and writable collection.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class ReadableAndWritable implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        $readable   = Readable::get();
        $writable   = Writable::get();
        $matches    = array_intersect($readable, $writable);

        return array_values($matches);
    }
}