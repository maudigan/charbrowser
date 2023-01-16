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
 *   September 23, 2018 - Maudigan
 *      Added 2h piercing, remove traps and tripple attack.
 *   September 7, 2019 - Kinglykrab
 *      fixed typo tripple => triple
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   January 16, 2023 - maudigan
 *     added missing quotes around the array index: barbarian
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
if ($char->Permission('skills')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_SKILLS'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'skills');

 
 
/*********************************************
              POPULATE BODY
*********************************************/

    
$skillsections = array();

if (!$char->Permission('languageskills')) {
   $skillsections[$language['SKILLS_LANGUAGE']] = array(
      array('NAME' => 'Common Tongue', 'VALUE' => $char->GetValue('common_tongue')), 
      array('NAME' => 'Barbarian', 'VALUE' => $char->GetValue('barbarian')), 
      array('NAME' => 'Erudian', 'VALUE' => $char->GetValue('erudian')), 
      array('NAME' => 'Elvish', 'VALUE' => $char->GetValue('elvish')), 
      array('NAME' => 'Dark Elvish', 'VALUE' => $char->GetValue('dark_elvish')), 
      array('NAME' => 'Dwarvish', 'VALUE' => $char->GetValue('dwarvish')), 
      array('NAME' => 'Troll', 'VALUE' => $char->GetValue('troll')), 
      array('NAME' => 'Ogre', 'VALUE' => $char->GetValue('ogre')), 
      array('NAME' => 'Gnomish', 'VALUE' => $char->GetValue('gnomish')), 
      array('NAME' => 'Halfling', 'VALUE' => $char->GetValue('halfling')), 
      array('NAME' => 'Thieves Cant', 'VALUE' => $char->GetValue('thieves_cant')), 
      array('NAME' => 'Old Erudian', 'VALUE' => $char->GetValue('old_erudian')), 
      array('NAME' => 'Elder Elvish', 'VALUE' => $char->GetValue('elder_elvish')), 
      array('NAME' => 'Froglok', 'VALUE' => $char->GetValue('froglok')), 
      array('NAME' => 'Goblin', 'VALUE' => $char->GetValue('goblin')), 
      array('NAME' => 'Gnoll', 'VALUE' => $char->GetValue('gnoll')), 
      array('NAME' => 'Combine Tongue', 'VALUE' => $char->GetValue('combine_tongue')), 
      array('NAME' => 'Elder Tier`dal', 'VALUE' => $char->GetValue('elder_teirdal')), 
      array('NAME' => 'LizardMan', 'VALUE' => $char->GetValue('lizardman')), 
      array('NAME' => 'Orcish', 'VALUE' => $char->GetValue('orcish')), 
      array('NAME' => 'Faerie', 'VALUE' => $char->GetValue('faerie')), 
      array('NAME' => 'Dragon', 'VALUE' => $char->GetValue('dragon')), 
      array('NAME' => 'Elder Dragon', 'VALUE' => $char->GetValue('elder_dragon')), 
      array('NAME' => 'Dark Speech', 'VALUE' => $char->GetValue('dark_speech')), 
      array('NAME' => 'Vah Shir', 'VALUE' => $char->GetValue('vah_shir'))
   );
}
$skillsections[$language['SKILLS_COMBAT']] = array(
   array('NAME' => '1H Blunt', 'VALUE' => $char->GetValue('1h_blunt')), 
   array('NAME' => '1H Piercing', 'VALUE' => $char->GetValue('piercing')), 
   array('NAME' => '1H Slashing', 'VALUE' => $char->GetValue('1h_slashing')), 
   array('NAME' => '2H Blunt', 'VALUE' => $char->GetValue('2h_blunt')), 
   array('NAME' => '2H Piercing', 'VALUE' => $char->GetValue('2h_piercing')),
   array('NAME' => '2H Slashing', 'VALUE' => $char->GetValue('2h_slashing')),
   array('NAME' => 'Archery', 'VALUE' => $char->GetValue('archery')), 
   array('NAME' => 'Bash', 'VALUE' => $char->GetValue('bash')), 
   array('NAME' => 'Block', 'VALUE' => $char->GetValue('block')),
   array('NAME' => 'Defense', 'VALUE' => $char->GetValue('defense')), 
   array('NAME' => 'Disarm', 'VALUE' => $char->GetValue('disarm')),
   array('NAME' => 'Dodge', 'VALUE' => $char->GetValue('dodge')), 
   array('NAME' => 'Double Attack', 'VALUE' => $char->GetValue('double_attack')),  
   array('NAME' => 'Dual Wield', 'VALUE' => $char->GetValue('dual_wield')),
   array('NAME' => 'Hand to Hand', 'VALUE' => $char->GetValue('hand_to_hand')), 
   array('NAME' => 'Kick', 'VALUE' => $char->GetValue('kick')), 
   array('NAME' => 'Offense', 'VALUE' => $char->GetValue('offense')), 
   array('NAME' => 'Parry', 'VALUE' => $char->GetValue('parry')), 
   array('NAME' => 'Riposte', 'VALUE' => $char->GetValue('riposte')), 
   array('NAME' => 'Throwing', 'VALUE' => $char->GetValue('throwing')), 
   array('NAME' => 'Triple Attack', 'VALUE' => $char->GetValue('triple_attack')), 
   array('NAME' => 'Intimidation', 'VALUE' => $char->GetValue('intimidation')), 
   array('NAME' => 'Taunt', 'VALUE' => $char->GetValue('taunt'))
);
$skillsections[$language['SKILLS_CASTING']] = array(
   array('NAME' => 'Abjuration', 'VALUE' => $char->GetValue('abjuration')),
   array('NAME' => 'Alteration', 'VALUE' => $char->GetValue('alteration')),
   array('NAME' => 'Channeling', 'VALUE' => $char->GetValue('channeling')),
   array('NAME' => 'Conjuration', 'VALUE' => $char->GetValue('conjuration')),
   array('NAME' => 'Divination', 'VALUE' => $char->GetValue('divination')),
   array('NAME' => 'Evocation', 'VALUE' => $char->GetValue('evocation')),
   array('NAME' => 'Specialize Abjure', 'VALUE' => $char->GetValue('specialize_abjure')),
   array('NAME' => 'Specialize Alteration', 'VALUE' => $char->GetValue('specialize_alteration')),
   array('NAME' => 'Specialize Conjuration', 'VALUE' => $char->GetValue('specialize_conjuration')),
   array('NAME' => 'Specialize Divination', 'VALUE' => $char->GetValue('specialize_divinatation')),
   array('NAME' => 'Specialize Evocation', 'VALUE' => $char->GetValue('specialize_evocation'))
);
$skillsections[$language['SKILLS_CLASS']] = array(
   array('NAME' => 'Dragon Punch', 'VALUE' => $char->GetValue('dragon_punch')),
   array('NAME' => 'Eagle Strike', 'VALUE' => $char->GetValue('eagle_strike')),
   array('NAME' => 'Round Kick', 'VALUE' => $char->GetValue('round_kick')),
   array('NAME' => 'Tiger Claw', 'VALUE' => $char->GetValue('tiger_claw')),
   array('NAME' => 'Flying Kick', 'VALUE' => $char->GetValue('flying_kick')),
   array('NAME' => 'Mend', 'VALUE' => $char->GetValue('mend')),
   array('NAME' => 'Feign Death', 'VALUE' => $char->GetValue('feign_death')),
   array('NAME' => 'Pick Lock', 'VALUE' => $char->GetValue('pick_lock')),
   array('NAME' => 'Apply Poison', 'VALUE' => $char->GetValue('apply_poison')),
   array('NAME' => 'Backstab', 'VALUE' => $char->GetValue('backstab')),
   array('NAME' => 'Disarm Traps', 'VALUE' => $char->GetValue('disarm_traps')),
   array('NAME' => 'Pick Pockets', 'VALUE' => $char->GetValue('pick_pockets')),
   array('NAME' => 'Remove Traps', 'VALUE' => $char->GetValue('remove_traps')),
   array('NAME' => 'Sense Traps', 'VALUE' => $char->GetValue('sense_traps')),
   array('NAME' => 'Berserking', 'VALUE' => $char->GetValue('berserking')),
   array('NAME' => 'Frenzy', 'VALUE' => $char->GetValue('frenzy')),
   array('NAME' => 'Brass Instruments', 'VALUE' => $char->GetValue('brass_instruments')),
   array('NAME' => 'Singing', 'VALUE' => $char->GetValue('sing')),
   array('NAME' => 'Stringed Instruments', 'VALUE' => $char->GetValue('stringed_instruments')),
   array('NAME' => 'Wind Instruments', 'VALUE' => $char->GetValue('wind_instruments')),
   array('NAME' => 'Percussion Instruments', 'VALUE' => $char->GetValue('percussion_instruments'))
);
$skillsections[$language['SKILLS_OTHER']] = array(
   array('NAME' => 'Bind Wound', 'VALUE' => $char->GetValue('bind_wound')),
   array('NAME' => 'Forage', 'VALUE' => $char->GetValue('forage')),
   array('NAME' => 'Hide', 'VALUE' => $char->GetValue('hide')),
   array('NAME' => 'Meditate', 'VALUE' => $char->GetValue('meditate')), 
   array('NAME' => 'Safe Fall', 'VALUE' => $char->GetValue('safe_fall')),
   array('NAME' => 'Sense Heading', 'VALUE' => $char->GetValue('sense_heading')),
   array('NAME' => 'Sneak', 'VALUE' => $char->GetValue('sneak')),
   array('NAME' => 'Swimming', 'VALUE' => $char->GetValue('swimming')),
   array('NAME' => 'Tracking', 'VALUE' => $char->GetValue('tracking')),
   array('NAME' => 'Fishing', 'VALUE' => $char->GetValue('fishing')),
   array('NAME' => 'Alcohol Tolerance', 'VALUE' => $char->GetValue('alcohol_tolerance')),
   array('NAME' => 'Begging', 'VALUE' => $char->GetValue('begging'))
);
$skillsections[$language['SKILLS_TRADE']] = array(
   array('NAME' => 'Make Poison', 'VALUE' => $char->GetValue('make_poison')),
   array('NAME' => 'Tinkering', 'VALUE' => $char->GetValue('tinkering')),
   array('NAME' => 'Research', 'VALUE' => $char->GetValue('research')),
   array('NAME' => 'Alchemy', 'VALUE' => $char->GetValue('alchemy')),
   array('NAME' => 'Baking', 'VALUE' => $char->GetValue('baking')),
   array('NAME' => 'Tailoring', 'VALUE' => $char->GetValue('tailoring')),
   array('NAME' => 'Blacksmithing', 'VALUE' => $char->GetValue('blacksmithing')),
   array('NAME' => 'Fletching', 'VALUE' => $char->GetValue('fletching')),
   array('NAME' => 'Brewing', 'VALUE' => $char->GetValue('brewing')),
   array('NAME' => 'Jewelry Making', 'VALUE' => $char->GetValue('jewelry_making')),
   array('NAME' => 'Pottery', 'VALUE' => $char->GetValue('pottery'))
);


$cb_template->set_filenames(array(
   'skills' => 'skills_body.tpl')
);



$i = 0;
foreach ($skillsections as $header => $skills)
{
   $cb_template->assign_block_vars("section", array(
      'TEXT' => $header." ".$language['SKILLS_SKILLS'],
      'TAB' => $header,
      'INDEX' => $i++
   ));
   foreach ($skills as $skillrow)
   {
      $cb_template->assign_both_block_vars("section.skillrow", $skillrow);  
   }
}


$cb_template->assign_both_vars(array(  
   'NAME' => $name)
);   

$cb_template->assign_vars(array( 
   'L_SKILLS' => $language['SKILLS_SKILLS'],
   'L_DONE' => $language['BUTTON_DONE'])
);

 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('skills');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>