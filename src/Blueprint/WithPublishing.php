<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Blueprint;

interface WithPublishing extends Blueprint
{
    public function getVersion(): string;
    public function getAuthorName(): ?string;
    public function getAuthorUrl(): ?string;
}
