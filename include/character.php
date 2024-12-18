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
 *   February 25, 2014 - added heroic stats/augs (Maudigan c/o Kinglykrab) 
 *   September 26, 2014 - Maudigan
 *      made STR/STA/DEX/etc lowercase to match the db column names
 *      Updated character table name
 *      rewrote the code that pulls guild name/rank
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the item links customizable
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   September 7, 2019 - Kinglykrab
 *      Added Corruption and added commas to large number formatting
 *   March 7, 2020 - Maudigan
 *      stopped augments on inventory items from effecting stats
 *      add template vars for buttons to open bags
 *      add template vars for soft deleted characters
 *   March 8, 2020 - Maudigan
 *      implement shared bank
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *      added template vars for inventory slots
 *      comment & code cleanup for the template section
 *   March 16, 2020 - Maudigan
 *      prepared for implementation of dynamic guild ranks
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 2, 2020 - Maudigan
 *     dont show anon guild members names
 *     show stack size code
 *     added item icon and stacksize to the item stat/inspect windows
 *   April 6, 2020 - Maudigan
 *     Make the way bags display more dynamic so they can be resized easily
 *   April 10, 2020 - Maudigan
 *     added race/gender/face id template vars so we can show an avatar
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   March 16, 2022 - Maudigan
 *     added item type to the API for each item
 *   January 11, 2023 - Maudigan
 *     removed language references to heroic stats as they aren't used
 *   August 9, 2024 - Maudigan
 *     removed the calculated stats and use the new stored values
 *     add buffs
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
include_once(__DIR__ . "/include/itemclass.php");
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
if ($char->Permission('inventory')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get character info
$class      = $char->GetValue('class');
$guild_name = '';
$guild_rank = '';

if ($char->GetValue('anon') != 1 || $showguildwhenanon || $charbrowser_is_admin_page) {
   /* this will get implemented in the server code soon, uncomment and remove the code below
   //load guild name
   $tpl = <<<TPL
   SELECT guilds.name, guild_ranks.title 
   FROM guilds
   JOIN guild_members
     ON guilds.id = guild_members.guild_id
   JOIN guild_ranks
     ON guild_members.rank = guild_ranks.rank 
    AND guild_members.guild_id = guild_ranks.guild_id
   WHERE guild_members.char_id = '%s' 
   LIMIT 1
   TPL;
   $query = sprintf($tpl, $charID);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   { 
      $row = $cbsql->nextrow($result);
      $guild_name = $row['name'];
      $guild_rank = $row['title'];
   }*/

   //load guild name statically
   $tpl = <<<TPL
   SELECT guilds.name, guild_members.rank 
   FROM guilds
   JOIN guild_members
     ON guilds.id = guild_members.guild_id
   WHERE guild_members.char_id = '%s' 
   LIMIT 1
TPL;
   $query = sprintf($tpl, $charID);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   { 
      $row = $cbsql->nextrow($result);
      $guild_name = $row['name'];
      $guild_rank = $guildranks[$row['rank']];
   }
}


//FETCH BUFFS


//---------------------------------
//           BUFFS
//---------------------------------
//gets this chars buffs
$buffs = $char->GetTable('character_buffs');
$spell_ids = array();
$buff_count = 0;
if (is_array($buffs)) {

   $buff_count = count($buffs);
   //build a list of all the buff spell ids
   foreach ($buffs as $slot => $buff) {
      $spell_ids[] = $buff['spell_id'];
   }
   
   //get all the spells
   $tpl = <<<TPL
   SELECT id,
          new_icon,
          name
   FROM spells_new
   WHERE id in (%s)
TPL;
   $query = sprintf($tpl, implode(',',$spell_ids));
   $result = $cbsql_content->query($query);   
   $spells = $cbsql_content->fetch_all($result); 
   
   //join the buffs and spells
   $buffs = manual_join($buffs, 'spell_id', $spells, 'id', 'inner');
}
else {
   $buffs = array();
}
//leave place holders if there's less than 5 buffs
//just to fill out the buff container
$min_buff_count = 10;
$sparebuffs = $min_buff_count - $buff_count;

//recalc the buffs to not be less than the min
$buff_count = max($min_buff_count, $buff_count);




//FETCH SHARED PLAT 
 $tpl = <<<TPL
SELECT sharedplat
FROM account
WHERE account.id = '%s'  
TPL;
$query = sprintf($tpl, $char->GetValue('account_id'));
$result = $cbsql->query($query);

if ($row = $cbsql->nextrow($result)) {
   $sbpp = $row['sharedplat'];
}


/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'character');

 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'character' => 'character_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'HIGHLIGHT_GM' => (($highlightgm && $char->GetValue('gm'))? "GM":""),
   'GUILD' => getGuildLink($guild_name, $guild_rank),
   'REGEN' => number_format($char->getRegen()),
   'FT' => number_format($char->getFT()),
   'DS' => number_format($char->getDS()),
   'HASTE' => $char->getHaste(),
   'DELETED' => (($char->GetValue('deleted_at')) ? " ".$language['CHAR_DELETED']:""),
   'FIRST_NAME' => $name,
   'LAST_NAME' => $char->GetValue('last_name'),
   'TITLE' => $char->GetValue('title'),
   'GUILD_NAME' => $guild_name,
   'LEVEL' => $char->GetValue('level'),
   'CLASS' => $dbclassnames[$class],
   'RACE' => $dbracenames[$char->GetValue('race')],
   'RACE_ID' => $char->GetValue('race'),
   'GENDER_ID' => $char->GetValue('gender'),
   'FACE_ID' => $char->GetValue('face'),
   'AVATAR_IMG' => getAvatarImage($char->GetValue('race'), $char->GetValue('gender'), $char->GetValue('face')),
   'CLASS_NUM' => $class,
   'DEITY' => $dbdeities[$char->GetValue('deity')],
   'HP' => number_format($char->GetValue('calculated_hp')),
   'MANA' => number_format($char->GetValue('calculated_mana')),
   'ENDR' => number_format($char->GetValue('calculated_endurance')),
   'AC' => number_format($char->GetValue('calculated_ac')),
   'MIT_AC' => number_format($char->GetValue('calculated_ac')),
   'ATK' => number_format($char->GetValue('calculated_attack')),
   'STR' => number_format($char->getSTR()),
   'STA' => number_format($char->getSTA()),
   'DEX' => number_format($char->getDEX()),
   'AGI' => number_format($char->getAGI()),
   'INT' => number_format($char->getINT()),
   'WIS' => number_format($char->getWIS()),
   'CHA' => number_format($char->getCHA()),
   'HSTR' => number_format($char->getHSTR()),  
   'HSTA' => number_format($char->getHSTA()),  
   'HDEX' => number_format($char->getHDEX()),  
   'HAGI' => number_format($char->getHAGI()),  
   'HINT' => number_format($char->getHINT()),  
   'HWIS' => number_format($char->getHWIS()),  
   'HCHA' => number_format($char->getHCHA()), 
   'POISON' => $char->getPR(),
   'FIRE' => $char->getFR(),
   'MAGIC' => $char->getMR(),
   'DISEASE' => $char->getDR(),
   'COLD' => $char->getCR(),
   'CORRUPT' => $char->getCOR(),
   'HPOISON' => $char->getHPR(), 
   'HFIRE' => $char->getHFR(), 
   'HMAGIC' => $char->getHMR(), 
   'HDISEASE' => $char->getHDR(), 
   'HCOLD' => $char->getHCR(), 
   'HCORRUPT' => $char->getHCOR(),
   'WEIGHT' => round($char->getWT()/10),
   'PP' => (($char->Permission('coininventory')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('platinum'))),
   'GP' => (($char->Permission('coininventory')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('gold'))),
   'SP' => (($char->Permission('coininventory')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('silver'))),
   'CP' => (($char->Permission('coininventory')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('copper'))),
   'BPP' => (($char->Permission('coinbank')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('platinum_bank'))),
   'BGP' => (($char->Permission('coinbank')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('gold_bank'))),
   'BSP' => (($char->Permission('coinbank')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('silver_bank'))),
   'BCP' => (($char->Permission('coinbank')) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('copper_bank'))),
   'SBPP' => (($char->Permission('coinsharedbank')) ? $language['MESSAGE_DISABLED'] : number_format($sbpp)))
);

$cb_template->assign_vars(array(  
   'ROOT_URL' => $charbrowser_root_url,
   
   'L_HEADER_INVENTORY' => $language['CHAR_INVENTORY'],
   'L_HEADER_BANK' => $language['CHAR_BANK'],
   'L_SHARED_BANK' => $language['CHAR_SHARED_BANK'],
   'L_REGEN' => $language['CHAR_REGEN'],
   'L_FT' => $language['CHAR_FT'],
   'L_DS' => $language['CHAR_DS'],
   'L_HASTE' => $language['CHAR_HASTE'],
   'L_HP' => $language['CHAR_HP'],
   'L_MANA' => $language['CHAR_MANA'],
   'L_ENDR' => $language['CHAR_ENDR'],
   'L_AC' => $language['CHAR_AC'],
   'L_MIT_AC' => $language['CHAR_MIT_AC'],
   'L_ATK' => $language['CHAR_ATK'],
   'L_STR' => $language['CHAR_STR'],
   'L_STA' => $language['CHAR_STA'],
   'L_DEX' => $language['CHAR_DEX'],
   'L_AGI' => $language['CHAR_AGI'],
   'L_INT' => $language['CHAR_INT'],
   'L_WIS' => $language['CHAR_WIS'],
   'L_CHA' => $language['CHAR_CHA'],
   'L_POISON' => $language['CHAR_POISON'],
   'L_MAGIC' => $language['CHAR_MAGIC'],
   'L_DISEASE' => $language['CHAR_DISEASE'],
   'L_FIRE' => $language['CHAR_FIRE'],
   'L_COLD' => $language['CHAR_COLD'],
   'L_CORRUPT' => $language['CHAR_CORRUPT'],
   'L_WEIGHT' => $language['CHAR_WEIGHT'],
   'L_CONTAINER' => $language['CHAR_CONTAINER'], 
   'L_DONE' => $language['BUTTON_DONE'],
   'L_OPEN_BAG' => $language['CHAR_OPEN_BAG'])
);

//---------------------------------
//     SLOTS TEMPLATE VARS
//---------------------------------
//INVENTORY
for ( $i = SLOT_INVENTORY_START; $i <= SLOT_INVENTORY_END; $i++ ) {
   $cb_template->assign_block_vars("invslots", array( 
      'SLOT' => $i)
   );
}
//EQUIPMENT
for ( $i = SLOT_EQUIPMENT_START; $i <= SLOT_EQUIPMENT_END; $i++ ) {
   $cb_template->assign_block_vars("equipslots", array( 
      'SLOT' => $i)
   );
}
//BANK
for ( $i = SLOT_BANK_START; $i <= SLOT_BANK_END; $i++ ) {
   $cb_template->assign_block_vars("bankslots", array( 
      'SLOT' => $i)
   );
}
//SHARED BANK
for ( $i = SLOT_SHAREDBANK_START; $i <= SLOT_SHAREDBANK_END; $i++ ) {
   $cb_template->assign_block_vars("sharedbankslots", array( 
      'SLOT' => $i)
   );
}

//---------------------------------
// ITEM ICONS TEMPLATE VARS
//---------------------------------
$allitems = $char->getAllItems();

//INVENTORY
if (!$char->Permission('bags')) {
   foreach ($allitems as $value) {
      if ($value->type() != INVENTORY) continue; 
      $cb_template->assign_block_vars("invitem", array( 
         'SLOT' => $value->slot(),      
         'ICON' => $value->icon(),      
         'STACK' => $value->stack())
      );
      if ($value->slotcount() > 0) {
         $cb_template->assign_block_vars("invitem.switch_is_bag", array());
      }
   }
}
//EQUIPMENT
foreach ($allitems as $value) {
   if ($value->type() != EQUIPMENT) continue; 
   $cb_template->assign_block_vars("equipitem", array( 
      'SLOT' => $value->slot(),      
      'ICON' => $value->icon(),      
      'STACK' => $value->stack())
   );
}
//BANK
if (!$char->Permission('bank')) {
   foreach ($allitems as $value) {  
      if ($value->type() != BANK) continue;    
      $cb_template->assign_block_vars("bankitem", array( 
         'SLOT' => $value->slot(),  
         'ICON' => $value->icon(),      
         'STACK' => $value->stack())
      );
      if ($value->slotcount() > 0) {
         $cb_template->assign_block_vars("bankitem.switch_is_bag", array());
      }  
   }
}
//SHARED BANK
if (!$char->Permission('sharedbank')) {
   foreach ($allitems as $value) {  
      if ($value->type() != SHAREDBANK) continue;    
      $cb_template->assign_block_vars("sharedbankitem", array( 
         'SLOT' => $value->slot(),  
         'ICON' => $value->icon(),      
         'STACK' => $value->stack())
      );
      if ($value->slotcount() > 0) {
         $cb_template->assign_block_vars("sharedbankitem.switch_is_bag", array());
      }  
   }
}

//---------------------------------
//     BAG WINDOW TEMPLATE VARS
//---------------------------------
//these are the vars to drop items/slots/etc
//for bag contents, this does equipment,
//inventory, bank and shared bank
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $char->Permission('bags')) continue; 
   if ($value->type() == BANK && $char->Permission('bank')) continue;
   if ($value->type() == SHAREDBANK && $char->Permission('sharedbank')) continue;
   if ($value->slotcount() > 0)  {
       
      //stage the bag in a temporary array
      $tempbag = array(); 
      
      //create each empty slot in the bag
      for ($i = 1;$i <= $value->slotcount(); $i++) {
         $tempbag[$i] = 0;
      }
         
      //find the item that goes in this slot   
      foreach ($allitems as $subvalue) {
         if ($subvalue->type() == $value->slot()) {
            //if the item is in this bag, but the bag doesn't have enough
            //slots to display it, skip it
            if ($subvalue->vslot() > $value->slotcount() || $subvalue->vslot() > MAX_BAG_SLOTS) {
               continue;
            }
            $tempbag[$subvalue->vslot()] = array(
               'BI_SLOT' => $subvalue->slot(),
               'BI_RELATIVE_SLOT' => $subvalue->vslot(),
               'BI_ICON' => $subvalue->icon(),      
               'STACK' => $subvalue->stack()
            );
         }
      }
         
      //populate the template now   
      $cb_template->assign_block_vars("bags", array( 
         'SLOT' => $value->slot(),  
         'SLOTCOUNT' => $value->slotcount(),      
         'ROWS' => floor($value->slotcount()/2))
      );
      
      foreach($tempbag as $slot_id => $slot) {
         $cb_template->assign_block_vars("bags.bagslots", array( 
            'BS_SLOT' => $slot_id)
         );
         //if there's array data in it, it's got an item
         if (is_array($slot)) {
            $cb_template->assign_block_vars("bags.bagslots.bagitems", $slot
            );
         }
      }
   } 
}


//---------------------------------
//   ITEM WINDOW TEMPLATE VARS
//---------------------------------
//the item inspect windows that hold
//the item stats. this does equipment,
//inventory, bank and shared bank
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $char->Permission('bags')) continue; 
   if ($value->type() == BANK && $char->Permission('bank')) continue;
   if ($value->type() == SHAREDBANK && $char->Permission('sharedbank')) continue;
   
   $cb_template->assign_both_block_vars("item", array(
      'SLOT' => $value->slot(),     
      'ICON' => $value->icon(),   
      'NAME' => $value->name(),  
      'STACK' => $value->stack(),
      'ID' => $value->id(),
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $value->id())),
      'HTML' => $value->html(),
      'ITEMTYPE' => $value->skill())
   );
   for ( $i = 0 ; $i < $value->augcount() ; $i++ ) {
      $cb_template->assign_both_block_vars("item.augment", array(       
         'AUG_NAME' => $value->augname($i),
         'AUG_ID' => $value->augid($i),
         'AUG_LINK' => QuickTemplate($link_item, array('ITEM_ID' => $value->augid($i))),
         'AUG_ICON' => $value->augicon($i),
         'AUG_HTML' => $value->aughtml($i))
      );
   }
}


//---------------------------------
//           BUFFS
//---------------------------------
//output buffs
foreach ($buffs as $slot => $buff) {
   $cb_template->assign_both_block_vars("buffs", array(
      'ICON' => $buff['new_icon'],
      'NAME' => $buff['name'],
      'SPELL_ID' => $buff['spell_id'],
      'TIME' => tics_to_time($buff['ticsremaining']),
      'HREF' => QuickTemplate($link_spell, array('SPELL_ID' => $buff['spell_id'])),
      'SLOT' => $slot)
   );
}

//output extra buffs if needed to make sure we meet the minimum
//number of buffs just to fill out the buff window so it's not
//empty 
for ($i = 0; $i <= $sparebuffs; $i++) {
   $cb_template->assign_both_block_vars("placeholderbuffs", array());
}

//output a count
$cb_template->assign_var('BUFFCOUNT',$buff_count);
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('character');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>