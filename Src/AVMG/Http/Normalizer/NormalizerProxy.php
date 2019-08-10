<?php
declare(strict_types=1);

namespace AVMG\Http\Normalizer;

use Throwable;
use InvalidArgumentException;

use function explode;
use function implode;
use function ucfirst;
use function array_map;
use function call_user_func;
use function class_exists;
/** ***********************************************************************************************
 * Normalizer proxy.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class NormalizerProxy
{
    /** **********************************************************************
     * Normalize data.
     *
     * @param   string  $type               Normalizer type.
     * @param   mixed   $value              Value.
     *
     * @return  mixed                       Normalized value.
     * @throws  InvalidArgumentException    Normalizer type incorrect.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $type, $value)
    {
        try {
            $normalizerClassName    = self::getNormalizerClassName($type);
            $normalizedValue        = call_user_func([$normalizerClassName, 'normalize'], $value);

            return $normalizedValue;
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException("normalizer type \"$type\" is unhandled", 0, $exception);
        } catch (Throwable $exception) {
            throw new NormalizingException($exception->getMessage(), 0, $exception);
        }
    }
    /** **********************************************************************
     * Get need normalizer class name by type.
     *
     * @param   string $type                Normalizer type.
     *
     * @return  string                      Normalizer class name.
     * @throws  InvalidArgumentException    Need normalizer class was not found.
     ************************************************************************/
    private static function getNormalizerClassName(string $type): string
    {
        $typeParts      = explode('.', $type);
        $typeParts      = array_map(function($value) {
            return ucfirst($value);
        }, $typeParts);
        $classNameShort = implode("\\", $typeParts);
        $classNameFull  = __NAMESPACE__."\\".$classNameShort;

        if (!class_exists($classNameFull)) {
            throw new InvalidArgumentException("class $classNameFull was not found");
        }

        return $classNameFull;
    }
}