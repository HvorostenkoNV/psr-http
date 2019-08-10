<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function in_array;
use function urlencode;
/** ***********************************************************************************************
 * URI query generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Query implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars   = CollectionMediator::get('text.specialCharacters');
        $result     = [
            'key'                           => 'key',
            'value'                         => 'value',

            'Key'                           => 'Key',
            'KEY'                           => 'KEY',
            'kEy'                           => 'kEy',

            'key '                          => 'key%20',
            ' key'                          => '%20key',
            'k e y'                         => 'k%20e%20y',

            'key='                          => 'key',
            'key=='                         => 'key==',
            'key=&'                         => 'key',
            'key=?'                         => 'key=?',
            'key=value'                     => 'key=value',

            'key1=value2&key2'              => 'key1=value2&key2',
            'key1=value2&key2='             => 'key1=value2&key2',
            'key1=value2&key2=value2'       => 'key1=value2&key2=value2',

            'key1=value2&'                  => 'key1=value2',
            'key1=value2&=value2'           => 'key1=value2',
            'key1==value2&&key2===value2'   => 'key1==value2&key2===value2'
        ];

        foreach ($allChars as $char) {
            if (in_array($char, ['=', '&', '?', '#'])) {
                continue;
            }

            $charEncoded                                = urlencode($char);
            $result["{$char}={$char}"]                  = "{$char}={$char}";
            $result["{$charEncoded}={$charEncoded}"]    = "{$charEncoded}={$charEncoded}";
        }

        return $result;
    }
}