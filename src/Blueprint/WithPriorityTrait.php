<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Chronos\Priority;

/**
 * @phpstan-require-implements WithPriority
 */
trait WithPriorityTrait
{
    protected Priority $priority = Priority::Medium;

    public function __construct(
        string|Priority $priority = Priority::Medium
    ) {
        $this->setPriority($priority);
    }

    /**
     * Set priority
     */
    public function setPriority(
        string|Priority $priority
    ): void {
        $this->priority = Priority::fromAny($priority);
    }

    /**
     * Get priority
     */
    public function getPriority(): Priority
    {
        return $this->priority;
    }

    /**
     * Export for serialization
     *
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            'priority' => $this->priority->name
        ];
    }
}
