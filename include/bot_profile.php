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
 *   APril 17, 2020 - Maudigan
 *       Initial revision
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *  
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/statsclass.php");

define('PROF_NOROWS', false);


//constants to reference indexes in the locator array
define('LOCATOR_TABLE',  0);
define('LOCATOR_COLUMN', 1);
define('LOCATOR_INDEX',  2);


class bot_profile {

   // Variables
   private $cached_tables = array();
   private $cached_records = array();
   private $char_id;
   private $bot_id;
   private $race;
   private $class;
   private $level;
   private $items_populated;
   private $itemstats;
   private $skills;
   private $allitems;
   private $base_data;
   private $db;
   private $db_content;
   private $language;
   private $aa_effects = array();
   

/********************************************
**           DATA LOCATOR ARRAYS           **
** these describe where different types    **
** of character data are found             **
********************************************/ 

// the name of the second pk of a table
// --------------------------------------------------------------
// SYNTAX:   "<TABLE>" => "<COLUMN>",
// --------------------------------------------------------------
// <TABLE>  = the name of the table
// <COLUMN> = the name of the tables secondary pk
private $locator_pk = array (
);


// the table, column, and index of where to find a value
// --------------------------------------------------------------
// SYNTAX:  "<DATA>" => array("<TABLE>", "<COLUMN>", "<INDEX>"),
// --------------------------------------------------------------
// <DATA>   = The shortname reference for the value, 
//            it usually matches the column name.
// <TABLE>  = the name of the table the data comes from
// <COLUMN> = the column the data appears in
// <INDEX>  = if there are multiple rows for the character
//            because of a second PK, then this is the 
//            value of that second PK, otherwise its false.
private $locator = array (
   "bot_id" => array("bot_data", "bot_id", false),
   "owner_id" => array("bot_data", "owner_id", false),
   "spells_id" => array("bot_data", "spells_id", false),
   "name" => array("bot_data", "name", false),
   "last_name" => array("bot_data", "last_name", false),
   "title" => array("bot_data", "title", false),
   "suffix" => array("bot_data", "suffix", false),
   "zone_id" => array("bot_data", "zone_id", false),
   "gender" => array("bot_data", "gender", false),
   "race" => array("bot_data", "race", false),
   "class" => array("bot_data", "class", false),
   "level" => array("bot_data", "level", false),
   "deity" => array("bot_data", "deity", false),
   "creation_day" => array("bot_data", "creation_day", false),
   "last_spawn" => array("bot_data", "last_spawn", false),
   "time_spawned" => array("bot_data", "time_spawned", false),
   "size" => array("bot_data", "size", false),
   "face" => array("bot_data", "face", false),
   "hair_color" => array("bot_data", "hair_color", false),
   "hair_style" => array("bot_data", "hair_style", false),
   "beard" => array("bot_data", "beard", false),
   "beard_color" => array("bot_data", "beard_color", false),
   "eye_color_1" => array("bot_data", "eye_color_1", false),
   "eye_color_2" => array("bot_data", "eye_color_2", false),
   "ac" => array("bot_data", "ac", false),
   "atk" => array("bot_data", "atk", false),
   "hp" => array("bot_data", "hp", false),
   "str" => array("bot_data", "str", false),
   "sta" => array("bot_data", "sta", false),
   "cha" => array("bot_data", "cha", false),
   "dex" => array("bot_data", "dex", false),
   "int" => array("bot_data", "int", false),
   "agi" => array("bot_data", "agi", false),
   "wis" => array("bot_data", "wis", false),
   "fire" => array("bot_data", "fire", false),
   "cold" => array("bot_data", "cold", false),
   "magic" => array("bot_data", "magic", false),
   "poison" => array("bot_data", "poison", false),
   "disease" => array("bot_data", "disease", false),
   "corruption" => array("bot_data", "corruption", false),
   "show_helm" => array("bot_data", "show_helm", false),
   "follow_distance" => array("bot_data", "follow_distance", false),
   "stop_melee_level" => array("bot_data", "stop_melee_level", false)
);   
   
   
private $skillsbyname = array (   
   "1h_blunt" => 0,
   "1h_slashing" => 1,
   "2h_blunt" => 2,
   "2h_slashing" => 3,
   "abjuration" => 4,
   "alteration" => 5,
   "apply_poison" => 6,
   "archery" => 7,
   "backstab" => 8,
   "bind_wound" => 9,
   "bash" => 10,
   "block" => 11,
   "brass_instruments" => 12,
   "channeling" => 13,
   "conjuration" => 14,
   "defense" => 15,
   "disarm" => 16,
   "disarm_traps" => 17,
   "divination" => 18,
   "dodge" => 19,
   "double_attack" => 20,
   "dragon_punch" => 21,
   "dual_wield" => 22,
   "eagle_strike" => 23,
   "evocation" => 24,
   "feign_death" => 25,
   "flying_kick" => 26,
   "forage" => 27,
   "hand_to_hand" => 28,
   "hide" => 29,
   "kick" => 30,
   "meditate" => 31,
   "mend" => 32,
   "offense" => 33,
   "parry" => 34,
   "pick_lock" => 35,
   "piercing" => 36,
   "riposte" => 37,
   "round_kick" => 38,
   "safe_fall" => 39,
   "sense_heading" => 40,
   "sing" => 41,
   "sneak" => 42,
   "specialize_abjure" => 43,
   "specialize_alteration" => 44,
   "specialize_conjuration" => 45,
   "specialize_divinatation" => 46,
   "specialize_evocation" => 47,
   "pick_pockets" => 48,
   "stringed_instruments" => 49,
   "swimming" => 50,
   "throwing" => 51,
   "tiger_claw" => 52,
   "tracking" => 53,
   "wind_instruments" => 54,
   "fishing" => 55,
   "make_poison" => 56,
   "tinkering" => 57,
   "research" => 58,
   "alchemy" => 59,
   "baking" => 60,
   "tailoring" => 61,
   "sense_traps" => 62,
   "blacksmithing" => 63,
   "fletching" => 64,
   "brewing" => 65,
   "alcohol_tolerance" => 66,
   "begging" => 67,
   "jewelry_making" => 68,
   "pottery" => 69,
   "percussion_instruments" => 70,
   "intimidation" => 71,
   "berserking" => 72,
   "taunt" => 73,
   "frenzy" => 74,
   "remove_traps" => 75,
   "triple_attack" => 76,
   "2h_piercing" => 77
);

   
   /********************************************
   **              CONSTRUCTOR                **
   ********************************************/   
   // get the basic data, like bot id.
   function __construct($name, &$db, &$db_content, &$language, $charbrowser_is_admin_page = false)
   {      
      //dont load characters items until we need to
      $this->items_populated = false;
      
      $this->db = $db;
      $this->db_content = $db_content;
      $this->language = $language;
      
      //we can't call the local query method as it assumes the character id
      //which we need to get in the first place
      $table_name = "bot_data";
      
      //don't go sticking just anything in the database
      if (!IsAlphaSpace($name)) cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_NAME_ALPHA']);
      
      //build the query
      $tpl = <<<TPL
SELECT * 
FROM `%s` 
WHERE `name` = '%s'
TPL;
      $query = sprintf($tpl, $table_name, $name);
      
      //get the result/error
      $result = $this->db->query($query);
      
      //collect the data from returned row
      if($this->db->rows($result))
      { 
         //fetch the row
         $row = $this->db->nextrow($result);
         //save it
         $this->cached_records[$table_name] = $row;
         $this->char_id = $row['owner_id'];
         $this->bot_id = $row['bot_id'];
         $this->race = $row['race'];
         $this->class = $row['class'];
         $this->level = $row['level'];
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
   
   // Return bot ID
   public function bot_id()
   {
      return $this->bot_id;
   }
   
   //gets all the records for a double pk bot from a table
   public function GetTable($table_name)
   {
      //we don't need to clean the name up before
      //handing it to the private function because
      //it has to bee in the locator arrays
      return $this->_getTableCache($table_name);
   }
   
   //gets a single record for a bot from a table
   public function GetRecord($table_name)
   {      
      //table name goes straight into a query 
      // so we need to escape it
      $table_name = $this->db->escape_string($table_name);
      
      return $this->_getRecordCache($table_name);
   }   
   
   //uses the locator data to find the requested setting
   public function GetValue($data_key, $default = 0)
   {
      return $this->_getValue($data_key, $default);
   } 
   
   //get a skill value by name
   public function GetSkill($data_key, $default = 0)
   {
      return $this->_getSkill($data_key, $default);
   }
   
   
   //return array of all the items for this character
   public function GetAllItems()
   {
      $this->_populateItems();
      return $this->allitems;
   }
   

   
   //get stats including items
   public function getSTR()
   {
      $this->_populateItems();
      return $this->_getValue('str', 0) + $this->itemstats->STR();
   }
   
   public function getSTA()
   {
      $this->_populateItems();
      return $this->_getValue('sta', 0) + $this->itemstats->STA();
   }
   
   public function getDEX()
   {
      $this->_populateItems();
      return $this->_getValue('dex', 0) + $this->itemstats->DEX();
   }
   
   public function getAGI()
   {
      $this->_populateItems();
      return $this->_getValue('agi', 0) + $this->itemstats->AGI();
   }
   
   public function getINT()
   {
      $this->_populateItems();
      return $this->_getValue('int', 0) + $this->itemstats->INT();
   }
   
   public function getWIS()
   {
      $this->_populateItems();
      return $this->_getValue('wis', 0) + $this->itemstats->WIS();
   }
   
   public function getCHA()
   {
      $this->_populateItems();
      return $this->_getValue('cha', 0) + $this->itemstats->CHA();
   }
   
   public function getHSTR()
   {
      $this->_populateItems();
      return $this->itemstats->HSTR();
   }
   
   public function getHSTA()
   {
      $this->_populateItems();
      return $this->itemstats->HSTA();
   }
   
   public function getHDEX()
   {
      $this->_populateItems();
      return $this->itemstats->HDEX();
   }
   
   public function getHAGI()
   {
      $this->_populateItems();
      return $this->itemstats->HAGI();
   }
   
   public function getHINT()
   {
      $this->_populateItems();
      return $this->itemstats->HINT();
   }
   
   public function getHWIS()
   {
      $this->_populateItems();
      return $this->itemstats->HWIS();
   }
   
   public function getHCHA()
   {
      $this->_populateItems();
      return $this->itemstats->HCHA();
   }
   
   public function getPR()
   {
      $this->_populateItems();
      return $this->_getValue('poison', 0) + $this->itemstats->PR();
   }
   
   public function getMR()
   {
      $this->_populateItems();
      return $this->_getValue('magic', 0) + $this->itemstats->MR();
   }
   
   public function getDR()
   {
      $this->_populateItems();
      return $this->_getValue('disease', 0) + $this->itemstats->DR();
   }
   
   public function getFR()
   {
      $this->_populateItems();
      return $this->_getValue('fire', 0) + $this->itemstats->FR();
   }
   
   public function getCR()
   {
      $this->_populateItems();
      return $this->_getValue('cold', 0) + $this->itemstats->CR();
   }
   
   public function getCOR()
   {
      $this->_populateItems();
      return $this->_getValue('corruption', 0) + $this->itemstats->COR();
   }
   
   public function getHPR()
   {
      $this->_populateItems();
      return $this->itemstats->HPR();
   }
   
   public function getHFR()
   {
      $this->_populateItems();
      return $this->itemstats->HFR();
   }
   
   public function getHMR()
   {
      $this->_populateItems();
      return $this->itemstats->HMR();
   }
   
   public function getHDR()
   {
      $this->_populateItems();
      return $this->itemstats->HDR();
   }
   
   public function getHCR()
   {
      $this->_populateItems();
      return $this->itemstats->HCR();
   }
   
   public function getHCOR()
   {
      $this->_populateItems();
      return $this->itemstats->HCOR();
   }
   
   public function getWT()
   {
      $this->_populateItems();
      return $this->itemstats->WT();
   }
   
   public function getFT()
   {
      $this->_populateItems();
      return $this->itemstats->FT();
   }
   
   public function getDS()
   {
      $this->_populateItems();
      return $this->itemstats->DS();
   }
   
   public function getHaste()
   {
      $this->_populateItems();
      return $this->itemstats->haste();
   }
   
   public function getRegen()
   {
      $this->_populateItems();
      return $this->itemstats->regen();
   }
   
   public function getItemAC()
   {
      $this->_populateItems();
      return $this->itemstats->AC();
   }
   
   public function getItemHP()
   {
      $this->_populateItems();
      return $this->itemstats->hp();
   }
   
   public function getItemATK()
   {
      $this->_populateItems();
      return $this->itemstats->attack();
   }
   
   public function getItemEndurance()
   {
      $this->_populateItems();
      return $this->itemstats->endurance();
   }
   
   public function getItemMana()
   {
      $this->_populateItems();
      return $this->itemstats->mana();
   }

   
   
/********************************************
**            PRIVATE FUNCTIONS            **
********************************************/


   //query this profiles items and add up all the stats
   private function _populateItems()
   {      
      //only run it once
      if ($this->items_populated) return;
      $this->items_populated = true;
      
      //place where all the items stats are added up
      $this->itemstats = new stats();

      //holds all of the items and info about them
      $this->allitems = array();

      //FETCH INVENTORY
      // pull bots inventory slotid is loaded as
      // "myslot" since items table also has a slotid field.
      $tpl = <<<TPL
      SELECT item_id, augment_1, augment_2, 
             augment_3, augment_4, 
             augment_5, augment_6,
             slot_id AS myslot, inst_charges
      FROM bot_inventories
      WHERE bot_id = '%s'  
TPL;
      $query = sprintf($tpl, $this->bot_id);
      $result = $this->db->query($query);
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later
      $tpl = <<<TPL
      SELECT * 
      FROM items 
      WHERE id = '%s' 
      LIMIT 1
TPL;
      while ($row = $this->db->nextrow($result)) {
         $query = sprintf($tpl, $row["item_id"]);
         $itemresult = $this->db_content->query($query);
         $itemrow = $this->db_content->nextrow($itemresult);
         //merge the inventory and item row
         $row = array_merge($itemrow, $row);
         $tempitem = new item($row);
         for ($i = 1; $i <= 5; $i++) {
            if ($row["augment_".$i]) {
               $query = sprintf($tpl, $row["augment_".$i]);
               $augresult = $this->db_content->query($query);
               $augrow = $this->db_content->nextrow($augresult);
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
   
   //get skill by name
   private function _getSkill($data_key, $default)
   {     
   
      //is this a valid skill name?
      if (!array_key_exists($data_key, $this->skillsbyname)) return $default;
      
      $skillid = $this->skillsbyname[$data_key];
       
      //if we've already loaded the table
      //send the result now
      if (is_array($skills)) 
      {
         if (!array_key_exists($skillid, $this->skills)) return $default;
         
         return $this->skills[$skillid];
      }
      

      //FETCH SKILLS
      $tpl = <<<TPL
      SELECT skillID, cap
      FROM skill_caps
      WHERE class = '%s'
      AND level = '%s'  
TPL;
      $query = sprintf($tpl, $this->class, $this->level);
      $result = $this->db_content->query($query);
      $this->skills = array();
      while ($row = $this->db_content->nextrow($result)) {
         $this->skills[$row['skillID']] = $row['cap'];
      }
      
      //return the requested skill
      if (!array_key_exists($skillid, $this->skills)) return $default;

      return $this->skills[$skillid];
   }
   
   //uses the locator data to find the requested setting
   private function _getValue($data_key, $default)
   {       
      // Pull bot_profile Info
      if (!array_key_exists($data_key, $this->locator))
      {
         cb_message_die('bot_profile.php', sprintf($this->language['MESSAGE_PROF_NOKEY'], $data_key),$this->language['MESSAGE_ERROR']);
      }
      
      //get the locator data for this setting so we can find it
      $table_name  = $this->locator[$data_key][LOCATOR_TABLE];
      $column_name = $this->locator[$data_key][LOCATOR_COLUMN];
      $index       = $this->locator[$data_key][LOCATOR_INDEX];
      
      //if the locator lists a strict index of false then there
      //will only be 1 record
      if ($index === false)
      {
         //fetch the cached record
         $cached_record = $this->_getRecordCache($table_name);
      }
      
      //otherwise the locator lists a numeric value representing
      //the value of the second pk
      else
      {         
         //fetch this table from the db/cache
         $cached_table = $this->_getTableCache($table_name);
      
         //this table has no rows at all         
         if ($cached_table == PROF_NOROWS)
         {
            return false;
         }
         
         //this is not a failure, this just means the character doesn't have a record
         //for this skill, or whatever is being requested
         if (!array_key_exists($index, $cached_table))
         {
            return $default;
         }
         
         $cached_record = $cached_table[$index];
      }
            
            
      //make sure our column exists in the record
      if (!array_key_exists($column_name, $cached_record))
      {
            cb_message_die('bot_profile.php', sprintf($this->language['MESSAGE_PROF_NOCACHE'], $data_key, $table_name, $column_name),$this->language['MESSAGE_ERROR']);
      }
      
      //return the value
      return $cached_record[$column_name];
   }
   
   
   
   // gets a TABLE, it loads it into memory so the same TABLE
   // isnt double queried. It keeps every record and uses the 
   // second column as the array index 
   private function _getTableCache($table_name)
   {
      //get the name of the second pk on the table
      if (!array_key_exists($table_name, $this->locator_pk))
      {
         cb_message_die('bot_profile.php', sprintf($this->language['MESSAGE_PROF_NOTABKEY'], $table_name),$this->language['MESSAGE_ERROR']);
      }
      $second_column_name = $this->locator_pk[$table_name];
      
      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->cached_tables))
      {
         //since we are accessing the database, we'll go ahead and 
         //load every column for the character and store it for later use
         $result = $this->_doBotQuery($table_name);

         //parse the result
         if($this->db->rows($result))
         { 
            //this is a table with two primary keys, we need to load it
            //into a supporting array, indexed by it's second pk
            $temp_array = array();
            while($row = $this->db->nextrow($result))
            {
               $temp_array[$row[$second_column_name]] = $row;
            }

            $this->cached_tables[$table_name] = $temp_array;
         } 
         else 
         {
            return PROF_NOROWS;
         }
      }
      
      //hand the table/record over
      return $this->cached_tables[$table_name];
   }
   
   
   
   // gets a RECORD, it loads it into memory so the same RECORD
   // isnt double queried.
   private function _getRecordCache($table_name)
   {
      
      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->cached_records))
      {
         //since we are accessing the database, we'll go ahead and 
         //load every column for the character and store it for later use
         $result = $this->_doBotQuery($table_name);

         //parse the result
         if($this->db->rows($result))
         { 
            //this is a simple table with only 1 row per character
            //we just store it in the root structure
            $this->cached_records[$table_name] = $this->db->nextrow($result);
         } 
         else cb_message_die('bot_profile.php', sprintf($this->language['MESSAGE_PROF_NOROWS'], $table_name),$this->language['MESSAGE_ERROR']);
      }
      
      //hand the table/record over
      return $this->cached_records[$table_name];
   }
   
   //gets all the records from a table for this character instance
   //we even get ones we dont need; they'll get cached for later use
   private function _doBotQuery($table_name)
   {   
      //build the query
      $tpl = <<<TPL
      SELECT * 
      FROM `%s` 
      WHERE `id` = '%d'
TPL;
      $query = sprintf($tpl, $table_name, $this->char_id);   
      
      //get the result/error
      $result = $this->db->query($query);
      
      //serve em up
      return $result;
   }   
   
}


?>