<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Destiny\Blueprint;

interface WithIdentity extends Blueprint
{
    public function getId(): string;
    public function getName(): string;
    public function getDescription(): ?string;
}
