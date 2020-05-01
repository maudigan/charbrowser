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

use CharBrowser\Stores\CharacterFactionValueStore;

class CharacterFactionValuesRepository
{
    /**
     * @var string
     */
    private static $table = 'faction_values';

    /**
     * @param $character_id
     *
     * @return array
     */
    public static function getFactionValuesByCharacterId($character_id)
    {
        global $cbsql;

        return $cbsql->fetch_all(
            $cbsql->query(
                sprintf(
                    "SELECT * FROM %s WHERE char_id = %s",
                    self::$table,
                    $character_id
                )
            )
        );
    }

    /**
     * @param $character_id
     */
    public static function preloadFactionValues($character_id)
    {
        $factions = self::getFactionValuesByCharacterId($character_id);

        $character_faction_value_store = [];
        foreach ($factions as $faction) {
            $faction_value = $faction['faction_id'];

            if (!$character_faction_value_store[$faction_value]) {
                $character_faction_value_store[$faction_value] = $faction;
            }
        }

        CharacterFactionValueStore::setStore($character_faction_value_store);
    }

    /**
     * @param $faction_id
     *
     * @return array|mixed|null
     */
    public static function getFaction($faction_id)
    {
        if (CharacterFactionValueStore::get($faction_id)) {
            return CharacterFactionValueStore::get($faction_id);
        }

        return [];
    }

    /**
     * @param $character_id
     * @param $faction_id
     *
     * @return mixed|null
     */
    public static function findOne($character_id, $faction_id)
    {
        global $cbsql;

        /**
         * Use cache if available
         */
        if (CharacterFactionValueStore::get($faction_id)) {
            return CharacterFactionValueStore::get($faction_id);
        }

        $ability = $cbsql->nextrow(
            $cbsql->query(
                sprintf(
                    "SELECT * FROM %s WHERE char_id = %s and faction_id = %s",
                    self::$table,
                    $character_id,
                    $faction_id
                )
            )
        );

        CharacterFactionValueStore::set($faction_id, $ability);

        return $ability;
    }
}
