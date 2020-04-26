<?php


namespace Magelo\Repositories;


class SharedBankRepository
{
    /**
     * @var string
     */
    private static $table = 'sharedbank';

    /**
     * @param int $account_id
     *
     * @return array
     */
    public static function getItemIdsByAccountId($account_id)
    {
        /**
         * @var \mysqli $cbsql
         */
        global $cbsql;

        $result = $cbsql->query(
            sprintf(
                "SELECT * FROM %s WHERE acctid = %s",
                self::$table,
                $account_id
            )
        );

        $item_ids = [];
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

        return $item_ids;
    }

}
