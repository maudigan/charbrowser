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
 *   October 30, 2022 - initial revision (Maudigan) 
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
include_once(__DIR__ . "/include/corpse_profile.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
       SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/
$CorpseID = preg_Get_Post('corpse', '/^[0-9]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_CORPSE'], true);
     
//bot initializations 
$corpse = new Charbrowser_Corpse($CorpseID); //the profile class will sanitize the char id
$charID = $corpse->char_id(); 
$CorpseID = $corpse->corpse_id();
$corpseName = $corpse->GetValue('charname');

//char initialization      
$char = new Charbrowser_Character($charID, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');

//block view if user level doesnt have permission
if ($char->Permission('corpse')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get corpse info
$corpse_class      = $corpse->GetValue('class');
$corpse_race       = $corpse->GetValue('race');
$corpse_gender     = $corpse->GetValue('gender');
$corpse_face       = $corpse->GetValue('face');


//get zone data
$tpl = <<<TPL
   SELECT long_name, short_name, zoneidnumber
   FROM zone 
   WHERE zoneidnumber = '%s' 
TPL;
$query = sprintf($tpl, $corpse->GetValue('zone_id'));
$result = $cbsql_content->query($query);

if (!($zone = $cbsql->nextrow($result)))
{
   $zone = array(
      'long_name' => 'unknown',
      'short_name' => 'unknown',
      'zoneidnumber' => '0'
   );
}


/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$corpseName.$language['PAGE_TITLES_CORPSE'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($charName, 'corpse');

 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'corpse' => 'corpse_body.tpl')
);


//prepare the link to the map
$find = array(
   'ZONE_SHORTNAME'  => $zone['short_name'],
   'ZONE_ID'         => $zone["zoneidnumber"],
   'TEXT'            => $charName."`s%20Corpse",
   'X'               => floor($corpse->GetValue('x')),
   'Y'               => floor($corpse->GetValue('y'))
);
$link_to_map = QuickTemplate($link_map, $find);

//prepare the link to the death zone
$find = array(
   'ZONE_SHORTNAME'  => $zone['short_name'],
   'ZONE_ID'         => $zone["zoneidnumber"]
);
$link_to_zone_died = QuickTemplate($link_zone, $find);

$tod = strtotime($corpse->GetValue('time_of_death'));
   
$cb_template->assign_both_vars(array(  
   'NAME' => $charName,
   'FIRST_NAME' => $corpseName.$language['PAGE_TITLES_CORPSE'],
   'LAST_NAME' => $char->GetValue('last_name'),
   'TITLE' => $char->GetValue('title'),
   'LEVEL' => $corpse->GetValue('level'),
   'PP' => $corpse->GetValue('platinum'),
   'GP' => $corpse->GetValue('gold'),
   'SP' => $corpse->GetValue('silver'),
   'CP' => $corpse->GetValue('copper'),
   'HEADING' => $corpse->GetValue('heading'),
   'TOD' =>  date('l, j F Y g:i A', $tod),
   'TOD_DAY' =>  date('l', $tod),
   'TOD_DATE' =>  date('F j, Y', $tod),
   'TOD_TIME' =>  date('g:i A', $tod),
   'REZZED_TEXT' => ($corpse->GetValue('is_rezzed') ? $language['CORPSE_REZZED_YES']:$language['CORPSE_REZZED_NO']),  
   'REZZED_STYLE' => ($corpse->GetValue('is_rezzed') ? "CB_Avatar_Rezzed":""),  
   'DIED_ZONE_LONG_NAME' => $zone['long_name'],
   'DIED_ZONE_SHORT_NAME' => $zone['short_name'],
   'DIED_LOC' => "(".floor($corpse->GetValue('y')).", ".floor($corpse->GetValue('x')).")",
   'DIED_ZONE_ID' => $zone['zoneidnumber'],
   'LINK_MAP' => $link_to_map,
   'DIED_LINK_ZONE' => $link_to_zone_died,
   'CLASS' => $dbclassnames[$corpse_class],
   'RACE' => $dbracenames[$corpse_race],
   'RACE_ID' => $corpse_race,
   'GENDER_ID' => $corpse_gender,
   'FACE_ID' => $corpse_face,
   'AVATAR_IMG' => getAvatarImage($corpse_race, $corpse_gender, $corpse_face, $corpse->GetValue('is_buried')),
   'CLASS_NUM' => $corpse_class,
   'DEITY' => $dbdeities[$corpse->GetValue('deity')],
   'WEIGHT' => round($corpse->getWT()/10))
);

$cb_template->assign_vars(array(  
   'ROOT_URL' => $charbrowser_root_url,
   
   'L_HEADER_INVENTORY' => $language['CHAR_INVENTORY'],
   'L_WEIGHT' => $language['CHAR_WEIGHT'],
   'L_WEIGHT_MAX' => $language['CORPSE_WEIGHT_MAX'],
   'L_CONTAINER' => $language['CHAR_CONTAINER'], 
   'L_BURIED' => $language['CORPSE_BURIED'],
   'L_TOD' => $language['CORPSE_TOD'],
   'L_VIEW_ON_MAP' => $language['CORPSE_VIEW_ON_MAP'],
   'L_STATUS' => $language['CORPSE_STATUS'],
   'L_BURIED_PREAMBLE' => $language['CORPSE_BURIED_PREAMBLE'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//burried switch
if ($corpse->GetValue('is_buried'))
{
   $find = array(
      'ZONE_SHORTNAME'  => "shadowrest",
      'ZONE_ID'         => "187"
   );
   $link_to_zone_buried = QuickTemplate($link_zone, $find);
   
   $cb_template->assign_both_block_vars("switch_is_buried", array(
      'ZONE_LONG_NAME' => 'Shadowrest',
      'ZONE_SHORT_NAME' => 'shadowrest',
      'ZONE_ID' => '`87',
      'LINK_TO_ZONE_BURIED' => $link_to_zone_buried)
   );
}



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


//---------------------------------
// ITEM ICONS TEMPLATE VARS
//---------------------------------
$allitems = $corpse->getAllItems();

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

//---------------------------------
//     BAG WINDOW TEMPLATE VARS
//---------------------------------
//these are the vars to drop items/slots/etc
//for bag contents, this does equipment,
//inventory, bank and shared bank
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $char->Permission('bags')) continue; 
   
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
//inventory
foreach ($allitems as $value) {
   if ($value->type() == INVENTORY && $char->Permission('bags')) continue; 
   
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
$cb_template->pparse('corpse');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>