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
 *
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['corpses']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get corpses
$tpl = <<<TPL
SELECT zone_id, 
       is_buried, x, y, 
       is_rezzed, time_of_death 
FROM character_corpses 
WHERE charid = %s 
ORDER BY time_of_death DESC
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
if (!$cbsql->rows($result)) cb_message_die($language['CORPSE_CORPSES']." - ".$name,$language['MESSAGE_NO_CORPSES']);

$corpses = array();
while ($row = $cbsql->nextrow($result)) {
   //get info about the zone the corpse is in
   //from the content database
   $tpl = <<<TPL
   SELECT short_name
   FROM zone 
   WHERE zoneidnumber = '%s' 
TPL;
   $query = sprintf($tpl, $row['zone_id']);
   $short_name = $cbsql_content->field_query('short_name', $query);

   //add zone short_name to corpse results
   $row['short_name'] = $short_name;
   $corpses[] = $row;
}
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_CORPSE'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'corpse');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'corpse' => 'corpse_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'NAME' => $name)
);
$cb_template->assign_vars(array( 
   'L_REZZED' => $language['CORPSE_REZZED'],
   'L_TOD' => $language['CORPSE_TOD'],
   'L_LOC' => $language['CORPSE_LOC'],
   'L_MAP' => $language['CORPSE_MAP'],
   'L_CORPSES' => $language['CORPSE_CORPSES'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//dump corpses
foreach($corpses as $corpse) {

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
   
   $cb_template->assign_both_block_vars("corpses", array( 
      'REZZED' => ((!$corpse['is_rezzed']) ? "0":"1"),      
      'TOD' => $corpse['time_of_death'],
      'LOC' => (($corpse['is_buried']) ?  "(buried)":"(".floor($corpse['y']).", ".floor($corpse['x']).")"),
      'ZONE' => (($corpse['is_buried']) ?  "shadowrest":$corpse['short_name']),
      'ZONE_ID' => $corpse["zone_id"],
      'LINK_MAP' => $link_to_map,
      'LINK_ZONE' => $link_to_zone,
      'X' => floor($corpse['y']),
      'Y' => floor($corpse['x']))
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('corpse');

$cb_template->destroy;
 
include(__DIR__ . "/include/footer.php");
?>