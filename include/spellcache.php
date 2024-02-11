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
 *      Initial revision: Store queried spells
 *   January 16, 2023 - Maudigan
 *      added _ prefix to private properties
 *      modified contructor to fetch global vars on its own
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
               SPELL CACHE
   reduces database load by storing spell
   rows to eliminate duplicate queries
*********************************************/
class Charbrowser_SpellCache
{
   private $_cached_records = array();
   
   //local references to external classes
   //imported using "global" in the constructor
   private $_error;
   private $_language;
   private $_sql_content;
   

   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct()
   {
      global $cb_error;
      global $language;
      global $cbsql_content;
      
      
      //make sure the error class exists, store pointer
      if (!isset($cb_error)) 
      {
         die("The Charbrowser_SpellCache class can't be initialized prior to the error class (error.php) being created.");
      }
      else
      {
         $this->_error = $cb_error;
      }
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_SpellCache class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //make sure the database classes exist, store pointers
      if (!isset($cbsql_content)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_SpellCache', 'db.php'));
      }
      else
      {
         $this->_sql_content = $cbsql_content;
      }
   }



   //-------------------------------------
   //            DESTRUCTOR
   //-------------------------------------
   function __destruct()
   {
   }
   
   
   //------------------------------------
   //    BUILD CACHE FROM ITEM SET
   // recieves an array of item rows
   // and uses that to cache all the
   // spell effects on the items
   //------------------------------------
   function build_cache_itemset($item_set)
   {
      if (!is_array($item_set)) return false;
      
      $spell_fields = array(
            "proceffect",
            "worneffect",
            "focuseffect",
            "clickeffect",
            "scrolleffect",
      );
      
      //store all the ID's found on any of the items
      $temp_spell_ids = array();
      foreach($item_set as $item)
      {
         foreach($spell_fields as $field)
         {
            $temp_spell_id = $item[$field];
            //if its a valid id, and isn't already in the array, add it.
            if ($temp_spell_id > 0 && !in_array($temp_spell_id, $temp_spell_ids))
            {
               $temp_spell_ids[] = $temp_spell_id;
            }
         }
      }
      
      //exit if we have no rows
      if (cb_count($temp_spell_ids) < 1) return false;
      
      //build the IN clause for the query using the spell id list
      $in_clause = implode(", ", $temp_spell_ids);
      
      //query all the spells
      $tpl = <<<TPL
      SELECT * 
      FROM `spells_new` 
      WHERE `id` IN (%s)
TPL;
      $query = sprintf($tpl, $in_clause);

      //get the result/error
      $result = $this->_sql_content->query($query);

      //load the result into the cache
      if($this->_sql_content->rows($result))
      {
         while ($row = $this->_sql_content->nextrow($result))
         {
            //queried by PK, so only 1 row in the result set
            $this->_cached_records[$row['id']] = $row;
         }
      }

      return true;
   }
   

   //------------------------------------
   //     GET A SPELL FROM THE CACHE
   // if it's not found in the cache it
   // will load it from the database
   //------------------------------------
   function get_spell($spell_id)
   {
      //check if we have this spell cached
      if (!array_key_exists($spell_id, $this->_cached_records))
      {
         //we don't have it cached, so we'll load it
         $tpl = <<<TPL
         SELECT * 
         FROM `spells_new` 
         WHERE `id` = '%d'
TPL;
         $query = sprintf($tpl, $this->_sql_content->escape_string($spell_id));

         //get the result/error
         $result = $this->_sql_content->query($query);

         //store the result in the cache
         if($this->_sql_content->rows($result))
         {
            //queried by PK, so only 1 row in the result set
            $this->_cached_records[$spell_id] = $this->_sql_content->nextrow($result);
         }
         else {
            return false;
         }
      }

      //hand the record over
      return $this->_cached_records[$spell_id];
      
   }


}


/*********************************************
      CREATE OUR CLASS INSTANCE(S)
*********************************************/
$cbspellcache = new Charbrowser_SpellCache();

?>