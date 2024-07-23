<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos;

trait BlueprintTrait
{
    /**
     * Export for debug
     */
    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }
}
