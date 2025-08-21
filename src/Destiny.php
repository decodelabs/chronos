<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Atlas\File;
use DecodeLabs\Destiny\Blueprint;
use DecodeLabs\Destiny\Blueprint\Factory as BlueprintFactory;
use DecodeLabs\Destiny\Blueprint\Validation\Result as ValidationResult;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;

class Destiny implements Service
{
    use ServiceTrait;

    public function loadBlueprint(
        string|File $file
    ): Blueprint {
        if (is_string($file)) {
            $file = Atlas::getFile($file);
        }

        $factory = new BlueprintFactory();
        return $factory->load($file);
    }

    public function loadBlueprintString(
        string $json
    ): Blueprint {
        $factory = new BlueprintFactory();
        return $factory->loadString($json);
    }

    public function validateBlueprint(
        string|File $file
    ): ValidationResult {
        if (is_string($file)) {
            $file = Atlas::getFile($file);
        }

        $factory = new BlueprintFactory();
        return $factory->validate($file);
    }

    public function validateBlueprintString(
        string $json
    ): ValidationResult {
        $factory = new BlueprintFactory();
        return $factory->validateString($json);
    }
}
