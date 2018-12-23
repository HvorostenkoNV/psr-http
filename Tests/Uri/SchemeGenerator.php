<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\Uri;
/** ***********************************************************************************************
 * URI data generator.
 *
 * @package avmg_psr_http_tests
 * @author  Hvorostenko
 *************************************************************************************************/
class SchemeGenerator
{
    private const
        SCHEME_AVAILABLE_SPECIAL_CHARS      =
            [
                '+', '-', '.'
            ],
        SCHEME_UNAVAILABLE_SPECIAL_CHARS    =
            [
                '/', '\\', '*',
                ':', '!', '?',
                '@', '=', '&'
            ];
    /** **********************************************************************
     * Generate schemes values array.
     *
     * @param   int $count                  Values count.
     * @return  string[]                    Correct schemes.
     ************************************************************************/
    public static function generateValues(int $count) : array
    {
        $result = [];

        for ($index = $count; $index > 0; $index--)
        {
            $schemeLength   = rand(1, 25);
            $scheme         = '';

            $scheme .= self::generateCorrectFirstSymbol();
            $schemeLength--;

            for ($index = $schemeLength; $index > 0; $index--)
            {
                $scheme .= self::getSchemeRandomSymbolCorrect();
            }

            $result[] = $scheme;
        }

        return $result;
    }
    /** **********************************************************************
     * Generate schemes incorrect values array.
     *
     * @param   int $count                  Values count.
     * @return  string[]                    Incorrect schemes.
     ************************************************************************/
    public static function generateIncorrectValues(int $count) : array
    {
        $correctValues      = self::generateValues($count);
        $incorrectValues    = [];

        if (count($correctValues) > 0)
        {
            array_pop($correctValues);
            $incorrectValues[] = '';
        }

        if (count($correctValues) > 5)
        {
            for ($index = 5; $index > 0; $index--)
            {
                $value = array_pop($correctValues);
                $value[0] = self::generateIncorrectFirstSymbol();
                $incorrectValues[] = $value;
            }
        }

        while (count($correctValues) > 0)
        {
            $value          = array_pop($correctValues);
            $valueLength    = strlen($value);

            if ($valueLength == 1)
            {
                $incorrectValues[] = $value;
            }
        }




        for ($index = 6; $index > 0; $index--)
        {
            $schemeLength   = rand(1, 25);
            $scheme         = '';

            $scheme .= self::generateCorrectFirstSymbol();
            $schemeLength--;

            for ($index = $schemeLength; $index > 0; $index--)
            {
                $scheme .= self::getSchemeRandomSymbolIncorrect();
            }

            $result[] = $scheme;
        }

        for ($index = 6; $index > 0; $index--)
        {
            $schemeLength   = rand(1, 25);
            $scheme         = '';

            $scheme .= self::getSchemeRandomSymbolIncorrect();
            $schemeLength--;

            for ($index = $schemeLength; $index > 0; $index--)
            {
                $scheme .= self::getSchemeRandomSymbolCorrect();
            }

            $result[] = $scheme;
        }

        for ($index = 7; $index > 0; $index--)
        {
            $schemeLength       = rand(1, 25);
            $schemeFirstLetter  = '';
            $scheme             = '';

            while (strlen($schemeFirstLetter) <= 0 || ctype_alpha($schemeFirstLetter))
            {
                $schemeFirstLetter = self::getSchemeRandomSymbolCorrect();
            }

            $scheme .= $schemeFirstLetter;
            $schemeLength--;

            for ($index = $schemeLength; $index > 0; $index--)
            {
                $scheme .= self::getSchemeRandomSymbolCorrect();
            }

            $result[] = $scheme;
        }

        $result[] = '';
        return $result;
    }
    /** **********************************************************************
     * Generate scheme correct first symbol.
     *
     * @return  string                      Symbol.
     ************************************************************************/
    private static function generateCorrectFirstSymbol() : string
    {
        $isUppercase = rand(0, 1) == 1;

        return self::getRandomLetter($isUppercase);
    }
    /** **********************************************************************
     * Generate scheme incorrect first symbol.
     *
     * @return  string                      Symbol.
     ************************************************************************/
    private static function generateIncorrectFirstSymbol() : string
    {

    }
    /** **********************************************************************
     * Generate scheme correct random symbol.
     *
     * @return  string                      Symbol.
     ************************************************************************/
    private static function getSchemeRandomSymbolCorrect() : string
    {
        switch (rand(1, 10))
        {
            case 1:
            case 2:
            case 3:
                return self::getRandomLetter(true);
            case 4:
            case 5:
            case 6:
                return (string) rand(0, 99);
            case 7:
                $specialChars = self::SCHEME_AVAILABLE_SPECIAL_CHARS;
                return $specialChars[array_rand($specialChars)];
            default:
                return self::getRandomLetter();
        }
    }
    /** **********************************************************************
     * Generate scheme incorrect random symbol.
     *
     * @return  string                      Symbol.
     ************************************************************************/
    private static function getSchemeRandomSymbolIncorrect() : string
    {
        $specialChars = self::SCHEME_UNAVAILABLE_SPECIAL_CHARS;
        return $specialChars[array_rand($specialChars)];
    }
    /** **********************************************************************
     * Generate random letter.
     *
     * @param   bool $upperCase             Get random letter uppercase.
     * @return  string                      Letter.
     ************************************************************************/
    private static function getRandomLetter(bool $upperCase = false) : string
    {
        return $upperCase
            ? chr(rand(65, 90))
            : chr(rand(97, 122));
    }
}