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
use DecodeLabs\Exceptional;

class Program implements
    WithIdentity,
    WithPublishing,
    WithPriority,
    WithDuration
{
    use BlueprintTrait;

    use WithIdentityTrait {
        WithIdentityTrait::__construct as private identityConstruct;
        WithIdentityTrait::jsonSerialize as private identityJsonSerialize;
    }

    use WithPublishingTrait {
        WithPublishingTrait::__construct as private publishingConstruct;
        WithPublishingTrait::jsonSerialize as private publishingJsonSerialize;
    }

    use WithPriorityTrait {
        WithPriorityTrait::__construct as private priorityConstruct;
        WithPriorityTrait::jsonSerialize as private priorityJsonSerialize;
    }

    use WithDurationTrait {
        WithDurationTrait::__construct as private durationConstruct;
        WithDurationTrait::jsonSerialize as private durationJsonSerialize;
    }

    /**
     * @var array<string>
     */
    protected array $categories = [];


    /**
     * @var array<string,Step>
     */
    protected array $steps = [];

    /**
     * @param array<string> $categories
     * @param array<Step> $steps
     */
    public function __construct(
        ?string $id,
        ?string $name,
        ?string $description = null,
        ?string $version = null,
        ?string $authorName = null,
        ?string $authorUrl = null,
        array $categories = [],
        string|CarbonInterval|null $duration = null,
        string|Priority $priority = Priority::Medium,
        array $steps = []
    ) {
        $this->identityConstruct(
            id: $id,
            name: $name,
            description: $description
        );

        $this->publishingConstruct(
            version: $version,
            authorName: $authorName,
            authorUrl: $authorUrl
        );

        $this->priorityConstruct(
            priority: $priority
        );

        $this->durationConstruct(
            duration: $duration
        );

        $this->setCategories(...$categories);
        $this->setSteps(...$steps);
    }

    /**
     * Set category IDs
     */
    public function setCategories(
        string ...$categories
    ): void {
        $this->categories = $categories;
    }

    /**
     * Get category IDs
     *
     * @return array<string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }


    /**
     * Set steps
     */
    public function setSteps(
        Step ...$steps
    ): void {
        $this->steps = [];

        foreach($steps as $step) {
            $this->addStep($step);
        }
    }

    /**
     * Get steps
     *
     * @return array<string,Step>
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Add step
     */
    public function addStep(
        Step $step
    ): void {
        $id = $step->getId();

        if(isset($this->steps[$id])) {
            throw Exceptional::InvalidArgument(
                'Step '.$id.' is already defined'
            );
        }

        $this->steps[$id] = $step;
    }

    /**
     * Get step
     */
    public function getStep(
        string $id
    ): ?Step {
        return $this->steps[$id] ?? null;
    }


    /**
     * Export for serialization
     */
    public function jsonSerialize(): array
    {
        return [
            ...$this->identityJsonSerialize(),
            ...$this->publishingJsonSerialize(),
            ...(!empty($this->categories) ? ['categories' => $this->categories] : []),
            ...$this->durationJsonSerialize(),
            ...$this->priorityJsonSerialize(),
            'steps' => $this->steps
        ];
    }
}
