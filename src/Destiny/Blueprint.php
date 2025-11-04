<?php

/**
 * Destiny
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny;

use DecodeLabs\Destiny\Blueprint\ActionSet;
use DecodeLabs\Destiny\Blueprint\Program;
use DecodeLabs\Destiny\Blueprint\Step;
use JsonSerializable;

interface Blueprint extends JsonSerializable
{
    public const BaseUrl = 'https://schema.decodelabs.com/destiny/';

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
