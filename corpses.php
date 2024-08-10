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
 *   September 26, 2014 - Maudigan 
 *      Updated character table name 
 *      repaired double carriage returns through whole file
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   May 30, 2016 - Maudigan
 *      Swapped from player_corpses to character_corpses; this was part of
 *      blob conversion that I had missed
 *   October 3, 2016 - Maudigan
 *      Made the corpse links customizable
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 3, 2020 - Maudigan
 *     changes to minimize database access
 *   November 1, 2022 - Maudigan
 *     Updated the display to be more aesthetically pleasing
 *     Updated the display to show if there are items on the corpse
 *
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
//define this as an entry point to unlock includes
if ( !defined('INCHARBROWSER') ) 
{
   define('INCHARBROWSER', true);
}
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
       SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/
$charName = preg_Get_Post('char', '/^[a-zA-Z]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR'], true);

//character initializations
$char = new Charbrowser_Character($charName, $showsoftdelete, $charbrowser_is_admin_page); //the Charbrowser_Character class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');

//block view if user level doesnt have permission
if ($char->Permission('corpses')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get corpses
$tpl = <<<TPL
SELECT corpses.id, 
       corpses.zone_id,
       corpses.is_buried, 
       corpses.x, 
       corpses.y, 
       corpses.is_rezzed, 
       corpses.time_of_death,
       corpses.race,
       corpses.gender,
       corpses.face,
       corpses.platinum + corpses.gold + corpses.silver + corpses.platinum AS coin_count,
       items.item_count
FROM character_corpses corpses
LEFT JOIN (
   SELECT COUNT(*) AS item_count,
          corpse_id
   FROM character_corpse_items
   GROUP BY corpse_id
) items
ON items.corpse_id = corpses.id
WHERE corpses.charid = %s 
ORDER BY corpses.time_of_death DESC
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
if (!$cbsql->rows($result)) $cb_error->message_die($language['CORPSES_CORPSES']." - ".$name,$language['MESSAGE_NO_CORPSES']);

$corpses = $cbsql->fetch_all($result);  
$zone_ids = get_id_list($corpses, 'zone_id');

//get zone data
$tpl = <<<TPL
SELECT short_name, long_name, zoneidnumber
FROM zone 
WHERE zoneidnumber IN (%s) 
TPL;
$query = sprintf($tpl, $zone_ids);
$result = $cbsql_content->query($query);

$zones = $cbsql_content->fetch_all($result);  

//join zones and corpse queries
$corpse_zones = manual_join($corpses, 'zone_id', $zones, 'zoneidnumber', 'inner');


 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_CORPSES'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'corpses');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'corpses' => 'corpses_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'NAME' => $name)
);
$cb_template->assign_vars(array( 
   'ROOT_URL' => $charbrowser_root_url,
   
   'L_CORPSES' => $language['CORPSES_CORPSES'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//dump corpses
foreach($corpse_zones as $corpse) {

   //prepare the link to the map
   $find = array(
      'ZONE_SHORTNAME'  => $corpse['short_name'],
      'ZONE_ID'         => $corpse["zone_id"],
      'TEXT'            => $name."`s%20Corpse",
      'X'               => floor($corpse['x']),
      'Y'               => floor($corpse['y'])
   );
   $link_to_map = QuickTemplate($link_map, $find);
   
   //prepare the link to the zone
   $find = array(
      'ZONE_SHORTNAME'  => $corpse['short_name'],
      'ZONE_ID'         => $corpse["zone_id"]
   );
   $link_to_zone = QuickTemplate($link_zone, $find);
   
   $tod = strtotime($corpse['time_of_death']);
   
   $hasitems = ($corpse['item_count'] > 0 || $corpse['coin_count'] > 0);
   
   if ($hasitems) 
   {
      $itemarray = array();
      if ($corpse['coin_count'] > 0)       $itemarray[] = $language['CORPSES_COIN'];
      if ($corpse['item_count'] > 1)       $itemarray[] = $language['CORPSES_ITEMS'];
      elseif ($corpse['item_count'] == 1)  $itemarray[] = $language['CORPSES_ITEM'];
      $itemlist = implode(" ".$language['CORPSES_AND']." ", $itemarray);
      $hasitems_text = sprintf($language['CORPSES_STUFF'], $itemlist);
      $hasitems_class = 'CB_Has_Items';
   }
   else
   {
      $hasitems_text = "";
      $hasitems_class = 'CB_No_Items';
   }
   
   if ($corpse['is_rezzed'])
   {
      $avatar_title = $language['CORPSES_REZZED'];
      $rezzed_class = "CB_Avatar_Rezzed";
   }
   else
   {
      $avatar_title = $language['CORPSES_UNREZZED'];
      $rezzed_class = "";
   }
   if ($corpse['is_buried'])
   {
      $avatar_title .= $language['CORPSES_BURIED'];
   }
   else
   {
      $avatar_title .= ".";
   }
   
   $cb_template->assign_both_block_vars("corpses", array( 
      'REZZED' => (($corpse['is_rezzed']) ? "1":"0"), 
      'REZZED_STYLE' => $rezzed_class, 
      'AVATAR_TITLE' => $avatar_title,
      'TOD_RAW' => $corpse['time_of_death'],
      'TOD' =>  date('l, j F Y g:i A', $tod),
      'TOD_DAY' =>  date('l', $tod),
      'TOD_DATE' =>  date('F j, Y', $tod),
      'TOD_TIME' =>  date('g:i A', $tod),
      'CORPSE_ID' => $corpse['id'],
      'ZONE_LONG_NAME' => $corpse['long_name'],
      'ZONE_SHORT_NAME' => $corpse['short_name'],
      'HAS_ITEMS_CLASS' => $hasitems_class,
      'HAS_ITEMS_TITLE' => $hasitems_text,
      'LOC' => "(".floor($corpse['y']).", ".floor($corpse['x']).")",
      'ZONE_ID' => $corpse['zoneidnumber'],
      'AVATAR_IMG' => getAvatarImage($corpse['race'], $corpse['gender'], $corpse['face'], $corpse['is_buried']),
      'LINK_MAP' => $link_to_map,
      'LINK_ZONE' => $link_to_zone,
      'X' => floor($corpse['y']),
      'Y' => floor($corpse['x']))
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('corpses');

$cb_template->destroy();
 
include(__DIR__ . "/include/footer.php");
?>