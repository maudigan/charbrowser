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
 *   October 25, 2022 - Maudigan
 *      initial revision of the leadership window
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
include_once(__DIR__ . "/include/itemcache.php");
include_once(__DIR__ . "/include/spellcache.php");


/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
$charName = $_GET['char'];

//we dont always have a charname, just when we crossreference
//a users inventory to all the items being bought
//when we have one we need to fetch their permissions to
//make sure we don't reveal any of their items
if ($charName)
{
   //character initializations 
   $char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
   $charID = $char->char_id(); 
   $name = $char->GetValue('name');
   $mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

   //block view if user level doesnt have permission
   if ($mypermission['inventory']) cb_message_die($language['MESSAGE_ERROR'],$language['BARTER_SELLER_NOPERM']);
}


/*********************************************
             GET/VALIDATE VARS
*********************************************/
$start      = (($_GET['start']) ? $_GET['start'] : "0");
$orderby    = (($_GET['orderby']) ? $_GET['orderby'] : "Name");
$invorderby    = (($_GET['invorderby']) ? $_GET['invorderby'] : "Name");
$item       = $_GET['item'];
$buyer       = $_GET['buyer'];
$direction  = (($_GET['direction']=="DESC") ? "DESC" : "ASC");
$invdirection  = (($_GET['invdirection']=="DESC") ? "DESC" : "ASC");

$perpage=30;

//build baselink
$baselink=(($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=barter&char=$name&buyer=$buyer&item=$item";

//security against sql injection
if (!IsAlphaSpace($item)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_ALPHA']);
if (!IsAlphaSpace($buyer)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NAME_ALPHA']);
if (!IsAlphaSpace($orderby)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ORDER_ALPHA']);
if (!is_numeric($start)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_START_NUMERIC']);


//dont display barter if blocked in config.php
if ($blockbarter) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
         GATHER ALL THE PAGE DATA
*********************************************/

//generating our list of items is problematic because the barter records and the item
// records can be in a different database. We also need to get the sellers inventory
// which is crossreferenced in the barter records. We accomplish this by querying the 
// seller inventory first. Then we get all the barter items. We can then manually filter 
// the barter by the seller inventory (if we're viewing one), only leaving matches in the  
// barter recordset. We then query all of the item records left in the barter recordset, 
// adding any search filters. We do an inner join of those item records and the barter items. 
// We can then manually sort and paginate those barter results. We then query all the item
// records for the sellers inventory and manually join those as well. Those two recordsets
// are temporarily merged to generate a list of spell effect ID's and those spells are then
// queried into the spellcache. Finally we seperately dump out the seller and barter 
// recordsets into seperate template segments


//GET SELLER INVENTORY
//if a $name is selected then we need to crossreferene the seller inventory
//to what items buyers are wanting
if ($name) 
{
   $filters = array();
   //filter by seller name
   $filters[] = "character_data.name = '".$name."'";
   
   //cant sell nodrop
   $filters[] = "instnodrop = 0";
   
   //we need to filter certain slots out if the seller has privacy settings
   if ($mypermission['bags']) 
   {
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_INVENTORY_START.' AND '.SLOT_INVENTORY_END;
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_INVENTORY_BAGS_START.' AND '.SLOT_INVENTORY_BAGS_END;
   }
   if ($mypermission['bank'])
   {
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_BANK_START.' AND '.SLOT_BANK_END;
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_BANK_BAGS_START.' AND '.SLOT_BANK_BAGS_END;
   }
   if ($mypermission['sharedbank'])
   {
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_SHAREDBANK_START.' AND '.SLOT_SHAREDBANK_END;
      $filters[] = 'inventory.slotid NOT BETWEEN '.SLOT_SHAREDBANK_BAG_START.' AND '.SLOT_SHAREDBANK_BAG_END;
   }
   
   $where = generate_where($filters);


   $tpl = <<<TPL
      SELECT inventory.itemid, 
             COUNT(*) as seller_qty,
             SUM(charges) as seller_charges
      FROM inventory
      LEFT JOIN character_data
             ON inventory.charid = character_data.id
      %s
      GROUP BY inventory.itemid
TPL;

   $query = sprintf($tpl, $where);
   $result = $cbsql->query($query);
      
   //grab all of the rows into an array
   $seller_inventory_rows = $cbsql->fetch_all($result);
}


//QUERY ALL THE BUYER ITEMS, PREFILTERED BY BUYER FIELDS
//build the where clause
$filters = array();
if ($buyer) $filters[] = "buychar.name = '".$buyer."'";
//cant buy from yourself
if ($name) $filters[] = "buychar.name != '".$name."'";
$where = generate_where($filters);


//no seller means we just show everything being bought
$tpl = <<<TPL
   SELECT buychar.name as charactername,
          buyer.price as buyerprice,
          buyer.itemid,
          buyer.quantity 
   FROM character_data buychar
   INNER JOIN buyer
           ON buychar.id = buyer.charid
   %s
TPL;


$query = sprintf($tpl, $where);
$result = $cbsql->query($query);

//error if there's no items being bought
if ($cbsql->rows($result)) 
{
   //build item id list for items being bought   
   $filtered_buyer_rows = $cbsql->fetch_all($result); 
   

   //filter buyer items by seller
   if ($name)
   {
      //if there's a seller, left join the buyer rows to filter out
      //items that the seller doesn't have
      $filtered_buyer_rows = manual_join($filtered_buyer_rows, 'itemid', $seller_inventory_rows, 'itemid', 'inner');
   }
   
   
   //PREFECTH BUYER ITEM RECORDS
   //we cant use the itemcache class for this since we need
   //to filter the query results by the search criteria
   //instead we'll query the items with the fitlers then
   //innerjoin this to the buyer records
   if (count($filtered_buyer_rows) > 0)
   {
      $filtered_buyer_item_ids = get_id_list($filtered_buyer_rows, 'itemid');
      
      //build the where clause
      $filters = array();
      $filters[] = "id IN (".$filtered_buyer_item_ids.")";
      if ($item) $filters[] = "Name LIKE '%".str_replace("_", "%", str_replace(" ","%",$item))."%'";
      $where = generate_where($filters);

      $tpl = <<<TPL
      SELECT *
      FROM items 
      %s
TPL;

      $query = sprintf($tpl, $where);
      $result = $cbsql_content->query($query);

      //only continue if we have rows
      if ($cbsql->rows($result)) 
      {
         //get the item results as an array
         $filtered_items = $cbsql_content->fetch_all($result); 

         //JOIN BUYER ITEMS TO FILTERED ITEM RESULTS
         //loop through the trader rows and join the item stats to it in a new array
         $buyer_item_results = manual_join($filtered_buyer_rows, 'itemid', $filtered_items, 'id', 'inner');
         $totalitems = count($buyer_item_results);

         //DO A MANUAL SORT OF THE RESULTS
         if ($orderby == 'Name' || $orderby == 'charactername') {
            $sort_type = 'string';
         }
         else {
            $sort_type = 'int';
         }
         sort_by($buyer_item_results, $orderby, $direction, $sort_type);


         //LIMIT TO 1 PAGE OF RESULTS
         $truncated_buyer_results = array();
         $finish = min($start + $perpage, $totalitems);
         for ($i = $start; $i < $finish; $i++) { 
            $truncated_buyer_results[] = $buyer_item_results[$i];
         }
      }
   }
}

//GET THE ITEM RECORDS FOR SELLER INVENTORY
if ($name && count($seller_inventory_rows) > 0)
{
   $seller_item_ids = get_id_list($seller_inventory_rows, 'itemid');
   
   $tpl = <<<TPL
   SELECT *
   FROM items 
   WHERE id IN (%s)
   AND nodrop = 1
TPL;
   $query = sprintf($tpl, $seller_item_ids);
   $result = $cbsql_content->query($query);
         
   //only continue if we have rows
   if ($cbsql->rows($result)) 
   {
      //get the item results as an array
      $seller_item_list = $cbsql_content->fetch_all($result); 
   }
   
   //JOIN SELLER INVENTORY TO FILTERED ITEM RESULTS
   //loop through the trader rows and join the item stats to it in a new array
   $seller_item_results = manual_join($seller_inventory_rows, 'itemid', $seller_item_list, 'id', 'inner');
   
   //CREATE COMPOSITE QUANTITY COLUMN
   //the quantity column requires data from the seller inventory record, and from the item record
   //so we need to manually create that column so we can sort by it
   foreach ($seller_item_results as &$seller_item)
   {
      if ($seller_item['stackable'])
      {
         $seller_item['quantity'] = $seller_item['seller_charges'];
      }
      else
      {
         $seller_item['quantity'] = $seller_item['seller_qty'];
      }
   }
   
   //DO A MANUAL SORT OF THE RESULTS
   if ($invorderby == 'Name') {
      $sort_type = 'string';
   }
   else {
      $sort_type = 'int';
   }
   sort_by($seller_item_results, $invorderby, $invdirection, $sort_type);
}


//BUILD SPELL CACHE FOR SELLER AND BUYER ITEM EFFECTS
//build a combined list of buyer/seller items
if (is_array($truncated_buyer_results) && is_array($seller_item_results))
{
   $merge_results = array_merge($truncated_buyer_results, $seller_item_results);
}
elseif (is_array($truncated_buyer_results))
{
   $merge_results = $truncated_buyer_results;
}
elseif (is_array($seller_item_results))
{
   $merge_results = $seller_item_results;
}
else
{
   $merge_results = array();
}

//trigger the cache to populate
$cbspellcache->build_cache_itemset($merge_results);

/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_BARTER'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
//if you're looking at buyers based off what a character has to sell, treat it like
//a profile page
if ($name) {
   output_profile_menu($name, 'barter');
}


/*********************************************
              POPULATE BODY
*********************************************/
//build body template
$cb_template->set_filenames(array(
  'barter' => 'barter_body.tpl')
);

$cb_template->assign_both_vars(array(
   'ORDERBY' => $orderby,
   'DIRECTION' => $direction,
   'START' => $start,
   'PERPAGE' => $perpage,
   'TOTALITEMS' => $totalitems,
   'ITEM' => $item,
   'BUYER' => $buyer,
   'SELLER' => $name)
);

$cb_template->assign_vars(array(
   'STORENAME' => ($name) ? " - ".$name : "",
   'INV_ORDER_LINK' => $baselink."&direction=$direction&orderby=$orderby&start=$start&invdirection=".(($invdirection=="ASC") ? "DESC":"ASC"),
   'ORDER_LINK' => $baselink."&invdirection=$invdirection&invorderby=$invorderby&start=$start&direction=".(($direction=="ASC") ? "DESC":"ASC"),
   'PAGINATION' => cb_generate_pagination("$baselink&orderby=$orderby&direction=$direction", $totalitems, $perpage, $start, true),

   'L_BARTER' => $language['BARTER_BARTER'],
   'L_NAME' => $language['BARTER_NAME'],
   'L_SELLER' => $language['BARTER_SELLER'],
   'L_PRICE' => $language['BARTER_PRICE'],
   'L_ITEM' => $language['BARTER_ITEM'],
   'L_QTY' => $language['BARTER_QUANTITY'],
   'L_SEARCH' => $language['BARTER_SEARCH'],
   'L_SEARCH_NAME' => $language['BARTER_SEARCH_NAME'],
   'L_SELLERS_INVENTORY' => sprintf($language['BARTER_SELLERS_INVENTORY'], $name),
   'L_MATCHING_BUYERS' => $language['BARTER_MATCHING_BUYERS'])
);

//dump buyer items for this page
$slotcounter = 0;
if (is_array($truncated_buyer_results))
{
   foreach ($truncated_buyer_results as $lot)
   {
      $tempitem = new item($lot);
      $price = $lot["buyerprice"];
      $plat = number_format(floor($price/1000));
      $price = $price % 1000;
      $gold = number_format(floor($price/100));
      $price = $price % 100;
      $silver = number_format(floor($price/10));
      $copper  = number_format($price % 10);
      $cb_template->assign_both_block_vars("buyer_items", array(
         'BUYER' => $lot['charactername'],
         'QUANTITY' => $lot['quantity'],
         'PRICE' => (($plat)?$plat."p ":"").(($gold)?$gold."g ":"").(($silver)?$silver."s ":"").(($copper)?$copper."c ":""),
         'NAME' => $tempitem->name(),
         'ID' => $tempitem->id(),
         'ICON' => $tempitem->icon(),
         'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $tempitem->id())),
         'HTML' => $tempitem->html(),
         'ITEMTYPE' => $tempitem->skill(),
         'SLOT' => $slotcounter)
      );
      $slotcounter ++;
   }
}

//dump seller items for this page
if ($name)
{
   //template switch since we don't always show seller results
   $cb_template->assign_both_block_vars("switch_seller_set", array());
   
   if (is_array($seller_item_results))
   {
      foreach ($seller_item_results as $lot)
      {
         $tempitem = new item($lot);
            
         $cb_template->assign_both_block_vars("switch_seller_set.seller_items", array(
            'QUANTITY' => number_format($lot['quantity']),
            'NAME' => $tempitem->name(),
            'ID' => $tempitem->id(),
            'ICON' => $tempitem->icon(),
            'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $tempitem->id())),
            'HTML' => $tempitem->html(),
            'ITEMTYPE' => $tempitem->skill(),
            'SLOT' => $slotcounter)
         );
         $slotcounter ++;
      }
   }
} 

/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('barter');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>
