<?php
declare(strict_types=1);

namespace Psr\Http\Client;

use Throwable;
/** ***********************************************************************************************
 * Every HTTP client related exception MUST implement this interface.
 *
 * @package AVMG\Http
 * @author  Hvorostenko
 *************************************************************************************************/
interface ClientExceptionInterface extends Throwable
{

}