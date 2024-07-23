<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

trait WithPublishingTrait
{
    protected ?string $version = null;
    protected ?string $authorName = null;
    protected ?string $authorUrl = null;

    public function __construct(
        ?string $version = null,
        ?string $authorName = null,
        ?string $authorUrl = null
    ) {
        $this->version = $version;
        $this->authorName = $authorName;
        $this->authorUrl = $authorUrl;
    }


    /**
     * Set version
     */
    public function setVersion(
        string $version
    ): void {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        if ($this->version === null) {
            return '0.0.1';
        }

        return $this->version;
    }

    /**
     * Set author name
     */
    public function setAuthorName(
        ?string $authorName
    ): void {
        $this->authorName = $authorName;
    }

    /**
     * Get author name
     */
    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    /**
     * Set author URL
     */
    public function setAuthorUrl(
        ?string $authorUrl
    ): void {
        $this->authorUrl = $authorUrl;
    }

    /**
     * Get author URL
     */
    public function getAuthorUrl(): ?string
    {
        return $this->authorUrl;
    }

    /**
     * Export for serialization
     *
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            ...($this->version !== null ? ['version' => $this->version] : []),
            ...($this->authorName !== null ? ['authorName' => $this->authorName] : []),
            ...($this->authorUrl !== null ? ['authorUrl' => $this->authorUrl] : [])
        ];
    }
}
