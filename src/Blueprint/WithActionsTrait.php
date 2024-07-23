<?php

/**
 * @package Chronos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Chronos\Blueprint;

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

    /**
     * Set actions
     */
    public function setActions(
        Action ...$actions
    ): void {
        $this->actions = [];

        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    /**
     * @return array<string,Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Add action
     */
    public function addAction(
        Action $action
    ): void {
        $this->actions[] = $action;
    }


    /**
     * Export for serialization
     *
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
