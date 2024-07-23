<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Blueprint;

interface WithIdentity extends Blueprint
{
    public function getId(): string;
    public function getName(): string;
    public function getDescription(): ?string;
}
