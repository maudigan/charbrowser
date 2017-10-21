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
 *   February 24, 2014 - spelling--uncommented (Maudigan c/o Kinglykrab)
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
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/config.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/global.php");
include_once(__DIR__ . "/include/language.php");
include_once(__DIR__ . "/include/functions.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations 
$char = new profile($charName); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['skills']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_SKILLS'];
include(__DIR__ . "/include/header.php");

 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'skills' => 'skills_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'NAME' => $name,
   '1H_BLUNT' => $char->GetValue('1h_blunt'), //TODO all these are in multi row tables now, needs updates
   '1H_SLASHING' => $char->GetValue('1h_slashing'), 
   '2H_BLUNT' => $char->GetValue('2h_blunt'), 
   '2H_SLASHING' => $char->GetValue('2h_slashing'),
   'ARCHERY' => $char->GetValue('archery'), 
   'BASH' => $char->GetValue('bash'), 
   'BLOCK' => $char->GetValue('block'),
   'DEFENSE' => $char->GetValue('defense'), 
   'DISARM' => $char->GetValue('disarm'),
   'DODGE' => $char->GetValue('dodge'), 
   'DOUBLE_ATTACK' => $char->GetValue('double_attack'),  
   'DUAL_WIELD' => $char->GetValue('dual_wield'),
   'HAND_TO_HAND' => $char->GetValue('hand_to_hand'), 
   'KICK' => $char->GetValue('kick'), 
   'OFFENSE' => $char->GetValue('offense'), 
   'PARRY' => $char->GetValue('parry'), 
   'PIERCING' => $char->GetValue('piercing'), 
   'RIPOSTE' => $char->GetValue('riposte'), 
   'THROWING' => $char->GetValue('throwing'), 
   'INTIMIDATION' => $char->GetValue('intimidation'), 
   'TAUNT' => $char->GetValue('taunt'),


   'ABJURATION' => $char->GetValue('abjuration'),
   'ALTERATION' => $char->GetValue('alteration'),
   'CHANNELING' => $char->GetValue('channeling'),
   'CONJURATION' => $char->GetValue('conjuration'),
   'DIVINATION' => $char->GetValue('divination'),
   'EVOCATION' => $char->GetValue('evocation'),
   'SPECIALIZE_ABJURE' => $char->GetValue('specialize_abjure'),
   'SPECIALIZE_ALTERATION' => $char->GetValue('specialize_alteration'),
   'SPECIALIZE_CONJURATION' => $char->GetValue('specialize_conjuration'),
   'SPECIALIZE_DIVINATION' => $char->GetValue('specialize_divinatation'),
   'SPECIALIZE_EVOCATION' => $char->GetValue('specialize_evocation'),


   'DRAGON_PUNCH' => $char->GetValue('dragon_punch'),
   'EAGLE_STRIKE' => $char->GetValue('eagle_strike'),
   'ROUND_KICK' => $char->GetValue('round_kick'),
   'TIGER_CLAW' => $char->GetValue('tiger_claw'),
   'FLYING_KICK' => $char->GetValue('flying_kick'),
   'MEND' => $char->GetValue('mend'),
   'FEIGN_DEATH' => $char->GetValue('feign_death'),
   'PICK_LOCK' => $char->GetValue('pick_lock'),
   'APPLY_POISON' => $char->GetValue('apply_poison'),
   'BACKSTAB' => $char->GetValue('backstab'),
   'DISARM_TRAPS' => $char->GetValue('disarm_traps'),
   'PICK_POCKETS' => $char->GetValue('pick_pockets'),
   'SENSE_TRAPS' => $char->GetValue('sense_traps'),
   'BERSERKING' => $char->GetValue('berserking'),
   'FRENZY' => $char->GetValue('frenzy'),
   'BRASS_INSTRUMENTS' => $char->GetValue('brass_instruments'),
   'SINGING' => $char->GetValue('sing'),
   'STRINGED_INSTRUMENTS' => $char->GetValue('stringed_instruments'),
   'WIND_INSTRUMENTS' => $char->GetValue('wind_instruments'),
   'PERCUSSION_INSTRUMENTS' => $char->GetValue('percussion_instruments'),


   'BIND_WOUND' => $char->GetValue('bind_wound'),
   'FORAGE' => $char->GetValue('forage'),
   'HIDE' => $char->GetValue('hide'),
   'MEDITATE' => $char->GetValue('meditate'), 
   'SAFE_FALL' => $char->GetValue('safe_fall'),
   'SENSE_HEADING' => $char->GetValue('sense_heading'),
   'SNEAK' => $char->GetValue('sneak'),
   'SWIMMING' => $char->GetValue('swimming'),
   'TRACKING' => $char->GetValue('tracking'),
   'FISHING' => $char->GetValue('fishing'),
   'ALCOHOL_TOLERANCE' => $char->GetValue('alcohol_tolerance'),
   'BEGGING' => $char->GetValue('begging'),


   'MAKE_POISON' => $char->GetValue('make_poison'),
   'TINKERING' => $char->GetValue('tinkering'),
   'RESEARCH' => $char->GetValue('research'),
   'ALCHEMY' => $char->GetValue('alchemy'),
   'BAKING' => $char->GetValue('baking'),
   'TAILORING' => $char->GetValue('tailoring'),
   'BLACKSMITHING' => $char->GetValue('blacksmithing'),
   'FLETCHING' => $char->GetValue('fletching'),
   'BREWING' => $char->GetValue('brewing'),
   'JEWELRY_MAKING' => $char->GetValue('jewelry_making'),
   'POTTERY' => $char->GetValue('pottery'))
);   

$cb_template->assign_vars(array( 
   'L_TRADE' => $language['SKILLS_TRADE'],
   'L_OTHER' => $language['SKILLS_OTHER'], 
   'L_CLASS' => $language['SKILLS_CLASS'],
   'L_CASTING' => $language['SKILLS_CASTING'],
   'L_COMBAT' => $language['SKILLS_COMBAT'],
   'L_LANGUAGE' => $language['SKILLS_LANGUAGE'] ,
   'L_SKILLS' => $language['SKILLS_SKILLS'],
   'L_DONE' => $language['BUTTON_DONE'],
   'L_AAS' => $language['BUTTON_AAS'],
   'L_KEYS' => $language['BUTTON_KEYS'],
   'L_FLAGS' => $language['BUTTON_FLAGS'],
   'L_SKILLS' => $language['BUTTON_SKILLS'],
   'L_BOOKMARK' => $language['BUTTON_BOOKMARK'],
   'L_CORPSE' => $language['BUTTON_CORPSE'],
   'L_FACTION' => $language['BUTTON_FACTION'],
   'L_INVENTORY' => $language['BUTTON_INVENTORY'],
   'L_CHARMOVE' => $language['BUTTON_CHARMOVE'])
);


if (!$mypermission['languageskills']) {
   $cb_template->assign_both_block_vars("switch_language", array( 
      'COMMON_TONGUE' => $char->GetValue('common_tongue'), 
      'BARBARIAN' => $char->GetValue('barbarian'), 
      'ERUDIAN' => $char->GetValue('erudian'), 
      'ELVISH' => $char->GetValue('elvish'), 
      'DARK_ELVISH' => $char->GetValue('dark_elvish'), 
      'DWARVISH' => $char->GetValue('dwarvish'), 
      'TROLL' => $char->GetValue('troll'), 
      'OGRE' => $char->GetValue('ogre'), 
      'GNOMISH' => $char->GetValue('gnomish'), 
      'HALFLING' => $char->GetValue('halfling'), 
      'THIEVES_CANT' => $char->GetValue('thieves_cant'), 
      'OLD_ERUDIAN' => $char->GetValue('old_erudian'), 
      'ELDER_ELVISH' => $char->GetValue('elder_elvish'), 
      'FROGLOK' => $char->GetValue('froglok'), 
      'GOBLIN' => $char->GetValue('goblin'), 
      'GNOLL' => $char->GetValue('gnoll'), 
      'COMBINE_TONGUE' => $char->GetValue('combine_tongue'), 
      'ELDER_TEIRDAL' => $char->GetValue('elder_teirdal'), 
      'LIZARDMAN' => $char->GetValue('lizardman'), 
      'ORCISH' => $char->GetValue('orcish'), 
      'FAERIE' => $char->GetValue('faerie'), 
      'DRAGON' => $char->GetValue('dragon'), 
      'ELDER_DRAGON' => $char->GetValue('elder_dragon'), 
      'DARK_SPEECH' => $char->GetValue('dark_speech'), 
      'VAH_SHIR' => $char->GetValue('vah_shir'))
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('skills');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>