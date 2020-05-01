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

class InventoryRepository
{
    /**
     * @var string
     */
    private static $table = 'inventory';

    /**
     * @param int $character_id
     *
     * @return array
     */
    public static function getItemIdsByCharacterId($character_id)
    {
        /**
         * @var \mysqli $cbsql
         */
        global $cbsql;

        $result = $cbsql->query(
            sprintf(
                "SELECT * FROM %s WHERE charid = %s",
                self::$table,
                $character_id
            )
        );

        $item_ids      = array();
        while ($row = $cbsql->nextrow($result)) {
            if ($row['itemid']) {
                $item_ids[] = $row['itemid'];
            }

            for ($i = 1; $i <= 5; $i++) {
                if ($row["augslot" . $i]) {
                    $item_ids[] = $row["augslot" . $i];
                }
            }
        }

        return array_unique($item_ids);
    }

    /**
     * @param $character_id
     *
     * @return array
     */
    public static function getSpellEffectIdsWornByCharacter($character_id)
    {
        /**
         * @var \mysqli $cbsql
         */
        global $cbsql;

        $result = $cbsql->query(
            sprintf(
                "SELECT * FROM %s WHERE charid = %s",
                self::$table,
                $character_id
            )
        );

        $effect_fields = array(
            "proceffect",
            "worneffect",
            "focuseffect",
            "clickeffect",
            "scrolleffect",
        );

        $spell_ids = array();
        while ($row = $cbsql->nextrow($result)) {
            foreach ($effect_fields as $effect_field) {
                if ($row[$effect_field]) {
                    $spell_ids[] = $row[$effect_field];
                }
            }
        }

        return array_unique($spell_ids);
    }
}
