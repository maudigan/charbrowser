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
 *   March 30, 2020 - Maudigan 
 *      Initial revision
 *
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
//dont display server comp if blocked in config.php 
if ($blockserverdata) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/

//get guild data
$tpl = <<<TPL
SELECT MAX(character_data.level) as maxlevel,
       MIN(character_data.level) as minlevel,
       AVG(character_data.level) as avglevel,
       COUNT(*) as count
FROM character_data 
WHERE character_data.deleted_at IS NULL
TPL;
 
$query = sprintf($tpl);
$result = $cbsql->query($query);

//no characters?
if (!($row = $cbsql->nextrow($result))) {
   cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_RESULTS_ITEMS']);
}

$maxlevel = $row['maxlevel'];
$minlevel = $row['minlevel'];
$avglevel = $row['avglevel'];
$charactercount = $row['count'];

//get guild class makeup data
$tpl = <<<TPL
SELECT count(*) as count, avg(character_data.level) as level, character_data.class
FROM character_data
WHERE character_data.deleted_at IS NULL
GROUP BY character_data.class
TPL;
 
$query = sprintf($tpl);
$result = $cbsql->query($query);

$classes = array();
if ($cbsql->rows($result)) {
   $maxclasspercent = 0;
   while ($row = $cbsql->nextrow($result)) {
      $classpercent = $row['count']/$charactercount;
      $maxclasspercent = max($maxclasspercent, $classpercent);
      $classes[] = array(
         'CLASS' => $dbclassnames[$row['class']],
         'COUNT' => $row['count'],
         'ROUNDED_PERCENT' => round($classpercent * 100),
         'CLEAN_PERCENT' => round($classpercent * 100, 2),
         'RAW_PERCENT' => $classpercent,
         'LEVEL' => round($row['level'])
      );
         
   }
}

//calculate relative percents so the max percent 
//takes up the full width or height of the container
foreach ($classes as $index => $value) {
   $classes[$index]['RELATIVE_CLEAN_PERCENT'] = round($classes[$index]['RAW_PERCENT']/$maxclasspercent * 100, 2);
}
 


//get level distribution
$tpl = <<<TPL
SELECT count(*) as count, character_data.level as level
FROM character_data
WHERE character_data.deleted_at IS NULL
GROUP BY character_data.level
TPL;
 
$query = sprintf($tpl);
$result = $cbsql->query($query);


$levelcounts = array();
//prime level array with zeroes
for ($i = $minlevel - 1; $i <= $maxlevel + 1; $i++) {
   $levelcounts[$i] = array(
      'LEVEL' => $i,
      'COUNT' => 0
   );
}
if ($cbsql->rows($result)) {
   while ($row = $cbsql->nextrow($result)) {
      $levelcounts[$row['level']] = array(
         'LEVEL' => $row['level'],
         'COUNT' => $row['count']
      );
   }
}

 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SERVER'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'body' => 'server_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'MIN_LEVEL' => $minlevel,  
   'MAX_LEVEL' => $maxlevel,
   'AVG_LEVEL' => round($avglevel), 
   'CHAR_COUNT' => $charactercount)
);
$cb_template->assign_vars(array( 
   'L_SERVER' => $language['SERVER_SERVER'],
   'L_MIN_LEVEL' => $language['SERVER_MIN_LEVEL'],
   'L_MAX_LEVEL' => $language['SERVER_MAX_LEVEL'],
   'L_AVG_LEVEL' => $language['SERVER_AVG_LEVEL'],
   'L_CHAR_COUNT' => $language['SERVER_CHAR_COUNT'],
   'L_CLASSES' => $language['SERVER_CLASSES'],
   'L_LEVELS' => $language['SERVER_LEVELS'],
   'L_CLASS' => $language['SERVER_CLASS'],
   'L_PERCENT' => $language['SERVER_PERCENT'],
   'L_COUNT' => $language['SERVER_COUNT'],
   'L_BACK' => $language['BUTTON_BACK'])
);

foreach ($classes as $class) {
   $cb_template->assign_both_block_vars('classes', $class);
}

foreach ($levelcounts as $levelcount) {
   $cb_template->assign_both_block_vars('levels', $levelcount);
}


 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('body');

$cb_template->destroy;
 
include(__DIR__ . "/include/footer.php");
?>