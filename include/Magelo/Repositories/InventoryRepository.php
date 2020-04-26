<?php


namespace Magelo\Repositories;

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

        $item_ids      = [];
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

        $effect_fields = [
            "proceffect",
            "worneffect",
            "focuseffect",
            "clickeffect",
            "scrolleffect",
        ];

        $spell_ids = [];
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
