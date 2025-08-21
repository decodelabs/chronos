<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DateTimeInterface;
use DecodeLabs\Destiny\Blueprint\Factory as BlueprintFactory;
use DecodeLabs\Exceptional;
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use stdClass;

/**
 * @phpstan-type ParameterValue string|int|float|bool|DateTimeInterface|array<string>|array<string,array<mixed>>|stdClass|ActionSet
 * @template T of string|int|float|bool|DateTimeInterface|array<string>|array<string,array<mixed>>|stdClass|ActionSet
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
                /** @phpstan-ignore-next-line */
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
                /**
                 * @var array<string,array<ParameterValue>>|stdClass $value
                 * @phpstan-ignore-next-line
                 */
                $this->value = new BlueprintFactory()->createActionSet($value);
            }
        } elseif ($value instanceof ActionSet) {
            $this->type = ParameterType::Action;
        } else {
            throw Exceptional::InvalidArgument(
                message: 'Invalid parameter value type'
            );
        }
    }

    /**
     * @return T
     */
    public function getValue(): string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet
    {
        return $this->value;
    }

    public function getType(): ParameterType
    {
        return $this->type;
    }


    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $entity->itemName = $this->type->name;
        $entity->value = $this->value;
        return $entity;
    }
}
