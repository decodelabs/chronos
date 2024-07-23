<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

enum ParameterType
{
    case String;
    case Number;
    case Boolean;
    case List;
    case Date;
    case Reference;
}
