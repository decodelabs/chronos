<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;

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
                'Id or name must be provided to blueprints'
            );
        } elseif ($id === null) {
            $id = Dictum::slug($name);
        } else {
            $name = Dictum::name($id);
        }

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Set blueprint ID
     */
    public function setId(
        string $id
    ): void {
        $this->id = $id;
    }

    /**
     * Get blueprint ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set name
     */
    public function setName(
        string $name
    ): void {
        $this->name = $name;
    }

    /**
     * Get blueprint name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set description
     */
    public function setDescription(
        ?string $description
    ): void {
        $this->description = $description;
    }

    /**
     * Get description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }


    /**
     * Export for serialization
     *
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
