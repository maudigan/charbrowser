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
 *   September 16, 2017 - added an optional simple footer.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *
 ***************************************************************************/
 
//dont make a header if there is an API request 
if (isset($_GET['api'])) return;
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}

//if db performance is turned on fetch the output
if (defined('DB_PERFORMANCE') && $cbsql)
{
   $dbp_output = $cbsql->dbp_fetch_parsed();
}


if ($charbrowser_simple_header)
{
   $cb_template->set_filenames(array(
     'footer' => 'footer_simple_body.tpl')
   );
}
else
{
   $cb_template->set_filenames(array(
     'footer' => 'footer_body.tpl')
   );
}

$cb_template->assign_vars(array(  
  'TITLE' => $mytitle,
  'VERSION' => $version,
  'DATABASE_PERFORMANCE' => $dbp_output,
  'ADVERTISEMENT' => $adscript)
);

$cb_template->pparse('footer');

$cb_template->destroy;
?>