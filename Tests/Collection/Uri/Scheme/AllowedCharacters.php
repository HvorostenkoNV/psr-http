<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Uri\Scheme;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Scheme allowed characters collection.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class AllowedCharacters implements CollectionInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function get(): array
    {
        return [
            '+',
            '-',
            '.'
        ];
    }
}