<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Blueprint;
use DecodeLabs\Chronos\BlueprintTrait;

class ActionSet implements
    Blueprint,
    WithActions
{
    use BlueprintTrait;
    use WithActionsTrait;

    public function __construct(
        Action ...$actions
    ) {
        $this->setActions(...$actions);
    }
}
