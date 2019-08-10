<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Resource\AccessMode;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode readable collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Readable implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            'r',    'rb',
            'r+',   'rb+',
            'w+',   'wb+',
            'a+',   'ab+',
            'x+',   'xb+',
            'c+',   'cb+'
        ];
    }
}