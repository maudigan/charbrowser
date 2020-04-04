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
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   March 14, 2020 - Maudigan
 *      broke the table header into it's own template var
 *   March 17, 2020 - Maudigan
 *      implemented self version checking
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   APril 2, 2020 - Maudigan
 *     swapped to version_compare for the version check so we don't show
 *     the update message if they have applied a patch that's newer than
 *     the latest release.
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");

/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
// keys match the permissions array in 
// config.php for easy foreach looping
$l_permission = array ( 
   'inventory'       => $language['SETTINGS_INVENTORY'],
   'coininventory'   => $language['SETTINGS_ICOIN'], 
   'coinbank'        => $language['SETTINGS_BCOIN'],
   'bags'            => $language['SETTINGS_BAGS'],
   'bank'            => $language['SETTINGS_BANK'],
   'corpses'         => $language['SETTINGS_CORPSES'],
   'flags'           => $language['SETTINGS_FLAGS'],
   'AAs'             => $language['SETTINGS_AAS'],
   'keys'            => $language['SETTINGS_KEYS'],
   'factions'        => $language['SETTINGS_FACTIONS'],
   'advfactions'     => $language['SETTINGS_ADVFACTIONS'],    
   'skills'          => $language['SETTINGS_SKILLS'],
   'languageskills'  => $language['SETTINGS_LSKILLS'],
   'signatures'      => $language['SETTINGS_SIGNATURES']
);

$l_users = array (
   'ALL'       => $language['SETTINGS_USERS_ALL'],
   'ROLEPLAY'  => $language['SETTINGS_USERS_RP'],
   'ANON'      => $language['SETTINGS_USERS_ANON'],
   'GM'        => $language['SETTINGS_USERS_GM'],
   'PUBLIC'    => $language['SETTINGS_USERS_PUBLIC'],
   'PRIVATE'   => $language['SETTINGS_USERS_PRIVATE'],
);
 
 
//CHECK GITHUB FOR CHARBROWSER UPDATES
//request the data from the github api
//github requires a user-agent so it'll respond
//dont remove it
$CB_opts = array(
   'http' => array(
      'method' => 'GET',
      'header' => array(
         'User-Agent: PHP'
      )
   )
);

$CB_context = stream_context_create($CB_opts);
//if you're using a custom repo/fork this would be the location to redirect to that URL instead
$CB_content = file_get_contents('https://api.github.com/repos/maudigan/charbrowser/releases/latest', false, $CB_context);
$CB_json = json_decode($CB_content);

//current version number
$CB_cur_version =  $CB_json->tag_name; 

//is this install older than the latest major release (new patches are ignored)
$CB_is_old = (version_compare($CB_cur_version, $version) == 1) ? true : false;

//if it's old grab the new version info, when it was published
//download url, and its description
if ($CB_is_old) {
   $CB_cur_version_url = $CB_json->html_url;
   $CB_cur_version_published = date("F j, Y, g:i a", strtotime($CB_json->published_at));
   $CB_cur_version_description = $CB_json->body;
}


 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SETTINGS'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'settings' => 'settings_body.tpl')
);
//column heads
$cb_template->assign_both_block_vars( "headers" , array());
$cb_template->assign_both_block_vars( "headers.cols" , array(
   'VALUE' => "" )
);  
foreach ($l_users as $key => $value) {
   $cb_template->assign_both_block_vars( "headers.cols" , array(
      'VALUE' => $value )
   );    
}

//column data
foreach ($l_permission as $key => $value) {
   $cb_template->assign_both_block_vars( "rows" , array());
   $cb_template->assign_both_block_vars( "rows.cols" , array(
      'VALUE' => $value)
   );   
   foreach ($l_users as $key2 => $value2) {
      $cb_template->assign_both_block_vars( "rows.cols" , array(
         'VALUE' => ($permissions[$key2][$key]) ? "" : "x" )
      );    
   }
}

//is this installation of charbrowser current
if ($CB_is_old && !$cb_blockversioncheck) {
   //all this data comes from github, so we want to santize it to prevent
   //cross site scripting attacks, do not remove xss_safe() calls
   //or you create a vulnerability
   $cb_template->assign_both_block_vars( "switch_new_version" , array(
      'VERSION' => xss_safe($CB_cur_version),
      'URL' => xss_safe($CB_cur_version_url),
      'PUBLISHED' => xss_safe($CB_cur_version_published),
      'DESCRIPTION' => xss_safe($CB_cur_version_description))
   ); 
}

$cb_template->assign_both_vars(array(  
   'S_RESULTS' => $numToDisplay,
   'S_HIGHLIGHT_GM' => (($highlightgm)?$language['SETTINGS_ENABLED']:$language['SETTINGS_DISABLED']),
   'S_BAZAAR' => (($blockbazaar)?$language['SETTINGS_DISABLED']:$language['SETTINGS_ENABLED']),
   'S_CHARMOVE' => (($blockcharmove)?$language['SETTINGS_DISABLED']:$language['SETTINGS_ENABLED']),
   'S_GUILDVIEW' => (($blockguilddata)?$language['SETTINGS_DISABLED']:$language['SETTINGS_ENABLED']),
   'S_SERVERVIEW' => (($blockserverdata)?$language['SETTINGS_DISABLED']:$language['SETTINGS_ENABLED']))
);
$cb_template->assign_vars(array(  
   'L_RESULTS' => $language['SETTINGS_RESULTS'],
   'L_CHARMOVE' => $language['SETTINGS_CHARMOVE'],
   'L_GUILDVIEW' => $language['SETTINGS_GUILDVIEW'],
   'L_SERVERVIEW' => $language['SETTINGS_SERVERVIEW'],
   'L_HIGHLIGHT_GM' => $language['SETTINGS_HIGHLIGHT_GM'],
   'L_UPDATES_EXIST' => $language['SETTINGS_UPDATES_EXIST'],
   'L_DOWNLOAD' => $language['SETTINGS_DOWNLOAD'],
   'L_BAZAAR' => $language['SETTINGS_BAZAAR'],
   'L_SETTINGS' => $language['SETTINGS_SETTINGS'],
   'L_BACK' => $language['BUTTON_BACK'])
);
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('settings');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>