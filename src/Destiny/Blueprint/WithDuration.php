<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use Carbon\CarbonInterval;
use DecodeLabs\Destiny\Blueprint;

interface WithDuration extends Blueprint
{
    public function setDuration(
        string|CarbonInterval|null $duration
    ): void;

    public function getDuration(): ?CarbonInterval;
}
