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
 *
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once("language.php");
include_once("config.php");

//holds timers
$timers = array();

//starts an indexed timer 
function timer_start($index)
{ 
   global $timers;
   $timers[$index] = microtime();
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


function GetPermissions($gm, $anonlevel, $char_id) {
   global $permissions;
 
   $tpl = <<<TPL
SELECT `value`
FROM `quest_globals` 
WHERE `charid` = %d 
AND `name` = 'charbrowser_profile';
TPL;
   $query = sprintf($tpl, $char_id);
   $result = cbsql_query($query);
   if(cbsql_rows($result))
   { 
      $row = cbsql_nextrow($result);
      if ($row['value'] == 1) return $permissions['PUBLIC'];
      if ($row['value'] == 2) return $permissions['PRIVATE'];
   }

   if ($gm) return $permissions['GM'];
   if ($anonlevel == 2)  return $permissions['ROLEPLAY'];
   if ($anonlevel == 1)  return $permissions['ANON'];
   return $permissions['ALL'];
}

function message_die($dietitle, $text) {
   global $language;
   global $template;
   
   //drop page
   $d_title = " - ".$dietitle;
   include("include/header.php");
   
   $template->set_filenames(array(
     'message' => 'message_body.tpl')
   );
   
   $template->assign_both_vars(array(  
      'DIETITLE' => $dietitle,
      'TEXT' => $text)
   );
   $template->assign_vars(array(      
      'L_BACK' => $language['BUTTON_BACK'])
   );
   
   $template->pparse('message');
   
   //dump footer
   include("include/footer.php");
   exit();
}

function message($title, $text) {
   global $language;
   global $template;
   $template->set_filenames(array(
      'message' => 'message_body.tpl')
   );

   $template->assign_both_vars(array(  
      'TITLE' => $title,
      'TEXT' => $text)
   );
   $template->assign_vars(array( 
      'L_BACK' => $language['BUTTON_BACK'])
   );

   $template->pparse('message');

}


function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = TRUE)
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
?>