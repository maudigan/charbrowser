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
 *   October 16, 2013 - Leere
 *      Fixed an error in the AA query that left out berzerker AAs 
 *   September 26, 2014 - Maudigan
 *      Added underscore to 'aapoints' to make it the same as the column name
 *      Updated character table name
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   September 28, 2014 - Maudigan
 *      replaced char blob
 *      added new aa tabs
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   May 30, 2016 - Maudigan
 *      updated the entire script to work with the new AA system, again do
 *      a compare to 2.41 to see the differences, it's basically a rewrite.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 7, 2020 - Maudigan
 *      Fixed an error causing the wrong class's AA to display
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 3, 2020 - Maudigan
 *     dont show AAs if they dont have a first rank, some custom server
 *     hide AA by deleting their ranks
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
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
             SUPPORT FUNCTIONS
*********************************************/

//counts how many ranks follow the provided rank
function getTotalRanks($first_rank)
{
   global $aa_ranks;
   
   $next_rank = $aa_ranks[$first_rank]['NEXT'];
   
   //if the next id exists, then that reflects
   //there being another rank after this current one
   //so recursively call to add it in
   if (array_key_exists($next_rank, $aa_ranks))
      return 1 + getTotalRanks($next_rank);
   else
      return 1;
} 


//gets the cost of the rank provided the 
//first rank and value the character has
function getRankCost($first_rank, $value)
{
   global $aa_ranks;
   
   $next_rank = $aa_ranks[$first_rank]['NEXT'];
   
   //if our value has reached zero then we are on the
   //one that has the relevant cost but we also need
   //to make sure we don't go past the tail. If we do
   //return a 0.
   if ($value > 0) //most likely scenario is first for speed
   {
      if (array_key_exists($next_rank, $aa_ranks))
         return getRankCost($next_rank, --$value);
      else //too high of a value was provided
         return '--';
   }
   elseif ($value == 0) //next most likely scenario
   {
      return $aa_ranks[$first_rank]['COST'];
   }
   
   return 0; //cant hurt
}
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations 
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['AAs']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/

//setup an array to output the display tabs
$aatabs = array();
$aatabs[1] = $language['AAS_TAB_1'];
$aatabs[2] = $language['AAS_TAB_2'];
$aatabs[3] = $language['AAS_TAB_3'];
$aatabs[4] = $language['AAS_TAB_4']; 

//GET THIS CHARS SPENT AA
$classbit = pow(2, $char->GetValue('class') - 1);
$character_aas = $char->GetTable("character_alternate_abilities");  

//GET RANK COSTS
$tpl = <<<TPL
SELECT id, cost, next_id
FROM aa_ranks 
TPL;
$query = $tpl;
$result = $cbsql_content->query($query);

//the ranks are stored in a record 
//that is similar to a linked list
//loop through each one and load it  
//into a poor-man's linked list
$aa_ranks = array();
while ($row = $cbsql_content->nextrow($result)) 
{
   $aa_rank = array('COST' => intval($row['cost']),
                    'NEXT' => intval($row['next_id']));
   $aa_ranks[intval($row['id'])] = $aa_rank;
}    

//GET ALL AAS FOR THIS CLASS
$tpl = <<<TPL
SELECT first_rank_id, name, type 
FROM aa_ability  
WHERE classes & %s 
  AND enabled = 1 
ORDER BY type, name 
TPL;
$query = sprintf($tpl, $classbit);
$result = $cbsql_content->query($query);

//stage them in the final array
$aa_abilities = array();
while ($row = $cbsql_content->nextrow($result)) 
{
   //calculate all the values
   $first_rank_id = $row['first_rank_id'];
   //skip this one if there is no first rank data
   if (!array_key_exists($first_rank_id, $aa_ranks)) continue;
   $cur = intval($character_aas[$first_rank_id]['aa_value']);
   $cost = getRankCost($first_rank_id, $cur);
   $max = getTotalRanks($first_rank_id);
   
   $aa_abilities[] = array(
      'TYPE' => $row['type'],
      'NAME' => $row['name'],
      'MAX' => $max,
      'CUR' => $cur,
      'COST' => $cost
   );
} 
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_AAS'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'aas');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'aas' => 'aas_body.tpl')
);


$cb_template->assign_both_vars(array(  
   'NAME' => $name,
   'AA_POINTS' => $char->GetValue("aa_points"), 
   'POINTS_SPENT' => $char->GetValue("aa_points_spent"))
);
$cb_template->assign_vars(array(  
   'L_ALTERNATE_ABILITIES' => $language['AAS_ALTERNATE_ABILITIES'], 
   'L_TITLE' => $language['AAS_TITLE'],
   'L_CUR_MAX' => $language['AAS_CUR_MAX'],
   'L_COST' => $language['AAS_COST'],
   'L_AA_POINTS' => $language['AAS_AA_POINTS'],
   'L_POINTS_SPENT' => $language['AAS_POINTS_SPENT'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//TODO don't hardcode these colors. It should maybe  
//     get set using two stylesheet flags... maybe
$Color = "7b714a";
foreach ($aatabs as $key => $value) {
  $cb_template->assign_block_vars("tabs", array( 
    'COLOR' => $Color,      
    'ID' => $key,
    'TEXT' => $value)
  );
  $Color = "FFFFFF";
}


$Display = "block";
$lasttype = 0;
foreach ($aa_abilities as $aa_ability) 
{
   //put them in a new box if it's a new type
   if ($aa_ability['TYPE'] != $lasttype)
   {
      $lasttype = $aa_ability['TYPE'];
      $cb_template->assign_both_block_vars("boxes", array(       
         'ID' => $lasttype,
         'DISPLAY' => $Display)
      );
      $Display = "none";
   }

   //output the row
   $cb_template->assign_both_block_vars("boxes.aas", $aa_ability);
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('aas');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>