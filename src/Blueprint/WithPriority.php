<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Blueprint;
use DecodeLabs\Chronos\Priority;

interface WithPriority extends Blueprint
{
    public function setPriority(
        string|Priority $priority
    ): void;

    public function getPriority(): Priority;
}
