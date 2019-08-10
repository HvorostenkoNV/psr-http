<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;

use function rand;
/** ***********************************************************************************************
 * URI port generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Port implements GeneratorInterface
{
    private const MAX_VALUE = 65535;
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $maxAvailableValue  = self::MAX_VALUE;
        $result             = [
            0                       => 0,
            -1                      => null,
            $maxAvailableValue      => $maxAvailableValue,
            $maxAvailableValue + 1  => null
        ];

        for ($index = 5; $index > 0; $index--) {
            $value          = rand(0, $maxAvailableValue);
            $result[$value] = $value;
        }
        for ($index = 5; $index > 0; $index--) {
            $value          = rand(-100, -1);
            $result[$value] = null;
        }
        for ($index = 5; $index > 0; $index--) {
            $value          = rand($maxAvailableValue + 1, $maxAvailableValue + 100);
            $result[$value] = null;
        }

        return $result;
    }
}