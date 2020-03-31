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
 *      
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];
     
//character initializations 
$char = new profile($charName, $cbsql, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['inventory']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get character info
$class      = $char->GetValue('class');

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


//---------------------------------
//HP CALCULATION
//---------------------------------
$totalHP = $char->CalcMaxHP($calc_rows_hp);
foreach ($calc_rows_hp as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


//---------------------------------
//ENDURANCE CALCULATION
//---------------------------------
$totalEndurance = $char->CalcMaxEndurance($calc_rows_end);
foreach ($calc_rows_end as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


//---------------------------------
//MANA CALCULATION
//---------------------------------
$totalMana = $char->CalcMaxMana($calc_rows_mana);
foreach ($calc_rows_mana as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


//---------------------------------
// MITIGATION AC CALCULATION
//---------------------------------
$totalMitigationAC = $char->ACSum(false, $calc_rows_mitigation_ac);
foreach ($calc_rows_mitigation_ac as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


//---------------------------------
//AC CALCULATION
//---------------------------------
$totalAC = $char->GetDisplayAC($calc_rows_ac);
foreach ($calc_rows_ac as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


//---------------------------------
//ATTCK CALCULATION
//---------------------------------
$totalAttack = $char->GetTotalATK($calc_rows_atk);
foreach ($calc_rows_atk as $row) {
   $cb_template->assign_both_block_vars($row['TYPE'], $row);
}


$cb_template->assign_both_vars(array(  
   'HIGHLIGHT_GM' => (($highlightgm && $gm)? "GM":""),
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
   'CLASS_NUM' => $class,
   'DEITY' => $dbdeities[$char->GetValue('deity')],
   'HP' => number_format($totalHP),
   'MANA' => number_format($totalMana),
   'ENDR' => number_format($totalEndurance),
   'AC' => number_format($totalAC),
   'MIT_AC' => number_format($totalMitigationAC),
   'ATK' => number_format($totalAttack),
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
   'PP' => (($mypermission['coininventory']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('platinum'))),
   'GP' => (($mypermission['coininventory']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('gold'))),
   'SP' => (($mypermission['coininventory']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('silver'))),
   'CP' => (($mypermission['coininventory']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('copper'))),
   'BPP' => (($mypermission['coinbank']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('platinum_bank'))),
   'BGP' => (($mypermission['coinbank']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('gold_bank'))),
   'BSP' => (($mypermission['coinbank']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('silver_bank'))),
   'BCP' => (($mypermission['coinbank']) ? $language['MESSAGE_DISABLED'] : number_format($char->GetValue('copper_bank'))),
   'SBPP' => (($mypermission['coinsharedbank']) ? $language['MESSAGE_DISABLED'] : number_format($sbpp)))
);

$cb_template->assign_vars(array(  
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
   'L_HSTR' => $language['CHAR_HSTR'],  
   'L_HSTA' => $language['CHAR_HSTA'], 
   'L_HDEX' => $language['CHAR_HDEX'], 
   'L_HAGI' => $language['CHAR_HAGI'], 
   'L_HINT' => $language['CHAR_HINT'], 
   'L_HWIS' => $language['CHAR_HWIS'], 
   'L_HCHA' => $language['CHAR_HCHA'], 
   'L_POISON' => $language['CHAR_POISON'],
   'L_MAGIC' => $language['CHAR_MAGIC'],
   'L_DISEASE' => $language['CHAR_DISEASE'],
   'L_FIRE' => $language['CHAR_FIRE'],
   'L_COLD' => $language['CHAR_COLD'],
   'L_CORRUPT' => $language['CHAR_CORRUPT'],
   'L_HPOISON' => $language['CHAR_HPOISON'], 
   'L_HMAGIC' => $language['CHAR_HMAGIC'], 
   'L_HDISEASE' => $language['CHAR_HDISEASE'], 
   'L_HFIRE' => $language['CHAR_HFIRE'], 
   'L_HCOLD' => $language['CHAR_HCOLD'], 
   'L_HCORRUPT' => $language['CHAR_HCORRUPT'],
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
if (!$mypermission['bags']) {
   foreach ($allitems as $value) {
      if ($value->type() != INVENTORY) continue; 
      $cb_template->assign_block_vars("invitem", array( 
         'SLOT' => $value->slot(),      
         'ICON' => $value->icon())
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
      'ICON' => $value->icon())
   );
}
//BANK
if (!$mypermission['bank']) {
   foreach ($allitems as $value) {  
      if ($value->type() != BANK) continue;    
      $cb_template->assign_block_vars("bankitem", array( 
         'SLOT' => $value->slot(),  
         'ICON' => $value->icon())
      );
      if ($value->slotcount() > 0) {
         $cb_template->assign_block_vars("bankitem.switch_is_bag", array());
      }  
   }
}
//SHARED BANK
if (!$mypermission['sharedbank']) {
   foreach ($allitems as $value) {  
      if ($value->type() != SHAREDBANK) continue;    
      $cb_template->assign_block_vars("sharedbankitem", array( 
         'SLOT' => $value->slot(),  
         'ICON' => $value->icon())
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
   if ($value->type() == INVENTORY && $mypermission['bags']) continue; 
   if ($value->type() == BANK && $mypermission['bank']) continue;
   if ($value->type() == SHAREDBANK && $mypermission['sharedbank']) continue;
   if ($value->slotcount() > 0)  {
  
      $cb_template->assign_block_vars("bags", array( 
         'SLOT' => $value->slot(),      
         'ROWS' => floor($value->slotcount()/2))
      );
       
      for ($i = 1;$i <= $value->slotcount(); $i++) {
         $cb_template->assign_block_vars("bags.bagslots", array( 
            'BS_SLOT' => $i)
         );
      }
         
      foreach ($allitems as $subvalue) {
         if ($subvalue->type() == $value->slot()) {
            $cb_template->assign_block_vars("bags.bagitems", array( 
               'BI_SLOT' => $subvalue->slot(),
               'BI_RELATIVE_SLOT' => $subvalue->vslot(),
               'BI_ICON' => $subvalue->icon())
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
   if ($value->type() == INVENTORY && $mypermission['bags']) continue; 
   if ($value->type() == BANK && $mypermission['bank']) continue;
   if ($value->type() == SHAREDBANK && $mypermission['sharedbank']) continue;
   
   $cb_template->assign_both_block_vars("item", array(
      'SLOT' => $value->slot(),     
      'ICON' => $value->icon(),   
      'NAME' => $value->name(),
      'ID' => $value->id(),
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $value->id())),
      'HTML' => $value->html())
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
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('character');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>