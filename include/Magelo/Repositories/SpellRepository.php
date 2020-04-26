<?php


namespace Magelo\Repositories;

use Magelo\Stores\SpellStore;

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

        $effect_fields = [
            "proceffect",
            "worneffect",
            "focuseffect",
            "clickeffect",
            "scrolleffect",
        ];

        $spell_ids = [];
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

        return [];
    }


    /**
     * @param $character_id
     */
    public static function preloadSpellsUsedByCharacterId($character_id)
    {
        $items      = self::getSpellsUsedByCharacterId($character_id);
        $item_store = [];

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
