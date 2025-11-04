<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;

/**
 * @phpstan-require-implements WithIdentity
 */
trait WithIdentityTrait
{
    protected string $id;
    protected string $name;
    protected ?string $description = null;

    public function __construct(
        ?string $id,
        ?string $name,
        ?string $description = null
    ) {
        if (
            $id === null &&
            $name === null
        ) {
            throw Exceptional::InvalidArgument(
                message: 'Id or name must be provided to blueprints'
            );
        } elseif ($id === null) {
            $id = Dictum::slug($name);
        } else {
            $name = Dictum::name($id);
        }

        $this->setId($id);
        $this->name = $name;
        $this->description = $description;
    }

    public function setId(
        string $id
    ): void {
        if (
            !preg_match('/^[a-z0-9-_]+$/', $id) ||
            strlen($id) < 5 ||
            strlen($id) > 64
        ) {
            throw Exceptional::InvalidArgument(
                message: 'Invalid blueprint ID: ' . $id
            );
        }

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(
        string $name
    ): void {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(
        ?string $description
    ): void {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }


    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            ...($this->description !== null ? ['description' => $this->description] : [])
        ];
    }
}
