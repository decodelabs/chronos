<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use Carbon\CarbonInterval;

/**
 * @phpstan-require-implements WithDuration
 */
trait WithDurationTrait
{
    protected ?CarbonInterval $duration = null;

    public function __construct(
        string|CarbonInterval|null $duration = null
    ) {
        $this->setDuration($duration);
    }

    public function setDuration(
        string|CarbonInterval|null $duration
    ): void {
        if ($duration !== null) {
            $duration = CarbonInterval::make($duration);
        }

        $this->duration = $duration;
    }

    public function getDuration(): ?CarbonInterval
    {
        return $this->duration;
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return $this->duration ?
            ['duration' => (string)$this->duration] :
            [];
    }
}
