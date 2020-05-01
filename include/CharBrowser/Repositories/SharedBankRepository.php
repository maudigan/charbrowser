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

        $item_ids = array();
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
