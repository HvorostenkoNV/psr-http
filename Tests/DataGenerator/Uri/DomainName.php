<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function in_array;
/** ***********************************************************************************************
 * URI domain name generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class DomainName implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars       = CollectionMediator::get('text.specialCharacters');
        $reservedChars  = CollectionMediator::get('uri.reservedCharacters');
        $allowedChars   = CollectionMediator::get('uri.domainName.allowedCharacters');
        $result         = [
            'site.com'      => 'site.com',
            'www.site.com'  => 'www.site.com',
            'localhost'     => 'localhost',

            'Site.com'      => 'site.com',
            'SITE.com'      => 'site.com',
            'sItE.com'      => 'site.com',

            'site10'        => 'site10',
            '10site'        => '10site',

            'site.com '     => null,
            ' site.com'     => null,
            's i t e.com'   => null
        ];

        foreach ($allChars as $char) {
            if (in_array($char, $reservedChars)) {
                continue;
            }

            if (in_array($char, $allowedChars)) {
                $result["site{$char}.com"]  = "site{$char}.com";
                $result["{$char}site.com"]  = null;
            } else {
                $result["site{$char}.com"]  = null;
                $result["{$char}site.com"]  = null;
            }
        }

        return $result;
    }
}