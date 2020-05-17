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
 *   November 24, 2013 - Maudigan
 *   Updated query to be compatible with the new way factions are stored in
 *     the database
 *   General code/comment/whitespace cleanup
 *   September 26, 2014 - Maudigan
 *      Updated character table name
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences.
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the faction links customizable
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 4, 2020 - Maudigan
 *     clean up of the content/character data join
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
             SUPPORT FUNCTIONS
*********************************************/
//converts faction values into a the string from the language file.
function FactionToString($character_value) {
   global $language;
   if($character_value >=  1101) return $language['FACTION_ALLY'];
   if($character_value >=   701 && $character_value <= 1100) return $language['FACTION_WARMLY'];
   if($character_value >=   401 && $character_value <=  700) return $language['FACTION_KINDLY'];
   if($character_value >=   101 && $character_value <=  400) return $language['FACTION_AMIABLE'];
   if($character_value >=     0 && $character_value <=  100) return $language['FACTION_INDIFF'];
   if($character_value >=  -100 && $character_value <=   -1) return $language['FACTION_APPR'];
   if($character_value >=  -700 && $character_value <= -101) return $language['FACTION_DUBIOUS'];
   if($character_value >=  -999 && $character_value <= -701) return $language['FACTION_THREAT'];
   if($character_value <= -1000) return $language['FACTION_SCOWLS'];
   return $language['FACTION_INDIFF'];
}


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
if ($mypermission['factions']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get content factions from the content db
$tpl = <<<TPL
SELECT fl.id, 
       fl.name, 
       IFNULL(fl.base, 0) AS base, 
       IFNULL(flmc.mod, 0) AS classmod, 
       IFNULL(flmr.mod, 0) AS racemod, 
       IFNULL(flmd.mod, 0) AS deitymod
FROM faction_list AS fl 
LEFT JOIN faction_list_mod AS flmc 
       ON fl.id = flmc.faction_id 
      AND (flmc.mod_name = 'c%d') 
LEFT JOIN faction_list_mod AS flmr 
       ON fl.id = flmr.faction_id 
      AND (flmr.mod_name = 'r%d') 
LEFT JOIN faction_list_mod AS flmd 
       ON fl.id = flmd.faction_id 
      AND (flmd.mod_name = 'd%d') 
ORDER BY name ASC 
TPL;

$query = sprintf(
    $tpl,
    $char->GetValue('class'),
    $char->GetValue('race'),
    ($char->GetValue('deity') == 396) ? "140" : $char->GetValue('deity')
);
$result = $cbsql_content->query($query);
$content_factions = $cbsql_content->fetch_all($result);

//get the characters factions from the player db
$tpl = <<<TPL
SELECT current_value,
       faction_ID
FROM faction_values 
WHERE char_id = '%s' 
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
$character_factions = $cbsql->fetch_all($result);


//DO A MANUAL JOIN OF THE RESULTS
$joined_factions = manual_join($content_factions, 'id', $character_factions, 'faction_ID', 'left');



/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_FACTIONS'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'factions');


/*********************************************
              POPULATE BODY
*********************************************/
if (!$mypermission['advfactions']) {
   $cb_template->set_filenames(array(
      'factions' => 'factions_advanced_body.tpl')
   );
}
else {
   $cb_template->set_filenames(array(
      'factions' => 'factions_basic_body.tpl')
   );
}

$cb_template->assign_both_vars(array(
   'NAME'        => $name)
);
$cb_template->assign_vars(array(
   'L_FACTIONS'  => $language['FACTION_FACTIONS'],
   'L_NAME'      => $language['FACTION_NAME'],
   'L_FACTION'   => $language['FACTION_FACTION'],
   'L_BASE'      => $language['FACTION_BASE'],
   'L_CHAR'      => $language['FACTION_CHAR'],
   'L_CLASS'     => $language['FACTION_CLASS'],
   'L_RACE'      => $language['FACTION_RACE'],
   'L_DEITY'     => $language['FACTION_DEITY'],
   'L_TOTAL'     => $language['FACTION_TOTAL'],
   'L_DONE'      => $language['BUTTON_DONE'])
);

foreach($joined_factions as $faction) {
   $charmod = intval($faction['current_value']);
   $total = $faction['base'] + $charmod + $faction['classmod'] + $faction['racemod'] + $faction['deitymod'];
   $cb_template->assign_both_block_vars("factions", array(
      'ID'      => $faction['id'],
      'LINK' => QuickTemplate($link_faction, array('FACTION_ID' => $faction['id'])),
      'NAME'    => $faction['name'],
      'FACTION' => FactionToString($total),
      'BASE'    => $faction['base'],
      'CHAR'    => $charmod,
      'CLASS'   => $faction['classmod'],
      'RACE'    => $faction['racemod'],
      'DEITY'   => $faction['deitymod'],
      'TOTAL'   => $total)
   );
}


/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('factions');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>
