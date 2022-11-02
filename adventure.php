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
 *   October 28, 2022 - Maudigan 
 *      Initial revision
 *
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");

 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
//dont display adventure stats if blocked in config.php 
if ($blockadventurestats) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);

$charName = $_GET['char'];

//we dont always have a charname, just when someone is here
//from having used a profile button. We customize the view
//a tiny bit in that case
if ($charName)
{
   //character initializations 
   $char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
   $name = $char->GetValue('name');
}
/*********************************************
             GET/VALIDATE VARS
*********************************************/
//fetch
$category      = (($_GET['category']!="") ? $_GET['category'] : "0");

//validate
if (!is_numeric($category) || $category < 0 || $category > 11) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_CATEGORY_INVALID']);

//how many leaders to show in the board
$number_of_rows=100;


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/

//build where clause
$filters = array();
$filters[] = 'rank <= '.$number_of_rows;
if ($name) $filters[] = "name = '".$name."'";
$where = generate_where($filters, "OR");

//build select category column criteria
if ($category == 2  || $category == 3 ) //guk
{
   $success_columns = 'adv.guk_wins';
   $failure_columns = 'adv.guk_losses';
   $data = 'GUK';
}
elseif ($category == 4  || $category == 5 ) //miragul
{
   $success_columns = 'adv.mir_wins';
   $failure_columns = 'adv.mir_losses';
   $data = 'MIR';
}
elseif ($category == 6  || $category == 7 ) //mistmoore
{
   $success_columns = 'adv.mmc_wins';
   $failure_columns = 'adv.mmc_losses';
   $data = 'MMC';
}
elseif ($category == 8  || $category == 9 ) //rujarkian
{
   $success_columns = 'adv.ruj_wins';
   $failure_columns = 'adv.ruj_losses';
   $data = 'RUJ';
}
elseif ($category == 10 || $category == 11) //takish
{
   $success_columns = 'adv.tak_wins';
   $failure_columns = 'adv.tak_losses';
   $data = 'TAK';
}
else //else show totals (option 0 and 1)
{
   $success_columns = 'adv.guk_wins + adv.mir_wins + adv.mmc_wins + adv.ruj_wins + adv.tak_wins';
   $failure_columns = 'adv.guk_losses + adv.mir_losses + adv.mmc_losses + adv.ruj_losses + adv.tak_losses';
   $data = "ALL";
}

//do we sort by wins or percentage of wins
//evens are by wins, odds are by percent
if ($category % 2 == 0)
{
   $orderby = 'success DESC'; 
   $orderby_clean = 'wins';
}
else
{
   $orderby = 'percent DESC, success DESC'; 
   $orderby_clean = 'percent';
}
   
//get adventure data
$tpl = <<<TPL
   SELECT *
   FROM (
      SELECT (@row_number:=@row_number + 1) AS rank,
             success, 
             failure, 
             (success/(failure + success) * 100 ) AS percent,
             name
      FROM (
         SELECT %s AS success,
                %s AS failure,
                cd.name
         FROM adventure_stats adv
         JOIN character_data cd
           ON cd.id = adv.player_id) totaled
      ORDER BY %s ) ranked
   %s  
TPL;

$query = sprintf($tpl, $success_columns, $failure_columns, $orderby, $where);
//set counter starting point for numbering the ranks
$result = $cbsql->query('SET @row_number = 0;'); 
//get the results
$result = $cbsql->query($query);


$leaders = array();
if ($cbsql->rows($result)) {
   while ($row = $cbsql->nextrow($result)) {
      if ($name && $row['name'] == $name)
      {
         $color = 'GOLD';
         $named_record = array(
            'COLOR' => $color,
            'RANK' => $row['rank'],
            'NAME' => $row['name'],
            'SUCCESS' => $row['success'],
            'FAILURE' => $row['failure'],
            'PERCENT' => number_format((float)$row['percent'], 1, '.', '')
         );
      }
      else
      {
         $color = '';
      }
      $leaders[$row['rank']] = array(
         'COLOR' => $color,
         'RANK' => $row['rank'],
         'NAME' => $row['name'],
         'SUCCESS' => $row['success'],
         'FAILURE' => $row['failure'],
         'PERCENT' => number_format((float)$row['percent'], 1, '.', '')
      );
   }
}


/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_ADVENTURE_LEADERBOARD'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
//if you're looking at buyers based off what a character has to sell, treat it like
//a profile page
if ($name) {
   output_profile_menu($name, 'adventure');
}
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'body' => 'adventure_body.tpl')
);


$cb_template->assign_both_vars(array(  
   'CATEGORY_ID' => $category,  
   'CATEGORY_NAME' => $language['ADVENTURE_SELECT_CATEGORY'][$category],
   'CHARACTER' => ($name) ? $name : "",
   'DATA' => $data,
   'ORDERBY' => $orderby)
);

$cb_template->assign_vars(array( 
   'L_RANK' => $language['ADVENTURE_RANK'],
   'L_NAME' => $language['ADVENTURE_NAME'],
   'L_SUCCESS' => $language['ADVENTURE_SUCCESS'],
   'L_FAILURE' => $language['ADVENTURE_FAILURE'],
   'L_PERCENT' => $language['ADVENTURE_PERCENT'],
   'L_ADVENTURE' => $language['ADVENTURE_LEADERBOARD'],
   'L_BACK' => $language['BUTTON_BACK'])
);

if ($name && is_array($named_record))
{
   $cb_template->assign_both_block_vars('current', $named_record);
}

foreach ($leaders as $leader) {
   $cb_template->assign_both_block_vars('leaders', $leader);
}

//category dropdown box
foreach ($language['ADVENTURE_SELECT_CATEGORY'] as $key => $value ) {
   $cb_template->assign_block_vars("select_category", array(
      'VALUE' => $key,
      'OPTION' => $value,
      'SELECTED' => (($category == $key) ? "selected":""))
   );
}

 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('body');

$cb_template->destroy;
 
include(__DIR__ . "/include/footer.php");
?>