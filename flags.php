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
 *   March 1, 2011 
 *      Planes of Power updated to reflect current PEQ Quest SVN flagging 
 *      Gates of Discord updated through Qvic access per PEQ Quest SVN flagging 
 *      Gates of Discord updated through Txevu assuming PEQ design does not change 
 *   August 1, 2011
 *      Fixed misprint on GOD flag, KT_2
 *   March 19, 2012
 *      Fixed misprint on GOD flag, KT_3
 *   November 17, 2013 - Sorvani
 *      Fixed bad getflag conditions in sewer 2/3/4 sections
 *      Fixed bad language array index in sewer 4 section
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
 *   May 17, 2017 - Maudigan
 *      Added omens of war flags.
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
             SUPPORT FUNCTIONS
*********************************************/
//check a quest global
function getflag($condition, $flagname) { 
   global $quest_globals;    
   if (!array_key_exists($flagname,$quest_globals)) return 0; 
   if ($quest_globals[$flagname]<$condition) return 0; 
   return 1; 
} 


//check a quest global bit
function getbitflag($bitset, $flagname) { 
   global $quest_globals;    
   if (!array_key_exists($flagname,$quest_globals)) return 0; 
   if ($quest_globals[$flagname] & $bitset) return 1; 
   return 0; 
} 


//check a zoneflag
function getzoneflag($zoneid) { 
   global $zone_flags;      
   if (!in_array($zoneid, $zone_flags)) return 0; 
   return 1; 
} 
  
 
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
if ($mypermission['flags']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']); 
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get quest globals from the db
$tpl = <<<TPL
SELECT name, value 
FROM quest_globals 
WHERE charid = %s
TPL;
$query = sprintf($tpl, $charID);
$result = cbsql_query($query);
$quest_globals = array();
while($row = cbsql_nextrow($result)) 
   $quest_globals[$row['name']] = $row['value']; 

//get zone flags from the db
$tpl = <<<TPL
SELECT zoneID 
FROM zone_flags 
WHERE charID = %s
TPL;
$query = sprintf($tpl, $charID);
$result = cbsql_query($query);
$zone_flags = array();
while($row = cbsql_nextrow($result)) 
   $zone_flags[] = $row['zoneID']; 
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_FLAGS']; 
include("include/header.php"); 
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$template->set_filenames(array( 
   'flags' => 'flags_body.tpl') 
); 


$template->assign_vars(array(  
   'NAME' => $name, 

   'L_DONE' => $language['BUTTON_DONE'], 
   'L_AAS' => $language['BUTTON_AAS'], 
   'L_KEYS' => $language['BUTTON_KEYS'],
   'L_FLAGS' => $language['BUTTON_FLAGS'], 
   'L_SKILLS' => $language['BUTTON_SKILLS'], 
   'L_CORPSE' => $language['BUTTON_CORPSE'], 
   'L_FACTION' => $language['BUTTON_FACTION'], 
   'L_BOOKMARK' => $language['BUTTON_BOOKMARK'], 
   'L_INVENTORY' => $language['BUTTON_INVENTORY'], 
   'L_CHARMOVE' => $language['BUTTON_CHARMOVE'],  
   'L_FLAGS' => $language['FLAG_FLAGS']) 
); 

//because they enabled the level bypass and the fact that clicking the door is what sets your zone flag. 
//this will also be important when the 85/15 raid rule is implemented for letting people into zones. 
//for most of the PoP zones, we can not just check the zone flag to know if we have the flag. 
//for each zone i used the zone flag in combination with enough flags for each zone that it would not show erroneously. 
//for some zones it is only 1 other flag, for others it was multiple other flags. 

// use HasFlag in if statement and then set the $template then reuse $HasFlag 
$HasFlag = 0; 


/*********************************************
              MAIN MENUS
*********************************************/

//POP
$template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_PoP']) ); 

if (getzoneflag(221) && getflag(1, "pop_pon_hedge_jezith") && getflag(1, "pop_pon_construct")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 1, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoNB']) ); 

if (getzoneflag(214) && getflag(1, "pop_poi_behometh_preflag") && getflag(1, "pop_poi_behometh_flag")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 2, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTactics']) ); 

if (getzoneflag(200) && getflag(1, "pop_pod_elder_fuirstel")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 3, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_CoD']) ); 

if (getzoneflag(208) && getflag(1, "pop_poj_valor_storms")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 4, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoSPoV']) ); 

if (getzoneflag(211) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pov_aerin_dar")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 5, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_HoHA']) ); 

if (getzoneflag(209) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pos_askr_the_lost_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 6, 'FLAG' =>  $HasFlag, 'TEXT' => $language['FLAG_PoP_BoT']) ); 

if (getzoneflag(220) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pov_aerin_dar") && getflag(1, "pop_hoh_faye") && getflag(1, "pop_hoh_trell") && getflag(1, "pop_hoh_garn")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 7, 'FLAG' =>  $HasFlag, 'TEXT' => $language['FLAG_PoP_HoHB']) ); 

if (getzoneflag(207) && getflag(1, "pop_pod_elder_fuirstel") && getflag(1, "pop_ponb_poxbourne") && getflag(1, "pop_cod_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 8, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTorment']) ); 

if (getzoneflag(212) && getflag(1, "pop_poi_behometh_flag") && getflag(1, "pop_tactics_tallon") && getflag(1, "pop_tactics_vallon") && getflag(1, "pop_hohb_marr") && getflag(1, "pop_pot_saryrn_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 9, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_SolRoTower']) ); 

if (getzoneflag(217) && getflag(1, "pop_poi_behometh_flag") && getflag(1, "pop_tactics_tallon") && getflag(1, "pop_tactics_vallon") && getflag(1, "pop_hohb_marr") && getflag(1, "pop_tactics_ralloz") && getflag(1, "pop_sol_ro_arlyxir") && getflag(1, "pop_sol_ro_dresolik") && getflag(1, "pop_sol_ro_jiva") && getflag(1, "pop_sol_ro_rizlona") && getflag(1, "pop_sol_ro_xuzl") && getflag(1, "pop_sol_ro_solusk")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 10, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoFire']) ); 

if (getzoneflag(216) && getflag(1, "pop_elemental_grand_librarian")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 11, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoAirEarthWater']) ); 

if (getflag(1, "pop_time_maelin") && getflag(1, "pop_fire_fennin_projection") && getflag(1, "pop_wind_xegony_projection") && getflag(1, "pop_water_coirnav_projection") && getflag(1, "pop_eartha_arbitor_projection") && getflag(1, "pop_earthb_rathe")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 12, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTime']) ); 


//GoD
$template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_GoD']) ); 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 13, 'FLAG' => getflag(1,"god_vxed_access"), 'TEXT' => $language['FLAG_GoD_Vxed']) ); 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 14, 'FLAG' => getflag(1,"god_tipt_access"), 'TEXT' => $language['FLAG_GoD_Tipt']) ); 

if (getzoneflag(293) && getflag(1, "god_vxed_access") && getflag(1, "god_tipt_access") && getflag(1, "god_kodtaz_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 15, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_KT_1']) ); 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 16, 'FLAG' => getflag(12,"ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_R3']) ); 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 17, 'FLAG' => getflag(14,"ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_R4']) ); 

if (getzoneflag(295) && getflag(1, "god_qvic_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 18, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_Qvic_1']) ); 

if (getzoneflag(297) && getflag(1, "god_txevu_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 19, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_Txevu_1']) ); 


//OOW
$template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_OOW']) ); 

if (getflag(63, "mpg_group_trials")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 20, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_OOW_MPG']) ); 

//TODO get criteria for MPG, zone flags?
if (getflag(63, "mpg_raid_trials") && getflag(1, "oow_rss_taromani_insignias")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 21, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_OOW_COA']) ); 



/*********************************************
           SECONDARY/SUB MENUS POP
*********************************************/
//PoN B 
$template->assign_both_block_vars( "head" , array( 'ID' => 1, 'NAME' => $language['FLAG_PoP_PoNB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
//Tactics 
$template->assign_both_block_vars( "head" , array( 'ID' => 2, 'NAME' => $language['FLAG_PoP_PoTactics']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_dragon"), 'TEXT' => $language['FLAG_PoP_Xana']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
//CoD 
$template->assign_both_block_vars( "head" , array( 'ID' => 3, 'NAME' => $language['FLAG_PoP_CoD']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
//Valor & Storms 
$template->assign_both_block_vars( "head" , array( 'ID' => 4, 'NAME' => $language['FLAG_PoP_PoSPoV']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
//HoH A 
$template->assign_both_block_vars( "head" , array( 'ID' => 5, 'NAME' => $language['FLAG_PoP_HoHA']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
//BoT 
$template->assign_both_block_vars( "head" , array( 'ID' => 6, 'NAME' => $language['FLAG_PoP_BoT']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "pop_pos_askr_the_lost"), 'TEXT' => $language['FLAG_PoP_Askr1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pos_askr_the_lost_final"), 'TEXT' => $language['FLAG_PoP_Askr2']) ); 
//HoH B 
$template->assign_both_block_vars( "head" , array( 'ID' => 7, 'NAME' => $language['FLAG_PoP_HoHB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_faye"), 'TEXT' => $language['FLAG_PoP_Faye']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_trell"), 'TEXT' => $language['FLAG_PoP_Trell']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_garn"), 'TEXT' => $language['FLAG_PoP_Garn']) ); 
//Torment 
$template->assign_both_block_vars( "head" , array( 'ID' => 8, 'NAME' => $language['FLAG_PoP_PoTorment']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_terris"), 'TEXT' => $language['FLAG_PoP_TT']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_poxbourne"), 'TEXT' => $language['FLAG_PoP_PostTerris']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_preflag"), 'TEXT' => $language['FLAG_PoP_Carpin']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_bertox"), 'TEXT' => $language['FLAG_PoP_Bertox']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_final"), 'TEXT' => $language['FLAG_PoP_PostBertox']) ); 
//Sol Ro Tower 
$template->assign_both_block_vars( "head" , array( 'ID' => 9, 'NAME' => $language['FLAG_PoP_SolRoTower']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_tallon"), 'TEXT' => $language['FLAG_PoP_TZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_vallon"), 'TEXT' => $language['FLAG_PoP_VZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_shadyglade"), 'TEXT' => $language['FLAG_PoP_PreSaryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_newleaf"), 'TEXT' => $language['FLAG_PoP_KoS']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn"), 'TEXT' => $language['FLAG_PoP_Saryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn_final"), 'TEXT' => $language['FLAG_PoP_PostSaryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 
//Fire 
$template->assign_both_block_vars( "head" , array( 'ID' => 10, 'NAME' => $language['FLAG_PoP_PoFire']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_tallon"), 'TEXT' => $language['FLAG_PoP_TZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_vallon"), 'TEXT' => $language['FLAG_PoP_VZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_ralloz"), 'TEXT' => $language['FLAG_PoP_RZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_arlyxir"), 'TEXT' => $language['FLAG_PoP_Arlyxir']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_dresolik"), 'TEXT' => $language['FLAG_PoP_Dresolik']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_jiva"), 'TEXT' => $language['FLAG_PoP_Jiva']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_rizlona"), 'TEXT' => $language['FLAG_PoP_Rizlona']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_xuzl"), 'TEXT' => $language['FLAG_PoP_Xusl']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_solusk"), 'TEXT' => $language['FLAG_PoP_SolRo']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 

//Air/Earth/Water 
$template->assign_both_block_vars( "head" , array( 'ID' => 11, 'NAME' => $language['FLAG_PoP_PoAirEarthWater']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_terris"), 'TEXT' => $language['FLAG_PoP_TT']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_poxbourne"), 'TEXT' => $language['FLAG_PoP_PostTerris']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "pop_pos_askr_the_lost"), 'TEXT' => $language['FLAG_PoP_Askr1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pos_askr_the_lost_final"), 'TEXT' => $language['FLAG_PoP_Askr2']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_bot_agnarr"), 'TEXT' => $language['FLAG_PoP_Agnarr']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_faye"), 'TEXT' => $language['FLAG_PoP_Faye']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_trell"), 'TEXT' => $language['FLAG_PoP_Trell']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_garn"), 'TEXT' => $language['FLAG_PoP_Garn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_preflag"), 'TEXT' => $language['FLAG_PoP_Carpin']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_bertox"), 'TEXT' => $language['FLAG_PoP_Bertox']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_final"), 'TEXT' => $language['FLAG_PoP_PostBertox']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_shadyglade"), 'TEXT' => $language['FLAG_PoP_PreSaryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn"), 'TEXT' => $language['FLAG_PoP_Saryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_newleaf"), 'TEXT' => $language['FLAG_PoP_KoS']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn_final"), 'TEXT' => $language['FLAG_PoP_PostSaryrn']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_ralloz"), 'TEXT' => $language['FLAG_PoP_RZ']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_elemental_grand_librarian"), 'TEXT' => $language['FLAG_PoP_Maelin']) ); 
//Time 
$template->assign_both_block_vars( "head" , array( 'ID' => 12, 'NAME' => $language['FLAG_PoP_PoTime']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_fire_fennin_projection"), 'TEXT' => $language['FLAG_PoP_Fennin']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_wind_xegony_projection"), 'TEXT' => $language['FLAG_PoP_Xegony']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_water_coirnav_projection"), 'TEXT' => $language['FLAG_PoP_Coirnav']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_eartha_arbitor_projection"), 'TEXT' => $language['FLAG_PoP_Arbitor']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_earthb_rathe"), 'TEXT' => $language['FLAG_PoP_Rathe']) ); 



/*********************************************
           SECONDARY/SUB MENUS GoD
*********************************************/
//Vxed 
$template->assign_both_block_vars( "head" , array( 'ID' => 13, 'NAME' => $language['FLAG_GoD_Vxed']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_vxed_access"), 'TEXT' => $language['FLAG_GoD_KT_2']) ); 
//Sewer 1 
if (getflag(1, "temp_sewers") || getflag(2, "sewers")) $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_1_1']) ); 
else $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_1_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(2, "sewers"), 'TEXT' => $language['FLAG_GoD_Sewer_1_2']) ); 
//Sewer 2 
if (getflag(2, "temp_sewers") || getflag(3, "sewers")) $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_2_1']) ); 
else $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_2_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "sewers"), 'TEXT' => $language['FLAG_GoD_Sewer_2_2']) ); 
//sewer 3 
if (getflag(3, "temp_sewers") || getflag(4, "sewers")) $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_3_1']) ); 
else $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_3_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(4, "sewers"), 'TEXT' => $language['FLAG_GoD_Sewer_3_2']) ); 
//sewer 4 
if (getflag(4, "temp_sewers") || getflag(5, "sewers")) $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_4_1']) ); 
else $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_4_1']) ); 
if (getflag(1, "god_vxed_access") && getflag(5, "sewers")) $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_4_2']) ); 
else $template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_4_2']) ); 
//Tipt 
$template->assign_both_block_vars( "head" , array( 'ID' => 14, 'NAME' => $language['FLAG_GoD_Tipt']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_tipt_access"), 'TEXT' => $language['FLAG_GoD_KT_3']) ); 
//KT
$template->assign_both_block_vars( "head" , array( 'ID' => 15, 'NAME' => $language['FLAG_GoD_KT_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_vxed_access"), 'TEXT' => $language['FLAG_GoD_KT_2']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_tipt_access"), 'TEXT' => $language['FLAG_GoD_KT_3']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_kodtaz_access"), 'TEXT' => $language['FLAG_GoD_KT_4']) ); 
//Request Ikkinz Raids 1-3 
$template->assign_both_block_vars( "head" , array( 'ID' => 16, 'NAME' => $language['FLAG_GoD_Ikky_R3']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(2, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_2']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_3']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(4, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_4']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(5, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_5']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(6, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_6']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(7, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_7']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(8, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_8']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(9, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_9']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(10, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_10']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(11, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_11']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(12, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_12']) ); 
//request Ikkinz Raid 4 
$template->assign_both_block_vars( "head" , array( 'ID' => 17, 'NAME' => $language['FLAG_GoD_Ikky_R4']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(13, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_13']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(14, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_14']) ); 
//Qvic 
$template->assign_both_block_vars( "head" , array( 'ID' => 18, 'NAME' => $language['FLAG_GoD_Qvic_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_qvic_access"), 'TEXT' => $language['FLAG_GoD_Qvic_2']) ); 
//Txevu 
$template->assign_both_block_vars( "head" , array( 'ID' => 19, 'NAME' => $language['FLAG_GoD_Txevu_1']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_txevu_access"), 'TEXT' => $language['FLAG_GoD_Txevu_2']) ); 



/*********************************************
           SECONDARY/SUB MENUS OOW
*********************************************/ 
//Muramite Proving Grounds
$template->assign_both_block_vars( "head" , array( 'ID' => 20, 'NAME' => $language['FLAG_OOW_MPG']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_FEAR']) );  
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(2, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_INGENUITY']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(4, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_WEAPONRY']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(8, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_SUBVERSION']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(16, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_EFFICIENCY']) );
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(32, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_DESTRUCTION']) );
//Citadel of Anguish
$template->assign_both_block_vars( "head" , array( 'ID' => 21, 'NAME' => $language['FLAG_OOW_COA']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_HATE']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(2, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_ENDURANCE']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(4, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_FORESIGHT']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(8, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_SPECIALIZATION']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(16, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_ADAPTATION']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(32, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_CORRUPTION']) ); 
$template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "oow_rss_taromani_insignias"), 'TEXT' => $language['FLAG_OOW_COA_TAROMANI']) ); 


 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$template->pparse('flags'); 

$template->destroy; 

include("include/footer.php"); 
?>