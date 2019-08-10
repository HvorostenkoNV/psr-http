<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Resource\AccessMode;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode non suitable collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class NonSuitable implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            'x',    'xb',
            'x+',   'xb+'
        ];
    }
}