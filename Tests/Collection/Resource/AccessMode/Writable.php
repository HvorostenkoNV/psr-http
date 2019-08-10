<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Resource\AccessMode;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode writable collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Writable implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            'r+',   'rb+',
            'w',    'wb',
            'w+',   'wb+',
            'a',    'ab',
            'a+',   'ab+',
            'x',    'xb',
            'x+',   'xb+',
            'c',    'cb',
            'c+',   'cb+'
        ];
    }
}