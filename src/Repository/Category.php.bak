<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos;

use DecodeLabs\Chronos\Definition\Category as CategoryDefinition;
use DecodeLabs\Chronos\Repository;

interface Category extends Repository
{
    public function create(
        CategoryDefinition $category
    ): string;

    public function store(
        CategoryDefinition $category
    ): void;

    public function fetchById(
        string $id
    ): CategoryDefinition;

    public function fetchBySlug(
        string $slug
    ): CategoryDefinition;

    public function delete(
        string|CategoryDefinition $category
    ): void;
}
