<?php
declare(strict_types=1);

namespace AVMG\Http\Collection\Resource\AccessMode;

use AVMG\Http\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode writable collection.
 *
 * @package AVMG\Http
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
            'rb+',
            'wb',   'wb+',
            'ab',   'ab+',
            'xb',   'xb+',
            'cb',   'cb+'
        ];
    }
}