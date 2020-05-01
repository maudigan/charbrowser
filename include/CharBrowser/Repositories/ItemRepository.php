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

namespace CharBrowser\Repositories;

use CharBrowser\Stores\ItemStore;

class ItemRepository
{
    /**
     * @var string
     */
    private static $table = 'items';

    /**
     * @param $character_id
     *
     * @return array
     */
    public static function getItemsByCharacterId($character_id)
    {
        global $cbsql_content;

        $item_ids = InventoryRepository::getItemIdsByCharacterId($character_id);
        if (count($item_ids) > 0) {
            return $cbsql_content->fetch_all(
                $cbsql_content->query(
                    sprintf(
                        "SELECT * FROM %s WHERE id IN (%s)",
                        self::$table,
                        implode(", ", $item_ids)
                    )
                )
            );
        }

        return array();
    }

    /**
     * @param $account_id
     *
     * @return array
     */
    public static function getItemsByAccountId($account_id)
    {
        global $cbsql_content;

        $item_ids = SharedBankRepository::getItemIdsByAccountId($account_id);
        if (count($item_ids) > 0) {
            return $cbsql_content->fetch_all(
                $cbsql_content->query(
                    sprintf(
                        "SELECT * FROM %s WHERE id IN (%s)",
                        self::$table,
                        implode(", ", $item_ids)
                    )
                )
            );
        }

        return array();
    }

    /**
     * @param $character_id
     */
    public static function preloadItemsByAccountCharacter($account_id, $character_id)
    {
        $items = array_merge(
            self::getItemsByCharacterId($character_id),
            self::getItemsByAccountId($account_id)
        );

        $item_store = array();
        foreach ($items as $item) {
            $item_id = $item['id'];

            if (!$item_store[$item_id]) {
                $item_store[$item_id] = $item;
            }
        }

        ItemStore::setStore($item_store);
    }

    /**
     * @param $item_id
     *
     * @return |null
     */
    public static function findOne($item_id)
    {
        global $cbsql_content;

        /**
         * Use cache if available
         */
        if (ItemStore::get($item_id)) {
            return ItemStore::get($item_id);
        }

        $item = $cbsql_content->nextrow(
            $cbsql_content->query(
                sprintf(
                    "SELECT * FROM %s WHERE id = %s",
                    self::$table,
                    $item_id
                )
            )
        );

        ItemStore::set($item_id, $item);

        return $item;
    }

    /**
     * @param $item_ids
     *
     * @return array
     */
    public static function preloadByItemIds($item_ids)
    {
        global $cbsql_content;

        if (count($item_ids) > 0) {
            $items = $cbsql_content->fetch_all(
                $cbsql_content->query(
                    sprintf(
                        "SELECT * FROM %s WHERE id IN (%s)",
                        self::$table,
                        implode(", ", $item_ids)
                    )
                )
            );

            $item_store = array();
            foreach ($items as $item) {
                $item_id = $item['id'];

                if (!$item_store[$item_id]) {
                    $item_store[$item_id] = $item;
                }
            }

            ItemStore::setStore($item_store);
        }

        return array();
    }

}
