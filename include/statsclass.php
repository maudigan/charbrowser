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
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}


class stats {
	var $myhp;
	var $mymana;
	var $myendurance;
	var $myAC;
	var $mySTR;
	var $mySTA;
	var $myAGI;
	var $myDEX;
	var $myWIS;
	var $myHSTR;
	var $myHSTA; 
	var $myHAGI; 
	var $myHDEX; 
	var $myHWIS; 
	var $myHINT; 
	var $myHCHA; 
	var $myINT;
	var $myCHA;
	var $myMR;
	var $myPR;
	var $myCR;
	var $myFR;
	var $myDR;
	var $myCOR;
	var $myHMR;
	var $myHPR; 
	var $myHCR; 
	var $myHFR; 
	var $myHDR;
	var $myHCOR;
	var $myFT;
	var $myDS;
	var $myregen;
	var $myattack;
	var $myhaste;
	var $myWT; 


	public function additem($row){
		$this->myhp += $row['hp'];
		$this->mymana += $row['mana'];
		$this->myendurance += $row['endur'];
		$this->myAC += $row['ac'];
		$this->mySTR += $row['astr'];
		$this->mySTA += $row['asta'];
		$this->myAGI += $row['aagi'];
		$this->myDEX += $row['adex'];
		$this->myWIS += $row['awis'];
		$this->myINT += $row['aint'];
		$this->myCHA += $row['acha'];
		$this->myHSTR += $row['heroic_str'];
		$this->myHSTA += $row['heroic_sta']; 
		$this->myHAGI += $row['heroic_agi']; 
		$this->myHDEX += $row['heroic_dex']; 
		$this->myHWIS += $row['heroic_wis']; 
		$this->myHINT += $row['heroic_int']; 
		$this->myHCHA += $row['heroic_cha']; 
		$this->myMR += $row['mr'];
		$this->myPR += $row['pr'];
		$this->myCR += $row['cr'];
		$this->myFR += $row['fr'];
		$this->myDR += $row['dr'];
		$this->myCOR += $row['svcorruption'];
		$this->myHMR += $row['heroic_mr'];
		$this->myHPR += $row['heroic_pr']; 
		$this->myHCR += $row['heroic_cr']; 
		$this->myHFR += $row['heroic_fr']; 
		$this->myHDR += $row['heroic_dr'];
		$this->myHCOR += $row['heroic_svcorrup'];
		$this->myFT += $row['manaregen'];
		$this->myDS += $row['damageshield'];
		$this->myregen += $row['regen'];
		$this->myattack += $row['attack'];
		$this->myhaste = max($row['haste'],$this->myhaste); //only save highest haste
	}

	public function addwt($wt) {
		$this->myWT +=  $wt; 
	}

	function hp() {
		return $this->myhp;
	}

	function mana() {
		return $this->mymana;
	}

	function endurance() {
		return $this->myendurance;
	}
	
	function AC() {
		return $this->myAC;
	}

	function STR() {
		return $this->mySTR;
	}

	function STA() {
		return $this->mySTA;
	}

	function AGI() {
		return $this->myAGI;
	}

	function DEX() {
		return $this->myDEX;
	}

	function WIS() {
		return $this->myWIS;
	}

	function INT() {
		return $this->myINT;
	}

	function CHA() {
		return $this->myCHA;
	}
   
	function HSTR() { 
		return $this->myHSTR; 
	} 
	
	function HSTA() { 
		return $this->myHSTA; 
	} 
	
	function HAGI() { 
		return $this->myHAGI; 
	} 
	
	function HDEX() { 
		return $this->myHDEX; 
	} 
	
	function HWIS() { 
		return $this->myHWIS; 
	} 
	
	function HINT() { 
		return $this->myHINT; 
	} 
	
	function HCHA() { 
		return $this->myHCHA; 
	}  
	
	function MR() {
		return $this->myMR;
	}
	
	function PR() {
		return $this->myPR;
	}
	
	function CR() {
		return $this->myCR;
	}
	
	function FR() {
		return $this->myFR;
	}
	
	function DR() {
		return $this->myDR;
	}
	
	function COR() {
		return $this->myCOR;
	}
	
	function HMR() { 
		return $this->myHMR; 
	} 
	
	function HPR() { 
		return $this->myHPR; 
	} 
	
	function HCR() { 
		return $this->myHCR; 
	} 
	
	function HFR() { 
		return $this->myHFR; 
	} 
	
	function HDR() { 
		return $this->myHDR; 
	}
	
	function HCOR() {
		return $this->myHCOR;
	}
	
	function FT() {
		return $this->myFT;
	}
	
	function DS() {
		return $this->myDS;
	}
	
	function regen() {
		return $this->myregen;
	}
	
	function attack() {
		return $this->myattack;
	}
	
	function haste() {
		return $this->myhaste; 
	}
	
	function WT() {
		return $this->myWT;
	}
 
}

?>