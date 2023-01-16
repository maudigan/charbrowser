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
 *   April 2, 2020 - Maudigan
 *     dont show anon guild members names
 *   April 2, 2020 - Maudigan
 *     removed some commented out test code
 *   April 3, 2020 - Maudigan
 *     removed some commented out test code
 *     added number_format to counts
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
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
         SETUP GUILD PERMISSIONS
*********************************************/
$guild = preg_Get_Post('guild', '/^.*?$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_GUILD'], true);

//this is a very permissive search, lets escape it immediately for safety
$guild = $cbsql->escape_string($guild);

//dont display guilds if blocked in config.php 
if ($blockguilddata) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);


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
 
$query = sprintf($tpl, $guild);
$result = $cbsql->query($query);

//does the guild exist?
if (!($row = $cbsql->nextrow($result))) {
   $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_NO_RESULTS_GUILD']);
}

//get leader/guild data
$guildname = $row['guildname'];
$guildid = $row['guildid'];
$guildleader = $row['name'];
$guildleaderid = $row['id'];

//fetch guilds permissions, set by guild leader
$myguildpermission = GetGuildPermissions($guildleaderid);

//block the view of this guild if the leader has it disabled
if ($myguildpermission['mainpage']) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

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
       guild_members.rank, character_data.anon
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
      //dont show anonymous guild members name
      if ($row['anon'] != 1 || $showguildwhenanon || $charbrowser_is_admin_page) {
         $charname = '<a href="'.$cb_index_url.'?page=character&char='.$row['name'].'">'.$row['name'].'</a>';
      }
      else {
         $charname = "Anonymous";
      }
      $guildavglevel += $row['level'];
      $guildmaxlevel = max($row['level'], $guildmaxlevel);
      $guildminlevel = min($row['level'], $guildminlevel);
      $guildmembers[] = array(
         'NAME' => $charname,
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
         'COUNT' => number_format($row['count']),
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
   'GUILD_COUNT' => number_format($guildmembercount), 
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

$cb_template->destroy();
 
include(__DIR__ . "/include/footer.php");
?>