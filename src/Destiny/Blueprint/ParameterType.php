<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

enum ParameterType
{
    case String;
    case Number;
    case Boolean;
    case Date;
    case Reference;
    case List;
    case Action;
}
