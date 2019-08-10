<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Resource\AccessMode;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode rewrite collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Rewrite implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            'w',    'wb',
            'w+',   'wb+'
        ];
    }
}