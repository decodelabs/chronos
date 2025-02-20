<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Destiny\Blueprint;
use DecodeLabs\Destiny\Blueprint\Validation\Error as ValidationError;
use DecodeLabs\Destiny\Blueprint\Validation\Result as ValidationResult;
use DecodeLabs\Exceptional;
use Opis\JsonSchema\Errors\ErrorFormatter as JsonErrorFormatter;
use Opis\JsonSchema\Validator as JsonValidator;
use stdClass;

/**
 * @phpstan-import-type ParameterValue from Parameter
 */
class Factory
{
    /**
     * Load from any JSON
     */
    public function load(
        File $file
    ): Blueprint {
        $data = $this->loadJsonFromFile($file);

        $schema = Coercion::tryString($data->{'$schema'} ?? null);
        $class = $this->getSchemaClass($schema);

        return match ($class) {
            //Campaign::class => $this->createCampaign($data),
            Program::class => $this->createProgram($data),
            Step::class => $this->createStep($data),
            default => throw Exceptional::UnexpectedValue(
                message: 'Unknown blueprint schema: ' . $schema
            )
        };
    }

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
     * Validate JSON file
     */
    public function validate(
        File $file
    ): ValidationResult {
        $data = $this->loadJsonFromFile($file);

        // Validate JSON
        $validator = new JsonValidator();
        $schemaPath = dirname(__DIR__, 2) . '/schema';

        foreach (Blueprint::Schemas as $name => $class) {
            foreach (Blueprint::Versions as $version) {
                $validator->resolver()?->registerFile(
                    Blueprint::BaseUrl . $version . '/' . $name . '.json',
                    $schemaPath . '/' . $version . '/' . $name . '.json'
                );
            }
        }

        $errors = [];
        /** @var bool|object|string $value */
        $value = $data->{'$schema'};
        $jsonResult = $validator->validate($data, $value);
        $jsonError = $jsonResult->error();

        if (
            !$jsonResult->isValid() &&
            $jsonError !== null
        ) {
            $jsonErrors = new JsonErrorFormatter()->format($jsonError, true);

            foreach ($jsonErrors as $location => $set) {
                /** @var list<string> $set */
                foreach ($set as $message) {
                    $errors[] = new ValidationError($location, $message);
                }
            }
        }

        return new ValidationResult(...$errors);
    }

    /**
     * Validate JSON string
     */
    public function validateString(
        string $json
    ): ValidationResult {
        $file = Atlas::createMemoryFile($json);
        return $this->validate($file);
    }

    /**
     * Load JSON from file
     */
    protected function loadJsonFromFile(
        File $file
    ): stdClass {
        if (!$file->exists()) {
            throw Exceptional::NotFound(
                message: 'Blueprint file not found'
            );
        }

        $data = json_decode($file->getContents());

        if (!$data instanceof stdClass) {
            throw Exceptional::UnexpectedValue(
                message: 'Invalid blueprint data'
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
                message: 'Blueprint schema is missing'
            );
        }

        if (
            (
                !str_starts_with($schema, Blueprint::BaseUrl) &&
                !preg_match('/^(\.\.)?\//', $schema)
            ) ||
            !preg_match('/\/[0-9\.]{1,4}\/([a-z0-9-]+)+\.json$/', $schema, $matches)
        ) {
            throw Exceptional::UnexpectedValue(
                message: 'Unknown blueprint schema: ' . $schema
            );
        }

        $name = $matches[1];

        if (!isset(Blueprint::Schemas[$name])) {
            throw Exceptional::UnexpectedValue(
                message: 'Unknown blueprint schema: ' . $schema
            );
        }

        return Blueprint::Schemas[$name];
    }


    /**
     * Create program
     */
    public function createProgram(
        stdClass $data,
        string $location = '/'
    ): Program {
        /** @var array<string> */
        $categories = Coercion::asArray($data->categories ?? []);
        $steps = [];
        $i = 0;

        foreach (Coercion::asArray($data->steps ?? []) as $stepData) {
            $steps[] = $this->createStep(
                data: Coercion::asStdClass($stepData),
                location: $location . 'steps/' . $i
            );

            $i++;
        }

        try {
            return new Program(
                id: Coercion::tryString($data->id ?? null),
                name: Coercion::tryString($data->name ?? null),
                description: Coercion::tryString($data->description ?? null),
                version: Coercion::tryString($data->version ?? null),
                authorName: Coercion::tryString($data->authorName ?? null),
                authorUrl: Coercion::tryString($data->authorUrl ?? null),
                categories: $categories,
                duration: Coercion::tryString($data->duration ?? null),
                priority: Coercion::tryString($data->priority ?? null) ?? 'medium',
                steps: $steps
            );
        } catch (Exceptional\Exception $e) {
            $e->data = ['location' => $location];
            throw $e;
        }
    }

    /**
     * Create step
     */
    public function createStep(
        stdClass $data,
        string $location = '/'
    ): Step {
        /** @var array<string,?string> $await */
        $await = Coercion::asArray($data->await ?? []);
        /** @var array<string,array<string,ParameterValue>> */
        $actions = Coercion::asArray($data->actions ?? []);

        try {
            return new Step(
                id: Coercion::tryString($data->id ?? null),
                name: Coercion::tryString($data->name ?? null),
                description: Coercion::tryString($data->description ?? null),
                priority: Coercion::tryString($data->priority ?? null) ?? 'medium',
                duration: Coercion::tryString($data->duration ?? null),
                await: $await,
                actions: $this->createActionList(
                    data: $actions,
                    location: $location . 'actions'
                ),
            );
        } catch (Exceptional\Exception $e) {
            $e->data = ['location' => $location];
            throw $e;
        }
    }

    /**
     * Create action set
     *
     * @param array<string,array<ParameterValue>>|stdClass $data
     */
    public function createActionSet(
        array|stdClass $data,
        string $location = '/'
    ): ActionSet {
        return new ActionSet(...$this->createActionList($data, $location));
    }

    /**
     * Create array of actions
     *
     * @phpstan-param array<string,array<ParameterValue>>|stdClass $data
     * @return array<Action>
     */
    protected function createActionList(
        array|stdClass $data,
        string $location = '/'
    ): array {
        $actions = [];

        foreach ((array)$data as $signature => $parameters) {
            if ($parameters === null) {
                $parameters = [];
            }

            /** @phpstan-var array<string,ParameterValue> $parameters */
            $parameters = Coercion::asArray($parameters);

            $actions[] = new Action(
                signature: $signature,
                parameters: $this->prepareParameters(
                    parameters: $parameters,
                    location: $location . $signature . '/'
                )
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
        array $parameters,
        string $location = '/'
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
                $actions = $this->createActionSet(
                    data: $value,
                    location: $location . $name . '/'
                );
                $value = $actions;
            }

            try {
                $output[$name] = new Parameter($value);
            } catch (Exceptional\Exception $e) {
                $e->data = ['location' => $location];
                throw $e;
            }
        }

        // @phpstan-ignore-next-line
        return $output;
    }
}
