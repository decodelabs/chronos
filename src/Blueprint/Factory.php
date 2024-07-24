<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Chronos\Blueprint;
//use DecodeLabs\Chronos\Blueprint\Campaign;
use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use stdClass;

/**
 * @phpstan-import-type ParameterValue from Parameter
 */
class Factory
{
    public const BASE_URL = 'https://schema.decodelabs.com/chronos/';

    public const SCHEMAS = [
        //'campaign' => Campaign::class,
        'program' => Program::class,
        'step' => Step::class,
    ];

    /**
     * Load from JSON string
     */
    public function loadString(
        string $json
    ): Blueprint {
        $file = Atlas::createMemoryFile($json);
        return $this->load($file);
    }

    /**
     * Load from any JSON
     */
    public function load(
        File $file
    ): Blueprint {
        $data = $this->loadJsonFromFile($file);

        $schema = Coercion::toStringOrNull($data['$schema'] ?? null);
        $class = $this->getSchemaClass($schema);

        return match ($class) {
            //Campaign::class => $this->createCampaign($data),
            Program::class => $this->createProgram($data),
            Step::class => $this->createStep($data),
            default => throw Exceptional::UnexpectedValue(
                'Unknown blueprint schema: ' . $schema
            )
        };
    }

    /**
     * Load JSON from file
     *
     * @return array<string,mixed>
     */
    protected function loadJsonFromFile(
        File $file
    ): array {
        if (!$file->exists()) {
            throw Exceptional::NotFound(
                'Blueprint file not found'
            );
        }

        $data = json_decode($file->getContents(), true);

        if (!is_array($data)) {
            throw Exceptional::UnexpectedValue(
                'Invalid blueprint data'
            );
        }

        return $data;
    }


    /**
     * Get blueprint class for schema
     *
     * @return class-string<Blueprint>
     */
    protected function getSchemaClass(
        ?string $schema
    ): string {
        if ($schema === null) {
            throw Exceptional::UnexpectedValue(
                'Blueprint schema is missing'
            );
        }

        if (!str_starts_with($schema, self::BASE_URL)) {
            throw Exceptional::UnexpectedValue(
                'Unknown blueprint schema: ' . $schema
            );
        }

        $name = substr($schema, strlen(self::BASE_URL));
        $parts = explode('/', $name, 2);
        $name = $parts[1] ?? '';

        if (!isset(self::SCHEMAS[$name])) {
            throw Exceptional::UnexpectedValue(
                'Unknown blueprint schema: ' . $schema
            );
        }

        return self::SCHEMAS[$name];
    }


    /**
     * Create program
     *
     * @param array<string,mixed> $data
     */
    public function createProgram(
        array $data
    ): Program {
        /** @var array<string> */
        $categories = Coercion::toArray($data['categories'] ?? []);
        $steps = [];

        foreach (Coercion::toArray($data['steps'] ?? []) as $stepData) {
            $steps[] = $this->createStep(Coercion::toArray($stepData));
        }

        return new Program(
            id: Coercion::toStringOrNull($data['id'] ?? null),
            name: Coercion::toStringOrNull($data['name'] ?? null),
            description: Coercion::toStringOrNull($data['description'] ?? null),
            version: Coercion::toStringOrNull($data['version'] ?? null),
            authorName: Coercion::toStringOrNull($data['authorName'] ?? null),
            authorUrl: Coercion::toStringOrNull($data['authorUrl'] ?? null),
            categories: $categories,
            duration: Coercion::toStringOrNull($data['duration'] ?? null),
            priority: Coercion::toStringOrNull($data['priority'] ?? null) ?? 'medium',
            steps: $steps
        );
    }

    /**
     * Create step
     *
     * @param array<string,mixed> $data
     */
    public function createStep(
        array $data
    ): Step {
        /** @var array<string,?string> $await */
        $await = Coercion::toArray($data['await'] ?? []);
        /** @var array<string,array<string,ParameterValue>> */
        $actions = Coercion::toArray($data['actions'] ?? []);

        return new Step(
            id: Coercion::toStringOrNull($data['id'] ?? null),
            name: Coercion::toStringOrNull($data['name'] ?? null),
            description: Coercion::toStringOrNull($data['description'] ?? null),
            priority: Coercion::toStringOrNull($data['priority'] ?? null) ?? 'medium',
            duration: Coercion::toStringOrNull($data['duration'] ?? null),
            await: $await,
            actions: $this->createActionList($actions),
        );
    }

    /**
     * Create action set
     *
     * @param array<string,array<ParameterValue>>|stdClass $data
     */
    public function createActionSet(
        array|stdClass $data
    ): ActionSet {
        return new ActionSet(...$this->createActionList($data));
    }

    /**
     * Create array of actions
     *
     * @param array<string,array<ParameterValue>>|stdClass $data
     * @return array<Action>
     */
    protected function createActionList(
        array|stdClass $data
    ): array {
        $actions = [];

        foreach ((array)$data as $signature => $parameters) {
            if (is_null($parameters)) {
                $parameters = [];
            }

            if (!is_array($parameters)) {
                throw Exceptional::UnexpectedValue(
                    'Invalid action parameters'
                );
            }

            $actions[] = new Action(
                signature: $signature,
                parameters: $this->prepareParameters($parameters)
            );
        }

        return $actions;
    }

    /**
     * Prepare parameters
     *
     * @phpstan-param array<string,ParameterValue> $parameters
     * @phpstan-return array<string,Parameter<ParameterValue>>
     */
    protected function prepareParameters(
        array $parameters
    ): array {
        $output = [];

        foreach ($parameters as $name => $value) {
            if (
                $value instanceof stdClass ||
                (
                    is_array($value) &&
                    !array_is_list($value)
                )
            ) {
                /** @var array<string,array<ParameterValue>>|stdClass $value */
                $actions = $this->createActionSet($value);
                $value = $actions;
            }

            /** @phpstan-var ParameterValue $value */
            $output[$name] = new Parameter($value);
        }

        return $output;
    }
}
