<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Destiny\Priority;

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

    public function setPriority(
        string|Priority $priority
    ): void {
        $this->priority = Priority::fromAny($priority);
    }

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            'priority' => $this->priority->name
        ];
    }
}
