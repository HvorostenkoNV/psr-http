<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Collection\Uri\DomainName;

use AVMG\Http\Tests\Collection\CollectionInterface;
/** ***********************************************************************************************
 * Domain name allowed characters collection.
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
            '-',
            '.'
        ];
    }
}