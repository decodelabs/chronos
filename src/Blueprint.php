<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos;

use DecodeLabs\Chronos\Blueprint\ActionSet;
use DecodeLabs\Chronos\Blueprint\Program;
use DecodeLabs\Chronos\Blueprint\Step;
use JsonSerializable;

interface Blueprint extends JsonSerializable
{
    public const BaseUrl = 'https://schema.decodelabs.com/chronos/';

    public const Versions = [
        '0.1',
    ];

    public const Schemas = [
        //'campaign' => Campaign::class,
        'program' => Program::class,
        'step' => Step::class,
        'actions' => ActionSet::class,
        'shared' => null
    ];
}
