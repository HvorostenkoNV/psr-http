<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\GeneratorInterface;
use AVMG\Http\Tests\Collection\CollectionMediator;

use function in_array;
use function urlencode;
/** ***********************************************************************************************
 * URI user info generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class UserInfo implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $allChars           = CollectionMediator::get('text.specialCharacters');
        $reservedChars      = CollectionMediator::get('uri.reservedCharacters');
        $unreservedChars    = CollectionMediator::get('uri.unreservedCharacters');
        $result             = [
            'login:password'    => 'login:password',
            'login'             => 'login',
            ':password'         => null,

            'Login:Password'    => 'Login:Password',
            'LOGIN:PASSWORD'    => 'LOGIN:PASSWORD',
            'lOgIn:PasSwOrD'    => 'lOgIn:PasSwOrD',

            'login10'           => 'login10',
            '10login'           => '10login',

            'login '            => 'login%20',
            ' login'            => '%20login',
            'l o g i n'         => 'l%20o%20g%20i%20n'
        ];

        foreach ($allChars as $char) {
            $charEncoded = urlencode($char);

            if (in_array($char, $reservedChars)) {
                $result["login{$charEncoded}"]  = "login{$charEncoded}";
            } elseif (in_array($char, $unreservedChars)) {
                $result["login{$char}"]         = "login{$char}";
                $result["login{$charEncoded}"]  = "login{$char}";
            } else {
                $result["login{$char}"]         = "login{$charEncoded}";
                $result["login{$charEncoded}"]  = "login{$charEncoded}";
            }
        }

        return $result;
    }
}