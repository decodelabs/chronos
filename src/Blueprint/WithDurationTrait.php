<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use Carbon\CarbonInterval;

trait WithDurationTrait
{
    protected ?CarbonInterval $duration = null;

    public function __construct(
        string|CarbonInterval|null $duration = null
    ) {
        $this->setDuration($duration);
    }

    /**
     * Set duration
     */
    public function setDuration(
        string|CarbonInterval|null $duration
    ): void {
        if ($duration !== null) {
            $duration = CarbonInterval::make($duration);
        }

        $this->duration = $duration;
    }

    /**
     * Get duration
     */
    public function getDuration(): ?CarbonInterval
    {
        return $this->duration;
    }

    /**
     * Export for serialization
     *
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return $this->duration ?
            ['duration' => (string)$this->duration] :
            [];
    }
}
