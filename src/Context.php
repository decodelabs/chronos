<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Chronos;
use DecodeLabs\Chronos\Blueprint\Factory as BlueprintFactory;
use DecodeLabs\Veneer;

class Context
{
    /**
     * Load a blueprint from json
     */
    public function loadBlueprint(
        string|File $file
    ): Blueprint {
        if (is_string($file)) {
            $file = Atlas::file($file);
        }

        $factory = new BlueprintFactory();
        return $factory->load($file);
    }
}

// Register the Veneer facade
Veneer::register(Context::class, Chronos::class);
