<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Runtime;

use DateTimeInterface;
use DecodeLabs\Chronos\Blueprint\ActionSet;
use DecodeLabs\Chronos\Blueprint\Parameter;
use stdClass;

/**
 * @phpstan-import-type ParameterValue from Parameter
 */
class Stack
{
    /**
     * @phpstan-var array<string,Parameter<ParameterValue>>
     */
    protected array $parameters = [];

    protected ?Stack $parent = null;

    /**
     * @phpstan-param array<ParameterValue|Parameter<ParameterValue>> $parameters
     */
    public function __construct(
        array $parameters = [],
        ?Stack $parent = null
    ) {
        $this->parent = $parent;

        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Set value
     *
     * @phpstan-param ParameterValue|Parameter<ParameterValue> $value
     */
    public function set(
        string $key,
        string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet|Parameter $value
    ): void {
        if (str_starts_with($key, '$')) {
            $key = substr($key, 1);

            if ($this->parent) {
                $this->parent->set($key, $value);
                return;
            }
        }

        if (!$value instanceof Parameter) {
            $value = new Parameter($value);
        }

        $this->parameters[$key] = $value;
    }

    /**
     * Set in parent
     *
     * @phpstan-param ParameterValue|Parameter<ParameterValue> $value
     */
    public function parentSet(
        string $key,
        string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet|Parameter $value
    ): void {
        if ($this->parent) {
            $this->parent->set($key, $value);
        } else {
            $this->set($key, $value);
        }
    }

    /**
     * Get value
     *
     * @phpstan-return Parameter<ParameterValue>|null
     */
    public function get(
        string $key
    ): ?Parameter {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        if ($this->parent) {
            return $this->parent->get($key);
        }

        return null;
    }
}
