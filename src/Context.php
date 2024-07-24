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
use DecodeLabs\Chronos\Blueprint\Validation\Result as ValidationResult;
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

    /**
     * Load blueprint string
     */
    public function loadBlueprintString(
        string $json
    ): Blueprint {
        $factory = new BlueprintFactory();
        return $factory->loadString($json);
    }

    /**
     * Validate blueprint
     */
    public function validateBlueprint(
        string|File $file
    ): ValidationResult {
        if (is_string($file)) {
            $file = Atlas::file($file);
        }

        $factory = new BlueprintFactory();
        return $factory->validate($file);
    }

    /**
     * Validate blueprint string
     */
    public function validateBlueprintString(
        string $json
    ): ValidationResult {
        $factory = new BlueprintFactory();
        return $factory->validateString($json);
    }
}

// Register the Veneer facade
Veneer::register(Context::class, Chronos::class);
