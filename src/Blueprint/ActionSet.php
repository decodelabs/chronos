<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Destiny\Blueprint;
use DecodeLabs\Destiny\BlueprintTrait;

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
