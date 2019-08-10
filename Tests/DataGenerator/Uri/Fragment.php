<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function urlencode;
/** ***********************************************************************************************
 * URI fragment generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Fragment implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars   = CollectionMediator::get('text.specialCharacters');
        $result     = [
            'fragment'          => 'fragment',
            'Fragment'          => 'Fragment',
            'FRAGMENT'          => 'FRAGMENT',
            'fRaGmEnT'          => 'fRaGmEnT',

            'fragment '         => 'fragment%20',
            ' fragment'         => '%20fragment',
            'f r a g m e n t'   => 'f%20r%20a%20g%20m%20e%20n%20t',

            'fragment10'        => 'fragment10',
            '10fragment'        => '10fragment'
        ];

        foreach ($allChars as $char) {
            $charEncoded                                    = urlencode($char);
            $result["{$char}fragment{$char}"]               = "{$char}fragment{$char}";
            $result["{$charEncoded}fragment{$charEncoded}"] = "{$charEncoded}fragment{$charEncoded}";
        }

        return $result;
    }
}