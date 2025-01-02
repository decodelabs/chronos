<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Destiny\Context as Inst;
use DecodeLabs\Atlas\File as Ref0;
use DecodeLabs\Destiny\Blueprint as Ref1;
use DecodeLabs\Destiny\Blueprint\Validation\Result as Ref2;

class Destiny implements Proxy
{
    use ProxyTrait;

    const Veneer = 'DecodeLabs\\Destiny';
    const VeneerTarget = Inst::class;

    public static Inst $instance;

    public static function loadBlueprint(Ref0|string $file): Ref1 {
        return static::$instance->loadBlueprint(...func_get_args());
    }
    public static function loadBlueprintString(string $json): Ref1 {
        return static::$instance->loadBlueprintString(...func_get_args());
    }
    public static function validateBlueprint(Ref0|string $file): Ref2 {
        return static::$instance->validateBlueprint(...func_get_args());
    }
    public static function validateBlueprintString(string $json): Ref2 {
        return static::$instance->validateBlueprintString(...func_get_args());
    }
};
