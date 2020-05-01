<?php

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   Portions of this program are derived from publicly licensed software
 *   projects including, but not limited to phpBB, Magelo Clone,
 *   EQEmulator, EQEditor, and Allakhazam Clone.
 *
 *                                  Author:
 *                           Maudigan(Airwalking)
 *
 ***************************************************************************/

namespace CharBrowser\Stores;

class ItemStore
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

    /**
     * @var string
     */
    private static $namespace = 'items';
}
