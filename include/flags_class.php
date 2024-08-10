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
 *   June 12, 2023 - Initial Revision. Centralize flag handling.
 *                       (Maudigan)
 *   June 14, 2023 - Made the query that preopulates data buckets
 *                   work with other key naming conventions
 *                       (Maudigan)
 *
 ***************************************************************************/

if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}



class Charbrowser_Flags
{
   //counts how many flag boxes have been output
   private $_zone_header_id = 0;
   private $_zone_popup_id = 0;
   
   //populates with characters quest/zone flag information
   private $_quest_globals = array();
   private $_zone_flags = array();
   private $_data_buckets = array();
   private $_data_buckets_character = array();
   
   //character this class was initialized for
   private $_charID = 0;
   
   //local references to external classes
   //imported using "global" in the constructor
   private $_error;
   private $_language;
   private $_sql;
   private $_template;


   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct($charID)
   {
      global $cb_error;
      global $language;
      global $cbsql;
      global $cb_template;
      
      //make sure the error class exists, store pointer
      if (!isset($cb_error)) 
      {
         die("The Charbrowser_Flags class can't be initialized prior to the error class (error.php) being created.");
      }
      else
      {
         $this->_error = $cb_error;
      }
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_Flags class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //make sure the database classes exist, store pointers
      if (!isset($cbsql)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Flags', 'db.php'));
      }
      else
      {
         $this->_sql = $cbsql;
      }

      //make sure the template class exists, store pointer
      if (!isset($cb_template)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Flags', 'template.php'));
      }
      else
      {
         $this->_template = $cb_template;
      }
      
      //save char id 
      $this->_charID = $charID;

      //get quest globals from the db
      $tpl = <<<TPL
      SELECT name, value 
      FROM quest_globals 
      WHERE charid = %s
TPL;
      $query = sprintf($tpl, $this->_charID);
      $result = $this->_sql->query($query);
      while($row = $this->_sql->nextrow($result)) {
         $this->_quest_globals[$row['name']] = $row['value']; 
      }

      //get zone flags from the db
      $tpl = <<<TPL
      SELECT zoneID 
      FROM zone_flags 
      WHERE charID = %s
TPL;
      $query = sprintf($tpl, $this->_charID);
      $result = $this->_sql->query($query);
      while($row = $this->_sql->nextrow($result)) {
         $this->_zone_flags[] = $row['zoneID']; 
      }

      //get data buckets
      $tpl = <<<TPL
      SELECT `key`, `value` 
      FROM `data_buckets` 
      WHERE `key` LIKE '%s-%%'
         OR `key` LIKE '%%-%s'
TPL;
      $query = sprintf($tpl, $this->_charID, $this->_charID);
      $result = $this->_sql->query($query);
      while($row = $this->_sql->nextrow($result)) {
         $this->_data_buckets[$row['key']] = $row['value']; 
      }

      //get data buckets character scope
      $tpl = <<<TPL
      SELECT `key`, `value` 
      FROM `data_buckets` 
      WHERE `character_id` = %s
TPL;
      $query = sprintf($tpl, $this->_charID);
      $result = $this->_sql->query($query);
      while($row = $this->_sql->nextrow($result)) {
         $this->_data_buckets_character[$row['key']] = $row['value']; 
      }
   }


   //-------------------------------------
   //            DESTRUCTOR
   //-------------------------------------
   function __destruct()
   {
      //place holder
   }
   
   
   //-------------------------------------
   //          QUEST GLOBAL
   // checks a quest global meets 
   // a condition
   //-------------------------------------
   function getflag($condition, $flagname) {
      if (!array_key_exists($flagname, $this->_quest_globals)) return 0; 
      
      if ($this->_quest_globals[$flagname] < $condition) return 0; 
      
      return 1; 
   } 
   

   //-------------------------------------
   //         QUEST GLOBAL BIT
   // checks if a bit is set in a  
   // specified quest global
   //-------------------------------------
   //check a quest global bit
   function getbitflag($bitset, $flagname) { 
      if (!array_key_exists($flagname, $this->_quest_globals)) return 0; 
      
      if ($this->_quest_globals[$flagname] & $bitset) return 1; 
      
      return 0; 
   } 


   //-------------------------------------
   //          DATABUCKET
   // recieves a databucket key suffix
   // then returns that keys value for
   // the current character
   //-------------------------------------
   function getdatabucket($key_suffix_prefix) { 
   
      //check for key with suffix
      $key_name = $this->_charID."-".$key_suffix_prefix;
      if (array_key_exists($key_name, $this->_data_buckets))
         return $this->_data_buckets[$key_name]; 
      
      //check for key with prefix
      $key_name = $key_suffix_prefix."-".$this->_charID;
      if (array_key_exists($key_name, $this->_data_buckets))
         return $this->_data_buckets[$key_name]; 
      
      return null; 
   } 


   //-------------------------------------
   //          DATABUCKET CHAR SCOPE
   // recieves a databucket key 
   // then returns that keys value for
   // the current character
   //-------------------------------------
   function getdatabucketcharacter($key_name) { 
   
      //check for key with suffix
      if (array_key_exists($key_name, $this->_data_buckets_character))
         return $this->_data_buckets_character[$key_name]; 
      
      return null; 
   } 
   
   


   //-------------------------------------
   //        DATABUCKET CHAR BIT
   // checks if a bit is set for a char
   // char scope bit flag
   //-------------------------------------
   function getdatabucketcharacterbitflag($bitset, $key_name) { 
      
      $key = $this->getdatabucketcharacter($key_name);
      
      if ($key === null) return false; 
      
      if ($key & $bitset) return 1; 
      
      return 0; 
   } 
   
  


   //-------------------------------------
   //        DATABUCKET ARRAY
   // query and explode a databucket
   // array
   //-------------------------------------
   function getdatabucketarray($delimiter, $key_suffix_prefix) { 
      
      $key = $this->getdatabucket($key_suffix_prefix);
      
      if ($key === null) return null;
      
      return explode($delimiter, $key);
   } 


   //-------------------------------------
   //          ZONE FLAG
   // checks if the current char has
   // access to a zone  
   //-------------------------------------
   function getzoneflag($zoneid) { 
      if (!in_array($zoneid, $this->_zone_flags)) return 0; 
      return 1; 
   } 


   //----------------------------------------------
   // OUTPUT THE EXPANSION HEADERS IN THE MAIN BOX
   //This function outputs the blue highlighted
   //expansion headers in the main flag box
   //----------------------------------------------
   function oexpansion($expansion) {
      $this->_template->assign_both_block_vars( "mainhead" , array( 
         'TEXT' => $expansion) 
      ); 
   }


   //----------------------------------------------
   //         OUTPUT A SINGLE ZONE
   //This function recieves any number of arguments
   //the arguments should be in condition/output pairs
   //ODD arguments are a true/false condition
   //EVEN arguments are the text that's output
   //  when the previous condition is met
   //
   //If ANY of the conditions are met:
   //   The bubble will be FILLED in and
   //   the language paired with the condition will
   //   be displayed.
   //
   //if NONE of the conditions are met:
   //   The bubble will NOT be filled in
   //   the language displayed will be the last
   //   one passed in the argument list
   //   OPTIONALLY - you can pass a final extra
   //   language, this will get used when no
   //   conditions are met.
   //
   // E.G.
   // Condition1, Language1
   // Condition1, Language1, LanguageElse
   // Condition1, Language1, Condition2, Language2
   // Condition1, Language1, Condition2, Language2, LanguageElse
   function ozone() {
      
      $args = func_get_args();
      
      //arguments alternate between the flag condition, and 
      //the language output for that condition so we will
      //loop through every other argument
      $arg_count = cb_count($args);
      $condition_count = floor($arg_count/2);
      for ($i = 0; $i < $condition_count; $i++)
      {
         $condition = $args[$i * 2];
         $language = $args[$i * 2 + 1];
         if ($condition)
         {
            $this->_template->assign_both_block_vars( "mainhead.main" , array( 
               'ID' => $this->_zone_header_id++,
               'FLAG' => "1", 
               'TEXT' => $language) 
            ); 
            return;
         }
      }
      
      //if we didn't find a good condition that means
      //they don't have the flag. If we have an odd, unused
      // parameter at the end, that's the language we use
      // otherwise we just use the last language found
      // in the loop
      
      //if odd, grab last arg
      if ($arg_count % 2 == 1) $language = $args[$arg_count - 1];
      
      $this->_template->assign_both_block_vars( "mainhead.main" , array( 
         'ID' => $this->_zone_header_id++,
         'FLAG' => "0", 
         'TEXT' => $language) 
      ); 
   }
   

   //----------------------------------------------
   //      OUTPUT THE BOX THE FLAGS GO IN
   //This function outputs the box that individual
   //flags are output into. This should be called
   //immediately before all the flags that go into
   //it. 
   //----------------------------------------------
   function otitle($title_language) {
      $this->_template->assign_both_block_vars( "head" , array( 
         'ID' => $this->_zone_popup_id++, 
         'NAME' => $title_language) 
      ); 
   }


   //----------------------------------------------
   //          OUTPUT A SINGLE FLAG
   //This function recieves any number of arguments
   //the arguments should be in condition/output pairs
   //ODD arguments are a true/false condition
   //EVEN arguments are the text that's output
   //  when the previous condition is met
   //
   //If ANY of the conditions are met:
   //   The bubble will be FILLED in and
   //   the language paired with the condition will
   //   be displayed.
   //
   //if NONE of the conditions are met:
   //   The bubble will NOT be filled in
   //   the language displayed will be the last
   //   one passed in the argument list
   //   OPTIONALLY - you can pass a final extra
   //   language, this will get used when no
   //   conditions are met.
   //
   // E.G.
   // Condition1, Language1
   // Condition1, Language1, LanguageElse
   // Condition1, Language1, Condition2, Language2
   // Condition1, Language1, Condition2, Language2, LanguageElse
   function oflag() {
      
      $args = func_get_args();
      
      //arguments alternate between the flag condition, and 
      //the language output for that condition so we will
      //loop through every other argument
      $arg_count = cb_count($args);
      $condition_count = floor($arg_count/2);
      for ($i = 0; $i < $condition_count; $i++)
      {
         $condition = $args[$i * 2];
         $language = $args[$i * 2 + 1];
         if ($condition)
         {
            $this->_template->assign_both_block_vars( "head.flags" , array( 
               'FLAG' => "1", 
               'TEXT' => $language) 
            ); 
            return;
         }
      }
      
      //if we didn't find a good condition that means
      //they don't have the flag. If we have an odd, unused
      // parameter at the end, that's the language we use
      // otherwise we just use the last language found
      // in the loop
      
      //if odd, grab last arg
      if ($arg_count % 2 == 1) $language = $args[$arg_count - 1];
      
      $this->_template->assign_both_block_vars( "head.flags" , array( 
         'FLAG' => "0", 
         'TEXT' => $language) 
      ); 
   }
   
   
}
?>