<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Blueprint;

interface WithActions extends Blueprint
{
    public function setActions(
        Action ...$actions
    ): void;

    /**
     * @return array<Action>
     */
    public function getActions(): array;

    public function addAction(
        Action $action
    ): void;
}
