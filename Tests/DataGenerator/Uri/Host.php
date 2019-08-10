<?php
declare(strict_types=1);

namespace AVMG\Http\Tests\DataGenerator\Uri;

use AVMG\Http\Tests\DataGenerator\{
    GeneratorInterface,
    GeneratorMediator
};

use function is_null;
use function array_merge;
/** ***********************************************************************************************
 * URI host generator.
 *
 * @package AVMG\Http\Tests
 * @author  Hvorostenko
 *************************************************************************************************/
class Host implements GeneratorInterface
{
    /** **********************************************************************
     * @inheritDoc
     ************************************************************************/
    public function generate(): array
    {
        $domainNameValues       = GeneratorMediator::generate('uri.domainName');
        $ipAddressesV4Values    = GeneratorMediator::generate('ipAddress.v4');
        $ipAddressesV6ValuesRaw = GeneratorMediator::generate('ipAddress.v6');
        $ipAddressesV6Values    = [];

        foreach ($ipAddressesV6ValuesRaw as $key => $value) {
            $ipAddressesV6Values["[$key]"] = !is_null($value)
                ? "[$value]"
                : null;
        }

        return array_merge(
            $domainNameValues,
            $ipAddressesV4Values,
            $ipAddressesV6Values
        );
    }
}