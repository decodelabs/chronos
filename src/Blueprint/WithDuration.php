<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use Carbon\CarbonInterval;
use DecodeLabs\Chronos\Blueprint;

interface WithDuration extends Blueprint
{
    public function setDuration(
        string|CarbonInterval|null $duration
    ): void;

    public function getDuration(): ?CarbonInterval;
}
