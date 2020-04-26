<?php

namespace Magelo\Repositories;

use Magelo\Stores\ItemStore;

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

        return $cbsql_content->fetch_all(
            $cbsql_content->query(
                sprintf(
                    "SELECT * FROM %s WHERE id IN (%s)",
                    self::$table,
                    implode(", ", InventoryRepository::getItemIdsByCharacterId($character_id))
                )
            )
        );
    }

    /**
     * @param $account_id
     *
     * @return array
     */
    public static function getItemsByAccountId($account_id)
    {
        global $cbsql_content;

        return $cbsql_content->fetch_all(
            $cbsql_content->query(
                sprintf(
                    "SELECT * FROM %s WHERE id IN (%s)",
                    self::$table,
                    implode(", ", SharedBankRepository::getItemIdsByAccountId($account_id))
                )
            )
        );
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

        $item_store = [];
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

}
