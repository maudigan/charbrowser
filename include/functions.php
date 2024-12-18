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
 *   September 28, 2014 - Maudigan
 *      added code to monitor database access upon request
 *   October 8, 2014 - Maudigan
 *      relocated the database monitoring functions to the db.php include
 *      & modified queries to use the new wrapper functions timer functions
 *      were kept here and renamed
 *   October 3, 2016 - Maudigan
 *      Added the QuickTemplate function which plugs values into a template
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 8, 2020 - Maudigan
 *      Added a way to override the default permissions when you wrap
 *      charbrowser in your own sites header/foot. That way you can
 *      have admin portals.
 *      Implemented shared bank.
 *   March 9, 2020 - Maudigan
 *      added function to output the profile menu
 *   March 14, 2020 - Maudigan
 *      made image version of message die
 *   March 15, 2020 - Maudigan
 *      added function to fetch a guild link
 *   March 17, 2020 - Maudigan
 *      we now display info from github so we need to sanitize against xss
 *      added a function for that
 *   March 28, 2020 - Maudigan
 *      added a quest global permission fetching function for guilds
 *      which is set by the guild leader
 *   April 2, 2020 - Maudigan
 *      flush the cache prior to outputting the image to make sure
 *      we don't send a text header
 *   April 11, 2020 - Maudigan
 *      tweaked guild name output a little
 *      added a function to calculate avatar image
 *   April 25, 2020 - Maudigan
 *      add profile button for bots menu, staged the begining of having 
 *      dynamically displayed/hidden buttons
 *   May 2, 2020 - Maudigan
 *      add function to build where clause 
 *   May 3, 2020 - Maudigan
 *      add function to get comma concatenated list of id's in an array
 *      add a function to join to arrays on a specific field
 *      add a function to sort arrays by sub element
 *   July 28, 2020 - Maudigan
 *      The manual_join function wasn't handling rows with the duplicate keys
 *      well since it was indexing the array by that key. 
 *   October 19, 2022 - Maudigan
 *      added leadership button (Maudigan)
 *   October 29, 2022 - Maudigan
 *      rewrote the generate_where function to include a customizable divider
 *        and simplified the functions logic
 *   November 1, 2022 - Added thea bility to have a gravestone as an
 *      an avatar image
 *   Novemter 3, 2022 - Reimplement the old-style of count() for php 7.4
 *      compatibility 
 *   November 23, 2022 - Added a funciton that checks your databse version
 *   December 3, 2022 - Allow guild/name search criteria to be echoed back
 *      in the header search fields
 *   January 11, 2023 - renamed count_ez function to cb_count to be more
 *      inline with other names (and cause I had a name collision on my 
 *         server). - Maudigan
 *      Removed IsAlphaSpae and IsAlphaNumericSpace and instead 
 *         use preg_validate and regex to check values
 *      added checkParm() to quicky check if get/post variables are set 
 *      added the breakout_bits() function which helps output the class, race
 *         and deity lists on items.
 *   August 9, 2024 - add a tick to times function -Maudigan
 *
 *
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/language.php");
include_once(__DIR__ . "/config.php");

//holds timers
$timers = array();

//there's a few places that a GET/POST parameter is either
//set or unset, this is used to check those parameters
//and/or change their state.
function checkParm($name, $value = -1)
{
   if ($value === false && $value === 0)
   {
      unset($_GET[$name]);
      unset($_POST[$name]);
   }
   elseif ($value === true && $value === 1)
   {
      $_GET[$name] = true;
      $_POST[$name] = true;
   }
   return isset($_GET[$name]) || isset($_POST[$name]);
}

//validates a value against a pattern, or returns a default value
function preg_validate($value, $pattern, $default = '')
{
   //if the pattern doesnt match, return the default
   if (!preg_match($pattern, $value)) 
   {
      return $default;
   }   

   return $value;
}

//Fetches GET/POST vars, validates it against a pattern,
//and either errors on a mismatch or returns a default
function preg_Get_Post($index, $pattern, $default = '', $error_title = false, $error_message = false, $require_value = false)
{
   global $cb_error;
   
   //initialize
   $return = '';
   
   //fetch
   if (isset($_GET[$index]))
   {
      $return = $_GET[$index];
   }
   elseif (isset($_POST[$index]))
   {
      $return = $_POST[$index];
   }
   else
   {
      if ($require_value) 
      {
         $cb_error->message_die($error_title, $error_message);
      }
      return $default;
   }
   
   //if it's blank, treat it like it's not set
   if ($return == '')
   {
      if ($require_value) 
      {
         $cb_error->message_die($error_title, $error_message);
      }
      return $default;
   }

   //check if pattern doesnt match
   if (!preg_match($pattern, $return)) 
   {
      //if not, and we have an error message, post it and die
      if ($error_title !== false && $error_message !== false)
      {
         $cb_error->message_die($error_title, $error_message);
      }
      
      //if we don't have error messages, return the default value
      return $default;
   }   
   
   return $return;
}


//simple count function that acts
// like the one prior to php 7.4
function cb_count($target)
{
   //is_countable added in 7.4 to work with the new version
   //of count. if it doesnt exist, we can safely use the
   //old count.
   if (!function_exists("is_countable")) return count($target);
   
   //otherwise we need to verify the $target can be counted
   //before counting it
   if (is_countable($target)) 
   {
      return count($target);
   }
   return 0;
}

//recieves an array of bitmasks, and their name
//uses that ask a lookup table to breakout
//a variable into a string of delimited
//values
function breakout_bits($value, $masks, $delimiter = " ", $none = "")
{
   if ($value == 0) return $none;
   
   $output = array();
   foreach ($masks as $mask => $bitname)
   {
      //if the value has the bit set for this mask
      if (($value & $mask) == $mask)
      {
         //then add the masks name to the output array
         $output[] = $bitname;
         
         //we have to unset that bit now because
         //it can actually be a combination of
         //two later items, I.E. "EARS" is a combination
         //of the left ear and right ear bit masks
         //and we don't want all 3 values in the list
         $value -= $mask; 
      }
   }
   
   //display NONE if there are no value bit masks
   if (!cb_count($output)) return $none;
   
   //delimit and return the output string
   return implode($delimiter, array_reverse($output));
}

//returns true if the database meets a minimum version number
function db_is_version($db_handle, $maria_vers, $percona_vers, $mysql_vers)
{
   //get database info
   $tpl = <<<TPL
      SELECT 
         @@VERSION AS VERSION,
         @@VERSION_COMMENT AS VERSION_COMMENT
      FROM DUAL;
TPL;
   
   //prepare query
   $query = $tpl;
   
   //get the results
   $result = $db_handle->query($query);

   if ($row = $db_handle->nextrow($result))
   {
      $target_vers = 0;
      $version = $row['VERSION'];
      $version_comment = $row['VERSION_COMMENT'];
      
      //user @@VERSION_COMMENT to determine if DB provider
      //is MariaDB, Percona or MySQL
      //Expected Values:
      //   Percona: "Percona Server (GPL), Release '11', Revision 'c1y2gr1df4a'"
      //   MariaDB: 'mariadb.org binary distribution'
      //   MySQL: 'MySQL Community Server (GPL)'

      //MariaDB
      if (is_numeric(stripos($version_comment, 'mariadb')))
      {
         $target_vers = $maria_vers;
      }
      
      //Percona
      elseif (is_numeric(stripos($version_comment, 'percona')))
      {
         $target_vers = $percona_vers;
      }
      
      //MySQL
      elseif (is_numeric(stripos($version_comment, 'mysql')))
      {
         $target_vers = $mysql_vers;
      }
      
      //is the real version greater than or equal to the target version
      if (version_compare($version, $target_vers) != -1)
      {
         return true;
      }
   }
   
   return false;
}

//starts an indexed timer 
function timer_start($index)
{ 
   global $timers;
   $timers[$index] = microtime();
} 

//sort arrays by sub element
function str_orderby($a, $b) {
   global $gcb_sort_dir;
   global $gcb_sort_col;
   if ($gcb_sort_dir == "ASC") {
      return strcmp($a[$gcb_sort_col], $b[$gcb_sort_col]);
   }
   else {
      return strcmp($b[$gcb_sort_col], $a[$gcb_sort_col]);
   }
}
function int_orderby($a, $b) {
   global $gcb_sort_dir;
   global $gcb_sort_col;
   if ($gcb_sort_dir == "ASC") {
      return $a[$gcb_sort_col] - $b[$gcb_sort_col];
   }
   else {
      return $b[$gcb_sort_col] - $a[$gcb_sort_col];
   }
}
function sort_by(&$array, $column, $direction = 'ASC', $type = 'string') {
   global $gcb_sort_dir;
   global $gcb_sort_col;
   
   //TODO, probably shouldn't be doing this with globals
   $gcb_sort_col = $column;
   $gcb_sort_dir = $direction;
   if ($type == 'string') {
      usort($array, "str_orderby");
   }
   else {
      usort($array, "int_orderby");
   }
}

//recieves an array of filters, returns where clause
function generate_where($filters, $divider = "AND") {
   //if it's not an array 
   if (!is_array($filters)) return "";
   
   if (!cb_count($filters)) return "";
   
   //build where
   $where = "WHERE ".implode(" ".$divider." ", $filters);
   
   return $where;
}

//recieves an array and a sub element
//returns a comma concatenated list of all of
//those values
function get_id_list($array, $key) {

   if (!is_array($array)) return "";
   
   $all_id = array();
   foreach($array as $row) {
      $all_id[] = $row[$key];
   }
   
   return implode(", ", $all_id);
}

//do a manual join of two db result sets
function manual_join($left, $leftKey, $right, $rightKey, $type = 'inner') {
   
   //return empty set if we dont have valid inputs
   if (!is_array($left) || !is_array($right)) return array();
   
   //right join
   //if its a right join just swap the left/right
   if ($type == 'right') {
      $temp = $left;
      $left = $right;
      $right = $temp;
      $temp = $leftKey;
      $leftKey = $rightKey;
      $rightKey = $temp;
      $type = 'left';
   }
   
   //left join
   $joined = array();
   if ($type == 'left') {
      foreach ($left as $row) {
         $keyVal = $row[$leftKey];
         //if this keyvalue exists in the right array, join them
         if (keyval_to_index($right, $rightKey, $keyVal, $index)) {
            $joined[] = array_merge($row, $right[$index]);
         }
         else {
            $joined[] = $row;
         }
      }
   }
   //inner join
   elseif ($type = 'inner') {
      foreach ($left as $row) {
         $keyVal = $row[$leftKey];
         //if this keyvalue exists in the right array, join them
         if (keyval_to_index($right, $rightKey, $keyVal, $index)) {
            $joined[] = array_merge($row, $right[$index]);
         }
      }
   }
   
   return $joined;
}

//finds a value in a column in an array and returns the index of the array element
function keyval_to_index($array, $key, $keyval, &$index) {
   if (!is_array($array)) return false;
   foreach ($array as $index => $row) {
      if ($row[$key] == $keyval) return true;
   }
   return false;
}

//returns how long the timer as been running in seconds
function timer_stop($index)
{
   global $timers;
   list($old_usec, $old_sec) = explode(' ',$timers[$index]);
   list($new_usec, $new_sec) = explode(' ',microtime());
   $old_mt = ((float)$old_usec + (float)$old_sec);
   $new_mt = ((float)$new_usec + (float)$new_sec);
   $timeout = sprintf("%01.6f",($new_mt - $old_mt));
   return $timeout;
}

//get a guild link for a character  
function getGuildLink ($guildname, $guildrank = "") {
   global $charbrowser_wrapped;
   global $blockguilddata;

   if ($guildname) { 
      if ($blockguilddata) {
         $output = "&lt;".$guildname."&gt;";
      }
      else {
         $output = "&lt;<a href='".(($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php")."?page=guild&guild=".$guildname."'>".$guildname."</a>&gt;";
      }
      if ($guildrank) {
         $output = $guildrank." of ".$output;
      }
      return $output;
   }
   else {
      return "";
   }
}  


function getAvatarImage($race, $gender, $face, $burried = 0) {
   if ($burried) return "gravestone.png";
   
   $tmp = "race_%s_gender_%s_face_%s.png";
   $race = intval($race);
   $gender = intval($gender);
   $face = intval($face);
   
   //clean up race and face
   //no idea why the client does this, but this
   //is how RoF2 picks the face to display based
   //on the face field from the character_data table
   switch ($race) {
      case CB_RACE_HUMAN: 
      case CB_RACE_BARBARIAN:
      case CB_RACE_ERUDITE:
      case CB_RACE_WOOD_ELF:
      case CB_RACE_DWARF:
      case CB_RACE_TROLL:
      case CB_RACE_OGRE:
      case CB_RACE_HALFLING:
      case CB_RACE_GNOME:
      case CB_RACE_IKSAR:   
      case CB_RACE_DRAKKIN: 
         //these races use face 1-8 directly, and for every other face they use face 8
         if ($face < 1 || $face > 8) $face = 8;
         if ($gender < 0 || $gender > 1) $gender = 0;
         break;
      case CB_RACE_HIGH_ELF:
      case CB_RACE_DARK_ELF:
      case CB_RACE_HALF_ELF:
      case CB_RACE_VAHSHIR: 
         //these races use face 1-8 directly, and for every other face they use face 8
         //until they get to face id 41, then they rotate through the 8 faces
         if (($face < 1 || $face > 8) && $face < 41) $face = 8;
         elseif ($face >= 41) $face = $face % 8;
         break;
      case CB_RACE_FROGLOK:    
         //this races use face 1-10 directly, and for every face after it rotates through that list
         if ($face < 1) $face = 10;
         else $face = $face % 10;
         break;
      default:
         //show a generic human for other races
         $race = 1;
         $gender = 0;
         $face = 8;
   }
   
   //clean up gender
   if ($gender < 0 || $gender > 1) $gender = 0;
   
   return sprintf($tmp, $race, $gender, $face);
}


//sanitize a string to prevent XSS
function xss_safe($string) {
   return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


//outputs the profile menu on the side
function output_profile_menu($charname, $curpage) {
   global $language;
   global $cb_template;
   global $cb_show_bots;
   
   //two sets of buttons, the profile ones which target the cur character, 
   //and the other which are just plain old vanilla links
   $profilebuttons = array(
      array( 'PAGE' => 'character', 'BUTTON_NAME' => $language['BUTTON_INVENTORY'], 'PERMISSION' => 1),
      array( 'PAGE' => 'aas', 'BUTTON_NAME' => $language['BUTTON_AAS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'leadership', 'BUTTON_NAME' => $language['BUTTON_LEADERSHIP'], 'PERMISSION' => 1),
      array( 'PAGE' => 'keys', 'BUTTON_NAME' => $language['BUTTON_KEYS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'flags', 'BUTTON_NAME' => $language['BUTTON_FLAGS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'skills', 'BUTTON_NAME' => $language['BUTTON_SKILLS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'corpses', 'BUTTON_NAME' => $language['BUTTON_CORPSES'], 'PERMISSION' => 1),
      array( 'PAGE' => 'factions', 'BUTTON_NAME' => $language['BUTTON_FACTION'], 'PERMISSION' => 1),
      array( 'PAGE' => 'bots', 'BUTTON_NAME' => $language['BUTTON_BOTS'], 'PERMISSION' => $cb_show_bots),
      array( 'PAGE' => 'bazaar', 'BUTTON_NAME' => $language['BUTTON_STORE'], 'PERMISSION' => 1),
      array( 'PAGE' => 'barter', 'BUTTON_NAME' => $language['BUTTON_BARTER'], 'PERMISSION' => 1),
      array( 'PAGE' => 'adventure', 'BUTTON_NAME' => $language['BUTTON_ADVENTURE'], 'PERMISSION' => 1),
      array( 'PAGE' => 'signaturebuilder', 'BUTTON_NAME' => $language['BUTTON_SIG'], 'PERMISSION' => 1),
      array( 'PAGE' => 'charmove', 'BUTTON_NAME' => $language['BUTTON_CHARMOVE'], 'PERMISSION' => 1),
   );
   
   $otherbuttons = array(
      array( 'BUTTON_NAME' => $language['BUTTON_BOOKMARK'], 'BUTTON_INDEX' => '#', 'BUTTON_TITLE' => $language['BUTTON_BOOKMARK'],  'BUTTON_ONCLICK' => 'cb_BookmarkThisPage();'),
   );
   
   $cb_template->set_filenames(array(
     'menu' => 'profile_menu.tpl')
   );
   
   foreach ($profilebuttons as $profilebutton) {
      if (!$profilebutton['PERMISSION']) continue;
      $cb_template->assign_block_vars( "profilebuttons", array(     
         'SWITCH_DIABLED' => ($profilebutton['PAGE'] == $curpage) ? "Disabled" : "",   
         'PAGE' => $profilebutton['PAGE'],
         'L_BUTTON_FACE' => $profilebutton['BUTTON_NAME'])
      );
   }
   
   foreach ($otherbuttons as $otherbutton) {
      $cb_template->assign_block_vars( "otherbuttons", array(     
         'SWITCH_DIABLED' => (false) ? "Disabled" : "", //placeholder for if the button is disabled
         'ONCLICK' => $otherbutton['BUTTON_ONCLICK'],
         'BUTTON_INDEX' => $otherbutton['BUTTON_INDEX'],
         'L_BUTTON_FACE' => $otherbutton['BUTTON_NAME'],
         'L_BUTTON_TITLE' => $otherbutton['BUTTON_TITLE'])
      );
   }

   $cb_template->assign_vars(array(     
      'CURPROFILE' => $charname,
      'BUTTON_COUNT' => cb_count($profilebuttons) + cb_count($otherbuttons),
      'L_PROFILE_MENU_TITLE' => $language['PROFILE_MENU_TITLE'])
   );
   
   $cb_template->pparse('menu');
}


function GetGuildPermissions($char_id) {
   global $guild_permissions;
   global $cbsql;
   global $charbrowser_is_admin_page;
  
   //if your wrap charbrowser in your own sites header
   //and footer. You can have your site override the
   //default permissions to always be enabled by setting 
   //$charbrowser_is_admin_page = true;
   //the intent of this is for charbrowser to inherit
   //your sites admin privileges
   //if it's set, return a permission array with 
   //everything enabled
   if ($charbrowser_is_admin_page) {
      return array(
         'mainpage'         => 0);
   }
 
   $tpl = <<<TPL
SELECT `value`
FROM `quest_globals` 
WHERE `character_id` = %d 
AND `name` = 'charbrowser_guild';
TPL;
   $query = sprintf($tpl, $char_id);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   { 
      $row = $cbsql->nextrow($result);
      if ($row['value'] == 1) return $guild_permissions['PUBLIC'];
      if ($row['value'] == 2) return $guild_permissions['PRIVATE'];
   }
   
   return $guild_permissions['ALL'];
}


function cb_generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = TRUE)
{
   global $language;

   $total_pages = ceil($num_items/$per_page);

   if ( $total_pages == 1 )
   {
      return '';
   }

   $on_page = floor($start_item / $per_page) + 1;

   $page_string = '';
   if ( $total_pages > 10 )
   {
      $init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

      for($i = 1; $i < $init_page_max + 1; $i++)
      {
         $page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . ($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
         if ( $i <  $init_page_max )
         {
            $page_string .= ", ";
         }
      }

      if ( $total_pages > 3 )
      {
         if ( $on_page > 1  && $on_page < $total_pages )
         {
            $page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';

            $init_page_min = ( $on_page > 4 ) ? $on_page : 5;
            $init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

            for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
            {
               $page_string .= ($i == $on_page) ? '<b>' . $i . '</b>' : '<a href="' . ($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
               if ( $i <  $init_page_max + 1 )
               {
                  $page_string .= ', ';
               }
            }

            $page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
         }
         else
         {
            $page_string .= ' ... ';
         }

         for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
         {
            $page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>'  : '<a href="' . ($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
            if( $i <  $total_pages )
            {
               $page_string .= ", ";
            }
         }
      }
   }
   else
   {
      for($i = 1; $i < $total_pages + 1; $i++)
      {
         $page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . ($base_url . "&amp;start=" . ( ( $i - 1 ) * $per_page ) ) . '">' . $i . '</a>';
         if ( $i <  $total_pages )
         {
            $page_string .= ', ';
         }
      }
   }

   if ( $add_prevnext_text )
   {
      if ( $on_page > 1 )
      {
         $page_string = ' <a href="' . ($base_url . "&amp;start=" . ( ( $on_page - 2 ) * $per_page ) ) . '">' . $language['SEARCH_PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
      }

      if ( $on_page < $total_pages )
      {
         $page_string .= '&nbsp;&nbsp;<a href="' . ($base_url . "&amp;start=" . ( $on_page * $per_page ) ) . '">' . $language['SEARCH_NEXT'] . '</a>';
      }

   }

   $page_string = $language['GOTO_PAGE'] . ' ' . $page_string;

   return $page_string;
   
}


//converts tics into a spell time
function tics_to_time($tics) {
   $tics *= 10;
   if ($tics > 59) {
      return floor($tics/60)."m";
   }
   
   return $tics."s";
}

//This plugs values from an array into a template
//the first parameter is a string template with values
//in it such as {X}, the second parm is an array with
//matching values as indexes and what they should
//be changed to, such as "X" => "255".
function QuickTemplate($cb_template, $values)
{
   //if the provided values aren't an array then
   //just return the template
   if (!is_array($values)) return $cb_template;
   
   //find and replace each value
   foreach($values as $find => $replace)
   {
      $cb_template = str_replace("{".$find."}", $replace, $cb_template);
   }
   
   return $cb_template;
}
?>