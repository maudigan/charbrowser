<?php


namespace Magelo\Repositories;


use Magelo\Stores\CharacterAlternateAbilityStore;

class CharacterAlternateAbilityRepository
{
    /**
     * @var string
     */
    private static $table = 'character_alternate_abilities';

    /**
     * @param $character_id
     *
     * @return array
     */
    public static function getAlternateAbilitiesByCharacterId($character_id)
    {
        global $cbsql;

        return $cbsql->fetch_all(
            $cbsql->query(
                sprintf(
                    "SELECT * FROM %s WHERE id = %s",
                    self::$table,
                    $character_id
                )
            )
        );
    }

    /**
     * @param $character_id
     */
    public static function preloadAlternateAbilities($character_id)
    {
        $abilities = self::getAlternateAbilitiesByCharacterId($character_id);

        $character_alternate_ability_store = [];
        foreach ($abilities as $ability) {
            $aa_id = $ability['aa_id'];

            if (!$character_alternate_ability_store[$aa_id]) {
                $character_alternate_ability_store[$aa_id] = $ability;
            }
        }

        CharacterAlternateAbilityStore::setStore($character_alternate_ability_store);
    }

    /**
     * @param $ability_id
     *
     * @return array|mixed|null
     */
    public static function getAbility($ability_id)
    {
        if (CharacterAlternateAbilityStore::get($ability_id)) {
            return CharacterAlternateAbilityStore::get($ability_id);
        }

        return [];
    }

    /**
     * @param $character_id
     * @param $ability_id
     *
     * @return mixed|null
     */
    public static function findOne($character_id, $ability_id)
    {
        global $cbsql;

        /**
         * Use cache if available
         */
        if (CharacterAlternateAbilityStore::get($ability_id)) {
            return CharacterAlternateAbilityStore::get($ability_id);
        }

        $ability = $cbsql->nextrow(
            $cbsql->query(
                sprintf(
                    "SELECT * FROM %s WHERE id = %s and aa_id = %s",
                    self::$table,
                    $character_id,
                    $ability_id
                )
            )
        );

        CharacterAlternateAbilityStore::set($ability_id, $ability);

        return $ability;
    }
}
