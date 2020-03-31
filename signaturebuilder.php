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
 *      turned the code that converts filenames in a directory
 *      into the option lists into a function.
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   March 14, 2020
 *      show char menu if we come in with a charname
 *      display optional charname in form
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
 
 
/*********************************************
             SUPPORT FUNCTIONS
*********************************************/
//for getting server locations for bbcode
function GetFileDir($php_self){ 
   $filename = explode("/", $php_self); // THIS WILL BREAK DOWN THE PATH INTO AN ARRAY 
   for( $i = 0; $i < (count($filename) - 1); ++$i ) { 
      $filename2 .= $filename[$i].'/'; 
   } 
   return $filename2; 
} 

//converts a directory into a list of options
function DirectoryToOptions($directory) {
   $filehandle = opendir($directory);
   $return = array();
   while (false != ($file = readdir($filehandle))) {
      $name = explode(".", $file);
      if ($name[0])
         $return[] = $name[0];
   }
   closedir($filehandle);
   
   return $return;
}


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//don't bother letting them build a signature
//if the server doesnt have GD installed
if (!SERVER_HAS_GD) {
   cb_message_die($language['MESSAGE_ERROR'], $language['MESSAGE_NO_GD']);
}

//prepopulate name if its provided
$name = $_GET['char'];
if (!IsAlphaSpace($name)) $name = "";

//build all the option lists for the dropdown boxes
//most are based off the files present in a directory
//some are static.
$epicborders = DirectoryToOptions(__DIR__."/images/signatures/epicborders");
$statborders = DirectoryToOptions(__DIR__."/images/signatures/statborders");
$borders = DirectoryToOptions(__DIR__."/images/signatures/borders");
$backgrounds = DirectoryToOptions(__DIR__."/images/signatures/backgrounds");
$screens = DirectoryToOptions(__DIR__."/images/signatures/screens");

if (SERVER_HAS_FREETYPE) 
   $fonts = DirectoryToOptions(__DIR__."/fonts");
else 
   $fonts = DirectoryToOptions(__DIR__."/fontsold"); //use old fonts if we can't handle TTF

$stats = array(
   'REGEN', 'FT', 'DS', 'HASTE', 'HP', 'MANA', 'ENDR', 'AC', 'ATK', 'STR', 'STA', 'DEX',
   'AGI', 'INT', 'WIS', 'CHA', 'PR', 'FR', 'MR', 'DR', 'CR', 'WT'
);
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SIGBUILD'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
//only drop this header if we came in with a character name
if ($name) {
   output_profile_menu($name, 'signaturebuilder');
}
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
   'settings' => 'settings_body.tpl')
); 

$cb_template->set_filenames(array(
   'sigbuild' => 'signature_builder_body.tpl')
);

$cb_template->assign_vars(array( 
   'CHARNAME' => $name,
   'SIGNATURE_ROOT_URL' => ($charbrowser_root_url) ? $charbrowser_root_url : "http://".$_SERVER['HTTP_HOST'].GetFileDir($_SERVER['PHP_SELF']),
   'SIGNATURE_INDEX_URL' => "http://".$_SERVER['HTTP_HOST'].GetFileDir($_SERVER['PHP_SELF']) . (($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php"),
   'CAN_CHANGE_FONT_SIZE' => (SERVER_HAS_FREETYPE) ? "" : "Disabled",

   'L_SIGNATURE_BUILDER' => $language['SIGNATURE_SIGNATURE_BUILDER'],
   'L_NAME' => $language['SIGNATURE_NAME'],
   'L_FONT_ONE' => $language['SIGNATURE_FONT_ONE'],
   'L_FONT_SIZE_ONE' => $language['SIGNATURE_FONT_SIZE_ONE'],
   'L_FONT_COLOR_ONE' => $language['SIGNATURE_FONT_COLOR_ONE'],
   'L_FONT_SHADOW_ONE' => $language['SIGNATURE_FONT_SHADOW_ONE'],
   'L_FONT_TWO' => $language['SIGNATURE_FONT_TWO'],
   'L_FONT_SIZE_TWO' => $language['SIGNATURE_FONT_SIZE_TWO'],
   'L_FONT_COLOR_TWO' => $language['SIGNATURE_FONT_COLOR_TWO'],
   'L_FONT_SHADOW_TWO' => $language['SIGNATURE_FONT_SHADOW_TWO'],
   'L_EPIC_BORDER' => $language['SIGNATURE_EPIC_BORDER'],
   'L_STAT_BORDER' => $language['SIGNATURE_STAT_BORDER'],
   'L_STAT_COLOR' => $language['SIGNATURE_STAT_COLOR'],
   'L_STATS' => $language['SIGNATURE_STATS'],
   'L_MAIN_BORDER' => $language['SIGNATURE_MAIN_BORDER'],
   'L_MAIN_BACKGROUND' => $language['SIGNATURE_MAIN_BACKGROUND'],
   'L_MAIN_COLOR' => $language['SIGNATURE_MAIN_COLOR'],
   'L_MAIN_SCREEN' => $language['SIGNATURE_MAIN_SCREEN'],
   'L_PREVIEW' => $language['SIGNATURE_PREVIEW'],
   'L_CREATE' => $language['SIGNATURE_CREATE'],
   'L_BBCODE' => $language['SIGNATURE_BBCODE'],
   'L_HTML' => $language['SIGNATURE_HTML'],
   'L_NEED_NAME' => $language['SIGNATURE_NEED_NAME'])
);

//display tabs
foreach($language['SIGNATURE_TABS'] as $key => $value)      
   $cb_template->assign_block_vars("tabs", array( 
      'ID' => $key,
      'TEXT' => $value)
   );

//fonts
foreach($fonts as $value)      
   $cb_template->assign_block_vars("font", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//epic borders
$cb_template->assign_block_vars("epicborders", array(  //insert an "off" option first
   'TEXT' => $language['SIGNATURE_OPTION_EPIC'],
   'VALUE' => 0)
);
foreach($epicborders as $value)      
   $cb_template->assign_block_vars("epicborders", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//stat borders
$cb_template->assign_block_vars("statborders", array(  //insert an "off" option first
   'TEXT' => $language['SIGNATURE_OPTION_STAT_ALL'],
   'VALUE' => 0)
);
foreach($statborders as $value)      
   $cb_template->assign_block_vars("statborders", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//stats
$cb_template->assign_block_vars("stats", array(  //insert an "off" option first
   'TEXT' => $language['SIGNATURE_OPTION_STAT_IND'],
   'VALUE' => 0)
);
foreach($stats as $value)      
   $cb_template->assign_block_vars("stats", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//borders
$cb_template->assign_block_vars("borders", array(  //insert an "off" option first
   'TEXT' => $language['SIGNATURE_OPTION_BORDER'],
   'VALUE' => 0)
);
foreach($borders as $value)      
   $cb_template->assign_block_vars("borders", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//backgrounds
$cb_template->assign_block_vars("backgrounds", array(  //insert an "off" option first
  'TEXT' => $language['SIGNATURE_OPTION_BACKGROUND'],
  'VALUE' => 0)
);
foreach($backgrounds as $value)      
   $cb_template->assign_block_vars("backgrounds", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//screen filters
$cb_template->assign_block_vars("screens", array( //insert an "off" option first
   'TEXT' => $language['SIGNATURE_OPTION_SCREEN'],
   'VALUE' => 0)
);
foreach($screens as $value)      
   $cb_template->assign_block_vars("screens", array( 
      'TEXT' => $value,
      'VALUE' => $value)
   );

//font sizes
for ($i = 5; $i <= 40; $i++ ) {
   $cb_template->assign_block_vars("fontsize", array(  
      'TEXT' => $i,
      'VALUE' => $i)
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('sigbuild');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>