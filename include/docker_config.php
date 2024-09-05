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
 *   Febuary, 2024 - Maudigan
 *      This overwrites the config file settings with environmental
 *      variables to make us compatible with docker.
 *
 *
 ***************************************************************************/
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}


// Overwrites the config file settings for the database
// with environmental variables (if they exist)
function env($variable, &$config)
{
    if ($ev = getenv($variable))
       $config = $ev;
}

//overwrite the database config 
//settings with environment variables
env('CHARBROWSER_DB_NAME' ,$cb_db);
env('CHARBROWSER_DB_HOST' ,$cb_host);
env('CHARBROWSER_DB_USER' ,$cb_user);
env('CHARBROWSER_DB_PASSWORD' ,$cb_pass);
env('CHARBROWSER_DB_PORT' ,$cb_port);

//overwrite content database settings
// with environment variables
env('CHARBROWSER_CONTENT_DB_NAME' ,$cb_content_db);
env('CHARBROWSER_CONTENT_DB_HOST' ,$cb_content_host);
env('CHARBROWSER_CONTENT_DB_USER' ,$cb_content_user);
env('CHARBROWSER_CONTENT_DB_PASSWORD' ,$cb_content_pass);
env('CHARBROWSER_CONTENT_DB_PORT' ,$cb_content_port);

?>