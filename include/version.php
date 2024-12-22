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
 *   March 22, 2020 - Maudigan
 *      Implemented a way to detect old config files
 *
 ***************************************************************************/
 
 
 
  

if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}

//version information
$version      =    "v3.8.2"; 

//this file always gets replaced on a reinstall
//this gets compared to the config file version stamp
//to detect old config files. This only gets updated
//when major changes needing attention are made to
//the config.template
$cb_expected_config_version = 'v2.1.0'
?>