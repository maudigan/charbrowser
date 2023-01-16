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
 *   May 17, 2017 - added version.php include and changed the config.php
 *                  include to include_once()
 *   September 16, 2017 - added an optional simple header.
 *   April 2, 2020 - made the index url a var so subsequent scripts can
 *                   use it too.
 *   October 24, 2022 - make this only run one time (maudigan)
 *                      added the no header option for ingame browser
 *   October 27, 2022 - Added barter link (maudigan)
 *   January 16, 2023 - make sure header vars are initialized
 *                      populate previous searches into the input fields so
 *                       users don't have to retype them
 *
 ***************************************************************************/
 
//dont make a header if there is an API request 
if (checkParm('api')) return;
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}

//this script only needs to run once
if ( !defined('CB_HEADER_RUN') )
{
   include_once(__DIR__ . "/config.php");
   include_once(__DIR__ . "/version.php");

   //header for embeded
   if ($charbrowser_simple_header)
   {
      $cb_template->set_filenames(array(
        'header' => 'header_simple_body.tpl')
      );
   }
   //header for ingame browser
   elseif (checkParm('nohead'))
   {
      $cb_template->set_filenames(array(
        'header' => 'header_none_body.tpl')
      );
   }
   //normal header
   else
   {
      $cb_template->set_filenames(array(
        'header' => 'header_body.tpl')
      );
   }
   
   //make sure search params were initialized
   if (!isset($header_name_search)) $header_name_search = '';
   if (!isset($header_guild_search)) $header_guild_search = '';

   $cb_template->assign_vars(array(  
     'TITLE' => $mytitle,
     'SUBTITLE' => $d_title,
     'VERSION' => $version,
     'ADVERTISEMENT' => $adscript,
     'ROOT_URL' => $charbrowser_root_url,
     'INDEX_URL' => $cb_index_url,
     'SEARCH_NAME' => $header_name_search,
     'SEARCH_GUILD' => $header_guild_search,
     
     'L_GUILD' => $language['HEADER_GUILD'],
     'L_NAME' => $language ['HEADER_NAME'],
     'L_SETTINGS' => $language['HEADER_SETTINGS'],
     'L_BAZAAR' => $language['HEADER_BAZAAR'],
     'L_BARTER' => $language['HEADER_BARTER'],
     'L_LEADERBOARD' => $language['HEADER_LEADERBOARD'],
     'L_HOME' => $language['HEADER_HOME'],
     'L_SERVER' => $language['HEADER_SERVER'],
     'L_CHARMOVE' => $language['HEADER_CHARMOVE'],
     'L_SIGBUILD' => $language['HEADER_SIGBUILD'],
     'L_REPORT_ERRORS' => $language['HEADER_REPORT_ERRORS'],
     'L_HELP' => $language ['HEADER_HELP'],
     'L_NAVIGATE' => $language ['HEADER_NAVIGATE'])
   );

   $cb_template->pparse('header');

   $cb_template->destroy();
   
   //LIMIT ME
   //this script only needs to run once
   define('CB_HEADER_RUN', true);
}
?>