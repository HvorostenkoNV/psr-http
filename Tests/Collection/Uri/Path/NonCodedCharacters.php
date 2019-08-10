<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Uri\Path;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Path non coded characters collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class NonCodedCharacters implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            '\'',
            '[', ']', '(', ')',
            '+', '-', '=', '*', '%',
            ',', '.', ':',
            '~', '!', '@',
            '$', '&', '_'
        ];
    }
}