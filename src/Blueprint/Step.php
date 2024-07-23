<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use Carbon\CarbonInterval;
use DecodeLabs\Chronos\BlueprintTrait;
use DecodeLabs\Chronos\Priority;

class Step implements
    WithIdentity,
    WithPriority,
    WithDuration,
    WithActions
{
    use BlueprintTrait;

    use WithIdentityTrait {
        WithIdentityTrait::__construct as private identityConstruct;
        WithIdentityTrait::jsonSerialize as private identityJsonSerialize;
    }

    use WithPriorityTrait {
        WithPriorityTrait::__construct as private priorityConstruct;
        WithPriorityTrait::jsonSerialize as private priorityJsonSerialize;
    }

    use WithDurationTrait {
        WithDurationTrait::__construct as private durationConstruct;
        WithDurationTrait::jsonSerialize as private durationJsonSerialize;
    }

    use WithActionsTrait {
        WithActionsTrait::__construct as private actionsConstruct;
        WithActionsTrait::jsonSerialize as private actionsJsonSerialize;
    }

    /**
     * @var array<string,CarbonInterval|null>
     */
    protected array $await = [];

    /**
     * @param array<string,string|CarbonInterval|null> $await
     * @param array<Action> $actions
     */
    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $description = null,
        string|Priority $priority = Priority::Medium,
        string|CarbonInterval|null $duration = null,
        array $await = [],
        array $actions = []
    ) {
        $this->identityConstruct(
            id: $id,
            name: $name,
            description: $description
        );

        $this->priorityConstruct(
            priority: $priority
        );

        $this->durationConstruct(
            duration: $duration
        );

        $this->setAwaits($await);

        $this->actionsConstruct(
            actions: $actions
        );
    }


    /**
     * Set awaits
     *
     * @param array<string,string|CarbonInterval|null> $await
     */
    public function setAwaits(
        array $await
    ): void {
        $this->await = [];

        foreach ($await as $id => $duration) {
            $this->addAwait($id, $duration);
        }
    }

    /**
     * Get awaits
     *
     * @return array<string,CarbonInterval|null>
     */
    public function getAwaits(): array
    {
        return $this->await;
    }

    /**
     * Add await
     */
    public function addAwait(
        string $id,
        string|CarbonInterval|null $duration
    ): void {
        if ($duration !== null) {
            $duration = CarbonInterval::make($duration);
        }

        $this->await[$id] = $duration;
    }

    /**
     * Get await duration
     */
    public function getAwaitDuration(
        string $id
    ): ?CarbonInterval {
        return $this->await[$id] ?? null;
    }

    /**
     * Will await
     */
    public function willAwait(
        string $id
    ): bool {
        return array_key_exists($id, $this->await);
    }


    /**
     * Export for serialization
     *
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        $await = [];

        foreach ($this->await as $key => $value) {
            $await[$key] = $value ? (string)$value : null;
        }

        return [
            ...$this->identityJsonSerialize(),
            ...$this->priorityJsonSerialize(),
            ...$this->durationJsonSerialize(),
            ...(!empty($await) ? ['await' => $await] : []),
            ...$this->actionsJsonSerialize()
        ];
    }
}
