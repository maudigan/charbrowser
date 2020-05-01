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
 *   February 24, 2014 - Changed items to png files (Maudigan c/o Warmonger)
 *   September 26, 2014 - Maudigan
 *      made STR/STA/DEX/etc lowercase to match the db column names
 *      Updated character table name
 *      rewrote the code that pulls guild name/rank
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences.
 *      Implemented new database wrapper.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 7, 2020 - Maudigan
 *      replaced the deprecated ereg_replace function
 *   March 14, 2020 - Maudigan
 *      moved error function to functions.php
 *      prepopulate charname in form if its provided
 *   March 16, 2020 - Maudigan
 *      prepared for implementation of dynamic guild ranks
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   March 31, 2020 - Maudigan
 *     updated to work with all the profile.php changes
 *   April 2, 2020 - Maudigan
 *      flush the cache prior to outputting an image to make sure
 *      we don't send a text header
 *   April 2, 2020 - Maudigan
 *     dont show anon guild members names
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 ***************************************************************************/

/**
 * Autoloader
 */
spl_autoload_register(
    function ($class_name) {
        $class_name = str_replace("\\", "/", $class_name);
        $class      = __DIR__ . "/include/{$class_name}.php";
        include_once($class);
    }
);

/*********************************************
                 INCLUDES
*********************************************/
define('INCHARBROWSER', true);
$charbrowser_image_script = true;
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");



/*********************************************
             SUPPORT FUNCTIONS
*********************************************/
//convert passed hex color to RGB
function HexToRGB($hex) {
   $hex = str_replace("#", "", $hex);
   $color = array();
   if(strlen($hex) == 3) {
      $color['r'] = hexdec(substr($hex, 0, 1) . $r);
      $color['g'] = hexdec(substr($hex, 1, 1) . $g);
      $color['b'] = hexdec(substr($hex, 2, 1) . $b);
   }
   else if(strlen($hex) == 6) {
      $color['r'] = hexdec(substr($hex, 0, 2));
      $color['g'] = hexdec(substr($hex, 2, 2));
      $color['b'] = hexdec(substr($hex, 4, 2));
   }
   return $color;
}



/*********************************************
          GATHER IMAGE PARAMETERS
*********************************************/
//exit now and post message if server doesnt have GD installed
if (!SERVER_HAS_GD) {
  print $language['MESSAGE_NO_GD'];
  exit();
}

//paths where files are saved
$path = array(
   'BACKGROUND'   => 'images/signatures/backgrounds/%s.png',
   'BORDER'       => 'images/signatures/borders/%s.png',
   'SCREEN'       => 'images/signatures/screens/%s.png',
   'STATBORDER'   => 'images/signatures/statborders/%s.png',
   'EPICBORDER'   => 'images/signatures/epicborders/%s.png',
   'EPIC'         => 'images/items/item_%s.png',
   'FONT'         => (SERVER_HAS_FREETYPE) ? 'fonts/%s.ttf' : 'fontsold/%s.gdf'
);

//get our starting values from _GET or presets
// several parameters are '-' delimited since mod_rewrite can only handle 9 parameters
$defaultcolor = array( 'r'=>255, 'g'=>255, 'b'=>255 );
$getone = explode("-", $_GET['one']);
$gettwo = explode("-", $_GET['two']);
$getstat = explode("-", $_GET['stat']);
$getbackground = explode("-", $_GET['background']);

$fontone    = ($getone[0]) ? sprintf($path['FONT'], $getone[0]) : sprintf($path['FONT'], "roman") ;
$sizeone    = ($getone[1]) ? $getone[1] : 20;
$colorone   = ($getone[2]) ? HexToRGB($getone[2]) : $defaultcolor;
$shadowone  = ($getone[3]) ? 1 : 0;

$fonttwo    = ($gettwo[0]) ? sprintf($path['FONT'], $gettwo[0]) : sprintf($path['FONT'], "roman") ;
$sizetwo    = ($gettwo[1]) ? $gettwo[1] : 10;
$colortwo   = ($gettwo[2]) ? HexToRGB($gettwo[2]) : $defaultcolor;
$shadowtwo  = ($gettwo[3]) ? 1 : 0;

$epicbg     = ($_GET['epic']) ? sprintf($path['EPICBORDER'], $_GET['epic']) : false ;

$statdisplay    = array();
$statdisplay[0] = ($getstat[2]) ? $getstat[2] : false ;
$statdisplay[1] = ($getstat[3]) ? $getstat[3] : false ;
$statdisplay[2] = ($getstat[4]) ? $getstat[4] : false ;
$statdisplay[3] = ($getstat[5]) ? $getstat[5] : false ;
$statdisplay[4] = ($getstat[6]) ? $getstat[6] : false ;
$statcolor      = ($getstat[1]) ? HexToRGB($getstat[1]) : $defaultcolor;
$statbg         = ($getstat[0]) ? sprintf($path['STATBORDER'], $getstat[0]) : false ;

$background     = ($getbackground[1]) ? sprintf($path['BACKGROUND'], $getbackground[1]) : false;
$bgcolor        = ($getbackground[0]) ? HexToRGB($getbackground[0]) : $defaultcolor;
$screen         = ($getbackground[2]) ? sprintf($path['SCREEN'], $getbackground[2]) : false;

$border         = ($_GET['border']) ? sprintf($path['BORDER'], $_GET['border']) : false;


//starting points of text
$line_start_x = 15;
$line_start_y = 12;

//stats constants, starting points, etc
$stat_start_x = 16;
$stat_start_y = 70;
$stat_step_x = 97;
$stat_width = 80;
$stat_height = 18;
$stat_text_y = 2;
$stat_text_x = 8;

//epic constants
$epic_x = 420;
$epic_y = 15;
$epic_icon_offset = 0;
$epic_width_height = 40;
$epic_icon_width_height = 40;

$signaturewidth = 500;
$signatureheight = 100;


/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];

//character initializations
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id();
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['signatures']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);


/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//load profile information for the character
$last_name  = $char->GetValue('last_name');
$title      = $char->GetValue('title');
$level      = $char->GetValue('level');
$deity      = $char->GetValue('deity');
$race       = $char->GetValue('race');
$class      = $char->GetValue('class');


if ($char->GetValue('anon') != 1 || $showguildwhenanon || $charbrowser_is_admin_page) {
   /* this will get implemented in the server code soon, uncomment and remove the code below
   //load guild name dynamically
   $tpl = <<<TPL
   SELECT guilds.name, guild_ranks.title
   FROM guilds
   JOIN guild_members
     ON guilds.id = guild_members.guild_id
   JOIN guild_ranks
     ON guild_members.rank = guild_ranks.rank
    AND guild_members.guild_id = guild_ranks.guild_id
   WHERE guild_members.char_id = '%s'
   LIMIT 1
   TPL;
   $query = sprintf($tpl, $charID);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   {
      $row = $cbsql->nextrow($result);
      $guild_name = $row['name'];
      $guild_rank = $row['title'];
   } */

   //load guild name statically
   $tpl = <<<TPL
   SELECT guilds.name, guild_members.rank 
   FROM guilds
   JOIN guild_members
     ON guilds.id = guild_members.guild_id
   WHERE guild_members.char_id = '%s' 
   LIMIT 1
TPL;
   $query = sprintf($tpl, $charID);
   $result = $cbsql->query($query);
   if($cbsql->rows($result))
   {
      $row = $cbsql->nextrow($result);
      $guild_name = $row['name'];
      $guild_rank = $guildranks[$row['rank']];
   }
}


//stage the data and do final calculations
$chardata = array(
   'FIRST_NAME' => $name,
   'LAST_NAME' => $last_name,
   'TITLE' => $title,
   'GUILD_NAME' => $guild_name,
   'GUILD_RANK' => $guild_rank,
   'LEVEL' => $level,
   'CLASS' => $dbclassnames[$class],
   'RACE' => $dbracenames[$race],
   'DEITY' => $dbdeities[$deity],
);

$stats = array(
   'REGEN' => $char->getRegen(),
   'FT' => $char->getFT(),
   'DS' => $char->getDS(),
   'HASTE' => $char->getHaste()."%",
   'HP' => $char->CalcMaxHP(),
   'MANA' => $char->CalcMaxMana(),
   'ENDR' => $char->CalcMaxEndurance(),
   'AC' => $char->GetDisplayAC(),
   'ATK' => $char->GetTotalATK(),
   'STR' => $char->getSTR(),
   'STA' => $char->getSTA(),
   'DEX' => $char->getDEX(),
   'AGI' => $char->getAGI(),
   'INT' => $char->getINT(),
   'WIS' => $char->getWIS(),
   'CHA' => $char->getCHA(),
   'PR' => $char->getPR(),
   'FR' => $char->getFR(),
   'MR' => $char->getMR(),
   'DR' => $char->getDR(),
   'CR' => $char->getCR(),
   'WT' => round($char->getWT()/10)
);


/*********************************************
              BUILD THE IMAGE
*********************************************/

/**************************************************
**  DO NOT USE png_cb_message_die past this point   **
**************************************************/

//create image
$image = imagecreatetruecolor($signaturewidth, $signatureheight);

//apply background color
$bgcolor = imagecolorallocate($image, $bgcolor['r'], $bgcolor['g'], $bgcolor['b']);
imagefilledrectangle($image, 0, 0, $signaturewidth - 1, $signatureheight - 1, $bgcolor);

//apply background image
if ($background) {
   $tempimage = imagecreatefrompng($background);
   imagecopy($image, $tempimage, 0, 0, 0, 0, $signaturewidth, $signatureheight);
   imagedestroy($tempimage);
}

//aply alpha screen to bg
if ($screen) {
   $tempimage = imagecreatefrompng($screen);
   imagecopy($image, $tempimage, 0, 0, 0, 0, $signaturewidth, $signatureheight);
   imagedestroy($tempimage);
}


//drop epic on if an icon was set earlier
if($epicbg && $epicicon) {
   $tempimage = imagecreatefrompng($epicbg);
   imagecopy($image, $tempimage, $epic_x, $epic_y, 0, 0, $epic_width_height, $epic_width_height);
   imagedestroy($tempimage);
   $tempimage = imagecreatefrompng($epicicon);
   imagecopy($image, $tempimage, $epic_x + $epic_icon_offset, $epic_y + $epic_icon_offset, 0, 0, $epic_icon_width_height , $epic_icon_width_height );
   imagedestroy($tempimage);
}

// for drawing the 3 lines of text
$black = imagecolorallocate($image, 0, 0, 0);
function drawtext( $image, $text, $size, $font, $color, $offsetx, $offsety, $shadow) {
   global $black;
   $color = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);

   if (SERVER_HAS_FREETYPE) {
      $bbox = imagettfbbox($size, 0, $font, $text);
      $height = abs($bbox[1]) + abs($bbox[5]);
      $width = abs($bbox[4]) + abs($bbox[0]);
      $x = $offsetx + abs($bbox[0]);
      $y = $offsety + abs($bbox[5]);
      if ($shadow) imagettftext($image, $size, 0, $x+1, $y+1, $black, $font, $text);
      imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
   }
   else {
      $hfont = ImageLoadFont($font);
      $width = ImageFontWidth($hfont) * strlen($text);
      $height = ImageFontHeight($hfont);
      if ($shadow) imagestring($image, $hfont, $offsetx+1, $offsety+1, $text, $black);
      imagestring($image, $hfont, $offsetx, $offsety, $text, $color);
   }
   return array( 'HEIGHT' => $height, 'WIDTH' => $width );
}


//draw line one
$ttftext = $chardata['FIRST_NAME']." ".$chardata['LAST_NAME'];
$ttfreturn = drawtext( $image, $ttftext, $sizeone, $fontone, $colorone, $line_start_x, $line_start_y, $shadowone);
$line_start_y += $ttfreturn['HEIGHT'];


//draw line two
$ttftext = $chardata['LEVEL']." ".$chardata['RACE']." ".$chardata['CLASS'];
$ttfreturn = drawtext( $image, $ttftext, $sizetwo, $fonttwo, $colortwo, $line_start_x, $line_start_y, $shadowtwo);
$line_start_y += $ttfreturn['HEIGHT'];

//draw line three
if ($chardata['GUILD_NAME']) {
   $ttftext = $chardata['GUILD_RANK']." of ".$chardata['GUILD_NAME'];
   $ttfreturn = drawtext( $image, $ttftext, $sizetwo, $fonttwo, $colortwo, $line_start_x, $line_start_y, $shadowtwo);
   $line_start_y += $ttfreturn['HEIGHT'];
}

// draw stats and boxes
if ($statbg) {
   $i = 0;
   $statcolor = imagecolorallocate($image, $statcolor['r'], $statcolor['g'], $statcolor['b']);
   foreach ($statdisplay as $key => $value) {
      if ($value > "")
         if (array_key_exists($value, $stats)) {
            $stattext = $value." ".$stats[$value];
            $tempimage = imagecreatefrompng($statbg);
            imagecopy($image, $tempimage, $stat_start_x + $stat_step_x * $i , $stat_start_y, 0, 0, $stat_width, $stat_height);
            imagestring($image, 2, $stat_text_x + $stat_start_x + $stat_step_x * $i , $stat_text_y + $stat_start_y, $stattext, $statcolor);
            imagedestroy($tempimage);
         }
         $i++;
   }
}

if ($border) {
   $tempimage = imagecreatefrompng($border);
   imagecopy($image, $tempimage, 0, 0, 0, 0, $signaturewidth, $signatureheight);
   imagedestroy($tempimage);
}


/*********************************************
               OUTPUT IMAGE
*********************************************/
ob_clean(); //make sure we haven't sent a text header
header("Content-Type: image/png");
imagepng($image);
ImageDestroy($image);
?>
