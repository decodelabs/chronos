<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DateTimeInterface;
use DecodeLabs\Chronos\Blueprint\Factory as BlueprintFactory;
use DecodeLabs\Exceptional;
use DecodeLabs\Glitch\Dumpable;
use stdClass;

/**
 * @phpstan-type ParameterValue string|int|float|bool|DateTimeInterface|array<string>|stdClass|ActionSet
 * @template T of string|int|float|bool|DateTimeInterface|array<string>|stdClass|ActionSet
 */
class Parameter implements Dumpable
{
    /**
     * @var T
     */
    protected string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet $value;
    protected ParameterType $type;

    /**
     * @param T $value
     */
    public function __construct(
        string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet $value
    ) {
        $this->value = $value;

        if (is_string($value)) {
            if (preg_match('/^\{\{([a-zA-Z0-9]+)\}\}$/', $value, $matches)) {
                $this->value = $matches[1];
                $this->type = ParameterType::Reference;
            } else {
                $this->type = ParameterType::String;
            }
        } elseif (
            is_int($value) ||
            is_float($value)
        ) {
            $this->type = ParameterType::Number;
        } elseif (is_bool($value)) {
            $this->type = ParameterType::Boolean;
        } elseif ($value instanceof DateTimeInterface) {
            $this->type = ParameterType::Date;
        } elseif (
            is_array($value) ||
            $value instanceof stdClass
        ) {
            if (
                is_array($value) &&
                array_is_list($value)
            ) {
                $this->type = ParameterType::List;
            } else {
                $this->type = ParameterType::Action;
                /** @var array<string,array<ParameterValue>>|stdClass $value */
                $this->value = (new BlueprintFactory())->createActionSet($value);
            }
        } elseif ($value instanceof ActionSet) {
            $this->type = ParameterType::Action;
        } else {
            throw Exceptional::InvalidArgument(
                'Invalid parameter value type'
            );
        }
    }

    /**
     * Get value
     *
     * @return T
     */
    public function getValue(): string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet
    {
        return $this->value;
    }

    /**
     * Get type
     */
    public function getType(): ParameterType
    {
        return $this->type;
    }


    /**
     * Export for dump
     */
    public function glitchDump(): iterable
    {
        yield 'className' => $this->type->name;
        yield 'value' => $this->value;
    }
}
