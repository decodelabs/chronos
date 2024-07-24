<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Chronos\Context as Inst;
use DecodeLabs\Atlas\File as Ref0;
use DecodeLabs\Chronos\Blueprint as Ref1;

class Chronos implements Proxy
{
    use ProxyTrait;

    const VENEER = 'DecodeLabs\\Chronos';
    const VENEER_TARGET = Inst::class;

    public static Inst $instance;

    public static function loadBlueprint(Ref0|string $file): Ref1 {
        return static::$instance->loadBlueprint(...func_get_args());
    }
};
