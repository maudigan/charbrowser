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
 *   April 17, 2020 - initial revision (Maudigan) 
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 3, 2020 - Maudigan
 *     optimize character initialization
 *   March 16, 2022 - Maudigan
 *     added item type to the API for each item
 *   January 11, 2023 - Maudigan
 *     removed the heroic stats since they aren't used
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
include_once(__DIR__ . "/include/bot_profile.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
       SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/
$botName = preg_Get_Post('bot', '/^[a-zA-Z]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_BOT'], true);
     
//bot initializations 
$bot = new Charbrowser_Bot($botName); //the profile class will sanitize the bot name
$charID = $bot->char_id(); 
$botID = $bot->bot_id(); 
$botName = $bot->GetValue('name');

//char initialization      
$char = new Charbrowser_Character($charID, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');

//block view if user level doesnt have permission
if ($char->Permission('bot')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get bot info
$class      = $bot->GetValue('class');


/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$botName.$language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($charName, 'bot');

 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'bot' => 'bot_body.tpl')
);


$cb_template->assign_both_vars(array(  
   'NAME' => $charName,
   'REGEN' => number_format($bot->getRegen()),
   'FT' => number_format($bot->getFT()),
   'DS' => number_format($bot->getDS()),
   'HASTE' => $bot->getHaste(),
   'DELETED' => (($char->GetValue('deleted_at')) ? " ".$language['CHAR_DELETED']:""),
   'FIRST_NAME' => $botName,
   'LAST_NAME' => $bot->GetValue('last_name'),
   'TITLE' => $bot->GetValue('title'),
   'LEVEL' => $bot->GetValue('level'),
   'CLASS' => $dbclassnames[$class],
   'RACE' => $dbracenames[$bot->GetValue('race')],
   'RACE_ID' => $bot->GetValue('race'),
   'GENDER_ID' => $bot->GetValue('gender'),
   'FACE_ID' => $bot->GetValue('face'),
   'AVATAR_IMG' => getAvatarImage($bot->GetValue('race'), $bot->GetValue('gender'), $bot->GetValue('face')),
   'CLASS_NUM' => $class,
   'DEITY' => $dbdeities[$bot->GetValue('deity')],
   'HP' => number_format($bot->GetItemHP()),
   'MANA' => number_format($bot->GetItemMana()),
   'ENDR' => number_format($bot->GetItemEndurance()),
   'AC' => number_format($bot->GetItemAC()),
   'ATK' => number_format($bot->GetItemATK()),
   'STR' => number_format($bot->getSTR()),
   'STA' => number_format($bot->getSTA()),
   'DEX' => number_format($bot->getDEX()),
   'AGI' => number_format($bot->getAGI()),
   'INT' => number_format($bot->getINT()),
   'WIS' => number_format($bot->getWIS()),
   'CHA' => number_format($bot->getCHA()),
   'HSTR' => number_format($bot->getHSTR()),  
   'HSTA' => number_format($bot->getHSTA()),  
   'HDEX' => number_format($bot->getHDEX()),  
   'HAGI' => number_format($bot->getHAGI()),  
   'HINT' => number_format($bot->getHINT()),  
   'HWIS' => number_format($bot->getHWIS()),  
   'HCHA' => number_format($bot->getHCHA()), 
   'POISON' => $bot->getPR(),
   'FIRE' => $bot->getFR(),
   'MAGIC' => $bot->getMR(),
   'DISEASE' => $bot->getDR(),
   'COLD' => $bot->getCR(),
   'CORRUPT' => $bot->getCOR(),
   'HPOISON' => $bot->getHPR(), 
   'HFIRE' => $bot->getHFR(), 
   'HMAGIC' => $bot->getHMR(), 
   'HDISEASE' => $bot->getHDR(), 
   'HCOLD' => $bot->getHCR(), 
   'HCORRUPT' => $bot->getHCOR(),
   'WEIGHT' => round($bot->getWT()/10))
);

$cb_template->assign_vars(array(  
   'ROOT_URL' => $charbrowser_root_url,
   
   'L_HEADER_INVENTORY' => $language['CHAR_INVENTORY'],
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
   'L_POISON' => $language['CHAR_POISON'],
   'L_MAGIC' => $language['CHAR_MAGIC'],
   'L_DISEASE' => $language['CHAR_DISEASE'],
   'L_FIRE' => $language['CHAR_FIRE'],
   'L_COLD' => $language['CHAR_COLD'],
   'L_CORRUPT' => $language['CHAR_CORRUPT'],
   'L_WEIGHT' => $language['CHAR_WEIGHT'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//---------------------------------
//     SLOTS TEMPLATE VARS
//---------------------------------
//EQUIPMENT
for ( $i = SLOT_EQUIPMENT_START; $i <= SLOT_EQUIPMENT_END; $i++ ) {
   $cb_template->assign_block_vars("equipslots", array( 
      'SLOT' => $i)
   );
}


//---------------------------------
// ITEM ICONS TEMPLATE VARS
//---------------------------------
$allitems = $bot->getAllItems();


//EQUIPMENT
foreach ($allitems as $value) {
   if ($value->type() != EQUIPMENT) continue; 
   $cb_template->assign_block_vars("equipitem", array( 
      'SLOT' => $value->slot(),      
      'ICON' => $value->icon(),      
      'STACK' => $value->stack())
   );
}


//---------------------------------
//   ITEM WINDOW TEMPLATE VARS
//---------------------------------
//the item inspect windows that hold
//the item stats. this does equipment,
//inventory, bank and shared bank
foreach ($allitems as $value) {   
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
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('bot');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>