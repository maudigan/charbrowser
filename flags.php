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
 *      Fixed bad $flg->getflag conditions in sewer 2/3/4 sections
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
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   January 17, 2022 - Maudigan
 *     implemented databucket support for Vxed flags
 *   June 12, 2023 - Maudigan
 *      moved flag query/functions to a new class
 *      changed the output of the flags and flag headers
 *      into functions to make the code more readable
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
include_once(__DIR__ . "/include/flags_class.php");
  
 
/*********************************************
       SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/
$charName = preg_Get_Post('char', '/^[a-zA-Z]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR'], true);

//character initializations
$char = new Charbrowser_Character($charName, $showsoftdelete, $charbrowser_is_admin_page); //the Charbrowser_Character class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');

//block view if user level doesnt have permission 
if ($char->Permission('flags')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']); 
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
$flg = new Charbrowser_Flags($charID);
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_FLAGS']; 
include(__DIR__ . "/include/header.php"); 
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'flags');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array( 
   'flags' => 'flags_body.tpl') 
); 


$cb_template->assign_vars(array(  
   'NAME' => $name, 

   'L_DONE' => $language['BUTTON_DONE'], 
   'L_FLAGS' => $language['FLAG_FLAGS']) 
); 


/*********************************************
              MAIN MENUS
*********************************************/

//POP
$flg->oexpansion($language['FLAG_PoP']); 
$flg->ozone(($flg->getzoneflag(221) && $flg->getflag(1, "pop_pon_hedge_jezith") && $flg->getflag(1, "pop_pon_construct")), $language['FLAG_PoP_PoNB']); 
$flg->ozone(($flg->getzoneflag(214) && $flg->getflag(1, "pop_poi_behometh_preflag") && $flg->getflag(1, "pop_poi_behometh_flag")), $language['FLAG_PoP_PoTactics']); 
$flg->ozone(($flg->getzoneflag(200) && $flg->getflag(1, "pop_pod_elder_fuirstel")), $language['FLAG_PoP_CoD']); 
$flg->ozone(($flg->getzoneflag(208) && $flg->getflag(1, "pop_poj_valor_storms")), $language['FLAG_PoP_PoSPoV']); 
$flg->ozone(($flg->getzoneflag(211) && $flg->getflag(1, "pop_poj_valor_storms") && $flg->getflag(1, "pop_pov_aerin_dar")), $language['FLAG_PoP_HoHA']); 
$flg->ozone(($flg->getzoneflag(209) && $flg->getflag(1, "pop_poj_valor_storms") && $flg->getflag(1, "pop_pos_askr_the_lost_final")), $language['FLAG_PoP_BoT']); 
$flg->ozone(($flg->getzoneflag(220) && $flg->getflag(1, "pop_poj_valor_storms") && $flg->getflag(1, "pop_pov_aerin_dar") && $flg->getflag(1, "pop_hoh_faye") && $flg->getflag(1, "pop_hoh_trell") && $flg->getflag(1, "pop_hoh_garn")), $language['FLAG_PoP_HoHB']); 
$flg->ozone(($flg->getzoneflag(207) && $flg->getflag(1, "pop_pod_elder_fuirstel") && $flg->getflag(1, "pop_ponb_poxbourne") && $flg->getflag(1, "pop_cod_final")), $language['FLAG_PoP_PoTorment']); 
$flg->ozone(($flg->getzoneflag(212) && $flg->getflag(1, "pop_poi_behometh_flag") && $flg->getflag(1, "pop_tactics_tallon") && $flg->getflag(1, "pop_tactics_vallon") && $flg->getflag(1, "pop_hohb_marr") && $flg->getflag(1, "pop_pot_saryrn_final")), $language['FLAG_PoP_SolRoTower']); 
$flg->ozone(($flg->getzoneflag(217) && $flg->getflag(1, "pop_poi_behometh_flag") && $flg->getflag(1, "pop_tactics_tallon") && $flg->getflag(1, "pop_tactics_vallon") && $flg->getflag(1, "pop_hohb_marr") && $flg->getflag(1, "pop_tactics_ralloz") && $flg->getflag(1, "pop_sol_ro_arlyxir") && $flg->getflag(1, "pop_sol_ro_dresolik") && $flg->getflag(1, "pop_sol_ro_jiva") && $flg->getflag(1, "pop_sol_ro_rizlona") && $flg->getflag(1, "pop_sol_ro_xuzl") && $flg->getflag(1, "pop_sol_ro_solusk")), $language['FLAG_PoP_PoFire']); 
$flg->ozone(($flg->getzoneflag(216) && $flg->getflag(1, "pop_elemental_grand_librarian")), $language['FLAG_PoP_PoAirEarthWater']); 
$flg->ozone(($flg->getflag(1, "pop_time_maelin") && $flg->getflag(1, "pop_fire_fennin_projection") && $flg->getflag(1, "pop_wind_xegony_projection") && $flg->getflag(1, "pop_water_coirnav_projection") && $flg->getflag(1, "pop_eartha_arbitor_projection") && $flg->getflag(1, "pop_earthb_rathe")), $language['FLAG_PoP_PoTime']); 

//GoD
$flg->oexpansion($language['FLAG_GoD']); 
$flg->ozone(($flg->getflag(1,"god_vxed_access")), $language['FLAG_GoD_Vxed']); 
$flg->ozone(($flg->getflag(1,"god_tipt_access")), $language['FLAG_GoD_Tipt']); 
$flg->ozone(($flg->getzoneflag(293) && $flg->getflag(1, "god_vxed_access") && $flg->getflag(1, "god_tipt_access") && $flg->getflag(1, "god_kodtaz_access")), $language['FLAG_GoD_KT_1']); 
$flg->ozone(($flg->getflag(12,"ikky")), $language['FLAG_GoD_Ikky_R3']); 
$flg->ozone(($flg->getflag(14,"ikky")), $language['FLAG_GoD_Ikky_R4']); 
$flg->ozone(($flg->getzoneflag(295) && $flg->getflag(1, "god_qvic_access")), $language['FLAG_GoD_Qvic_1']); 
$flg->ozone(($flg->getzoneflag(297) && $flg->getflag(1, "god_txevu_access")), $language['FLAG_GoD_Txevu_1']); 

//OOW
$flg->oexpansion($language['FLAG_OOW']); 
$flg->ozone(($flg->getflag(63, "mpg_group_trials")), $language['FLAG_OOW_MPG']); 
//TODO get criteria for MPG, zone flags?
$flg->ozone(($flg->getflag(63, "mpg_raid_trials") && $flg->getflag(1, "oow_rss_taromani_insignias")), $language['FLAG_OOW_COA']); 

//OOW
$flg->oexpansion($language['FLAG_DON']); 
$flg->ozone(($flg->getdatabucketcharacter("don_good") == 268435455), $language['FLAG_DON_GOOD']); 
$flg->ozone(($flg->getdatabucketcharacter("don_evil") == 268435455), $language['FLAG_DON_EVIL']); 



/*********************************************
           SECONDARY/SUB MENUS POP
*********************************************/

//PoN B 
$flg->otitle($language['FLAG_PoP_PoNB']);
$flg->oflag($flg->getflag(1, "pop_pon_hedge_jezith"), $language['FLAG_PoP_PreHedge'] ); 
$flg->oflag($flg->getflag(1, "pop_pon_construct"), $language['FLAG_PoP_Hedge'] ); 
//Tactics 
$flg->otitle($language['FLAG_PoP_PoTactics']);
$flg->oflag($flg->getflag(1, "pop_poi_dragon"), $language['FLAG_PoP_Xana'] ); 
$flg->oflag($flg->getflag(1, "pop_poi_behometh_preflag"), $language['FLAG_PoP_PreMB'] ); 
$flg->oflag($flg->getflag(1, "pop_poi_behometh_flag"), $language['FLAG_PoP_MB'] ); 
//CoD 
$flg->otitle($language['FLAG_PoP_CoD']);
$flg->oflag($flg->getflag(1, "pop_pod_alder_fuirstel"), $language['FLAG_PoP_PreGrummus']);
$flg->oflag($flg->getflag(1, "pop_pod_grimmus_planar_projection"), $language['FLAG_PoP_Grummus']);
$flg->oflag($flg->getflag(1, "pop_pod_elder_fuirstel"), $language['FLAG_PoP_PostGrummus']);
//Valor & Storms 
$flg->otitle($language['FLAG_PoP_PoSPoV']);
$flg->oflag($flg->getflag(1, "pop_poj_mavuin"), $language['FLAG_PoP_PreTrial']);
$flg->oflag($flg->getflag(1, "pop_poj_tribunal"), $language['FLAG_PoP_Trial']);
$flg->oflag($flg->getflag(1, "pop_poj_valor_storms"), $language['FLAG_PoP_PostTrial']);
//HoH A 
$flg->otitle($language['FLAG_PoP_HoHA']);
$flg->oflag($flg->getflag(1, "pop_poj_mavuin"), $language['FLAG_PoP_PreTrial']);
$flg->oflag($flg->getflag(1, "pop_poj_tribunal"), $language['FLAG_PoP_Trial']);
$flg->oflag($flg->getflag(1, "pop_poj_valor_storms"), $language['FLAG_PoP_PostTrial']);
$flg->oflag($flg->getflag(1, "pop_pov_aerin_dar"), $language['FLAG_PoP_AD']);
//BoT 
$flg->otitle($language['FLAG_PoP_BoT']);
$flg->oflag($flg->getflag(1, "pop_poj_mavuin"), $language['FLAG_PoP_PreTrial']);
$flg->oflag($flg->getflag(1, "pop_poj_tribunal"), $language['FLAG_PoP_Trial']);
$flg->oflag($flg->getflag(1, "pop_poj_valor_storms"), $language['FLAG_PoP_PostTrial']);
$flg->oflag($flg->getflag(3, "pop_pos_askr_the_lost"), $language['FLAG_PoP_Askr1']);
$flg->oflag($flg->getflag(1, "pop_pos_askr_the_lost_final"), $language['FLAG_PoP_Askr2']);
//HoH B 
$flg->otitle($language['FLAG_PoP_HoHB']);
$flg->oflag($flg->getflag(1, "pop_poj_mavuin"), $language['FLAG_PoP_PreTrial']);
$flg->oflag($flg->getflag(1, "pop_poj_tribunal"), $language['FLAG_PoP_Trial']);
$flg->oflag($flg->getflag(1, "pop_poj_valor_storms"), $language['FLAG_PoP_PostTrial']);
$flg->oflag($flg->getflag(1, "pop_pov_aerin_dar"), $language['FLAG_PoP_AD']);
$flg->oflag($flg->getflag(1, "pop_hoh_faye"), $language['FLAG_PoP_Faye']);
$flg->oflag($flg->getflag(1, "pop_hoh_trell"), $language['FLAG_PoP_Trell']);
$flg->oflag($flg->getflag(1, "pop_hoh_garn"), $language['FLAG_PoP_Garn']);
//Torment 
$flg->otitle($language['FLAG_PoP_PoTorment']);
$flg->oflag($flg->getflag(1, "pop_pod_alder_fuirstel"), $language['FLAG_PoP_PreGrummus']);
$flg->oflag($flg->getflag(1, "pop_pod_grimmus_planar_projection"), $language['FLAG_PoP_Grummus']);
$flg->oflag($flg->getflag(1, "pop_pod_elder_fuirstel"), $language['FLAG_PoP_PostGrummus']);
$flg->oflag($flg->getflag(1, "pop_pon_hedge_jezith"), $language['FLAG_PoP_PreHedge']);
$flg->oflag($flg->getflag(1, "pop_pon_construct"), $language['FLAG_PoP_Hedge']);
$flg->oflag($flg->getflag(1, "pop_ponb_terris"), $language['FLAG_PoP_TT']);
$flg->oflag($flg->getflag(1, "pop_ponb_poxbourne"), $language['FLAG_PoP_PostTerris']);
$flg->oflag($flg->getflag(1, "pop_cod_preflag"), $language['FLAG_PoP_Carpin']);
$flg->oflag($flg->getflag(1, "pop_cod_bertox"), $language['FLAG_PoP_Bertox']);
$flg->oflag($flg->getflag(1, "pop_cod_final"), $language['FLAG_PoP_PostBertox']);
//Sol Ro Tower 
$flg->otitle($language['FLAG_PoP_SolRoTower']);
$flg->oflag($flg->getflag(1, "pop_poi_behometh_preflag"), $language['FLAG_PoP_PreMB']);
$flg->oflag($flg->getflag(1, "pop_poi_behometh_flag"), $language['FLAG_PoP_MB']);
$flg->oflag($flg->getflag(1, "pop_tactics_tallon"), $language['FLAG_PoP_TZ']);
$flg->oflag($flg->getflag(1, "pop_tactics_vallon"), $language['FLAG_PoP_VZ']);
$flg->oflag($flg->getflag(1, "pop_pot_shadyglade"), $language['FLAG_PoP_PreSaryrn']);
$flg->oflag($flg->getflag(1, "pop_pot_newleaf"), $language['FLAG_PoP_KoS']);
$flg->oflag($flg->getflag(1, "pop_pot_saryrn"), $language['FLAG_PoP_Saryrn']);
$flg->oflag($flg->getflag(1, "pop_pot_saryrn_final"), $language['FLAG_PoP_PostSaryrn']);
$flg->oflag($flg->getflag(1, "pop_hohb_marr"), $language['FLAG_PoP_MM']);
//Fire 
$flg->otitle($language['FLAG_PoP_PoFire']);
$flg->oflag($flg->getflag(1, "pop_poi_behometh_preflag"), $language['FLAG_PoP_PreMB']);
$flg->oflag($flg->getflag(1, "pop_poi_behometh_flag"), $language['FLAG_PoP_MB']);
$flg->oflag($flg->getflag(1, "pop_tactics_tallon"), $language['FLAG_PoP_TZ']);
$flg->oflag($flg->getflag(1, "pop_tactics_vallon"), $language['FLAG_PoP_VZ']);
$flg->oflag($flg->getflag(1, "pop_tactics_ralloz"), $language['FLAG_PoP_RZ']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_arlyxir"), $language['FLAG_PoP_Arlyxir']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_dresolik"), $language['FLAG_PoP_Dresolik']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_jiva"), $language['FLAG_PoP_Jiva']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_rizlona"), $language['FLAG_PoP_Rizlona']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_xuzl"), $language['FLAG_PoP_Xusl']);
$flg->oflag($flg->getflag(1, "pop_sol_ro_solusk"), $language['FLAG_PoP_SolRo']);
$flg->oflag($flg->getflag(1, "pop_hohb_marr"), $language['FLAG_PoP_MM']);

//Air/Earth/Water 
$flg->otitle($language['FLAG_PoP_PoAirEarthWater']);
$flg->oflag($flg->getflag(1, "pop_pon_hedge_jezith"), $language['FLAG_PoP_PreHedge']);
$flg->oflag($flg->getflag(1, "pop_pon_construct"), $language['FLAG_PoP_Hedge']);
$flg->oflag($flg->getflag(1, "pop_poj_mavuin"), $language['FLAG_PoP_PreTrial']);
$flg->oflag($flg->getflag(1, "pop_poj_tribunal"), $language['FLAG_PoP_Trial']);
$flg->oflag($flg->getflag(1, "pop_poj_valor_storms"), $language['FLAG_PoP_PostTrial']);
$flg->oflag($flg->getflag(1, "pop_ponb_terris"), $language['FLAG_PoP_TT']);
$flg->oflag($flg->getflag(1, "pop_ponb_poxbourne"), $language['FLAG_PoP_PostTerris']);
$flg->oflag($flg->getflag(1, "pop_pod_alder_fuirstel"), $language['FLAG_PoP_PreGrummus']);
$flg->oflag($flg->getflag(1, "pop_pod_grimmus_planar_projection"), $language['FLAG_PoP_Grummus']);
$flg->oflag($flg->getflag(1, "pop_pod_elder_fuirstel"), $language['FLAG_PoP_PostGrummus']);
$flg->oflag($flg->getflag(3, "pop_pos_askr_the_lost"), $language['FLAG_PoP_Askr1']);
$flg->oflag($flg->getflag(1, "pop_pos_askr_the_lost_final"), $language['FLAG_PoP_Askr2']);
$flg->oflag($flg->getflag(1, "pop_bot_agnarr"), $language['FLAG_PoP_Agnarr']);
$flg->oflag($flg->getflag(1, "pop_pov_aerin_dar"), $language['FLAG_PoP_AD']);
$flg->oflag($flg->getflag(1, "pop_hoh_faye"), $language['FLAG_PoP_Faye']);
$flg->oflag($flg->getflag(1, "pop_hoh_trell"), $language['FLAG_PoP_Trell']);
$flg->oflag($flg->getflag(1, "pop_hoh_garn"), $language['FLAG_PoP_Garn']);
$flg->oflag($flg->getflag(1, "pop_hohb_marr"), $language['FLAG_PoP_MM']);
$flg->oflag($flg->getflag(1, "pop_cod_preflag"), $language['FLAG_PoP_Carpin']);
$flg->oflag($flg->getflag(1, "pop_cod_bertox"), $language['FLAG_PoP_Bertox']);
$flg->oflag($flg->getflag(1, "pop_cod_final"), $language['FLAG_PoP_PostBertox']);
$flg->oflag($flg->getflag(1, "pop_pot_shadyglade"), $language['FLAG_PoP_PreSaryrn']);
$flg->oflag($flg->getflag(1, "pop_pot_saryrn"), $language['FLAG_PoP_Saryrn']);
$flg->oflag($flg->getflag(1, "pop_pot_newleaf"), $language['FLAG_PoP_KoS']);
$flg->oflag($flg->getflag(1, "pop_pot_saryrn_final"), $language['FLAG_PoP_PostSaryrn']);
$flg->oflag($flg->getflag(1, "pop_tactics_ralloz"), $language['FLAG_PoP_RZ']);
$flg->oflag($flg->getflag(1, "pop_elemental_grand_librarian"), $language['FLAG_PoP_Maelin']);
//Time 
$flg->otitle($language['FLAG_PoP_PoTime']);
$flg->oflag($flg->getflag(1, "pop_fire_fennin_projection"), $language['FLAG_PoP_Fennin']);
$flg->oflag($flg->getflag(1, "pop_wind_xegony_projection"), $language['FLAG_PoP_Xegony']);
$flg->oflag($flg->getflag(1, "pop_water_coirnav_projection"), $language['FLAG_PoP_Coirnav']);
$flg->oflag($flg->getflag(1, "pop_eartha_arbitor_projection"), $language['FLAG_PoP_Arbitor']);
$flg->oflag($flg->getflag(1, "pop_earthb_rathe"), $language['FLAG_PoP_Rathe']);



/*********************************************
           SECONDARY/SUB MENUS GoD
*********************************************/

//Vxed 
$flg->otitle($language['FLAG_GoD_Vxed']);
$flg->oflag($flg->getflag(1, "god_vxed_access"), $language['FLAG_GoD_KT_2']);
//Sewer 1 
$flg->oflag(($flg->getdatabucket("god_snplant") == '1'), $language['FLAG_GoD_Sewer_1_1'],
           ($flg->getdatabucket("god_snplant") == 'T'), $language['FLAG_GoD_Sewer_1_T'],
           $language['FLAG_GoD_Sewer_1_1']);
//Sewer 2
$flg->oflag(($flg->getdatabucket("god_sncrematory") == '1'), $language['FLAG_GoD_Sewer_2_1'],
           ($flg->getdatabucket("god_sncrematory") == 'T'), $language['FLAG_GoD_Sewer_2_T'],
           $language['FLAG_GoD_Sewer_2_1']);
//Sewer 3
$flg->oflag(($flg->getdatabucket("god_snlair") == '1'), $language['FLAG_GoD_Sewer_3_1'],
           ($flg->getdatabucket("god_snlair") == 'T'), $language['FLAG_GoD_Sewer_3_T'],
           $language['FLAG_GoD_Sewer_3_1']);
//Sewer 4
$flg->oflag(($flg->getdatabucket("god_snpool") == '1'), $language['FLAG_GoD_Sewer_4_1'],
           ($flg->getdatabucket("god_snpool") == 'T'), $language['FLAG_GoD_Sewer_4_T'],
           $language['FLAG_GoD_Sewer_4_1']);
//Tipt 
$flg->otitle($language['FLAG_GoD_Tipt']);
$flg->oflag($flg->getflag(1, "god_tipt_access"), $language['FLAG_GoD_KT_3']);
//KT
$flg->otitle($language['FLAG_GoD_KT_1']);
$flg->oflag($flg->getflag(1, "god_vxed_access"), $language['FLAG_GoD_KT_2']);
$flg->oflag($flg->getflag(1, "god_tipt_access"), $language['FLAG_GoD_KT_3']);
$flg->oflag($flg->getflag(1, "god_kodtaz_access"), $language['FLAG_GoD_KT_4']);
//Request Ikkinz Raids 1-3 
$flg->otitle($language['FLAG_GoD_Ikky_R3']);
$flg->oflag($flg->getflag(2, "ikky"), $language['FLAG_GoD_Ikky_2']);
$flg->oflag($flg->getflag(3, "ikky"), $language['FLAG_GoD_Ikky_3']);
$flg->oflag($flg->getflag(4, "ikky"), $language['FLAG_GoD_Ikky_4']);
$flg->oflag($flg->getflag(5, "ikky"), $language['FLAG_GoD_Ikky_5']);
$flg->oflag($flg->getflag(6, "ikky"), $language['FLAG_GoD_Ikky_6']);
$flg->oflag($flg->getflag(7, "ikky"), $language['FLAG_GoD_Ikky_7']);
$flg->oflag($flg->getflag(8, "ikky"), $language['FLAG_GoD_Ikky_8']);
$flg->oflag($flg->getflag(9, "ikky"), $language['FLAG_GoD_Ikky_9']);
$flg->oflag($flg->getflag(10, "ikky"), $language['FLAG_GoD_Ikky_10']);
$flg->oflag($flg->getflag(11, "ikky"), $language['FLAG_GoD_Ikky_11']);
$flg->oflag($flg->getflag(12, "ikky"), $language['FLAG_GoD_Ikky_12']);
//request Ikkinz Raid 4 
$flg->otitle($language['FLAG_GoD_Ikky_R4']);
$flg->oflag($flg->getflag(13, "ikky"), $language['FLAG_GoD_Ikky_13']);
$flg->oflag($flg->getflag(14, "ikky"), $language['FLAG_GoD_Ikky_14']);
//Qvic 
$flg->otitle($language['FLAG_GoD_Qvic_1']);
$flg->oflag($flg->getflag(1, "god_qvic_access"), $language['FLAG_GoD_Qvic_2']);
//Txevu 
$flg->otitle($language['FLAG_GoD_Txevu_1']);
$flg->oflag($flg->getflag(1, "god_txevu_access"), $language['FLAG_GoD_Txevu_2']);



/*********************************************
           SECONDARY/SUB MENUS OOW
*********************************************/ 

//Muramite Proving Grounds
$flg->otitle($language['FLAG_OOW_MPG']);
$flg->oflag($flg->getbitflag(1, "mpg_group_trials"), $language['FLAG_OOW_MPG_FEAR']);
$flg->oflag($flg->getbitflag(2, "mpg_group_trials"), $language['FLAG_OOW_MPG_INGENUITY']);
$flg->oflag($flg->getbitflag(4, "mpg_group_trials"), $language['FLAG_OOW_MPG_WEAPONRY']);
$flg->oflag($flg->getbitflag(8, "mpg_group_trials"), $language['FLAG_OOW_MPG_SUBVERSION']);
$flg->oflag($flg->getbitflag(16, "mpg_group_trials"), $language['FLAG_OOW_MPG_EFFICIENCY']);
$flg->oflag($flg->getbitflag(32, "mpg_group_trials"), $language['FLAG_OOW_MPG_DESTRUCTION']);
//Citadel of Anguish
$flg->otitle($language['FLAG_OOW_COA']);
$flg->oflag($flg->getbitflag(1, "mpg_raid_trials"), $language['FLAG_OOW_COA_HATE']);
$flg->oflag($flg->getbitflag(2, "mpg_raid_trials"), $language['FLAG_OOW_COA_ENDURANCE']);
$flg->oflag($flg->getbitflag(4, "mpg_raid_trials"), $language['FLAG_OOW_COA_FORESIGHT']);
$flg->oflag($flg->getbitflag(8, "mpg_raid_trials"), $language['FLAG_OOW_COA_SPECIALIZATION']);
$flg->oflag($flg->getbitflag(16, "mpg_raid_trials"), $language['FLAG_OOW_COA_ADAPTATION']);
$flg->oflag($flg->getbitflag(32, "mpg_raid_trials"), $language['FLAG_OOW_COA_CORRUPTION']);
$flg->oflag($flg->getbitflag(1, "oow_rss_taromani_insignias"), $language['FLAG_OOW_COA_TAROMANI']);




/*********************************************
           SECONDARY/SUB MENUS DON
*********************************************/ 

//norraths keepers
$flg->otitle($language['FLAG_DON_GOOD']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1, "don_good"), $language['FLAG_DON_GOOD_1']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2, "don_good"), $language['FLAG_DON_GOOD_2']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4, "don_good"), $language['FLAG_DON_GOOD_3']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8, "don_good"), $language['FLAG_DON_GOOD_4']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16, "don_good"), $language['FLAG_DON_GOOD_5']);
$flg->oflag($flg->getdatabucketcharacterbitflag(32, "don_good"), $language['FLAG_DON_GOOD_6']);
$flg->oflag($flg->getdatabucketcharacterbitflag(64, "don_good"), $language['FLAG_DON_GOOD_7']);
$flg->oflag($flg->getdatabucketcharacterbitflag(128, "don_good"), $language['FLAG_DON_GOOD_8']);
$flg->oflag($flg->getdatabucketcharacterbitflag(256, "don_good"), $language['FLAG_DON_GOOD_9']);
$flg->oflag($flg->getdatabucketcharacterbitflag(512, "don_good"), $language['FLAG_DON_GOOD_10']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1024, "don_good"), $language['FLAG_DON_GOOD_11']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2048, "don_good"), $language['FLAG_DON_GOOD_12']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4096, "don_good"), $language['FLAG_DON_GOOD_13']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8192, "don_good"), $language['FLAG_DON_GOOD_14']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16384, "don_good"), $language['FLAG_DON_GOOD_15']);
$flg->oflag($flg->getdatabucketcharacterbitflag(32768, "don_good"), $language['FLAG_DON_GOOD_16']);
$flg->oflag($flg->getdatabucketcharacterbitflag(65536, "don_good"), $language['FLAG_DON_GOOD_17']);
$flg->oflag($flg->getdatabucketcharacterbitflag(131072, "don_good"), $language['FLAG_DON_GOOD_18']);
$flg->oflag($flg->getdatabucketcharacterbitflag(262144, "don_good"), $language['FLAG_DON_GOOD_19']);
$flg->oflag($flg->getdatabucketcharacterbitflag(524288, "don_good"), $language['FLAG_DON_GOOD_20']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1048576, "don_good"), $language['FLAG_DON_GOOD_21']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2097152, "don_good"), $language['FLAG_DON_GOOD_22']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4194304, "don_good"), $language['FLAG_DON_GOOD_23']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8388608, "don_good"), $language['FLAG_DON_GOOD_24']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16777216, "don_good"), $language['FLAG_DON_GOOD_25']);
$flg->oflag($flg->getdatabucketcharacterbitflag(33554432, "don_good"), $language['FLAG_DON_GOOD_26']);
$flg->oflag($flg->getdatabucketcharacterbitflag(67108864, "don_good"), $language['FLAG_DON_GOOD_27']);
//dark reign
$flg->otitle($language['FLAG_DON_EVIL']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1, "don_evil"), $language['FLAG_DON_EVIL_1']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2, "don_evil"), $language['FLAG_DON_EVIL_2']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4, "don_evil"), $language['FLAG_DON_EVIL_3']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8, "don_evil"), $language['FLAG_DON_EVIL_4']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16, "don_evil"), $language['FLAG_DON_EVIL_5']);
$flg->oflag($flg->getdatabucketcharacterbitflag(32, "don_evil"), $language['FLAG_DON_EVIL_6']);
$flg->oflag($flg->getdatabucketcharacterbitflag(64, "don_evil"), $language['FLAG_DON_EVIL_7']);
$flg->oflag($flg->getdatabucketcharacterbitflag(128, "don_evil"), $language['FLAG_DON_EVIL_8']);
$flg->oflag($flg->getdatabucketcharacterbitflag(256, "don_evil"), $language['FLAG_DON_EVIL_9']);
$flg->oflag($flg->getdatabucketcharacterbitflag(512, "don_evil"), $language['FLAG_DON_EVIL_10']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1024, "don_evil"), $language['FLAG_DON_EVIL_11']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2048, "don_evil"), $language['FLAG_DON_EVIL_12']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4096, "don_evil"), $language['FLAG_DON_EVIL_13']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8192, "don_evil"), $language['FLAG_DON_EVIL_14']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16384, "don_evil"), $language['FLAG_DON_EVIL_15']);
$flg->oflag($flg->getdatabucketcharacterbitflag(32768, "don_evil"), $language['FLAG_DON_EVIL_16']);
$flg->oflag($flg->getdatabucketcharacterbitflag(65536, "don_evil"), $language['FLAG_DON_EVIL_17']);
$flg->oflag($flg->getdatabucketcharacterbitflag(131072, "don_evil"), $language['FLAG_DON_EVIL_18']);
$flg->oflag($flg->getdatabucketcharacterbitflag(262144, "don_evil"), $language['FLAG_DON_EVIL_19']);
$flg->oflag($flg->getdatabucketcharacterbitflag(524288, "don_evil"), $language['FLAG_DON_EVIL_20']);
$flg->oflag($flg->getdatabucketcharacterbitflag(1048576, "don_evil"), $language['FLAG_DON_EVIL_21']);
$flg->oflag($flg->getdatabucketcharacterbitflag(2097152, "don_evil"), $language['FLAG_DON_EVIL_22']);
$flg->oflag($flg->getdatabucketcharacterbitflag(4194304, "don_evil"), $language['FLAG_DON_EVIL_23']);
$flg->oflag($flg->getdatabucketcharacterbitflag(8388608, "don_evil"), $language['FLAG_DON_EVIL_24']);
$flg->oflag($flg->getdatabucketcharacterbitflag(16777216, "don_evil"), $language['FLAG_DON_EVIL_25']);
$flg->oflag($flg->getdatabucketcharacterbitflag(33554432, "don_evil"), $language['FLAG_DON_EVIL_26']);
$flg->oflag($flg->getdatabucketcharacterbitflag(67108864, "don_evil"), $language['FLAG_DON_EVIL_27']);
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('flags'); 

$cb_template->destroy(); 

include(__DIR__ . "/include/footer.php"); 
?>