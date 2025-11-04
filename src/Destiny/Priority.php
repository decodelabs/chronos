<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny;

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
