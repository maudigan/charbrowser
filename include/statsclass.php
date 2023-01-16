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
 *   February 25, 2014 - added heroic and aug types (Maudigan c/o Kinglykrab)
 *   February 25, 2014 - whitespace cleanup--uncommented (Maudigan)
 *   September 7, 2019 - added corruption, removed old comments (Kinglykrab)
 *   January 16, 2023 - Maudigan
 *      added _ prefix to private properties
 *      modified contructor to fetch global vars on its own
 *      renamed class with Charbrowser_ prefix
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}


class Charbrowser_Stats 
{

   private $_myhp;
   private $_mymana;
   private $_myendurance;
   private $_myAC;
   private $_mySTR;
   private $_mySTA;
   private $_myAGI;
   private $_myDEX;
   private $_myWIS;
   private $_myHSTR; 
   private $_myHSTA; 
   private $_myHAGI; 
   private $_myHDEX; 
   private $_myHWIS; 
   private $_myHINT; 
   private $_myHCHA; 
   private $_myINT;
   private $_myCHA;
   private $_myMR;
   private $_myPR;
   private $_myCR;
   private $_myFR;
   private $_myDR;
   private $_myCOR;
   private $_myHMR;
   private $_myHPR; 
   private $_myHCR; 
   private $_myHFR; 
   private $_myHDR; 
   private $_myHCOR;
   private $_myFT;
   private $_myDS;
   private $_myregen;
   private $_myattack;
   private $_myhaste;
   private $_myWT; 


   public function additem($row){
      $this->_myhp += $row['hp'];
      $this->_mymana += $row['mana'];
      $this->_myendurance += $row['endur'];
      $this->_myAC += $row['ac'];
      $this->_mySTR += $row['astr'];
      $this->_mySTA += $row['asta'];
      $this->_myAGI += $row['aagi'];
      $this->_myDEX += $row['adex'];
      $this->_myWIS += $row['awis'];
      $this->_myINT += $row['aint'];
      $this->_myCHA += $row['acha'];
      $this->_myHSTR += $row['heroic_str']; 
      $this->_myHSTA += $row['heroic_sta']; 
      $this->_myHAGI += $row['heroic_agi']; 
      $this->_myHDEX += $row['heroic_dex']; 
      $this->_myHWIS += $row['heroic_wis']; 
      $this->_myHINT += $row['heroic_int']; 
      $this->_myHCHA += $row['heroic_cha']; 
      $this->_myMR += $row['mr'];
      $this->_myPR += $row['pr'];
      $this->_myCR += $row['cr'];
      $this->_myFR += $row['fr'];
      $this->_myDR += $row['dr'];
      $this->_myCOR += $row['svcorruption'];
      $this->_myHMR += $row['heroic_mr']; 
      $this->_myHPR += $row['heroic_pr']; 
      $this->_myHCR += $row['heroic_cr']; 
      $this->_myHFR += $row['heroic_fr']; 
      $this->_myHDR += $row['heroic_dr']; 
      $this->_myHCOR += $row['heroic_svcorrup'];
      $this->_myFT += $row['manaregen'];
      $this->_myDS += $row['damageshield'];
      $this->_myregen += $row['regen'];
      $this->_myattack += $row['attack'];
      $this->_myhaste = max($row['haste'],$this->_myhaste); //only save highest haste
   }

   public function addwt($wt) {
      $this->_myWT +=  $wt; 
   }

   function hp() {
      return $this->_myhp;
   }

   function mana() {
      return $this->_mymana;
   }

   function endurance() {
      return $this->_myendurance;
   }

   function AC() {
      return $this->_myAC;
   }

   function STR() {
      return $this->_mySTR;
   }

   function STA() {
      return $this->_mySTA;
   }

   function AGI() {
      return $this->_myAGI;
   }

   function DEX() {
      return $this->_myDEX;
   }

   function WIS() {
      return $this->_myWIS;
   }

   function INT() {
      return $this->_myINT;
   }

   function CHA() {
      return $this->_myCHA;
   }
   
   function HSTR() { 
      return $this->_myHSTR; 
   } 

   function HSTA() { 
      return $this->_myHSTA; 
   } 

   function HAGI() { 
      return $this->_myHAGI; 
   } 

   function HDEX() { 
      return $this->_myHDEX; 
   } 

   function HWIS() { 
      return $this->_myHWIS; 
   } 

   function HINT() { 
      return $this->_myHINT; 
   } 

   function HCHA() { 
      return $this->_myHCHA; 
   }  

   function MR() {
      return $this->_myMR;
   }

   function PR() {
      return $this->_myPR;
   }

   function CR() {
      return $this->_myCR;
   }

   function FR() {
      return $this->_myFR;
   }

   function DR() {
      return $this->_myDR;
   }
	
   function COR() {
      return $this->_myCOR;
   }
   function HMR() { 
      return $this->_myHMR; 
   } 

   function HPR() { 
      return $this->_myHPR; 
   } 

   function HCR() { 
      return $this->_myHCR; 
   } 

   function HFR() { 
      return $this->_myHFR; 
   } 

   function HDR() { 
      return $this->_myHDR; 
   }  
	
   function HCOR() {
      return $this->_myHCOR;
   }

   function FT() {
      return $this->_myFT;
   }

   function DS() {
      return $this->_myDS;
   }

   function regen() {
      return $this->_myregen;
   }

   function attack() {
      return $this->_myattack;
   }

   function haste() {
      return $this->_myhaste; 
   }

   function WT() {
      return $this->_myWT;
   }
 
}

?>