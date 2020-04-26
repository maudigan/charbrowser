<?php


namespace Magelo\Repositories;


use Magelo\Stores\CharacterFactionValueStore;

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
