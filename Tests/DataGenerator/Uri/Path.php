<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function in_array;
use function urlencode;
/** ***********************************************************************************************
 * URI path generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Path implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars       = CollectionMediator::get('text.specialCharacters');
        $reservedChars  = CollectionMediator::get('uri.reservedCharacters');
        $nonCodedChars  = CollectionMediator::get('uri.path.nonCodedCharacters');
        $result         = [
            'path'                      => 'path',
            'path1/path2'               => 'path1/path2',
            'path1/path2/path3-path4'   => 'path1/path2/path3-path4',

            'Path'                      => 'Path',
            'PATH'                      => 'PATH',
            'pAtH'                      => 'pAtH',

            'path '                     => 'path%20',
            ' path'                     => '%20path',
            'p a t h'                   => 'p%20a%20t%20h'
        ];

        foreach ($allChars as $char) {
            $charEncoded = urlencode($char);

            if (in_array($char, $nonCodedChars)) {
                $result["path{$char}"]          = "path{$char}";
                $result["path{$charEncoded}"]   = "path{$char}";
            } elseif (in_array($char, $reservedChars)) {
                $result["path{$charEncoded}"]   = "path{$charEncoded}";
            } else {
                $result["path{$char}"]          = "path{$charEncoded}";
                $result["path{$charEncoded}"]   = "path{$charEncoded}";
            }
        }

        return $result;
    }
}