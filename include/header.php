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
 *
 ***************************************************************************/
 
//dont make a header if there is an API request 
if (isset($_GET['api'])) return;
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}
include_once("config.php");
include_once("version.php");

$template->set_filenames(array(
  'header' => 'header_body.tpl')
);

$template->assign_vars(array(  
  'TITLE' => $mytitle,
  'SUBTITLE' => $d_title,
  'VERSION' => $version,
  'ADVERTISEMENT' => $adscript,
  'L_GUILD' => $language['HEADER_GUILD'],
  'L_NAME' => $language ['HEADER_NAME'],
  'L_SETTINGS' => $language['HEADER_SETTINGS'],
  'L_BAZAAR' => $language['HEADER_BAZAAR'],
  'L_HOME' => $language['HEADER_HOME'],
  'L_CHARMOVE' => $language['HEADER_CHARMOVE'],
  'L_SIGBUILD' => $language['HEADER_SIGBUILD'],
  'L_REPORT_ERRORS' => $language['HEADER_REPORT_ERRORS'],
  'L_HELP' => $language ['HEADER_HELP'])
);

$template->pparse('header');

$template->destroy;
?>