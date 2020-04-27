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
 *   September 26, 2014 - Maudigan
 *      Updated character table name
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences.
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the item links customizable
 *   January 7, 2018 - Maudigan
 *      fixed a typo when loading the $lots array
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 2, 2020 - Maudigan
 *     search by seller name (thanks croco/kinglykrab)
 *   April 3, 2020 - Maudigan
 *     add icons to inspect
 *     added number_format to prices
 *
 ***************************************************************************/

use CharBrowser\Repositories\ItemRepository;


/*********************************************
                 INCLUDES
*********************************************/
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");


/*********************************************
             GET/VALIDATE VARS
*********************************************/
$start      = (($_GET['start']) ? $_GET['start'] : "0");
$orderby    = (($_GET['orderby']) ? $_GET['orderby'] : "Name");
$class      = (($_GET['class']!="") ? $_GET['class'] : "-1");
$race       = (($_GET['race']!="") ? $_GET['race'] : "-1");
$slot       = (($_GET['slot']!="") ? $_GET['slot'] : "-1");
$type       = (($_GET['type']!="") ? $_GET['type'] : "-1");
$pricemin   = $_GET['pricemin'];
$pricemax   = $_GET['pricemax'];
$item       = $_GET['item'];
$seller       = $_GET['char'];
$direction  = (($_GET['direction']=="DESC") ? "DESC" : "ASC");

$perpage=25;

//build baselink
$baselink=(($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=bazaar&class=$class&race=$race&slot=$slot&type=$type&pricemin=$pricemin&pricemax=$pricemax&item=$item";

//security against sql injection
if (!IsAlphaSpace($item)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_ALPHA']);
if (!IsAlphaSpace($seller)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NAME_ALPHA']);
if (!IsAlphaSpace($orderby)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ORDER_ALPHA']);
if (!is_numeric($start)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_START_NUMERIC']);
if (!is_numeric($pricemin) && $pricemin != "") cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_PRICE_NUMERIC']);
if (!is_numeric($pricemax) && $pricemax != "") cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_PRICE_NUMERIC']);
if (!is_numeric($class)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_CLASS_NUMERIC']);
if (!is_numeric($race)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_RACE_NUMERIC']);
if (!is_numeric($slot)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_SLOT_NUMERIC']);
if (!is_numeric($type)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_TYPE_NUMERIC']);

//dont display bazaaar if blocked in config.php
if ($blockbazaar) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        BUILD AND EXECUTE THE SEARCH
*********************************************/
//build the where clause
$where = "";
$items_where = "";
$divider = "WHERE ";
if ($item) {
   $items_where .= $divider."items.Name LIKE '%".str_replace("_", "%", str_replace(" ","%",$item))."%'";
   $divider = " AND ";
}
if ($seller) {
   $where .= $divider."character_data.name = '".$seller."'";
   $divider = " AND ";
}
if ($pricemin) {
   $modprice = $pricemin * 1000;
   $where .= $divider."trader.item_cost >= $modprice";
   $divider = " AND ";
}
if ($pricemax) {
   $modprice = $pricemax * 1000;
   $where .= $divider."trader.item_cost <= $modprice";
   $divider = " AND ";
}
if($class > -1) {
   $items_where .= $divider."items.classes & $class";
   $divider = " AND ";
}
if($race > -1) {
   $items_where .= $divider."items.races   & $race";
   $divider = " AND ";
}
if($type > -1) {
   $items_where .= $divider."items.itemtype = $type";
   $divider = " AND ";
}
if($slot > -1) {
   $items_where .= $divider."items.slots   & $slot";
   $divider = " AND ";
}

//build the orderby & limit clauses
$order = "ORDER BY $orderby $direction LIMIT $start, $perpage";

$filtered_item_ids = [];
if ($items_where != "") {
   $query  = sprintf("SELECT id FROM items %s", $items_where);
   $result = $cbsql_content->query($query);
   while ($row = $cbsql_content->nextrow($result)) {
      $filtered_item_ids[] = $row['id'];
   }
}

$filtered_item_ids_where = "";
if (count($filtered_item_ids) > 0) {
   $filtered_item_ids_where = ($where != "" ? " AND" : " WHERE") . " trader.item_id IN (" . implode(", ", $filtered_item_ids) . ") ";
}

//build the query, leave a spot for the where
//and the orderby clauses
$tpl = <<<TPL
SELECT character_data.name as charactername,
       trader.item_cost as tradercost,
       trader.*
FROM character_data 
INNER JOIN trader
        ON character_data.id = trader.char_id
        %s %s %s
TPL;

$item_ids = [];
$result   = $cbsql->query(sprintf($tpl, $where, $filtered_item_ids_where, $order));
while ($row = $cbsql->nextrow($result)) {
   $item_ids[] = $row['item_id'];
}

if (count($item_ids) == 0) {
   cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_RESULTS_ITEMS']);
}

ItemRepository::preloadByItemIds($item_ids);

//now add on the limit & ordering and query again for just this page
$result = $cbsql->query(sprintf($tpl, $where, $filtered_item_ids_where, $order));
$lots   = [];
while ($row = $cbsql->nextrow($result)) {
   $trader_item_id = $row['item_id'];
   $row            = array_merge($row, ItemRepository::findOne($trader_item_id));
   $lots[]         = $row;
}

/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_BAZAAR'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
//if you're looking at a players store, treat it like
//a profile page
if ($seller) {
   output_profile_menu($seller, 'bazaar');
}


/*********************************************
              POPULATE BODY
*********************************************/
//build body template
$cb_template->set_filenames(array(
  'bazaar' => 'bazaar_body.tpl')
);

$cb_template->assign_both_vars(array(
   'ORDERBY' => $orderby,
   'DIRECTION' => $direction,
   'START' => $start,
   'PERPAGE' => $perpage,
   'TOTALITEMS' => $totalitems)
);

$cb_template->assign_vars(array(
   'ITEM' => $item,
   'SELLER' => $seller,
   'STORENAME' => ($seller) ? " - ".$seller : "",
   'ORDER_LINK' => $baselink."&start=$start&direction=".(($direction=="ASC") ? "DESC":"ASC"),
   'PAGINATION' => cb_generate_pagination("$baselink&orderby=$orderby&direction=$direction", $totalitems, $perpage, $start, true),
   'PRICE_MIN' => $pricemin,
   'PRICE_MAX' => $pricemax,

   'L_BAZAAR' => $language['BAZAAR_BAZAAR'],
   'L_NAME' => $language['BAZAAR_NAME'],
   'L_PRICE' => $language['BAZAAR_PRICE'],
   'L_ITEM' => $language['BAZAAR_ITEM'],
   'L_SEARCH' => $language['BAZAAR_SEARCH'],
   'L_SEARCH_NAME' => $language['BAZAAR_SEARCH_NAME'],
   'L_SEARCH_CLASS' => $language['BAZAAR_SEARCH_CLASS'],
   'L_SEARCH_RACE' => $language['BAZAAR_SEARCH_RACE'],
   'L_SEARCH_SLOT' => $language['BAZAAR_SEARCH_SLOT'],
   'L_SEARCH_TYPE' => $language['BAZAAR_SEARCH_TYPE'],
   'L_SEARCH_PRICE_MIN' => $language['BAZAAR_SEARCH_PRICE_MIN'],
   'L_SEARCH_PRICE_MAX' => $language['BAZAAR_SEARCH_PRICE_MAX'])
);

//dump items
$slotcounter = 0;
foreach($lots as $lot) {
   $tempitem = new item($lot);
   $price = $lot["tradercost"];
   $plat = number_format(floor($price/1000));
   $price = $price % 1000;
   $gold = number_format(floor($price/100));
   $price = $price % 100;
   $silver = number_format(floor($price/10));
   $copper  = number_format($price % 10);
   $cb_template->assign_both_block_vars("items", array(
      'SELLER' => $lot['charactername'],
      'PRICE' => (($plat)?$plat."p ":"").(($silver)?$silver."s ":"").(($gold)?$gold."g ":"").(($copper)?$copper."c ":""),
      'NAME' => $tempitem->name(),
      'ID' => $tempitem->id(),
      'ICON' => $tempitem->icon(),
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $tempitem->id())),
      'HTML' => $tempitem->html(),
      'SLOT' => $slotcounter)
   );
   $slotcounter ++;
}


//built combo box options
foreach ($language['BAZAAR_ARRAY_SEARCH_TYPE'] as $key => $value ) {
   $cb_template->assign_block_vars("select_type", array(
      'VALUE' => $key,
      'OPTION' => $value,
      'SELECTED' => (($type == $key) ? "selected":""))
   );
}
foreach ($language['BAZAAR_ARRAY_SEARCH_CLASS'] as $key => $value ) {
   $cb_template->assign_block_vars("select_class", array(
      'VALUE' => $key,
      'OPTION' => $value,
      'SELECTED' => (($class == $key) ? "selected":""))
   );
}
foreach ($language['BAZAAR_ARRAY_SEARCH_RACE'] as $key => $value ) {
   $cb_template->assign_block_vars("select_race", array(
      'VALUE' => $key,
      'OPTION' => $value,
      'SELECTED' => (($race == $key) ? "selected":""))
   );
}
foreach ($language['BAZAAR_ARRAY_SEARCH_SLOT'] as $key => $value ) {
   $cb_template->assign_block_vars("select_slot", array(
      'VALUE' => $key,
      'OPTION' => $value,
      'SELECTED' => (($slot == $key) ? "selected":""))
   );
}


/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('bazaar');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>
