<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Destiny\Blueprint;
use DecodeLabs\Destiny\Priority;

interface WithPriority extends Blueprint
{
    public function setPriority(
        string|Priority $priority
    ): void;

    public function getPriority(): Priority;
}
