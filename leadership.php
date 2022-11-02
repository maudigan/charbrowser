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
 *   October 19, 2022 - Maudigan
 *      initial revision of the leadership window
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

//counts the maximum number of ranks for a leadership aa
function getTotalRanks($rankdata)
{
   $currank = 0;
   while($rankdata[$currank] != 0) 
   {
      $currank++;
   }
   return $currank;
}


//gets the cost of the next rank of the leadership aa
function getRankCost($rankdata, $currank)
{
   $cost = $rankdata[$currank];
   if ($cost == 0)
   {
      return '--';
   }
   
   return $cost;
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
if ($mypermission['leadership']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/

//GET THIS CHARS LEADERSHIP AA
$character_leadership_aas = $char->GetTable("character_leadership_abilities");

//calculate the display values for each group aa
$group_aa_abilities = array();
foreach($groupaa as $rankid => $rankname)
{
   //calculate all the values
   $dbcur = $character_leadership_aas[$rankid]['rank'];
   $cur = ($dbcur) ? $character_leadership_aas[$rankid]['rank'] : 0; 
   $cost = getRankCost($dbleadershipranks[$rankid], $cur);
   $max = getTotalRanks($dbleadershipranks[$rankid]);
  
   $group_aa_abilities[] = array(
      'NAME' => $rankname,
      'MAX' => $max,
      'CUR' => $cur,
      'COST' => $cost
   );
} 

//calculate the display values for each raid aa
$raid_aa_abilities = array();
foreach($raidaa as $rankid => $rankname)
{
   //calculate all the values
   $dbcur = $character_leadership_aas[$rankid]['rank'];
   $cur = ($dbcur) ? $character_leadership_aas[$rankid]['rank'] : 0; 
   $cost = getRankCost($dbleadershipranks[$rankid], $cur);
   $max = getTotalRanks($dbleadershipranks[$rankid]);
  
   $raid_aa_abilities[] = array(
      'NAME' => $rankname,
      'MAX' => $max,
      'CUR' => $cur,
      'COST' => $cost
   );
}  

//combine the two pages with an id to loop through later
$leadership_aa_abilities = array(
   1 => array( 'NAME' => $language['LEADERSHIP_TAB_1'], 'ABILITIES' => $group_aa_abilities),
   2 => array( 'NAME' => $language['LEADERSHIP_TAB_2'], 'ABILITIES' => $raid_aa_abilities)
);

 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_LEADERSHIP'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'leadership');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'leadership' => 'leadership_body.tpl')
);


$cb_template->assign_both_vars(array(  
   'NAME' => $name,
   'GROUP_LEADERSHIP_POINTS' => $char->GetValue("group_leadership_points").$language['GROUP_POINTS_OF'], 
   'RAID_LEADERSHIP_POINTS' => $char->GetValue("raid_leadership_points").$language['RAID_POINTS_OF'])
);

$cb_template->assign_vars(array(  
   'L_LEADERSHIP' => $language['LEADERSHIP'], 
   'L_TITLE' => $language['AAS_TITLE'],
   'L_CUR_MAX' => $language['AAS_CUR_MAX'],
   'L_COST' => $language['AAS_COST'],
   'L_GROUP_POINTS' => $language['GROUP_POINTS'],
   'L_RAID_POINTS' => $language['RAID_POINTS'],
   'L_DONE' => $language['BUTTON_DONE'])
);


//OUTPUT THE AA TABS/DIVS/DATA
//TODO don't hardcode these colors. It should maybe  
//     get set using two stylesheet flags... maybe
$Color = "7b714a"; //first tab is diff com_addref
foreach ($leadership_aa_abilities  as $id => $aatab)
{
   //make the tab/div the subtypes are displayed in
   $cb_template->assign_block_vars("tabs", array( 
      'COLOR' => $Color,      
      'ID' => $id,
      'TEXT' => $aatab['NAME'])
   );
   
   //the info displayed in the div
   foreach($aatab['ABILITIES'] as $ability)
   {
      $cb_template->assign_both_block_vars("tabs.aas", $ability);
   }
   
   //adjust the styles for the rest of the tabs
   $Color = "FFFFFF";
}

 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('leadership');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>