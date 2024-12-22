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
 *   January 16, 2023 - Maudigan
 *       renamed class with Charbrowser_ prefix
 *       added _ prefix to private properties
 *       changed constructor to fetch local referenecs to global objects
 *
 *  
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/statsclass.php");
include_once(__DIR__ . "/spellcache.php");
include_once(__DIR__ . "/itemcache.php");

class Charbrowser_Corpse 
{
   
   // Variables
   private $_cached_corpse;
   private $_char_id;
   private $_corpse_id;
   private $_items_populated;
   private $_itemstats;
   private $_allitems;
   
   //local references to external classes
   //imported using "global" in the constructor
   private $_error;
   private $_language;
   private $_sql;
   private $_sql_content;
   
   /********************************************
   **              CONSTRUCTOR                **
   ********************************************/   
   // get the basic data, like corpse id.
   function __construct($id)
   {
      global $cb_error;
      global $language;
      global $cbsql;
      global $cbsql_content;
      
      //make sure the error class exists, store pointer
      if (!isset($cb_error)) 
      {
         die("The Charbrowser_Corpse class can't be initialized prior to the error class (error.php) being created.");
      }
      else
      {
         $this->_error = $cb_error;
      }
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_Corpse class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //make sure the database classes exist, store pointers
      if (!isset($cbsql)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Corpse', 'db.php'));
      }
      else
      {
         $this->_sql = $cbsql;
      }
      if (!isset($cbsql_content)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Corpse', 'db.php'));
      }
      else
      {
         $this->_sql_content = $cbsql_content;
      }
      
      //dont load characters items until we need to
      $this->_items_populated = false;
      
      //don't go sticking just anything in the database
      if (!is_numeric($id)) $this->_error->message_die($this->_language['MESSAGE_ERROR'],$this->_language['MESSAGE_CORPSE_NON_NUMERIC']);
      
      //build the query
      $tpl = <<<TPL
         SELECT * 
         FROM `character_corpses` 
         WHERE `id` = '%s'
TPL;
      $query = sprintf($tpl, $id);
      
      //get the result/error
      $result = $this->_sql->query($query);
      
      //collect the data from returned row
      if($this->_sql->rows($result))
      { 
         //fetch the row
         $row = $this->_sql->nextrow($result);
         //save it
         $this->_cached_corpse = $row;
         $this->_char_id = $row['charid'];
         $this->_corpse_id = $row['id'];
      }   
      else $this->_error->message_die($this->_language['MESSAGE_ERROR'],$this->_language['MESSAGE_NO_FIND']);


   }
   
   /********************************************
   **              DESTRUCTOR                 **
   ********************************************/  
   function __destruct()
   {
      unset($this->_sql);
      unset($this->_language); 
   }
   
   
   /********************************************
   **            PUBLIC FUNCTIONS             **
   ********************************************/
   
   // Return char ID
   public function char_id()
   {
      return $this->_char_id;
   }   
   
   // Return corpse ID
   public function corpse_id()
   {
      return $this->_corpse_id;
   }
   
   //gets the corpse record
   public function GetRecord()
   {
      return $this->_cached_corpse;
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
      return $this->_allitems;
   }
   
   ///weight of items on corpse
   public function getWT()
   {
      $this->_populateItems();
      return $this->_itemstats->WT();
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
      if ($this->_items_populated) return;
      $this->_items_populated = true;
      
      //place where all the items stats are added up
      $this->_itemstats = new Charbrowser_Stats();

      //holds all of the items and info about them
      $this->_allitems = array();

      //FETCH INVENTORY ROWS
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
      $query = sprintf($tpl, $this->_corpse_id);
      $result = $this->_sql->query($query);
      $inventory_results = $this->_sql->fetch_all($result);
      
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
         $tempitem = new Charbrowser_Item($row);
         for ($i = 1; $i <= 6; $i++) {
            if ($row["augslot".$i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = $cbitemcache->get_item($aug_item_id);
               $tempitem->addaug($augrow);
               //add stats only if it's equiped
               if ($tempitem->type() == EQUIPMENT) {
                  $this->_itemstats->additem($augrow);
               }
            }
         }

         if ($tempitem->type() == EQUIPMENT)
            $this->_itemstats->additem($row);
        
         if ($tempitem->type() == EQUIPMENT || $tempitem->type() == INVENTORY)
            $this->_itemstats->addWT($row['weight']);
         
         $this->_allitems[$tempitem->slot()] = &$tempitem;
         unset($tempitem);
      }
      
   }
   
   //uses the locator data to find the requested setting
   private function _getValue($column_name, $default)
   {           
      //make sure our column exists in the record
      if (!array_key_exists($column_name, $this->_cached_corpse))
      {
            $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_PROF_NOCACHE'], $data_key, $table_name, $column_name));
      }
      
      //return the value
      return $this->_cached_corpse[$column_name];
   }
   
}


?>