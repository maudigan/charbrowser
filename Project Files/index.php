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
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once("include/config.php");
include_once("include/version.php");
include_once("include/language.php");
include_once("include/functions.php");
include_once("include/global.php");
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = $subtitle;
include("include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$template->set_filenames(array(
  'index' => 'index_body.tpl')
);

$template->assign_both_vars(array(  
   'TITLE' => $mytitle,
   'VERSION' => $version)
);
$template->assign_vars(array(  
   'L_VERSION' => $language['INDEX_VERSION'],
   'L_BY' => $language['INDEX_BY'],
   'L_INTRO' => $language['INDEX_INTRO'])
);
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$template->pparse('index');

$template->destroy;

include("include/footer.php");
?>