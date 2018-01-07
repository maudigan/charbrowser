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
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/config.php");
include_once(__DIR__ . "/include/global.php");
include_once(__DIR__ . "/include/language.php");
include_once(__DIR__ . "/include/functions.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/statsclass.php");
include_once(__DIR__ . "/include/calculatestats.php");
include_once(__DIR__ . "/include/db.php");
  
 
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
if ($mypermission['inventory']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get character info
$last_name  = $char->GetValue('last_name');
$title      = $char->GetValue('title');
$level      = $char->GetValue('level');
$deity      = $char->GetValue('deity');
$baseSTR    = $char->GetValue('str'); 
$baseSTA    = $char->GetValue('sta');
$baseAGI    = $char->GetValue('agi');
$baseDEX    = $char->GetValue('dex');
$baseWIS    = $char->GetValue('wis');
$baseINT    = $char->GetValue('int');
$baseCHA    = $char->GetValue('cha');
$defense    = $char->GetValue('defense'); //TODO multi row table
$offense    = $char->GetValue('offense'); //TODO multi row table
$race       = $char->GetValue('race');
$class      = $char->GetValue('class');
$pp         = $char->GetValue('platinum');
$gp         = $char->GetValue('gold');
$sp         = $char->GetValue('silver');
$cp         = $char->GetValue('copper');
$bpp        = $char->GetValue('platinum_bank');
$bgp        = $char->GetValue('gold_bank');
$bsp        = $char->GetValue('silver_bank');
$bcp        = $char->GetValue('copper_bank'); 

//load guild name
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

//place where all the items stats are added up
$itemstats = new stats();

//holds all of the items and info about them
$allitems = array();

// pull characters inventory slotid is loaded as
// "myslot" since items table also has a slotid field.
$tpl = <<<TPL
SELECT items.*, inventory.augslot1, inventory.augslot2, 
       inventory.augslot3, inventory.augslot4, 
       inventory.augslot5, inventory.slotid AS myslot 
FROM items
JOIN inventory 
  ON items.id = inventory.itemid
WHERE inventory.charid = '%s'  
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
// loop through inventory results saving Name, Icon, and preload HTML for each
// item to be pasted into its respective div later
$tpl = <<<TPL
SELECT * 
FROM items 
WHERE id = '%s' 
LIMIT 1
TPL;
while ($row = $cbsql->nextrow($result)) {
   $tempitem = new item($row);
   for ($i = 1; $i <= 5; $i++) {
      if ($row["augslot".$i]) {
         $query = sprintf($tpl, $row["augslot".$i]);
         $augresult = $cbsql->query($query);
         $augrow = $cbsql->nextrow($augresult);
         $tempitem->addaug($augrow);
         $itemstats->additem($augrow);
      }
   }

   if ($tempitem->type() == EQUIPMENT)
      $itemstats->additem($row);
  
   if ($tempitem->type() == EQUIPMENT || $tempitem->type() == INVENTORY)
      $itemstats->addWT($row['weight']);
  
   $allitems[$tempitem->slot()] = $tempitem;
}
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'character' => 'character_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'HIGHLIGHT_GM' => (($highlightgm && $gm)? "GM":""),
   'REGEN' => $itemstats->regen(),
   'FT' => $itemstats->FT(),
   'DS' => $itemstats->DS(),
   'HASTE' => $itemstats->haste(),
   'FIRST_NAME' => $name,
   'LAST_NAME' => $last_name,
   'TITLE' => $title,
   'GUILD_NAME' => $guild_name,
   'LEVEL' => $level,
   'CLASS' => $dbclassnames[$class],
   'RACE' => $dbracenames[$race],
   'CLASS_NUM' => $class,
   'DEITY' => $dbdeities[$deity],
   'HP' => GetMaxHP($level,$class,($baseSTA+$itemstats->STA()),$itemstats->hp()),
   'MANA' => GetMaxMana($level,$class,($baseINT+$itemstats->INT()),($baseWIS+$itemstats->WIS()),+$itemstats->mana()),
   'ENDR' => GetMaxEndurance(($baseSTR+$itemstats->STR()),($baseSTA+$itemstats->STA()),($baseDEX+$itemstats->DEX()),($baseAGI+$itemstats->AGI()),$level,$itemstats->endurance()),
   'AC' => GetMaxAC(($baseAGI+$itemstats->AGI()), $level, $defense, $class, $itemstats->AC(), $race),
   'ATK' => GetMaxAtk($itemstats->attack(), ($baseSTR+$itemstats->STR()), $offense),
   'STR' => ($baseSTR+$itemstats->STR()),
   'STA' => ($baseSTA+$itemstats->STA()),
   'DEX' => ($baseDEX+$itemstats->DEX()),
   'AGI' => ($baseAGI+$itemstats->AGI()),
   'INT' => ($baseINT+$itemstats->INT()),
   'WIS' => ($baseWIS+$itemstats->WIS()),
   'CHA' => ($baseCHA+$itemstats->CHA()),
   'HSTR' => ($itemstats->HSTR()),  
   'HSTA' => ($itemstats->HSTA()),  
   'HDEX' => ($itemstats->HDEX()),  
   'HAGI' => ($itemstats->HAGI()),  
   'HINT' => ($itemstats->HINT()),  
   'HWIS' => ($itemstats->HWIS()),  
   'HCHA' => ($itemstats->HCHA()), 
   'POISON' => (PRbyRace($race)+$PRbyClass[$class]+$itemstats->PR()),
   'FIRE' => (FRbyRace($race)+$FRbyClass[$class]+$itemstats->FR()),
   'MAGIC' => (MRbyRace($race)+$MRbyClass[$class]+$itemstats->MR()),
   'DISEASE' => (DRbyRace($race)+$DRbyClass[$class]+$itemstats->DR()),
   'COLD' => (CRbyRace($race)+$CRbyClass[$class]+$itemstats->CR()),
   'HPOISON' => $itemstats->HPR(), 
   'HFIRE' => $itemstats->HFR(), 
   'HMAGIC' => $itemstats->HMR(), 
   'HDISEASE' => $itemstats->HDR(), 
   'HCOLD' => $itemstats->HCR(), 
   'WEIGHT' => round($itemstats->WT()/10),
   'PP' => (($mypermission['coininventory'])?$language['MESSAGE_DISABLED']:$pp),
   'GP' => (($mypermission['coininventory'])?$language['MESSAGE_DISABLED']:$gp),
   'SP' => (($mypermission['coininventory'])?$language['MESSAGE_DISABLED']:$sp),
   'CP' => (($mypermission['coininventory'])?$language['MESSAGE_DISABLED']:$cp),
   'BPP' => (($mypermission['coinbank'])?$language['MESSAGE_DISABLED']:$bpp),
   'BGP' => (($mypermission['coinbank'])?$language['MESSAGE_DISABLED']:$bgp),
   'BSP' => (($mypermission['coinbank'])?$language['MESSAGE_DISABLED']:$bsp),
   'BCP' => (($mypermission['coinbank'])?$language['MESSAGE_DISABLED']:$bcp))
);

$cb_template->assign_vars(array(  
   'L_HEADER_INVENTORY' => $language['CHAR_INVENTORY'],
   'L_HEADER_BANK' => $language['CHAR_BANK'],
   'L_REGEN' => $language['CHAR_REGEN'],
   'L_FT' => $language['CHAR_FT'],
   'L_DS' => $language['CHAR_DS'],
   'L_HASTE' => $language['CHAR_HASTE'],
   'L_HP' => $language['CHAR_HP'],
   'L_MANA' => $language['CHAR_MANA'],
   'L_ENDR' => $language['CHAR_ENDR'],
   'L_AC' => $language['CHAR_AC'],
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
   'L_HPOISON' => $language['CHAR_HPOISON'], 
   'L_HMAGIC' => $language['CHAR_HMAGIC'], 
   'L_HDISEASE' => $language['CHAR_HDISEASE'], 
   'L_HFIRE' => $language['CHAR_HFIRE'], 
   'L_HCOLD' => $language['CHAR_HCOLD'], 
   'L_WEIGHT' => $language['CHAR_WEIGHT'],
   'L_AAS' => $language['BUTTON_AAS'],
   'L_KEYS' => $language['BUTTON_KEYS'],
   'L_FLAGS' => $language['BUTTON_FLAGS'],
   'L_SKILLS' => $language['BUTTON_SKILLS'],
   'L_CORPSE' => $language['BUTTON_CORPSE'],
   'L_INVENTORY' => $language['BUTTON_INVENTORY'],
   'L_FACTION' => $language['BUTTON_FACTION'],
   'L_BOOKMARK' => $language['BUTTON_BOOKMARK'],
   'L_CHARMOVE' => $language['BUTTON_CHARMOVE'],
   'L_CONTAINER' => $language['CHAR_CONTAINER'],
   'L_DONE' => $language['BUTTON_DONE'])
);



//dump inventory items ICONS
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $mypermission['bags']) continue; 
   if ($value->type() == EQUIPMENT || $value->type() == INVENTORY)
      $cb_template->assign_block_vars("invitem", array( 
         'SLOT' => $value->slot(),      
         'ICON' => $value->icon(),
         'ISBAG' => (($value->slotcount() > 0) ? "true":"false"))
      );
}



//dump bags windows
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $mypermission['bags']) continue; 
   if ($value->type() == BANK && $mypermission['bank']) continue;
   if ($value->slotcount() > 0)  {
  
      $cb_template->assign_block_vars("bags", array( 
         'SLOT' => $value->slot(),      
         'ROWS' => floor($value->slotcount()/2))
      );
       
      for ($i = 1;$i <= $value->slotcount(); $i++) 
         $cb_template->assign_block_vars("bags.bagslots", array( 
            'BS_SLOT' => $i)
         );
         
      foreach ($allitems as $subvalue) 
         if ($subvalue->type() == $value->slot()) 
            $cb_template->assign_block_vars("bags.bagitems", array( 
               'BI_SLOT' => $subvalue->slot(),
               'BI_RELATIVE_SLOT' => $subvalue->vslot(),
               'BI_ICON' => $subvalue->icon())
            );
   } 
}


//dump bank items ICONS
if (!$mypermission['bank']) {
   foreach ($allitems as $value) {
      if ($value->type() == BANK) 
         $cb_template->assign_block_vars("bankitem", array( 
            'SLOT' => $value->slot(),  
            'ICON' => $value->icon(),
            'ISBAG' => (($value->slotcount() > 0) ? "true":"false"))
         );
   }
}

//dump items WINDWOS
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $mypermission['bags']) continue; 
   if ($value->type() == BANK && $mypermission['bank']) continue;
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