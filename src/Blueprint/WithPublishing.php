<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Destiny\Blueprint;

interface WithPublishing extends Blueprint
{
    public function getVersion(): string;
    public function getAuthorName(): ?string;
    public function getAuthorUrl(): ?string;
}
