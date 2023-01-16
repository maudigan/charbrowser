
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
 *   November 23, 2022 - Maudigan 
 *      MariaiaDB 10.6 wasn't numbering the point rankings correctly. They'd
 *      be sorted right, but the numbering was off (e.g. 10, 12, 99, 1...).
 *      Trust suggested the RANK() MySql function instead of the variable
 *      but that isn't supported on all version of MySql. So I added
 *      a conditional behavior that uses the variables for old MySQL 
 *      versions, and the RANK() function for newer ones. 
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
//dont display adventure stats if blocked in config.php 
if ($blockadventurestats) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

$charName = preg_Get_Post('char', '/^[a-zA-Z]+$/', false);

//we dont always have a charname, just when someone is here
//from having used a profile button. We customize the view
//a tiny bit in that case
if ($charName)
{
   //character initializations 
   $char = new Charbrowser_Character($charName, $showsoftdelete, $charbrowser_is_admin_page); //the Charbrowser_Character class will sanitize the character name
   $name = $char->GetValue('name');
}
else
{
   $name = '';
}
/*********************************************
             GET/VALIDATE VARS
*********************************************/
//fetch category (0-11)
$category = preg_Get_Post('category', '/^([0-9]|1[0-1])$/', "0", $language['MESSAGE_ERROR'],$language['MESSAGE_CATEGORY_INVALID']);

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
   $success_columns = 'guk_wins';
   $failure_columns = 'guk_losses';
   $data = 'GUK';
}
elseif ($category == 4  || $category == 5 ) //miragul
{
   $success_columns = 'mir_wins';
   $failure_columns = 'mir_losses';
   $data = 'MIR';
}
elseif ($category == 6  || $category == 7 ) //mistmoore
{
   $success_columns = 'mmc_wins';
   $failure_columns = 'mmc_losses';
   $data = 'MMC';
}
elseif ($category == 8  || $category == 9 ) //rujarkian
{
   $success_columns = 'ruj_wins';
   $failure_columns = 'ruj_losses';
   $data = 'RUJ';
}
elseif ($category == 10 || $category == 11) //takish
{
   $success_columns = 'tak_wins';
   $failure_columns = 'tak_losses';
   $data = 'TAK';
}
else //else show totals (option 0 and 1)
{
   $success_columns = 'guk_wins + mir_wins + mmc_wins + ruj_wins + tak_wins';
   $failure_columns = 'guk_losses + mir_losses + mmc_losses + ruj_losses + tak_losses';
   $data = "ALL";
}

//do we sort by wins or percentage of wins
//evens are by wins, odds are by percent
if ($category % 2 == 0)
{
   $orderby = 'success DESC, percent DESC'; 
   $orderby_clean = 'wins';
}
else
{
   $orderby = 'percent DESC, success DESC'; 
   $orderby_clean = 'percent';
}

//determine if the database is high enough version to have the
// RANK() window function. It was added to mariadb in 10.2 and 
// percona and mysql added it in 8.0
$rank_function_exists = db_is_version($cbsql, '10.2', '8.0', '8.0');

if ($rank_function_exists)
{  
   //use the RANK() window function
   $rank_syntax = "RANK() OVER ( ORDER BY success DESC, percent DESC, failure ASC) rank,";
}
else
{
   //use variables to count the ranks
   //initialize the variable and set the syntax
   $result = $cbsql->query('SET @row_number = 0;'); 
   $rank_syntax = "(@row_number:=@row_number + 1) AS rank,";
}

$tpl = <<<TPL
   SELECT 
      *
   FROM (
      SELECT 
         %s
         success, 
         failure, 
         IFNULL((success/(failure + success) * 100 ), 0) AS percent,
         name
      FROM (
         SELECT 
            IFNULL(%s, 0) AS success,
            IFNULL(%s, 0) AS failure,
            name
         FROM character_data
         LEFT JOIN adventure_stats
            ON id = player_id
      ) totaled
      ORDER BY %s 
   ) ranked
   %s  
TPL;

$query = sprintf($tpl, $rank_syntax, $success_columns, $failure_columns, $orderby, $where);

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

$cb_template->destroy();
 
include(__DIR__ . "/include/footer.php");
?>