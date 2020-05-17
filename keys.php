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
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 3, 2020 - Maudigan
 *     changes to minimize database access
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
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id();
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['keys']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get keys from the character db
$tpl = <<<TPL
SELECT item_id 
FROM keyring 
WHERE char_id =  %s 
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);

//error if there's no results that match
if (!$cbsql->rows($result)) cb_message_die($language['KEYS_KEY'],$language['MESSAGE_NO_KEYS']);

$keys = $cbsql->fetch_all($result);  
$key_ids = get_id_list($keys, 'item_id');


//get item data using list of key ids
$tpl = <<<TPL
SELECT Name, id
FROM items 
WHERE id in (%s)
ORDER BY Name ASC
TPL;

$query = sprintf($tpl, $key_ids);
$result = $cbsql_content->query($query);

$keys = $cbsql_content->fetch_all($result);  



/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_KEYS'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'keys');


/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'keys' => 'keys_body.tpl')
);

$cb_template->assign_both_vars(array(
   'NAME' => $name)
);
$cb_template->assign_vars(array(
   'L_KEY' => $language['KEYS_KEY'],
   'L_DONE' => $language['BUTTON_DONE'])
);

foreach ($keys as $key) {
   $cb_template->assign_both_block_vars("keys", array(
      'KEY' => $key['Name'],
      'ITEM_ID' => $key["id"],
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $key["id"])))
   );
}


/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('keys');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>
