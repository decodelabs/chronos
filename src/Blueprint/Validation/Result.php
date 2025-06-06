<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint\Validation;

use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use Generator;

class Result implements Dumpable
{
    /**
     * @var array<Error>
     */
    protected array $errors = [];

    public function __construct(
        Error ...$errors
    ) {
        $this->errors = $errors;
    }

    /**
     * Get all errors
     *
     * @return array<Error>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Is valid
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Scan errors
     */
    public function scanErrors(): Generator
    {
        foreach ($this->errors as $error) {
            yield $error->getLocation() => $error->getMessage();
        }
    }


    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $entity->meta['valid'] = $this->isValid();
        $errors = [];

        foreach ($this->errors as $error) {
            $location = $error->getLocation();

            if (isset($errors[$location])) {
                if (is_array($errors[$location])) {
                    $errors[$location][] = $error->getMessage();
                } else {
                    $errors[$location] = [$errors[$location], $error->getMessage()];
                }
            } else {
                $errors[$location] = $error->getMessage();
            }
        }

        $entity->values = $errors;
        return $entity;
    }
}
