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
include_once(__DIR__ . "/include/config.php");
include_once(__DIR__ . "/include/global.php");



/*********************************************
          GATHER IMAGE PARAMETERS
*********************************************/
//exit now and post message if server doesnt have GD installed
if (!SERVER_HAS_GD) {
   print $language['MESSAGE_NO_GD'];
   exit();
}

if(!$titlefontR)     $titlefontR = 1;
if(!$titlefontG)     $titlefontG = 1;
if(!$titlefontB)     $titlefontB = 1;
if(!$titlefontsize)  $titlefontsize = 25;
if(!$mytitle)        $mytitle = "No Title";
$angle = 0;


/*********************************************
              BUILD THE IMAGE
*********************************************/
//has freetype
if (SERVER_HAS_FREETYPE) {
   $titlefont = "fonts/$titlefont.ttf";
   $bbox = imagettfbbox($titlefontsize, $angle, $titlefont, $mytitle);

   $width = abs($bbox[4]) + abs($bbox[0]);
   $height = abs($bbox[1]) + abs($bbox[5]);
   $x = abs($bbox[0]);
   $y = abs($bbox[5]);

   $image = imagecreatetruecolor($width, $height);
   $color = imagecolorallocate($image, $titlefontR, $titlefontG, $titlefontB);
   $white = imagecolorallocate($image, 255, 255, 255);
   imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $white);
   imagettftext($image, $titlefontsize, $angle, $x, $y, $color, $titlefont, $mytitle);
}

// does not have freetype
else {
   $titlefont = "fontsold/$titlefont.gdf";
   $titlefont = ImageLoadFont($titlefont);

   $width = ImageFontWidth($titlefont) * strlen($mytitle);
   $height = ImageFontHeight($titlefont);

   $image = imagecreatetruecolor($width, $height);
   $color = imagecolorallocate($image, $titlefontR, $titlefontG, $titlefontB);
   $white = imagecolorallocate($image, 255, 255, 255);
   imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $white);
   ImageString($image, $titlefont, 0, 0, $mytitle, $color); 
} 


/*********************************************
               OUTPUT IMAGE
*********************************************/
header("Content-Type: image/png"); 
imagepng($image); 
ImageDestroy($image);
?> 