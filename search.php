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
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once("include/config.php");
include_once("include/global.php");
include_once("include/language.php");
include_once("include/functions.php");
include_once("include/db.php");

 
/*********************************************
             GET/VALIDATE VARS
*********************************************/ 
$start      = (($_GET['start']) ? $_GET['start'] : "0");
$orderby    = (($_GET['orderby']) ? $_GET['orderby'] : "name");
$direction  = (($_GET['direction']=="DESC") ? "DESC" : "ASC");
$name       = $_GET['name'];
$guild      = $_GET['guild'];

//build baselink
$baselink="search.php?name=$name&guild=$guild";

//security for injection attacks
if (!IsAlphaSpace($name)) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NAME_ALPHA']);
if (!IsAlphaSpace($guild)) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_GUILD_ALPHA']);
if (!IsAlphaSpace($orderby)) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ORDER_ALPHA']);
if (!is_numeric($start)) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_START_NUMERIC']);
 
 
/*********************************************
        BUILD AND EXECUTE THE SEARCH
*********************************************/ 
//build where clause
$where = "";
$divider = "WHERE ";
if ($name) {
   $where .= $divider."character_data.name LIKE '%".str_replace("_", "%", str_replace(" ","%",$name))."%'"; 
   $divider = "AND ";
}
if ($guild) {
   $where .= $divider."guilds.name LIKE '%".str_replace("_", "%", str_replace(" ","%",$guild))."%'";
   $divider = "AND ";
}

//build the orderby & limit clauses
$order = "ORDER BY $orderby $direction LIMIT $start, $numToDisplay;";

//build the query, leave a spot for the where
//and the orderby clauses
$tpl = <<<TPL
SELECT character_data.class, character_data.level, 
       character_data.name, guilds.name AS guildname 
FROM character_data 
LEFT JOIN guild_members
       ON character_data.id = guild_members.char_id 
LEFT JOIN guilds
       ON guilds.id = guild_members.guild_id 
%s 
%s
TPL;
 
//query once with no limit by just to get the count
$query = sprintf($tpl, $where, '');
$result = cbsql_query($query);
$totalchars = cbsql_rows($result);
if (!$totalchars) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_RESULTS']);

//now add on the limit & ordering and query again for just this page
$query = sprintf($tpl, $where, $order);
$result = cbsql_query($query);
$characters = array();
while ($row = cbsql_nextrow($result)) {
   $characters[] = $row;
}
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SEARCH'];
include("include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
//build body template
$template->set_filenames(array(
   'body' => 'search_body.tpl')
);

$template->assign_vars(array(  
   'ORDER_LINK' => $baselink."&start=$start&direction=".(($direction=="ASC") ? "DESC":"ASC"), 
   'PAGINATION' => generate_pagination("$baselink&orderby=$orderby&direction=$direction", $totalchars, $numToDisplay, $start, true),
   
   'L_RESULTS' => $language['SEARCH_RESULTS'],
   'L_NAME' => $language['SEARCH_NAME'],
   'L_LEVEL' => $language['SEARCH_LEVEL'],
   'L_CLASS' => $language['SEARCH_CLASS'],)
);

foreach ($characters as $character) {
   $template->assign_both_block_vars("characters", array( 
      'CLASS' => $dbclassnames[$character["class"]],      
      'LEVEL' => $character["level"],
      'NAME' => $character["name"],
      'GUILD_NAME' => (($character["guildname"]) ? "&lt;".$character["guildname"]."&gt;":"") )
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$template->pparse('body');

$template->destroy;

include("include/footer.php");
?>