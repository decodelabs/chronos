<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DateTimeInterface;
use DecodeLabs\Destiny\Blueprint;
use DecodeLabs\Destiny\BlueprintTrait;
use DecodeLabs\Exceptional;
use stdClass;

/**
 * @phpstan-import-type ParameterValue from Parameter
 */
class Action implements Blueprint
{
    use BlueprintTrait;

    protected string $initiator;
    protected ?string $return = null;

    /**
     * @var array<string,Parameter<ParameterValue>>
     */
    protected array $parameters = [];

    /**
     * @param array<string,ParameterValue|Parameter<ParameterValue>> $parameters
     */
    public function __construct(
        ?string $signature = null,
        ?string $initiator = null,
        ?string $return = null,
        array $parameters = [],
    ) {
        if ($signature !== null) {
            [
                'initiator' => $initiator,
                'return' => $return,
            ] = $this->parseSignature($signature);
        }

        if ($initiator === null) {
            throw Exceptional::InvalidArgument(
                message: 'Action signature is missing initiator'
            );
        }

        $this->setInitiator($initiator);
        $this->setReturn($return);
        $this->setParameters($parameters);
    }

    /**
     * @return array{initiator: string, return: string|null}
     */
    public function parseSignature(
        string $signature
    ): array {
        $parts = explode(':', $signature, 2);

        return [
            'initiator' => $parts[0],
            'return' => $parts[1] ?? null,
        ];
    }

    public function getSignature(): string
    {
        $output = $this->initiator;

        if ($this->return !== null) {
            $output .= ':' . $this->return;
        }

        return $output;
    }

    public function setInitiator(
        string $initiator
    ): void {
        if (!preg_match('/^([A-Z][a-zA-Z0-9]+)\.([A-Z][a-zA-Z0-9]+)$/', $initiator)) {
            throw Exceptional::InvalidArgument(
                message: 'Invalid action initiator: ' . $initiator
            );
        }

        $this->initiator = $initiator;
    }

    public function getInitiator(): string
    {
        return $this->initiator;
    }

    public function setReturn(
        ?string $return
    ): void {
        if (
            $return !== null &&
            !preg_match('/^\$?[a-zA-Z0-9]+$/', $return)
        ) {
            throw Exceptional::InvalidArgument(
                message: 'Invalid action return: ' . $return
            );
        }

        $this->return = $return;
    }

    public function getReturn(): ?string
    {
        return $this->return;
    }


    /**
     * @param array<string,ParameterValue|Parameter<ParameterValue>> $parameters
     */
    public function setParameters(
        array $parameters
    ): void {
        foreach ($parameters as $name => $parameter) {
            $this->addParameter($name, $parameter);
        }
    }

    /**
     * @return array<string,Parameter<ParameterValue>>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param ParameterValue|Parameter<ParameterValue> $parameter
     */
    public function addParameter(
        string $name,
        string|int|float|bool|DateTimeInterface|array|stdClass|ActionSet|Parameter $parameter
    ): void {
        if (!$parameter instanceof Parameter) {
            $parameter = new Parameter($parameter);
        }

        $this->parameters[$name] = $parameter;
    }

    /**
     * @return Parameter<ParameterValue>|null
     */
    public function getParameter(
        string $id
    ): ?Parameter {
        return $this->parameters[$id] ?? null;
    }

    /**
     * @return array<string,mixed>|object
     */
    public function jsonSerialize(): array|object
    {
        if (empty($this->parameters)) {
            return (object)[];
        }

        return $this->parameters;
    }
}
