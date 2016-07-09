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
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once("include/config.php");
include_once("include/profile.php");
include_once("include/global.php");
include_once("include/language.php");
include_once("include/functions.php");
include_once("include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations
$char = new profile($charName); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['corpses']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get corpses
$tpl = <<<TPL
SELECT z.short_name, z.zoneidnumber, 
       cc.is_buried, cc.x, cc.y, 
       cc.is_rezzed, cc.time_of_death 
FROM character_corpses cc
JOIN zone z
  ON z.zoneidnumber = cc.zone_id 
WHERE cc.charid = %s 
ORDER BY cc.time_of_death DESC
TPL;
$query = sprintf($tpl, $charID);
$result = cbsql_query($query);
if (!cbsql_rows($result)) message_die($language['CORPSE_CORPSES']." - ".$name,$language['MESSAGE_NO_CORPSES']);
$corpses = array();
while ($row = cbsql_nextrow($result)) {
   $corpses[] = $row;
}
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_CORPSE'];
include("include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$template->set_filenames(array(
   'corpse' => 'corpse_body.tpl')
);

$template->assign_both_vars(array(  
   'NAME' => $name)
);
$template->assign_vars(array( 
   'L_REZZED' => $language['CORPSE_REZZED'],
   'L_TOD' => $language['CORPSE_TOD'],
   'L_LOC' => $language['CORPSE_LOC'],
   'L_MAP' => $language['CORPSE_MAP'],
   'L_CORPSES' => $language['CORPSE_CORPSES'],
   'L_AAS' => $language['BUTTON_AAS'],
   'L_KEYS' => $language['BUTTON_KEYS'],
   'L_FLAGS' => $language['BUTTON_FLAGS'],
   'L_SKILLS' => $language['BUTTON_SKILLS'],
   'L_CORPSE' => $language['BUTTON_CORPSE'],
   'L_FACTION' => $language['BUTTON_FACTION'],
   'L_BOOKMARK' => $language['BUTTON_BOOKMARK'],
   'L_INVENTORY' => $language['BUTTON_INVENTORY'],
   'L_CHARMOVE' => $language['BUTTON_CHARMOVE'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//dump corpses
foreach($corpses as $corpse) {
   $template->assign_both_block_vars("corpses", array( 
      'REZZED' => ((!$corpse['is_rezzed']) ? "0":"1"),      
      'TOD' => $corpse['time_of_death'],
      'LOC' => (($corpse['is_buried']) ?  "(buried)":"(".floor($corpse['y']).", ".floor($corpse['x']).")"),
      'ZONE' => (($corpse['is_buried']) ?  "shadowrest":$corpse['short_name']),
      'ZONE_ID' => $corpse["zoneidnumber"],
      'X' => floor($corpse['y']),
      'Y' => floor($corpse['x']))
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$template->pparse('corpse');

$template->destroy;
 
include("include/footer.php");
?>