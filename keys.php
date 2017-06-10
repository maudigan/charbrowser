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
 *   September 26 2014 
 *      cleaned up double carriage returns through whole file 
 *      update character table name
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the item links customizable
 ***************************************************************************/ 
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true); 
include_once("include/config.php");
include_once("include/profile.php");
include_once("include/global.php");
include_once("include/language.php");
include_once("include/functions.php");
include_once("include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']); 
else $charName = $_GET['char']; 

//character initializations
$char = new profile($charName); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission 
if ($mypermission['keys']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']); 
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get keys from the db
$tpl = <<<TPL
SELECT k.item_id, i.Name AS 'key' 
FROM keyring AS k 
LEFT JOIN items AS i 
       ON i.id = k.item_id 
WHERE char_id =  %s 
ORDER BY i.Name
TPL;
$query = sprintf($tpl, $charID);
$result = cbsql_query($query);
$keys = array();
if (cbsql_rows($result))
   while($row = cbsql_nextrow($result)) 
      $keys[] = $row;
else
   message_die($language['KEYS_KEY']." - ".$name,$language['MESSAGE_NO_KEYS']);
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_KEYS']; 
include("include/header.php"); 
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$template->set_filenames(array( 
   'keys' => 'keys_body.tpl') 
); 

$template->assign_both_vars(array(  
   'NAME' => $name)
);
$template->assign_vars(array(  
   'L_KEY' => $language['KEYS_KEY'],
   'L_KEYS' => $language['BUTTON_KEYS'],
   'L_AAS' => $language['BUTTON_AAS'],
   'L_FLAGS' => $language['BUTTON_FLAGS'],
   'L_SKILLS' => $language['BUTTON_SKILLS'],
   'L_CORPSE' => $language['BUTTON_CORPSE'],
   'L_FACTION' => $language['BUTTON_FACTION'],
   'L_BOOKMARK' => $language['BUTTON_BOOKMARK'],
   'L_INVENTORY' => $language['BUTTON_INVENTORY'],
   'L_CHARMOVE' => $language['BUTTON_CHARMOVE'],
   'L_DONE' => $language['BUTTON_DONE'])
);

foreach ($keys as $key) {
   $template->assign_both_block_vars("keys", array( 
      'KEY' => $key['key'],
      'ITEM_ID' => $key["item_id"],
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $key["item_id"])))
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$template->pparse('keys');

$template->destroy;

include("include/footer.php"); 
?>