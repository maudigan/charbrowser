<?php


namespace Magelo\Stores;

/**
 * Class BaseStoreTrait
 *
 * @package Magelo\Stores
 */
trait BaseStoreTrait
{
    /**
     * @var array
     */
    private static $store;

    /**
     * @return string
     */
    private static function getNamespace()
    {
        return self::$namespace ? self::$namespace : 'default';
    }

    /**
     * @return mixed
     */
    public static function getStore()
    {
        return self::$store[self::getNamespace()];
    }

    /**
     * @param mixed $store
     */
    public static function setStore($store)
    {
        self::$store[self::getNamespace()] = $store;
    }

    /**
     * @param $id
     *
     * @return |null
     */
    public static function get($id)
    {
        return self::$store[self::getNamespace()][$id] ? self::$store[self::getNamespace()][$id] : null;
    }

    /**
     * @param $id
     * @param $value
     *
     * @return mixed
     */
    public static function set($id, $value)
    {
        return self::$store[self::getNamespace()][$id] = $value;
    }
}
