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
 *   October 30, 2022 - Maudigan
 *       Initial revision
 *  
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/statsclass.php");
include_once(__DIR__ . "/spellcache.php");
include_once(__DIR__ . "/itemcache.php");

class corpse_profile {

   // Variables
   private $cached_corpse;
   private $char_id;
   private $corpse_id;
   private $items_populated;
   private $itemstats;
   private $allitems;
   private $db;
   private $db_content;
   private $language;
   
   /********************************************
   **              CONSTRUCTOR                **
   ********************************************/   
   // get the basic data, like corpse id.
   function __construct($id, &$db, &$db_content, &$language, $charbrowser_is_admin_page = false)
   {      
      //dont load characters items until we need to
      $this->items_populated = false;
      
      $this->db = $db;
      $this->db_content = $db_content;
      $this->language = $language;
      
      //don't go sticking just anything in the database
      if (!is_numeric($id)) cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_CORPSE_NON_NUMERIC']);
      
      //build the query
      $tpl = <<<TPL
         SELECT * 
         FROM `character_corpses` 
         WHERE `id` = '%s'
TPL;
      $query = sprintf($tpl, $id);
      
      //get the result/error
      $result = $this->db->query($query);
      
      //collect the data from returned row
      if($this->db->rows($result))
      { 
         //fetch the row
         $row = $this->db->nextrow($result);
         //save it
         $this->cached_corpse = $row;
         $this->char_id = $row['charid'];
         $this->corpse_id = $row['id'];
      }   
      else cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_NO_FIND']);


   }
   
   /********************************************
   **              DESTRUCTOR                 **
   ********************************************/  
   function __destruct()
   {
      unset($this->db);
      unset($this->language); 
   }
   
   
   /********************************************
   **            PUBLIC FUNCTIONS             **
   ********************************************/
   
   // Return char ID
   public function char_id()
   {
      return $this->char_id;
   }   
   
   // Return corpse ID
   public function corpse_id()
   {
      return $this->corpse_id;
   }
   
   //gets the corpse record
   public function GetRecord()
   {
      return $this->cached_corpse;
   }   
   
   //uses the locator data to find the requested setting
   public function GetValue($data_key, $default = 0)
   {
      return $this->_getValue($data_key, $default);
   } 
   
   
   //return array of all the items for this character
   public function GetAllItems()
   {
      $this->_populateItems();
      return $this->allitems;
   }
   
   ///weight of items on corpse
   public function getWT()
   {
      $this->_populateItems();
      return $this->itemstats->WT();
   }
   
/********************************************
**            PRIVATE FUNCTIONS            **
********************************************/


   //query this profiles items and add up all the stats
   private function _populateItems()
   {      
      global $cbspellcache;
      global $cbitemcache;
      
      //only run it once
      if ($this->items_populated) return;
      $this->items_populated = true;
      
      //place where all the items stats are added up
      $this->itemstats = new stats();

      //holds all of the items and info about them
      $this->allitems = array();

      //FETCH INVENTORY ROWS
      // pull bots inventory slotid is loaded as
      // "myslot" since items table also has a slotid field.
      $tpl = <<<TPL
      SELECT item_id AS itemid, 
             aug_1 AS augslot1, 
             aug_2 AS augslot2, 
             aug_3 AS augslot3, 
             aug_4 AS augslot4, 
             aug_5 AS augslot5, 
             aug_6 AS augslot6,
             equip_slot AS myslot, 
             charges AS charges
      FROM character_corpse_items
      WHERE corpse_id = '%s'  
TPL;
      $query = sprintf($tpl, $this->corpse_id);
      $result = $this->db->query($query);
      $inventory_results = $this->db->fetch_all($result);
      
      //CACHE ITEMS
      //preload all the items on the inventory using the item set
      $cbitemcache->build_cache_inventory($inventory_results);
      
      //CACHE SPELLS
      //preload all the spells that are on all the preloaded items
      $item_list = $cbitemcache->fetch_cache();
      $cbspellcache->build_cache_itemset($item_list);
      
      //PROCESS INVENTORY ROWS
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later
      foreach ($inventory_results as $row)
      {
         $itemrow = $cbitemcache->get_item($row['itemid']);
         //merge the inventory and item row
         $row = array_merge($itemrow, $row);
         $tempitem = new item($row);
         for ($i = 1; $i <= 6; $i++) {
            if ($row["augslot".$i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = $cbitemcache->get_item($aug_item_id);
               $tempitem->addaug($augrow);
               //add stats only if it's equiped
               if ($tempitem->type() == EQUIPMENT) {
                  $this->itemstats->additem($augrow);
               }
            }
         }

         if ($tempitem->type() == EQUIPMENT)
            $this->itemstats->additem($row);
        
         if ($tempitem->type() == EQUIPMENT || $tempitem->type() == INVENTORY)
            $this->itemstats->addWT($row['weight']);
         
         $this->allitems[$tempitem->slot()] = &$tempitem;
         unset($tempitem);
      }
      
   }
   
   //uses the locator data to find the requested setting
   private function _getValue($column_name, $default)
   {           
      //make sure our column exists in the record
      if (!array_key_exists($column_name, $this->cached_corpse))
      {
            cb_message_die('corpse_profile.php', sprintf($this->language['MESSAGE_PROF_NOCACHE'], $data_key, $table_name, $column_name),$this->language['MESSAGE_ERROR']);
      }
      
      //return the value
      return $this->cached_corpse[$column_name];
   }
   
}


?>