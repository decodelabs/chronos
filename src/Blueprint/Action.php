<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use Carbon\CarbonInterval;
use DateTimeInterface;
use DecodeLabs\Chronos\Blueprint;
use DecodeLabs\Chronos\BlueprintTrait;
use DecodeLabs\Chronos\Priority;
use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;

class Action implements Blueprint
{
    use BlueprintTrait;

    protected string $initiator;
    protected ?string $return = null;

    /**
     * @var array<string,Parameter>
     */
    protected array $parameters = [];

    /**
     * @param array<string|int|float|bool|array<string>|DateTimeInterface|Parameter> $parameters
     */
    public function __construct(
        ?string $signature = null,
        ?string $initiator = null,
        ?string $return = null,
        array $parameters = [],
    ) {
        if($signature !== null) {
            [
                'initiator' => $initiator,
                'return' => $return,
            ] = $this->parseSignature($signature);
        }

        if($initiator === null) {
            throw Exceptional::InvalidArgument(
                'Action signature is missing initiator'
            );
        }

        $this->setInitiator($initiator);
        $this->setReturn($return);
        $this->setParameters($parameters);
    }

    /**
     * Parse signature
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

    /**
     * Get signature
     */
    public function getSignature(): string
    {
        $output = $this->initiator;

        if($this->return !== null) {
            $output .= ':'.$this->return;
        }

        return $output;
    }

    /**
     * Set initiator
     */
    public function setInitiator(
        string $initiator
    ): void {
        if(!preg_match('/^([A-Z][a-zA-Z0-9]+)\.([A-Z][a-zA-Z0-9]+)$/', $initiator)) {
            throw Exceptional::InvalidArgument(
                'Invalid action initiator: '.$initiator
            );
        }

        $this->initiator = $initiator;
    }

    /**
     * Get initiator
     */
    public function getInitiator(): string
    {
        return $this->initiator;
    }

    /**
     * Set return
     */
    public function setReturn(
        ?string $return
    ): void {
        if(
            $return !== null &&
            !preg_match('/^\$?[a-zA-Z0-9]+$/', $return)
        ) {
            throw Exceptional::InvalidArgument(
                'Invalid action return: '.$return
            );
        }

        $this->return = $return;
    }

    /**
     * Get return
     */
    public function getReturn(): ?string
    {
        return $this->return;
    }


    /**
     * Set parameters
     *
     * @param array<string,string|int|float|bool|array<string>|DateTimeInterface|Parameter> $parameters
     */
    public function setParameters(
        array $parameters
    ): void {
        foreach($parameters as $name => $parameter) {
            $this->addParameter($name, $parameter);
        }
    }

    /**
     * Get parameters
     *
     * @return array<string,Parameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Add parameter
     */
    public function addParameter(
        string $name,
        string|int|float|bool|array|DateTimeInterface|Parameter $parameter
    ): void {
        if(!$parameter instanceof Parameter) {
            $parameter = new Parameter($parameter);
        }

        $this->parameters[$name] = $parameter;
    }

    /**
     * Get parameter
     */
    public function getParameter(
        string $id
    ): ?Parameter {
        return $this->parameters[$id] ?? null;
    }

    /**
     * Export for serialization
     */
    public function jsonSerialize(): array
    {
        return $this->parameters;
    }
}
