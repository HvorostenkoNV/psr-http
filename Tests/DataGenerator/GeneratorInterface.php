<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator;
/** ***********************************************************************************************
 * Data generator interface.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
interface GeneratorInterface
{
    /** **********************************************************************
     * Generate data.
     *
     * @return  array                       Generated data.
     ************************************************************************/
    public function generate(): array;
}