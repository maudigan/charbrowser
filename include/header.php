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
 *
 ***************************************************************************/
 
//dont make a header if there is an API request 
if (isset($_GET['api'])) return;
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}
include_once(__DIR__ . "/config.php");
include_once(__DIR__ . "/version.php");

if ($charbrowser_simple_header)
{
   $cb_template->set_filenames(array(
     'header' => 'header_simple_body.tpl')
   );
}
else
{
   $cb_template->set_filenames(array(
     'header' => 'header_body.tpl')
   );
}

$cb_template->assign_vars(array(  
  'TITLE' => $mytitle,
  'SUBTITLE' => $d_title,
  'VERSION' => $version,
  'ADVERTISEMENT' => $adscript,
  'ROOT_URL' => $charbrowser_root_url,
  'INDEX_URL' => ($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php",
  'L_GUILD' => $language['HEADER_GUILD'],
  'L_NAME' => $language ['HEADER_NAME'],
  'L_SETTINGS' => $language['HEADER_SETTINGS'],
  'L_BAZAAR' => $language['HEADER_BAZAAR'],
  'L_HOME' => $language['HEADER_HOME'],
  'L_SERVER' => $language['HEADER_SERVER'],
  'L_CHARMOVE' => $language['HEADER_CHARMOVE'],
  'L_SIGBUILD' => $language['HEADER_SIGBUILD'],
  'L_REPORT_ERRORS' => $language['HEADER_REPORT_ERRORS'],
  'L_HELP' => $language ['HEADER_HELP'],
  'L_NAVIGATE' => $language ['HEADER_NAVIGATE'])
);

$cb_template->pparse('header');

$cb_template->destroy;
?>