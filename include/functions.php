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
function generate_where($filters) {
   $where = "";
   $divider = "WHERE ";

   if (is_array($filters)) {
      foreach ($filters as $filter) {
         $where .= $divider.$filter;
         $divider = " AND ";
      }
   }
   
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


function getAvatarImage($race, $gender, $face) {
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
   
   $menubuttons = array(
      array( 'PAGE' => 'character', 'BUTTON_NAME' => $language['BUTTON_INVENTORY'], 'PERMISSION' => 1),
      array( 'PAGE' => 'aas', 'BUTTON_NAME' => $language['BUTTON_AAS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'keys', 'BUTTON_NAME' => $language['BUTTON_KEYS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'flags', 'BUTTON_NAME' => $language['BUTTON_FLAGS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'skills', 'BUTTON_NAME' => $language['BUTTON_SKILLS'], 'PERMISSION' => 1),
      array( 'PAGE' => 'corpse', 'BUTTON_NAME' => $language['BUTTON_CORPSE'], 'PERMISSION' => 1),
      array( 'PAGE' => 'factions', 'BUTTON_NAME' => $language['BUTTON_FACTION'], 'PERMISSION' => 1),
      array( 'PAGE' => 'bots', 'BUTTON_NAME' => $language['BUTTON_BOTS'], 'PERMISSION' => $cb_show_bots),
      array( 'PAGE' => 'bazaar', 'BUTTON_NAME' => $language['BUTTON_STORE'], 'PERMISSION' => 1),
      array( 'PAGE' => 'signaturebuilder', 'BUTTON_NAME' => $language['BUTTON_SIG'], 'PERMISSION' => 1),
      array( 'PAGE' => 'charmove', 'BUTTON_NAME' => $language['BUTTON_CHARMOVE'], 'PERMISSION' => 1),
   );
   
   $cb_template->set_filenames(array(
     'menu' => 'profile_menu.tpl')
   );
   
   foreach ($menubuttons as $menubutton) {
      if (!$menubutton['PERMISSION']) continue;
      $cb_template->assign_block_vars( "menuitems", array(     
         'SWITCH_DIABLED' => ($menubutton['PAGE'] == $curpage) ? "Disabled" : "",   
         'PAGE' => $menubutton['PAGE'],
         'L_BUTTON_FACE' => $menubutton['BUTTON_NAME'])
      );
   }

   $cb_template->assign_vars(array(     
      'CURPROFILE' => $charname,
      'L_BOOKMARK' => $language['BUTTON_BOOKMARK'])
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
WHERE `charid` = %d 
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


function GetPermissions($gm, $anonlevel, $char_id) {
   global $permissions;
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
         'inventory'         => 0,
         'coininventory'     => 0,
         'coinbank'          => 0,
         'coinsharedbank'    => 0,
         'bags'              => 0,
         'bank'              => 0,
         'sharedbank'        => 0,
         'corpses'           => 0,
         'flags'             => 0,
         'AAs'               => 0,
         'factions'          => 0,
         'advfactions'       => 0,
         'skills'            => 0,
         'languageskills'    => 0,
         'keys'              => 0,
         'signatures'        => 0);
   }
 
   $tpl = <<<TPL
SELECT `value`
FROM `quest_globals` 
WHERE `charid` = %d 
AND `name` = 'charbrowser_profile';
TPL;
   $query = sprintf($tpl, $char_id);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   { 
      $row = $cbsql->nextrow($result);
      if ($row['value'] == 1) return $permissions['PUBLIC'];
      if ($row['value'] == 2) return $permissions['PRIVATE'];
   }

   if ($gm) return $permissions['GM'];
   if ($anonlevel == 2)  return $permissions['ROLEPLAY'];
   if ($anonlevel == 1)  return $permissions['ANON'];
   return $permissions['ALL'];
}

function cb_message_die($dietitle, $text) {
   global $language;
   global $cb_template;
   //these have to be included to pass through to header.php
   global $charbrowser_root_url;
   global $charbrowser_wrapped;
   global $charbrowser_simple_header;
   global $charbrowser_image_script;
   
   //output error as an image
   if ($charbrowser_image_script) {
      $defaultcolor = array( 'r'=>255, 'g'=>255, 'b'=>255 );
      $imgwidth = 500;
      $imgheight = 100;
      $error_image = imagecreatetruecolor($imgwidth, $imgheight);
      $error_color = imagecolorallocate($error_image, $defaultcolor['r'], $defaultcolor['g'], $defaultcolor['b']);
      imagestring($error_image, 5, 10, 30, $dietitle, $error_color);
      imagestring($error_image, 2, 10, 50, $text, $error_color); 
      ob_clean(); //make sure we haven't sent a text header
      header("Content-Type: image/png"); 
      imagepng($error_image); 
      ImageDestroy($error_image);
      exit();
   }
   
   //drop page
   $d_title = " - ".$dietitle;
   include(__DIR__ . "/header.php");
   
   $cb_template->set_filenames(array(
     'message' => 'message_body.tpl')
   );
   
   $cb_template->assign_both_vars(array(  
      'DIETITLE' => $dietitle,
      'TEXT' => $text)
   );
   $cb_template->assign_vars(array(      
      'L_BACK' => $language['BUTTON_BACK'])
   );
   
   $cb_template->pparse('message');
   
   //dump footer
   include(__DIR__ . "/footer.php");
   exit();
}

function cb_message($title, $text) {
   global $language;
   global $cb_template;
   $cb_template->set_filenames(array(
      'message' => 'message_body.tpl')
   );

   $cb_template->assign_both_vars(array(  
      'TITLE' => $title,
      'TEXT' => $text)
   );
   $cb_template->assign_vars(array( 
      'L_BACK' => $language['BUTTON_BACK'])
   );

   $cb_template->pparse('message');

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

   $page_string = $lang['Goto_page'] . ' ' . $page_string;

   return $page_string;
   
}

function IsAlphaSpace($str)
{
   $old = Array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", " ");
   $new = Array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
   if (str_replace($old, $new, $str) == "")
   {
      return (true);
   }
   else
   {
      return (false);
   }
}

function IsAlphaNumericSpace($str)
{
   $old = Array(" ", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
   $new = Array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
   if (str_replace($old, $new, $str) == "")
   {
      return (true);
   }
   else
   {
      return (false);
   }
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