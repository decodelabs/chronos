# Destiny — Package Specification

> **Cluster:** `logic`
> **Language:** `php`
> **Milestone:** `m6`
> **Repo:** `https://github.com/decodelabs/destiny`
> **Role:** Scheduled interactions

This document describes the purpose, contracts, and design of **Destiny** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** Destiny in their own applications or libraries.
- Contributors **maintaining or extending** Destiny.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

Destiny provides an extensible format and operating structures for defining complex event programs. It offers a blueprint system for creating structured schedules with steps, actions, priorities, durations, and dependencies. The package provides JSON schema validation, program/step/action definitions, parameter handling, and runtime execution support. It's designed to manage scheduled interactions and complex workflows.

### 1.2 Non-Goals

Destiny does **not**:

- Execute actions or run programs — it only defines and validates blueprints
- Provide scheduling or cron functionality — it defines schedules, not execution
- Handle event loops or async execution — it's a definition format
- Provide action implementations — it only defines action signatures
- Handle state management or persistence — it's a blueprint format
- Provide UI or visualization tools — it's a data format
- Handle authentication or authorization — it's a pure definition system

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `logic` (see Chorus taxonomy)
- Destiny is a logic package that provides schedule management capabilities for the Decode Labs ecosystem. It sits in the logic cluster alongside other workflow and scheduling utilities. It depends on Atlas, Coercion, Dictum, Enumerable, Exceptional, Kingdom, Nuance, Slingshot, Carbon, and Opis JSON Schema. It's used for defining complex event programs and scheduled interactions.

### 2.2 Typical Usage Contexts

Typical places Destiny appears:

- Event program definitions
- Scheduled interaction workflows
- Multi-step process definitions
- Action sequence blueprints
- Workflow orchestration
- Campaign or automation definitions
- Complex scheduled operations

Destiny is intended to be used whenever code needs to define structured programs with steps, actions, priorities, durations, and dependencies in a validated, extensible format.

---

## 3. Public Surface

> This section focuses on the conceptual API, not every symbol.

### 3.1 Key Types

The primary public types are:

- `DecodeLabs\Destiny`
  Main service class implementing `Kingdom\Service`. Provides methods for loading and validating blueprints.

- `DecodeLabs\Destiny\Blueprint`
  Interface for blueprint definitions. Defines schema constants and supported blueprint types (Program, Step, ActionSet).

- `DecodeLabs\Destiny\Blueprint\Program`
  Program blueprint implementation. Represents a complete program with steps, categories, priority, duration, and publishing metadata.

- `DecodeLabs\Destiny\Blueprint\Step`
  Step blueprint implementation. Represents a program step with actions, await dependencies, priority, and duration.

- `DecodeLabs\Destiny\Blueprint\Action`
  Action blueprint implementation. Represents an action with initiator, return value, and parameters.

- `DecodeLabs\Destiny\Blueprint\ActionSet`
  Collection of actions. Implements `Blueprint` and `WithActions`.

- `DecodeLabs\Destiny\Blueprint\Parameter`
  Parameter value wrapper. Handles various parameter types (String, Number, Boolean, Date, Reference, List, Action).

- `DecodeLabs\Destiny\Blueprint\ParameterType`
  Enum for parameter types (String, Number, Boolean, Date, Reference, List, Action).

- `DecodeLabs\Destiny\Blueprint\Factory`
  Factory for creating blueprint instances from JSON data. Handles loading, validation, and creation of programs, steps, and actions.

- `DecodeLabs\Destiny\Blueprint\Validation\Result`
  Validation result containing errors. Implements `Dumpable` for debugging.

- `DecodeLabs\Destiny\Blueprint\Validation\Error`
  Validation error with location and message.

- `DecodeLabs\Destiny\Priority`
  Enum for priority levels (Low, Medium, High, Critical). Implements `Enumerable\Unit\Named`.

- `DecodeLabs\Destiny\Runtime\Stack`
  Runtime parameter stack for execution. Supports parent stacks and variable references.

- `DecodeLabs\Destiny\Repository`
  Interface for blueprint repositories (currently empty, for future use).

### 3.2 Main Entry Points

The main usage pattern is through the `Destiny` service:

```php
use DecodeLabs\Destiny;

$destiny = new Destiny();
$blueprint = $destiny->loadBlueprint('path/to/program.json');
$result = $destiny->validateBlueprint('path/to/program.json');
```

---

## 4. Dependencies

### 4.1 Decode Labs

- `decodelabs/atlas` (required)
  Used for file operations when loading blueprint files.

- `decodelabs/coercion` (required)
  Used for type coercion when parsing blueprint data.

- `decodelabs/dictum` (required)
  Used for text formatting (slug generation, name formatting) in identity traits.

- `decodelabs/enumerable` (required)
  Used for `Priority` enum implementation via `Named` interface.

- `decodelabs/exceptional` (required)
  Used for exception handling when blueprint operations fail.

- `decodelabs/kingdom` (required)
  Used for service interface (`Service`, `ServiceTrait`).

- `decodelabs/nuance` (required)
  Used for debugging and inspection via `Dumpable` interface on `Parameter` and `Validation\Result`.

- `decodelabs/slingshot` (required)
  Used for dependency injection (if needed for future extensions).

### 4.2 External

- `nesbot/carbon` (required)
  Used for duration handling (`CarbonInterval`) in steps and programs.

- `opis/json-schema` (required)
  Used for JSON schema validation of blueprint files.

### 4.3 Optional Integrations

- None

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- Blueprints are defined in JSON format with `$schema` property
- Blueprint IDs must be lowercase alphanumeric with hyphens/underscores, 5-64 characters
- Action initiators must match pattern `[A-Z][a-zA-Z0-9]+\.[A-Z][a-zA-Z0-9]+`
- Action return values must match pattern `\$?[a-zA-Z0-9]+`
- Steps can await other steps with optional durations
- Parameters support references via `{{variable}}` syntax
- Programs contain steps, steps contain actions
- Validation uses JSON Schema with versioned schemas
- All blueprints implement `JsonSerializable`

### 5.2 Input & Output Contracts

**Destiny Service Operations:**
- `loadBlueprint(string|File $file): Blueprint` — Loads blueprint from file
- `loadBlueprintString(string $json): Blueprint` — Loads blueprint from JSON string
- `validateBlueprint(string|File $file): ValidationResult` — Validates blueprint file
- `validateBlueprintString(string $json): ValidationResult` — Validates blueprint JSON string

**Program Operations:**
- `getId(): string` — Gets program ID
- `getName(): string` — Gets program name
- `getDescription(): ?string` — Gets program description
- `getCategories(): array` — Gets category list
- `setCategories(string ...$categories): void` — Sets categories
- `getSteps(): array` — Gets step map (id => Step)
- `setSteps(Step ...$steps): void` — Sets steps
- `addStep(Step $step): void` — Adds step
- `getStep(string $id): ?Step` — Gets step by ID
- `getDuration(): ?CarbonInterval` — Gets program duration
- `getPriority(): Priority` — Gets program priority
- `getVersion(): ?string` — Gets program version
- `getAuthorName(): ?string` — Gets author name
- `getAuthorUrl(): ?string` — Gets author URL

**Step Operations:**
- `getId(): string` — Gets step ID
- `getName(): string` — Gets step name
- `getDescription(): ?string` — Gets step description
- `getActions(): array` — Gets action list
- `setActions(Action ...$actions): void` — Sets actions
- `addAction(Action $action): void` — Adds action
- `getAwaits(): array` — Gets await map (step-id => duration)
- `setAwaits(array $await): void` — Sets await dependencies
- `addAwait(string $id, string|CarbonInterval|null $duration): void` — Adds await dependency
- `getAwaitDuration(string $id): ?CarbonInterval` — Gets await duration
- `willAwait(string $id): bool` — Checks if step awaits another
- `getDuration(): ?CarbonInterval` — Gets step duration
- `getPriority(): Priority` — Gets step priority

**Action Operations:**
- `getSignature(): string` — Gets action signature (initiator:return)
- `getInitiator(): string` — Gets action initiator
- `setInitiator(string $initiator): void` — Sets action initiator
- `getReturn(): ?string` — Gets return variable name
- `setReturn(?string $return): void` — Sets return variable name
- `getParameters(): array` — Gets parameter map (name => Parameter)
- `setParameters(array $parameters): void` — Sets parameters
- `addParameter(string $name, mixed $parameter): void` — Adds parameter
- `getParameter(string $id): ?Parameter` — Gets parameter by name
- `parseSignature(string $signature): array` — Parses signature into initiator and return

**Parameter Operations:**
- `getValue(): mixed` — Gets parameter value
- `getType(): ParameterType` — Gets parameter type

**Factory Operations:**
- `load(File $file): Blueprint` — Loads blueprint from file
- `loadString(string $json): Blueprint` — Loads blueprint from JSON string
- `validate(File $file): ValidationResult` — Validates blueprint file
- `validateString(string $json): ValidationResult` — Validates blueprint JSON string
- `createProgram(stdClass $data, string $location): Program` — Creates program from data
- `createStep(stdClass $data, string $location): Step` — Creates step from data
- `createActionSet(array|stdClass $data, string $location): ActionSet` — Creates action set from data

**Validation Operations:**
- `isValid(): bool` — Checks if validation passed
- `getErrors(): array` — Gets validation errors
- `scanErrors(): Generator` — Scans errors as generator (location => message)

**Runtime Stack Operations:**
- `set(string $key, mixed $value): void` — Sets parameter (supports `$key` for parent scope)
- `parentSet(string $key, mixed $value): void` — Sets parameter in parent scope
- `get(string $key): ?Parameter` — Gets parameter (checks parent if not found)

### 5.3 Blueprint Schema

Blueprints use JSON Schema validation:
- Base URL: `https://schema.decodelabs.com/destiny/`
- Supported versions: `0.1`
- Supported schemas: `program`, `step`, `actions`
- Schema files located in package `schema/` directory
- Validation uses Opis JSON Schema validator

### 5.4 Action Signatures

Action signatures use format: `Initiator:Return`
- Initiator: `Class.Method` (e.g., `Email.Send`)
- Return: Optional variable name (e.g., `result`, `$result`)
- Signature parsing splits on `:` character

### 5.5 Parameter Types

Parameters support various types:
- **String**: Plain string values
- **Number**: Integer or float values
- **Boolean**: Boolean values
- **Date**: DateTimeInterface values
- **Reference**: Variable references via `{{variable}}` syntax
- **List**: Array lists (indexed arrays)
- **Action**: Action sets (nested actions or ActionSet objects)

### 5.6 Step Dependencies

Steps can await other steps:
- `await` property maps step IDs to durations
- Duration can be `null` (no delay) or `CarbonInterval` string
- Used for sequencing and timing control

### 5.7 Identity Generation

Identity traits auto-generate IDs or names:
- If ID provided, name is generated from ID using `Dictum::name()`
- If name provided, ID is generated from name using `Dictum::slug()`
- At least one of ID or name must be provided

---

## 6. Error Handling

- Missing blueprint files throw `Exceptional::NotFound`
- Invalid JSON data throws `Exceptional::UnexpectedValue`
- Missing schema property throws `Exceptional::UnexpectedValue`
- Unknown schema types throw `Exceptional::UnexpectedValue`
- Invalid blueprint IDs throw `Exceptional::InvalidArgument`
- Invalid action initiators throw `Exceptional::InvalidArgument`
- Invalid action return values throw `Exceptional::InvalidArgument`
- Duplicate step IDs throw `Exceptional::InvalidArgument`
- Invalid parameter types throw `Exceptional::InvalidArgument`
- Validation errors are collected in `ValidationResult`
- Factory methods include location context in exceptions

---

## 7. Configuration & Extensibility

- Blueprint schemas are versioned and extensible
- New schema types can be added to `Blueprint::Schemas`
- Custom blueprint types can be created by implementing `Blueprint` interface
- Traits provide reusable functionality (`WithIdentity`, `WithPriority`, `WithDuration`, `WithActions`, `WithPublishing`)
- Repository interface exists for future blueprint storage
- Runtime stack supports hierarchical parameter scoping

---

## 8. Interactions with Other Packages

### 8.1 Atlas

Destiny uses Atlas for file operations when loading blueprint files from the filesystem.

### 8.2 Coercion

Destiny uses Coercion for type conversion when parsing blueprint JSON data into PHP types.

### 8.3 Dictum

Destiny uses Dictum for text formatting in identity traits:
- `Dictum::slug()` for generating IDs from names
- `Dictum::name()` for generating names from IDs

### 8.4 Enumerable

Destiny uses Enumerable's `Named` interface for the `Priority` enum, providing named enum functionality.

### 8.5 Exceptional

Destiny uses Exceptional for all exception handling, providing consistent error reporting across the ecosystem.

### 8.6 Kingdom

Destiny implements Kingdom's `Service` interface, allowing it to be registered as a service in the Kingdom service container.

### 8.7 Nuance

Destiny implements Nuance's `Dumpable` interface on `Parameter` and `Validation\Result`, allowing these objects to be inspected and debugged using Nuance's debugging tools.

### 8.8 Carbon

Destiny uses Carbon's `CarbonInterval` for duration handling in programs and steps. Durations can be specified as strings and are converted to `CarbonInterval` instances.

### 8.9 Opis JSON Schema

Destiny uses Opis JSON Schema for validating blueprint files against JSON schemas. Schemas are versioned and located in the package's `schema/` directory.

---

## 9. Usage Examples

### 9.1 Loading a Blueprint

```php
use DecodeLabs\Destiny;

$destiny = new Destiny();
$blueprint = $destiny->loadBlueprint('path/to/program.json');

// Or from string
$json = '{"$schema": "https://schema.decodelabs.com/destiny/0.1/program.json", ...}';
$blueprint = $destiny->loadBlueprintString($json);
```

### 9.2 Validating a Blueprint

```php
use DecodeLabs\Destiny;

$destiny = new Destiny();
$result = $destiny->validateBlueprint('path/to/program.json');

if (!$result->isValid()) {
    foreach ($result->getErrors() as $error) {
        echo $error->getLocation() . ': ' . $error->getMessage() . "\n";
    }
}
```

### 9.3 Creating a Program

```php
use DecodeLabs\Destiny\Blueprint\Program;
use DecodeLabs\Destiny\Blueprint\Step;
use DecodeLabs\Destiny\Blueprint\Action;
use DecodeLabs\Destiny\Priority;

$step1 = new Step(
    id: 'step-1',
    name: 'First Step',
    priority: Priority::High,
    actions: [
        new Action(
            signature: 'Email.Send:result',
            parameters: [
                'to' => 'user@example.com',
                'subject' => 'Hello',
                'body' => 'World'
            ]
        )
    ]
);

$step2 = new Step(
    id: 'step-2',
    name: 'Second Step',
    await: ['step-1' => '1 hour'],
    actions: [
        new Action(
            signature: 'Sms.Send',
            parameters: [
                'to' => '{{result}}',
                'message' => 'Follow up'
            ]
        )
    ]
);

$program = new Program(
    id: 'my-program',
    name: 'My Program',
    description: 'A sample program',
    priority: Priority::Medium,
    duration: '2 days',
    steps: [$step1, $step2]
);
```

### 9.4 Action Signatures

```php
use DecodeLabs\Destiny\Blueprint\Action;

// With return value
$action = new Action(
    signature: 'Email.Send:result',
    parameters: ['to' => 'user@example.com']
);

// Without return value
$action = new Action(
    signature: 'Sms.Send',
    parameters: ['to' => '1234567890']
);

// Or construct directly
$action = new Action(
    initiator: 'Email.Send',
    return: 'result',
    parameters: ['to' => 'user@example.com']
);
```

### 9.5 Parameter References

```php
use DecodeLabs\Destiny\Blueprint\Action;

// Reference to variable
$action = new Action(
    signature: 'Email.Send',
    parameters: [
        'to' => '{{userId}}',
        'template' => 'welcome'
    ]
);
```

### 9.6 Nested Actions

```php
use DecodeLabs\Destiny\Blueprint\Action;

// Nested action set as parameter
$action = new Action(
    signature: 'Workflow.Execute',
    parameters: [
        'steps' => [
            'Email.Send' => ['to' => 'user@example.com'],
            'Sms.Send' => ['to' => '1234567890']
        ]
    ]
);
```

### 9.7 Runtime Stack

```php
use DecodeLabs\Destiny\Runtime\Stack;

$stack = new Stack(['userId' => '123']);
$childStack = new Stack([], $stack);

// Set in child
$childStack->set('local', 'value');

// Set in parent (using $ prefix)
$childStack->set('$parent', 'value');

// Get from child or parent
$value = $childStack->get('userId'); // From parent
```

---

## 10. Implementation Notes (for Contributors)

### 10.1 Blueprint Schema System

Blueprints use a schema-based system:
- Schema URL format: `https://schema.decodelabs.com/destiny/{version}/{type}.json`
- Schemas are registered with Opis JSON Schema validator
- Schema files located in `schema/{version}/{type}.json`
- Factory resolves schema type from `$schema` property

### 10.2 Trait-Based Composition

Blueprint types use traits for composition:
- `WithIdentityTrait` — ID, name, description
- `WithPriorityTrait` — Priority level
- `WithDurationTrait` — Duration (CarbonInterval)
- `WithActionsTrait` — Action collection
- `WithPublishingTrait` — Version, author metadata
- Traits use constructor aliasing to avoid conflicts

### 10.3 Identity Generation

Identity traits auto-generate missing values:
- ID from name: `Dictum::slug($name)`
- Name from ID: `Dictum::name($id)`
- At least one must be provided
- ID validation: lowercase alphanumeric with hyphens/underscores, 5-64 chars

### 10.4 Action Signature Parsing

Action signatures are parsed by splitting on `:`:
- Format: `Initiator:Return`
- Initiator validation: `[A-Z][a-zA-Z0-9]+\.[A-Z][a-zA-Z0-9]+`
- Return validation: `\$?[a-zA-Z0-9]+`
- Return is optional

### 10.5 Parameter Type Detection

Parameter types are detected from value:
- String: `is_string()` (checks for `{{ref}}` pattern for Reference)
- Number: `is_int()` or `is_float()`
- Boolean: `is_bool()`
- Date: `instanceof DateTimeInterface`
- List: `is_array()` and `array_is_list()`
- Action: `is_array()` and not list, or `stdClass`, or `ActionSet`

### 10.6 JSON Serialization

All blueprints implement `JsonSerializable`:
- Programs serialize with identity, publishing, priority, duration, categories, steps
- Steps serialize with identity, priority, duration, await, actions
- Actions serialize as parameter map (empty object if no parameters)
- ActionSets serialize as signature => action map
- Parameters serialize as their value (type-specific)

### 10.7 Validation System

Validation uses Opis JSON Schema:
- Schemas registered from package `schema/` directory
- Validation errors converted to `ValidationError` objects
- Error locations and messages extracted from JSON Schema errors
- Validation result provides `isValid()` and error iteration

### 10.8 Runtime Stack

Runtime stack provides parameter scoping:
- Parameters stored as `Parameter` objects
- `$key` prefix sets parameter in parent scope
- `get()` checks current stack, then parent
- Supports hierarchical parameter access

---

## 11. Testing & Quality

- **Code Quality Score:** 2.5/5
- **README Quality Score:** 3/5
- **Documentation Score:** 0/5 (this spec)
- **Test Coverage Score:** 0/5

See `composer.json` for supported PHP versions.

---

## 12. Roadmap & Future Ideas

- Add Campaign blueprint type
- Add blueprint execution engine
- Add action registry system
- Improve documentation and usage examples
- Add test coverage
- Consider adding blueprint versioning
- Consider adding blueprint sharing/export
- Consider adding visual blueprint editor support
- Complete Repository interface implementation

---

## 13. References

- [Atlas Package](https://github.com/decodelabs/atlas) — File operations
- [Coercion Package](https://github.com/decodelabs/coercion) — Type conversion
- [Dictum Package](https://github.com/decodelabs/dictum) — Text formatting
- [Enumerable Package](https://github.com/decodelabs/enumerable) — Enum support
- [Exceptional Package](https://github.com/decodelabs/exceptional) — Exception handling
- [Kingdom Package](https://github.com/decodelabs/kingdom) — Service container
- [Nuance Package](https://github.com/decodelabs/nuance) — Debugging tools
- [Slingshot Package](https://github.com/decodelabs/slingshot) — Dependency injection
- [Carbon Package](https://github.com/briannesbitt/Carbon) — Date intervals
- [Opis JSON Schema Package](https://github.com/opis/json-schema) — JSON Schema validation
- [JSON Schema Specification](https://json-schema.org/) — JSON Schema standard
- [Chorus Package Index](../../../chorus/config/packages.json) — Ecosystem metadata

