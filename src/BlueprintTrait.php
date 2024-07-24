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
        $output = $this->jsonSerialize();

        /** @phpstan-ignore-next-line */
        if (is_object($output)) {
            $output = get_object_vars($output);
        }

        if (!is_array($output)) {
            dd($output);
        }

        return $output;
    }
}
