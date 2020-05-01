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

use CharBrowser\Stores\SpellStore;

class SpellRepository
{
    /**
     * @var string
     */
    private static $table = 'spells_new';

    /**
     * @param $character_id
     *
     * @return array
     */
    public static function getSpellsUsedByCharacterId($character_id)
    {
        /**
         * @var \mysqli $cbsql_content
         */
        global $cbsql_content;

        $items = ItemRepository::getItemsByCharacterId($character_id);

        $effect_fields = array(
            "proceffect",
            "worneffect",
            "focuseffect",
            "clickeffect",
            "scrolleffect",
        );

        $spell_ids = array();
        foreach ($items as $item) {
            foreach ($effect_fields as $effect_field) {
                if ($item[$effect_field] && $item[$effect_field] > 0) {
                    $spell_ids[] = $item[$effect_field];
                }
            }
        }

        if (count($spell_ids) > 0) {
            return $cbsql_content->fetch_all(
                $cbsql_content->query(
                    sprintf(
                        "SELECT * FROM %s WHERE id IN (%s)",
                        self::$table,
                        implode(", ", $spell_ids)
                    )
                )
            );
        }

        return array();
    }


    /**
     * @param $character_id
     */
    public static function preloadSpellsUsedByCharacterId($character_id)
    {
        $items      = self::getSpellsUsedByCharacterId($character_id);
        $item_store = array();

        foreach ($items as $item) {
            $item_id = $item['id'];

            if (!$item_store[$item_id]) {
                $item_store[$item_id] = $item;
            }
        }

        SpellStore::setStore($item_store);
    }

    /**
     * @param $item_id
     *
     * @return |null
     */
    public static function findOne($item_id)
    {
        /**
         * @var \mysqli $cbsql_content
         */
        global $cbsql_content;

        /**
         * Use cache if available
         */
        if (SpellStore::get($item_id)) {
            return SpellStore::get($item_id);
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

        SpellStore::set($item_id, $item);

        return $item;
    }
}
