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
 *      added code to destroy template when finished
 *      added code to monitor database performance
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 7, 2020 - Maudigan
 *      modified to accommodate soft deletes
 *   March 8, 2020 - Maudigan
 *      make soft deletes display if this is a wrapped install
 *      and the admin flag is turned on
 *   March 14, 2020 - Maudigan
 *      fixed the missing space between AND in the query
 *   March 15, 2020 - Maudigan
 *      implemented guild page
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 2, 2020 - Maudigan
 *     dont show anon guild members names
 *   April 17, 2020 - Maudigan
 *     show a nicer error when there are no results
 *   May 4, 2020 - Maudigan
 *     reduce the nyumber of queries, implement the where building function
 *   Devember 3, 2022 - Allow guild/name search criteria to be echoed back
 *      in the header search fields
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
             GET/VALIDATE VARS
*********************************************/ 
$start       = preg_Get_Post('start', '/^[0-9]+$/', '0', $language['MESSAGE_ERROR'], $language['MESSAGE_START_NUMERIC']);
$orderby     = preg_Get_Post('orderby', '/^[a-zA-Z]*$/', 'name', $language['MESSAGE_ERROR'], $language['MESSAGE_ORDER_ALPHA']);
$direction   = preg_Get_Post('direction', '/^(DESC|ASC|desc|asc)$/', 'ASC');
$name        = preg_Get_Post('name', '/^[a-zA-Z]*$/', '', $language['MESSAGE_NOTICE'], $language['MESSAGE_NAME_ALPHA']);
$guild_dirty = preg_Get_Post('guild', '/^[a-zA-Z\-\ \']*$/', '', $language['MESSAGE_NOTICE'], $language['MESSAGE_GUILD_ALPHA']);

//security against sql injection, escape strings that don't have 
//sufficiently restricted regex checks in the above section
$guild = $cbsql->escape_string($guild_dirty);

//convert integer parameters
$start = intval($start);

//build baselink
$baselink= (($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=search&name=$name&guild=$guild_dirty";

//these get passed to the search fields in the header
$header_name_search = $name;
$header_guild_search = $guild_dirty;
 
 
/*********************************************
        BUILD AND EXECUTE THE SEARCH
*********************************************/ 
//build where clause
$filters = array();
if (!$showsoftdelete && !$charbrowser_is_admin_page) $filters[] = "character_data.deleted_at IS NULL"; 
if ($name !== '') $filters[] = "character_data.name LIKE '%".$name."%'"; 
if ($guild !== '') 
{
   $filters[] = "guilds.name LIKE '%".str_replace(" ","%",$guild)."%'";
   
   //if the char is anon, dont show them in a guild search
   if (!$showguildwhenanon && !$charbrowser_is_admin_page) $filters[] = "character_data.anon != '1'";
}
$where = generate_where($filters);

//build the query, leave a spot for the where
//and the orderby clauses
$tpl = <<<TPL
SELECT character_data.class, character_data.level, 
       character_data.name, guilds.name AS guildname, 
       character_data.deleted_at, character_data.anon
FROM character_data 
LEFT JOIN guild_members
       ON character_data.id = guild_members.char_id 
LEFT JOIN guilds
       ON guilds.id = guild_members.guild_id 
%s 
ORDER BY %s %s
TPL;
 
$query = sprintf($tpl, $where, $orderby, $direction);
$result = $cbsql->query($query);

//fetch the results
$characters = $cbsql->fetch_all($result);
$totalchars = cb_count($characters);

//error if there are no characters
if (!$totalchars) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_NO_RESULTS_ITEMS']);

 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SEARCH'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
//build body template
$cb_template->set_filenames(array(
   'body' => 'search_body.tpl')
);

$cb_template->assign_vars(array(  
   'ORDER_LINK' => $baselink."&start=$start&direction=".(($direction=="ASC") ? "DESC":"ASC"), 
   'PAGINATION' => cb_generate_pagination("$baselink&orderby=$orderby&direction=$direction", $totalchars, $numToDisplay, $start, true),
   
   'L_RESULTS' => $language['SEARCH_RESULTS'],
   'L_NAME' => $language['SEARCH_NAME'],
   'L_LEVEL' => $language['SEARCH_LEVEL'],
   'L_CLASS' => $language['SEARCH_CLASS'],)
);

//calculate last char index for this page
$finish = min($totalchars, $start + $numToDisplay);

//output this page of chars to template
for ($i = $start; $i < $finish; $i++) {
   $character = $characters[$i];
   //dont show anon guild names unless config enables it
   if ($character["anon"] != 1 || $showguildwhenanon || $charbrowser_is_admin_page) {
      $charguildname = getGuildLink($character["guildname"]);
   }
   else {
      $charguildname = "";
   }
   $cb_template->assign_both_block_vars("characters", array( 
      'CLASS' => $dbclassnames[$character["class"]],      
      'LEVEL' => $character["level"],     
      'DELETED' => (($character["deleted_at"]) ? " ".$language['CHAR_DELETED']:""),
      'NAME' => $character["name"],
      'GUILD_NAME' => $charguildname )
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('body');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>