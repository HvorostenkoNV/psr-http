<?php
declare(strict_types=1);

namespace AVMG\Http\Collection\Resource\AccessMode;

use AVMG\Http\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Resource access mode readable collection.
 *
 * @package AVMG\Http
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
            'rb', 'rb+',
            'wb+',
            'ab+',
            'xb+',
            'cb+'
        ];
    }
}