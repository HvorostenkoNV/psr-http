<?php
declare(strict_types=1);

namespace AVMG\Http\Normalizer\Resource;

use InvalidArgumentException;
use AVMG\Http\{
    Normalizer\NormalizingException,
    Normalizer\NormalizerInterface,
    Collection\CollectionProxy
};

use function str_replace;
use function strpos;
use function count;
use function in_array;
use function array_merge;
use function array_unique;
/** ***********************************************************************************************
 * Resource access mode normalizer.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
class AccessMode implements NormalizerInterface
{
    private const SPECIAL_FLAG  = 'b';
    private const POSTFIX       = '+';

    private static $accessModes = [];
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public static function normalize($value)
    {
        $hasPostfix         = strpos($value, self::POSTFIX) !== false;
        $valueClear         = str_replace([self::SPECIAL_FLAG, self::POSTFIX], '', $value);
        $valueNormalized    = $hasPostfix
            ? $valueClear.self::SPECIAL_FLAG.self::POSTFIX
            : $valueClear.self::SPECIAL_FLAG;
        $availableValues    = self::getAvailableValues();

        if (!in_array($valueNormalized, $availableValues)) {
            throw new NormalizingException("mode \"$value\" is unknown");
        }

        return $valueNormalized;
    }
    /** **********************************************************************
     * Get available resource access modes.
     *
     * @return  array                       Available resource access modes.
     ************************************************************************/
    private static function getAvailableValues(): array
    {
        if (count(self::$accessModes) === 0) {
            try {
                $accessModes        = array_merge(
                    CollectionProxy::receive('resource.accessMode.readable'),
                    CollectionProxy::receive('resource.accessMode.writable')
                );

                self::$accessModes  = array_unique($accessModes);
            } catch (InvalidArgumentException $exception) {

            }
        }

        return self::$accessModes;
    }
}