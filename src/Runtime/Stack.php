<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Runtime;

use DateTimeInterface;

class Stack
{
    /**
     * @var array<string,Parameter>
     */
    protected array $parameters = [];

    protected ?Stack $parent = null;

    /**
     * @param array<string,string|int|float|bool|array<mixed>|DateTimeInterface|Parameter> $parameters
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
     */
    public function set(
        string $key,
        string|int|float|bool|array|DateTimeInterface|Parameter $value
    ): void {
        if(str_starts_with($key, '$')) {
            $key = substr($key, 1);

            if($this->parent) {
                $this->parent->set($key, $value);
                return;
            }
        }

        if (!$value instanceof Parameter) {
            $value = new Parameter($key, $value);
        }

        $this->parameters[$key] = $value;
    }

    /**
     * Set in parent
     */
    public function parentSet(
        string $key,
        string|int|float|bool|array|DateTimeInterface|Parameter $value
    ): void {
        if ($this->parent) {
            $this->parent->set($key, $value);
        } else {
            $this->set($key, $value);
        }
    }

    /**
     * Get value
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
