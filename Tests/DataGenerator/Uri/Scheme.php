<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function in_array;
/** ***********************************************************************************************
 * URI scheme generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Scheme implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars       = CollectionMediator::get('text.specialCharacters');
        $reservedChars  = CollectionMediator::get('uri.reservedCharacters');
        $allowedChars   = CollectionMediator::get('uri.scheme.allowedCharacters');
        $result         = [
            'http'          => 'http',
            'https'         => 'https',
            'ftp'           => 'ftp',
            'scheme'        => 'scheme',

            'Http'          => 'http',
            'HTTP'          => 'http',
            'hTtP'          => 'http',

            'scheme10'      => 'scheme10',
            '10scheme'      => null,

            'scheme '       => null,
            ' scheme'       => null,
            's c h e m e'   => null
        ];

        foreach ($allChars as $char) {
            if (in_array($char, $reservedChars)) {
                continue;
            }

            if (in_array($char, $allowedChars)) {
                $schemes["scheme{$char}"]   = "scheme{$char}";
                $schemes["{$char}scheme"]   = null;
            } else {
                $schemes["scheme{$char}"]   = null;
                $schemes["{$char}scheme"]   = null;
            }
        }

        return $result;
    }
}