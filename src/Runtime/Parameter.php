<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Runtime;

use DateTimeInterface;
use DecodeLabs\Chronos\Blueprint\ParameterType;

/**
 * @template T of string|int|float|bool|array<string>|DateTimeInterface
 */
class Parameter
{
    /**
     * @var T
     */
    protected string|int|float|bool|array|DateTimeInterface $value;

    protected ParameterType $type;

    public function __construct(
        string|int|float|bool|array|DateTimeInterface $value
    ) {
        $this->value = $value;

        if(is_string($value)) {
            if(preg_match('/^\{\{([a-zA-Z0-9]+\}\}$/', $value, $matches)) {
                $this->value = $matches[1];
                $this->type = ParameterType::Reference;
            } else {
                $this->type = ParameterType::String;
            }
        } elseif(
            is_int($value) ||
            is_float($value)
        ) {
            $this->type = ParameterType::Number;
        } elseif(is_bool($value)) {
            $this->type = ParameterType::Boolean;
        } elseif(is_array($value)) {
            $this->type = ParameterType::List;
        } elseif($value instanceof DateTimeInterface) {
            $this->type = ParameterType::Date;
        }
    }

    /**
     * Get value
     *
     * @return T
     */
    public function getValue(): string|int|float|bool|array|DateTimeInterface
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
}
