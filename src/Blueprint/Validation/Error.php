<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint\Validation;

class Error
{
    public function __construct(
        protected string $location,
        protected string $message,
    ) {
    }

    /**
     * Get the location of the error
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get the error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
