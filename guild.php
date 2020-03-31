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
 *   March 15, 2020 - Maudigan 
 *      Initial revision
 *   March 22, 2020 - Maudigan
 *     impemented common.php
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
if(!$_GET['guild']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_GUILD']);
else $guild = $_GET['guild'];


//dont display guilds if blocked in config.php 
if ($blockguilddata) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/

//get guild data
$tpl = <<<TPL
SELECT guilds.id as guildid, guilds.name as guildname, 
       character_data.name, character_data.id  
FROM guilds 
LEFT JOIN character_data
       ON character_data.id = guilds.leader
WHERE guilds.name = '%s' 
AND character_data.deleted_at IS NULL
TPL;
 
$query = sprintf($tpl, $cbsql->escape_string($guild));
$result = $cbsql->query($query);

//does the guild exist?
if (!($row = $cbsql->nextrow($result))) {
   cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_RESULTS_GUILD']);
}

//get leader/guild data
$guildname = $row['guildname'];
$guildid = $row['guildid'];
$guildleader = $row['name'];
$guildleaderid = $row['id'];

//fetch guilds permissions, set by guild leader
$myguildpermission = GetGuildPermissions($guildleaderid);

//block the view of this guild if the leader has it disabled
if ($myguildpermission['mainpage']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);

/* this will get implemented in the server code soon, uncomment and remove the code below
//get guild member data with dynamic ranks
$tpl = <<<TPL
SELECT character_data.name, character_data.race, 
       character_data.class, character_data.level,
       guild_ranks.title
FROM guild_members
LEFT JOIN character_data
       ON character_data.id = guild_members.char_id 
LEFT JOIN guild_ranks
  ON guild_members.rank = guild_ranks.rank 
 AND guild_members.guild_id = guild_ranks.guild_id
WHERE guild_members.guild_id = '%s' 
AND character_data.deleted_at IS NULL
ORDER BY character_data.name
TPL;
 
$query = sprintf($tpl, $guildid);
$result = $cbsql->query($query);

$guildmembercount = $cbsql->rows($result);
if ($guildmembercount) {
   $guildavglevel = 0;
   $guildmembers = array();
   while ($row = $cbsql->nextrow($result)) {
      $guildavglevel += $row['level'];
      $guildmembers[] = array(
         'NAME' => $row['name'],
         'RACE' => $dbracenames[$row['race']],
         'RANK' => $row['title'],
         'CLASS' => $dbclassnames[$row['class']],
         'LEVEL' => $row['level']
      );
   }
   $guildavglevel = round($guildavglevel/$cbsql->rows($result));
} */

//get guild member data with static ranks
$tpl = <<<TPL
SELECT character_data.name, character_data.race, 
       character_data.class, character_data.level,
       guild_members.rank
FROM guild_members
LEFT JOIN character_data
       ON character_data.id = guild_members.char_id 
WHERE guild_members.guild_id = '%s' 
AND character_data.deleted_at IS NULL
ORDER BY character_data.name
TPL;
 
$query = sprintf($tpl, $guildid);
$result = $cbsql->query($query);

$guildmembers = array();
$guildmembercount = $cbsql->rows($result);
$guildavglevel = 0;
$guildmaxlevel = 0;
$guildminlevel = 500;
if ($guildmembercount) {
   while ($row = $cbsql->nextrow($result)) {
      $guildavglevel += $row['level'];
      $guildmaxlevel = max($row['level'], $guildmaxlevel);
      $guildminlevel = min($row['level'], $guildminlevel);
      $guildmembers[] = array(
         'NAME' => $row['name'],
         'RACE' => $dbracenames[$row['race']],
         'RANK' => $guildranks[$row['rank']],
         'CLASS' => $dbclassnames[$row['class']],
         'LEVEL' => $row['level']
      );
   }
   $guildavglevel = round($guildavglevel/$guildmembercount);
}


//get guild class makeup data
$tpl = <<<TPL
SELECT count(*) as count, avg(character_data.level) as level, character_data.class
FROM guild_members
LEFT JOIN character_data
       ON character_data.id = guild_members.char_id 
WHERE guild_members.guild_id = '%s' 
AND character_data.deleted_at IS NULL
GROUP BY character_data.class
TPL;
 
$query = sprintf($tpl, $guildid);
$result = $cbsql->query($query);

$guildclasses = array();
if ($cbsql->rows($result)) {
   $maxclasspercent = 0;
   while ($row = $cbsql->nextrow($result)) {
      $classpercent = $row['count']/$guildmembercount;
      $maxclasspercent = max($maxclasspercent, $classpercent);
      $guildclasses[] = array(
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
foreach ($guildclasses as $index => $value) {
   $guildclasses[$index]['RELATIVE_CLEAN_PERCENT'] = round($guildclasses[$index]['RAW_PERCENT']/$maxclasspercent * 100, 2);
}
 


//get guild level distribution
$tpl = <<<TPL
SELECT count(*) as count, character_data.level as level
FROM guild_members
LEFT JOIN character_data
       ON character_data.id = guild_members.char_id 
WHERE guild_members.guild_id = '%s' 
AND character_data.deleted_at IS NULL
GROUP BY character_data.level
TPL;
 
$query = sprintf($tpl, $guildid);
$result = $cbsql->query($query);

//show one extra on either side
$guildminlevel--;
$guildmaxlevel++;

$guildlevelcounts = array();
//prime level array with zeroes
for ($i = $guildminlevel; $i <= $guildmaxlevel; $i++) {
   $guildlevelcounts[$i] = array(
      'LEVEL' => $i,
      'COUNT' => 0
   );
}
if ($cbsql->rows($result)) {
   while ($row = $cbsql->nextrow($result)) {
      $guildlevelcounts[$row['level']] = array(
         'LEVEL' => $row['level'],
         'COUNT' => $row['count']
      );
   }
}


     /*$guildlevelcounts[1] = array('LEVEL' => 1, 'COUNT' => 0);
      $guildlevelcounts[2] = array('LEVEL' => 2, 'COUNT' => 0);
      $guildlevelcounts[3] = array('LEVEL' => 3, 'COUNT' => 0);
      $guildlevelcounts[4] = array('LEVEL' => 4, 'COUNT' => 0);
      $guildlevelcounts[5] = array('LEVEL' => 5, 'COUNT' => 0);
      $guildlevelcounts[6] = array('LEVEL' => 6, 'COUNT' => 0);
      $guildlevelcounts[7] = array('LEVEL' => 7, 'COUNT' => 0);
      $guildlevelcounts[8] = array('LEVEL' => 8, 'COUNT' => 0);
      $guildlevelcounts[9] = array('LEVEL' => 9, 'COUNT' => 0);
      $guildlevelcounts[10] = array('LEVEL' => 10, 'COUNT' => 0);
      $guildlevelcounts[11] = array('LEVEL' => 11, 'COUNT' => 0);
      $guildlevelcounts[12] = array('LEVEL' => 12, 'COUNT' => 0);
      $guildlevelcounts[13] = array('LEVEL' => 13, 'COUNT' => 0);
      $guildlevelcounts[14] = array('LEVEL' => 14, 'COUNT' => 0);
      $guildlevelcounts[15] = array('LEVEL' => 15, 'COUNT' => 0);
      $guildlevelcounts[16] = array('LEVEL' => 16, 'COUNT' => 0);
      $guildlevelcounts[17] = array('LEVEL' => 17, 'COUNT' => 0);
      $guildlevelcounts[18] = array('LEVEL' => 18, 'COUNT' => 0);
      $guildlevelcounts[19] = array('LEVEL' => 19, 'COUNT' => 0);
      $guildlevelcounts[20] = array('LEVEL' => 20, 'COUNT' => 0);
      $guildlevelcounts[21] = array('LEVEL' => 21, 'COUNT' => 0);
      $guildlevelcounts[22] = array('LEVEL' => 22, 'COUNT' => 0);
      $guildlevelcounts[23] = array('LEVEL' => 23, 'COUNT' => 0);
      $guildlevelcounts[24] = array('LEVEL' => 24, 'COUNT' => 0);
      $guildlevelcounts[25] = array('LEVEL' => 25, 'COUNT' => 0);
      $guildlevelcounts[26] = array('LEVEL' => 26, 'COUNT' => 0);
      $guildlevelcounts[27] = array('LEVEL' => 27, 'COUNT' => 0);
      $guildlevelcounts[28] = array('LEVEL' => 28, 'COUNT' => 0);
      $guildlevelcounts[29] = array('LEVEL' => 29, 'COUNT' => 0);
      $guildlevelcounts[30] = array('LEVEL' => 30, 'COUNT' => 0);
      $guildlevelcounts[31] = array('LEVEL' => 31, 'COUNT' => 0);
      $guildlevelcounts[32] = array('LEVEL' => 32, 'COUNT' => 0);
      $guildlevelcounts[33] = array('LEVEL' => 33, 'COUNT' => 0);
      $guildlevelcounts[34] = array('LEVEL' => 34, 'COUNT' => 0);
      $guildlevelcounts[35] = array('LEVEL' => 35, 'COUNT' => 0);
      $guildlevelcounts[36] = array('LEVEL' => 36, 'COUNT' => 0);
      $guildlevelcounts[37] = array('LEVEL' => 37, 'COUNT' => 0);
      $guildlevelcounts[38] = array('LEVEL' => 38, 'COUNT' => 0);
      $guildlevelcounts[39] = array('LEVEL' => 39, 'COUNT' => 0);
      $guildlevelcounts[40] = array('LEVEL' => 40, 'COUNT' => 0);
      $guildlevelcounts[41] = array('LEVEL' => 41, 'COUNT' => 0);
      $guildlevelcounts[42] = array('LEVEL' => 42, 'COUNT' => 0);
      $guildlevelcounts[43] = array('LEVEL' => 43, 'COUNT' => 0);
      $guildlevelcounts[44] = array('LEVEL' => 44, 'COUNT' => 0);
      $guildlevelcounts[45] = array('LEVEL' => 45, 'COUNT' => 0);
      $guildlevelcounts[46] = array('LEVEL' => 46, 'COUNT' => 0);
      $guildlevelcounts[47] = array('LEVEL' => 47, 'COUNT' => 0);
      $guildlevelcounts[48] = array('LEVEL' => 48, 'COUNT' => 50);
      $guildlevelcounts[49] = array('LEVEL' => 49, 'COUNT' => 0);
      $guildlevelcounts[50] = array('LEVEL' => 50, 'COUNT' => 0);
      $guildlevelcounts[51] = array('LEVEL' => 51, 'COUNT' => 0);
      $guildlevelcounts[52] = array('LEVEL' => 52, 'COUNT' => 0);
      $guildlevelcounts[53] = array('LEVEL' => 53, 'COUNT' => 0);
      $guildlevelcounts[54] = array('LEVEL' => 54, 'COUNT' => 0);
      $guildlevelcounts[55] = array('LEVEL' => 55, 'COUNT' => 0);
      $guildlevelcounts[56] = array('LEVEL' => 56, 'COUNT' => 0);
      $guildlevelcounts[57] = array('LEVEL' => 57, 'COUNT' => 16);
      $guildlevelcounts[58] = array('LEVEL' => 58, 'COUNT' => 10);
      $guildlevelcounts[59] = array('LEVEL' => 59, 'COUNT' => 10);
      $guildlevelcounts[60] = array('LEVEL' => 60, 'COUNT' => 0);
      $guildlevelcounts[61] = array('LEVEL' => 61, 'COUNT' => 6);
      $guildlevelcounts[62] = array('LEVEL' => 62, 'COUNT' => 0);
      $guildlevelcounts[63] = array('LEVEL' => 63, 'COUNT' => 8);
      $guildlevelcounts[64] = array('LEVEL' => 64, 'COUNT' => 0);
      $guildlevelcounts[65] = array('LEVEL' => 65, 'COUNT' => 8);*/
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$guildname.$language['PAGE_TITLES_GUILD'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'body' => 'guild_body.tpl')
);

$cb_template->assign_both_vars(array(  
   'GUILD_NAME' => $guildname,  
   'GUILD_LEADER' => $guildleader,
   'GUILD_COUNT' => $guildmembercount, 
   'GUILD_AVG_LEVEL' => $guildavglevel)
);
$cb_template->assign_vars(array( 
   'L_GUILD' => $language['GUILD_GUILD'],
   'L_MEMBERS' => $language['GUILD_MEMBERS'],
   'L_CLASS' => $language['GUILD_CLASS'],
   'L_CLASSES' => $language['GUILD_CLASSES'],
   'L_LEVELS' => $language['GUILD_LEVELS'],
   'L_RACE' => $language['GUILD_RACE'],
   'L_RANK' => $language['GUILD_RANK'],
   'L_AVG_LEVEL' => $language['GUILD_AVG_LEVEL'],
   'L_LEADER' => $language['GUILD_LEADER'],
   'L_PERCENT' => $language['GUILD_PERCENT'],
   'L_COUNT' => $language['GUILD_COUNT'],
   'L_LEVEL' => $language['GUILD_LEVEL'],
   'L_NAME' => $language['GUILD_NAME'],
   'L_BACK' => $language['BUTTON_BACK'])
);

foreach ($guildmembers as $member) {
   $cb_template->assign_both_block_vars('guildmembers', $member);
}

foreach ($guildclasses as $class) {
   $cb_template->assign_both_block_vars('guildclasses', $class);
}

foreach ($guildlevelcounts as $levelcount) {
   $cb_template->assign_both_block_vars('guildlevels', $levelcount);
}


 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('body');

$cb_template->destroy;
 
include(__DIR__ . "/include/footer.php");
?>