<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos;

use DecodeLabs\Enumerable\Unit\Named;
use DecodeLabs\Enumerable\Unit\NamedTrait;

enum Priority implements Named
{
    use NamedTrait;

    case Low;
    case Medium;
    case High;
    case Critical;
}
