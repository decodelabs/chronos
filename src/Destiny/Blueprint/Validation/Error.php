<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint\Validation;

class Error
{
    public function __construct(
        protected string $location,
        protected string $message,
    ) {
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
