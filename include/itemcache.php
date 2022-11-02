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
 *
 *   October 24, 2022 - Maudigan
 *      Initial revision: Store queried items
 ***************************************************************************/


if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}



/*********************************************
                 INCLUDES
*********************************************/
include_once(__DIR__ . "/db.php");
include_once(__DIR__ . "/language.php");



/*********************************************
               ITEM CACHE
   reduces database load by storing item
   rows to eliminate duplicate queries
*********************************************/
class Charbrowser_ItemCache
{
   private $cached_records = array();
   private $db_content;
   private $language;
   



   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct(&$db_content, &$language)
   {
      $this->db_content = $db_content;
      $this->language = $language;
   }



   //-------------------------------------
   //            DESTRUCTOR
   //-------------------------------------
   function __destruct()
   {
   }
   
   
   //------------------------------------
   //   BUILD CACHE FROM INVENTORY ROWS
   // recieves an array of inventory rows
   // and uses that to cache all the
   // item records for the inventory
   //------------------------------------
   function build_cache_inventory($inventory)
   {
      if (!is_array($inventory)) return false;
      
      $item_fields = array(
            "itemid",
            "augslot1",
            "augslot2",
            "augslot3",
            "augslot4",
            "augslot5",
            "augslot6",
            "item_id",
            "augment_1",
            "augment_2",
            "augment_3",
            "augment_4",
            "augment_5",
            "augment_6",
      );
      
      //store all the ID's found on any of the inventory rows
      $temp_item_ids = array();
      
      foreach($inventory as $inventory_row)
      {
         foreach($item_fields as $field)
         {
            $temp_item_id = $inventory_row[$field];
            //if its a valid id, and isn't already in the array, add it.
            if ($temp_item_id > 0 && !in_array($temp_item_id, $temp_item_ids))
            {
               $temp_item_ids[] = $temp_item_id;
            }
         }
      }
      
      //exit if we have no rows
      if (count($temp_item_ids) < 1) return false;
      
      //build the IN clause for the query using the spell id list
      $in_clause = implode(", ", $temp_item_ids);
      
      //query all the items
      $tpl = <<<TPL
      SELECT * 
      FROM `items` 
      WHERE `id` IN (%s)
TPL;
      $query = sprintf($tpl, $in_clause);
      
      //get the result/error
      $result = $this->db_content->query($query);

      //load the result into the cache
      if($this->db_content->rows($result))
      {
         while ($row = $this->db_content->nextrow($result))
         {
            //queried by PK, so only 1 row in the result set
            $this->cached_records[$row['id']] = $row;
         }
      }
      else cb_message_die('itemcache.php', sprintf($this->language['MESSAGE_ITEM_NOROWS'], $item_id),$this->language['MESSAGE_ERROR']);
      
      return true;
   }
   

   //------------------------------------
   //     GET EVERY CACHED ITEM
   //------------------------------------
   function fetch_cache()
   {
      return $this->cached_records;
   }
   

   //------------------------------------
   //     GET AN ITEM FROM THE CACHE
   // if it's not found in the cache it
   // will load it from the database
   //------------------------------------
   function get_item($item_id)
   {
      //check if we have this spell cached
      if (!array_key_exists($item_id, $this->cached_records))
      {
         //we don't have it cached, so we'll load it
         $tpl = <<<TPL
         SELECT * 
         FROM `items` 
         WHERE `id` = '%d'
TPL;
         $query = sprintf($tpl, $this->db_content->escape_string($item_id));

         //get the result/error
         $result = $this->db_content->query($query);

         //store the result in the cache
         if($this->db_content->rows($result))
         {
            //queried by PK, so only 1 row in the result set
            $this->cached_records[$item_id] = $this->db_content->nextrow($result);
         }
         else cb_message_die('itemcache.php', sprintf($this->language['MESSAGE_ITEM_NOROWS'], $item_id),$this->language['MESSAGE_ERROR']);
      }

      //hand the record over
      return $this->cached_records[$item_id];
      
   }


}


/*********************************************
      CREATE OUR CLASS INSTANCE(S)
*********************************************/
$cbitemcache = new Charbrowser_ItemCache($cbsql_content, $language);

?>