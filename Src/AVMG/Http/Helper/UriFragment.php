<?php
declare(strict_types=1);

namespace AVMG\Http\Helper;

use AVMG\Http\Exception\NormalizingException;
/** ***********************************************************************************************
 * URI fragment class.
 *
 * @package avmg_psr_http
 * @author  Hvorostenko
 *************************************************************************************************/
class UriFragment
{
    /** **********************************************************************
     * Normalize the URI fragment.
     *
     * @param   string $fragment            URI fragment.
     *
     * @return  string                      Normalized URI fragment.
     * @throws  NormalizingException        Normalizing error.
     ************************************************************************/
    public static function normalize(string $fragment) : string
    {
        if (strlen($fragment) <= 0)
        {
            throw new NormalizingException('value is empty');
        }

        $fragmentConverted = str_replace(' ', '%20', $fragment);

        return $fragmentConverted;
    }
}