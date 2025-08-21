<?php

/**
 * @package Destiny
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Destiny\Blueprint;

/**
 * @phpstan-require-implements WithActions
 */
trait WithActionsTrait
{
    /**
     * @var array<Action>
     */
    protected array $actions = [];

    /**
     * @param array<Action> $actions
     */
    public function __construct(
        array $actions = []
    ) {
        $this->setActions(...$actions);
    }

    public function setActions(
        Action ...$actions
    ): void {
        $this->actions = [];

        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function addAction(
        Action $action
    ): void {
        $this->actions[] = $action;
    }


    /**
     * @return array<string,Action>
     */
    public function jsonSerialize(): array
    {
        if (empty($this->actions)) {
            return [];
        }

        $output = [];

        foreach ($this->actions as $action) {
            $output[$action->getSignature()] = $action;
        }

        return $output;
    }
}
