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
 *   March 14, 2020
 *      added alpha background
 *   March 15, 2020
 *      clean special chars out of the title before generating image
 *      removed the old deprecated font method for people without ttftext
 *      rewrote the image generation to have a border and static colors
 *      made the image cache itself to reduce server load and improve speed
 *   March 22, 2020 - Maudigan
 *      Implemented common.php
 *   March 22, 2020 - Maudigan
 *      Added client caching
 *   April 2, 2020 - Maudigan
 *      flush the cache prior to outputting the image to make sure
 *      we don't send a text header
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
//define this as an entry point to unlock includes
if ( !defined('INCHARBROWSER') ) 
{
   define('INCHARBROWSER', true);
}
define('IS_IMAGE_SCRIPT', true);
include_once(__DIR__ . "/include/common.php");



/*********************************************
          GATHER IMAGE PARAMETERS
*********************************************/
//exit now and post message if server doesnt have GD installed
if (!SERVER_HAS_GD) {
   print $language['MESSAGE_NO_GD'];
   exit();
}

//force a recache of the title image
$recache = (checkParm('recache')) ? true : false;


//CACHE CONTROL
$config_time = filemtime('./include/config.php');

//if the one they have is newer than the config, then theirs is right
//if this is a modified check, and their timestamp is more recent than the config file, regenerate the image
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $config_time)
{
    header('HTTP/1.0 304 Not Modified');
    exit;
}


if(!$titlefontsize)  $titlefontsize = 25;
if(!$mytitle)        $mytitle = "No Title";
$angle = 0;

//clean the site title for the image
$cleantitle = preg_replace("/[^a-zA-Z0-9\ ]+/", "", $mytitle);

//path to cache the title image
//or to load the static title image
if (isset($titleimage)) {
   $cachedfile = __DIR__ . "/images/".$titleimage;
}
else {
   $cachedfile = __DIR__ . "/images/cached_titles/".$cleantitle."_".$titlefontsize."_".$titlefont.".png";
}


/*********************************************
                FUNCTIONS
*********************************************/
function fontBorder($image, $size, $angle, $x, $y, $color, $font, $text, $radius) {
   for ($hor = 0 - $radius; $hor <= $radius; $hor++) {
      for ($ver = 0 - $radius; $ver <= $radius; $ver++) {
         imagettftext($image, $size, $angle, $x + $hor, $y + $ver, $color, $font, $text);
      }
   }
}



/*********************************************
              BUILD THE IMAGE
             OR LOAD FROM CACHE
*********************************************/
if (!file_exists($cachedfile) || $recache) {
   $fontfile = "fonts/$titlefont.ttf";
   
   //calculate dimensions
   $bbox = imagettfbbox($titlefontsize, $angle, $fontfile, $cleantitle);
   $width = abs($bbox[4]) + abs($bbox[0]) + 13;
   $height = abs($bbox[1]) + abs($bbox[5]) + 13;
   $x = abs($bbox[0]) + 5;
   $y = abs($bbox[5]) + 5;

   //create image and colors
   $image = imagecreatetruecolor($width, $height);
   imagealphablending($image, true);
   $yellow = imagecolorallocatealpha($image, 229, 202, 0, 0);
   $black = imagecolorallocatealpha($image, 0, 0, 0, 0);
   $white = imagecolorallocatealpha($image, 255, 255, 255, 0);
   $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
   
   //draw image
   imagefill($image, 0, 0, $transparent);
   imagesavealpha($image, true);
   fontBorder($image, $titlefontsize, $angle, $x, $y, $white, $fontfile, $cleantitle, 3);
   fontBorder($image, $titlefontsize, $angle, $x, $y, $black, $fontfile, $cleantitle, 2);
   imagettftext($image, $titlefontsize, $angle, $x, $y, $yellow, $fontfile, $cleantitle);
   
   //save it for future use
   imagepng($image, $cachedfile);
}
else {
   //load previously generated image
   $image = imagecreatefrompng($cachedfile);
   imagesavealpha($image, true);
}




/*********************************************
               OUTPUT IMAGE
*********************************************/
if (ob_get_contents()) ob_clean(); //make sure we haven't sent a text header, squelch or it'll post a notice when already empty
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $config_time).' GMT', true, 200);
header("Content-Type: image/png"); 
imagepng($image);
ImageDestroy($image);
?>