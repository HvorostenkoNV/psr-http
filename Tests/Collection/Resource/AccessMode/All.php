<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Resource\AccessMode;

use AVMG\Http\Tests\Collection\{
    CollectionInterface,
    CollectionMediator
};

use function array_merge;
use function array_unique;
/** ***********************************************************************************************
 * Resource access mode all values collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class All implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        $readable   = CollectionMediator::get('resource.accessMode.readable');
        $writable   = CollectionMediator::get('resource.accessMode.writable');
        $all        = array_merge($readable, $writable);

        return array_unique($all);
    }
}