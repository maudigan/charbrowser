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
 ***************************************************************************/
 
 
 
 
 if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}
function GetMaxAtk($iatk, $str, $offense) {
	$myatk = $iatk + (($str + $offense) * 9 / 10);
	return floor($myatk);
}

function acmod($agility,$level) {

	if($agility < 1 || $level < 1)
		return(0);
	
	if ($agility <=74){
		if ($agility == 1)
			return -24;
		else if ($agility <=3)
			return -23;
		else if ($agility == 4)
			return -22;
		else if ($agility <=6)
			return -21;
		else if ($agility <=8)
			return -20;
		else if ($agility == 9)
			return -19;
		else if ($agility <=11)
			return -18;
		else if ($agility == 12)
			return -17;
		else if ($agility <=14)
			return -16;
		else if ($agility <=16)
			return -15;
		else if ($agility == 17)
			return -14;
		else if ($agility <=19)
			return -13;
		else if ($agility == 20)
			return -12;
		else if ($agility <=22)
			return -11;
		else if ($agility <=24)
			return -10;
		else if ($agility == 25)
			return -9;
		else if ($agility <=27)
			return -8;
		else if ($agility == 28)
			return -7;
		else if ($agility <=30)
			return -6;
		else if ($agility <=32)
			return -5;
		else if ($agility == 33)
			return -4;
		else if ($agility <=35)
			return -3;
		else if ($agility == 36)
			return -2;
		else if ($agility <=38)
			return -1;
		else if ($agility <=65)
			return 0;
		else if ($agility <=70)
			return 1;
		else if ($agility <=74)
			return 5;
	}
	else if($agility <= 137) {
		if ($agility == 75){
			if ($level <= 6)
				return 9;
			else if ($level <= 19)
				return 23;
			else if ($level <= 39)
				return 33;
			else
				return 39;
		}
		else if ($agility >= 76 && $agility <= 79){
			if ($level <= 6)
				return 10;
			else if ($level <= 19)
				return 23;
			else if ($level <= 39)
				return 33;
			else
				return 40;
		}
		else if ($agility == 80){
			if ($level <= 6)
				return 11;
			else if ($level <= 19)
				return 24;
			else if ($level <= 39)
				return 34;
			else
				return 41;
		}
		else if ($agility >= 81 && $agility <= 85){
			if ($level <= 6)
				return 12;
			else if ($level <= 19)
				return 25;
			else if ($level <= 39)
				return 35;
			else
				return 42;
		}
		else if ($agility >= 86 && $agility <= 90){
			if ($level <= 6)
				return 12;
			else if ($level <= 19)
				return 26;
			else if ($level <= 39)
				return 36;
			else
				return 42;
		}
		else if ($agility >= 91 && $agility <= 95){
			if ($level <= 6)
				return 13;
			else if ($level <= 19)
				return 26;
			else if ($level <= 39)
				return 36;
			else
				return 43;
		}
		else if ($agility >= 96 && $agility <= 99){
			if ($level <= 6)
				return 14;
			else if ($level <= 19)
				return 27;
			else if ($level <= 39)
				return 37;
			else 
				return 44;
		}
		else if ($agility == 100 && $level >= 7){
			if ($level <= 19)
				return 28;
			else if ($level <= 39)
				return 38;
			else
				return 45;
		}
		else if ($level <= 6) {
			return 15;
		}
		//$level is >6
		else if ($agility >= 101 && $agility <= 105){
			if ($level <= 19)
				return 29;
			else if ($level <= 39)
				return 39;// not verified
			else
				return 45;
		}
		else if ($agility >= 106 && $agility <= 110){
			if ($level <= 19)
				return 29;
			else if ($level <= 39)
				return 39;// not verified
			else
				return 46;
		}
		else if ($agility >= 111 && $agility <= 115){
			if ($level <= 19)
				return 30;
			else if ($level <= 39)
				return 40;// not verified
			else
				return 47;
		}
		else if ($agility >= 116 && $agility <= 119){
			if ($level <= 19)
				return 31;
			else if ($level <= 39)
				return 41;
			else
				return 47;
		}
		else if ($level <= 19) {
				return 32;
		}
		//$level is > 19
		else if ($agility == 120){
			if ($level <= 39)
				return 42;
			else
				return 48;
		}
		else if ($agility <= 125){
			if ($level <= 39)
				return 42;
			else
				return 49;
		}
		else if ($agility <= 135){
			if ($level <= 39)
				return 42;
			else
				return 50;
		}
		else {
			if ($level <= 39)
				return 42;
			else
				return 51;
		}
	} else if($agility <= 300) {
		if($level <= 6) {
			if($agility <= 139)
				return(21);
			else if($agility == 140)
				return(22);
			else if($agility <= 145)
				return(23);
			else if($agility <= 150)
				return(23);
			else if($agility <= 155)
				return(24);
			else if($agility <= 159)
				return(25);
			else if($agility == 160)
				return(26);
			else if($agility <= 165)
				return(26);
			else if($agility <= 170)
				return(27);
			else if($agility <= 175)
				return(28);
			else if($agility <= 179)
				return(28);
			else if($agility == 180)
				return(29);
			else if($agility <= 185)
				return(30);
			else if($agility <= 190)
				return(31);
			else if($agility <= 195)
				return(31);
			else if($agility <= 199)
				return(32);
			else if($agility <= 219)
				return(33);
			else if($agility <= 239)
				return(34);
			else
				return(35);
		} else if($level <= 19) {
			if($agility <= 139)
				return(34);
			else if($agility == 140)
				return(35);
			else if($agility <= 145)
				return(36);
			else if($agility <= 150)
				return(37);
			else if($agility <= 155)
				return(37);
			else if($agility <= 159)
				return(38);
			else if($agility == 160)
				return(39);
			else if($agility <= 165)
				return(40);
			else if($agility <= 170)
				return(40);
			else if($agility <= 175)
				return(41);
			else if($agility <= 179)
				return(42);
			else if($agility == 180)
				return(43);
			else if($agility <= 185)
				return(43);
			else if($agility <= 190)
				return(44);
			else if($agility <= 195)
				return(45);
			else if($agility <= 199)
				return(45);
			else if($agility <= 219)
				return(46);
			else if($agility <= 239)
				return(47);
			else
				return(48);
		} else if($level <= 39) {
			if($agility <= 139)
				return(44);
			else if($agility == 140)
				return(45);
			else if($agility <= 145)
				return(46);
			else if($agility <= 150)
				return(47);
			else if($agility <= 155)
				return(47);
			else if($agility <= 159)
				return(48);
			else if($agility == 160)
				return(49);
			else if($agility <= 165)
				return(50);
			else if($agility <= 170)
				return(50);
			else if($agility <= 175)
				return(51);
			else if($agility <= 179)
				return(52);
			else if($agility == 180)
				return(53);
			else if($agility <= 185)
				return(53);
			else if($agility <= 190)
				return(54);
			else if($agility <= 195)
				return(55);
			else if($agility <= 199)
				return(55);
			else if($agility <= 219)
				return(56);
			else if($agility <= 239)
				return(57);
			else
				return(58);
		} else {	//lvl >= 40
			if($agility <= 139)
				return(51);
			else if($agility == 140)
				return(52);
			else if($agility <= 145)
				return(53);
			else if($agility <= 150)
				return(53);
			else if($agility <= 155)
				return(54);
			else if($agility <= 159)
				return(55);
			else if($agility == 160)
				return(56);
			else if($agility <= 165)
				return(56);
			else if($agility <= 170)
				return(57);
			else if($agility <= 175)
				return(58);
			else if($agility <= 179)
				return(58);
			else if($agility == 180)
				return(59);
			else if($agility <= 185)
				return(60);
			else if($agility <= 190)
				return(61);
			else if($agility <= 195)
				return(61);
			else if($agility <= 199)
				return(62);
			else if($agility <= 219)
				return(63);
			else if($agility <= 239)
				return(64);
			else
				return(65);
		}
	}
	else{
		//seems about 21 agil per extra AC pt over 300...
        return (65 + (($agility-300) / 21));
	}

	return 0;
};


function GetMaxAC($agility, $level, $defense, $class, $iac, $race) {

	$WARRIOR = 1;	
	$CLERIC = 2;
	$PALADIN = 3;
	$RANGER = 4;
	$SHADOWKNIGHT = 5;
	$DRUID = 6;
	$MONK = 7;
	$BARD = 8;
	$ROGUE = 9;
	$SHAMAN = 10;
	$NECROMANCER = 11;
	$WIZARD = 12;
	$MAGICIAN = 13;
	$ENCHANTER = 14;
	$BEASTLORD = 15;
	$BERSERKER = 16;
	
	// new formula
	$avoidance = 0;
	$avoidance = (acmod($agility,$level) + (($defense*16)/9));
	if ($avoidance < 0)
		$avoidance = 0;

	$mitigation = 0;
	if ($class == $WIZARD || $class == $MAGICIAN || $class == $NECROMANCER || $class == $ENCHANTER) {

		$mitigation = $defense/4 + ($iac+1);

		$mitigation -= 4;
	} else {

		$mitigation = $defense/3 + (($iac*4)/3);
		if($class == $MONK)
			$mitigation += $level * 13/10;	//the 13/10 might be wrong, but it is close...
	}
	$displayed = 0;
	$displayed += (($avoidance+$mitigation)*1000)/847;	//natural AC
	
	//Iksar AC, untested
	if ($race == 128) {
		$displayed += 12;
		$iksarlevel = $evel;
		$iksarlevel -= 10;
		if ($iksarlevel > 25)
			$iksarlevel = 25;
		if ($iksarlevel > 0)
			$displayed += $iksarlevel * 12 / 10;
	}
	
	//spell AC bonuses are added directly to natural total

	
	$AC = $displayed;
	return floor($AC);
}


$PRbyClass=array(0,0,0,0,4,0,0,0,8,0,0,0,0,0,0,0);
$MRbyClass=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$DRbyClass=array(0,0,8,0,4,0,0,0,0,0,0,0,0,0,4,0);
$FRbyClass=array(0,0,0,4,0,0,8,0,0,0,0,0,0,0,0,0);
$CRbyClass=array(0,0,0,4,0,0,0,0,0,0,0,0,0,0,4,0); 
function PRbyRace($race) {
       if($race == 8)    return 20; 
  else if($race == 330)  return 30; 
  else if($race == 74) return 30; 
  else if($race == 11) return 20; 
  else return 15;
}

function MRbyRace($race) {
       if($race == 8)    return 30;
  else if($race == 3)  return 30;
  else if($race == 330)  return 30;
  else if($race == 74) return 30;
  else return 25;
}

function DRbyRace($race) {
       if($race == 3)  return 10;
  else if($race == 11) return 20;
  else return 15;
}

function FRbyRace($race) {
       if($race == 128) return 30;
  else if($race == 9) return 5;
  else return 25;	
}

function CRbyRace($race) {
       if($race == 2) return 35;
  else if($race == 128)     return 15;
  else return 25;
}


/* replaced by new sod hp/mana/end calculations
//function copied/converted from EQEMU sourcecode may 2, 2009
function GetClassLevelFactor($mlevel,$class) {
	$multiplier = 0;

	$WARRIOR = 1;	
	$CLERIC = 2;
	$PALADIN = 3;
	$RANGER = 4;
	$SHADOWKNIGHT = 5;
	$DRUID = 6;
	$MONK = 7;
	$BARD = 8;
	$ROGUE = 9;
	$SHAMAN = 10;
	$NECROMANCER = 11;
	$WIZARD = 12;
	$MAGICIAN = 13;
	$ENCHANTER = 14;
	$BEASTLORD = 15;
	$BERSERKER = 16;

	switch($class)
	{
		case $WARRIOR:{
			if ($mlevel < 20)
				$multiplier = 220;
			else if ($mlevel < 30)
				$multiplier = 230;
			else if ($mlevel < 40)
				$multiplier = 250;
			else if ($mlevel < 53)
				$multiplier = 270;
			else if ($mlevel < 57)
				$multiplier = 280;
			else if ($mlevel < 60)
				$multiplier = 290;
			else if ($mlevel < 70)
				$multiplier = 300;
			else 
				$multiplier = 311;
			break;
		}
		case $DRUID:
		case $CLERIC:
		case $SHAMAN:{
			if ($mlevel < 70)
				$multiplier = 150;
			else
				$multiplier = 157;
			break;
		}
		case $BERSERKER:
		case $PALADIN:
		case $SHADOWKNIGHT:{
			if ($mlevel < 35)
				$multiplier = 210;
			else if ($mlevel < 45)
				$multiplier = 220;
			else if ($mlevel < 51)
				$multiplier = 230;
			else if ($mlevel < 56)
				$multiplier = 240;
			else if ($mlevel < 60)
				$multiplier = 250;
			else if ($mlevel < 68)
				$multiplier = 260;
			else
				$multiplier = 270;
			break;
		}
		case $MONK:
		case $BARD:
		case $ROGUE:
		case $BEASTLORD:{
			if ($mlevel < 51)
				$multiplier = 180;
			else if ($mlevel < 58)
				$multiplier = 190;
			else if ($mlevel < 70)
				$multiplier = 200;
			else
				$multiplier = 210;
			break;
		}
		case $RANGER:{
			if ($mlevel < 58)
				$multiplier = 200;
			else if ($mlevel < 70)
				$multiplier = 210;
			else
				$multiplier = 220;
			break;
		}
		case $MAGICIAN:
		case $WIZARD:
		case $NECROMANCER:
		case $ENCHANTER:{
			if ($mlevel < 70)
				$multiplier = 120;
			else
				$multiplier = 127;
			break;
		}
		default:{
			if ($mlevel < 35)
				$multiplier = 210;
			else if ($mlevel < 45)
				$multiplier = 220;
			else if ($mlevel < 51)
				$multiplier = 230;
			else if ($mlevel < 56)
				$multiplier = 240;
			else if ($mlevel < 60)
				$multiplier = 250;
			else
				$multiplier = 260;
			break;
		}
	}
	return $multiplier;
}

//function copied/converted from EQEMU sourcecode may 2, 2009
function GetMaxHP($mlevel,$class,$sta,$ihp)
{
	$lm = GetClassLevelFactor($mlevel,$class);
	$Post255 = 0;
	if(($sta-255)/2 > 0)
		$Post255 = ($sta-255)/2;
	else
		$Post255 = 0;
		
	$base_hp = (5)+($mlevel*$lm/10) + ((($sta-$Post255)*$mlevel*$lm/3000)) + (($Post255*$mlevel)*$lm/6000);

	$base_hp += $ihp;
	return floor($base_hp);
}*/

//function copied/converted from EQEMU sourcecode oct 26, 2010 
function GetMaxHP($mlevel,$class,$sta,$ihp) 
{ 
        $ClassHPFactor = array( 1 => 300,  2 => 264,  3 => 288,  4 => 276,
                                5 => 288,  6 => 240,  7 => 255,  8 => 264,
                                9 => 255,  10 => 255, 11 => 240, 12 => 240, 
                                13 => 240, 14 => 240, 15 => 255, 16 => 255
                         );

        $Post255 = 0; 

        if((($sta- 255) / 2) > 0) 
                $Post255 = (($sta- 255) / 2); 
        else 
                $Post255 = 0; 

        $hp_factor = max(240,$ClassHPFactor[$class]); 
                
        if ($mlevel < 41) { 
                $base_hp = (5 + ($mlevel * $hp_factor / 12) + 
                        (($sta- $Post255) * $level * $hp_factor / 3600)); 
        } 
        else if ($mlevel < 81) { 
                $base_hp = (5 + (40 * hp_factor / 12) + (($mlevel - 40) * $hp_factor / 6) + 
                        (($sta- $Post255) * $hp_factor / 90) + 
                        (($sta- $Post255) * ($mlevel - 40) * $hp_factor / 1800)); 
        } 
        else { 
                $base_hp = (5 + (80 * $hp_factor / 8) + (($mlevel - 80) * $hp_factor / 10) + 
                        (($sta- $Post255) * $hp_factor / 90) + 
                        (($sta- $Post255) * $hp_factor / 45)); 
        } 

        $base_hp += $ihp; 
        return floor($base_hp); 
} 

//function copied/converted from EQEMU sourcecode may 2, 2009
function GetCasterClass($class){
	$WARRIOR = 1;	
	$CLERIC = 2;
	$PALADIN = 3;
	$RANGER = 4;
	$SHADOWKNIGHT = 5;
	$DRUID = 6;
	$MONK = 7;
	$BARD = 8;
	$ROGUE = 9;
	$SHAMAN = 10;
	$NECROMANCER = 11;
	$WIZARD = 12;
	$MAGICIAN = 13;
	$ENCHANTER = 14;
	$BEASTLORD = 15;
	$BERSERKER = 16;

	switch($class)
	{
	case $CLERIC:
	case $PALADIN:
	case $RANGER:
	case $DRUID:
	case $SHAMAN:
	case $BEASTLORD:
		return 'W';
		break;

	case $SHADOWKNIGHT:
	case $BARD:
	case $NECROMANCER:
	case $WIZARD:
	case $MAGICIAN:
	case $ENCHANTER:
		return 'I';
		break;

	default:
		return 'N';
		break;
	}
}

/* replaced by new sod hp/mana/end calculations
//function copied/converted from EQEMU sourcecode may 2, 2009, was  named CalcMaxMana();
function GetMaxMana($level,$class,$int,$wis,$imana)
{
	$WisInt = 0;
	$MindLesserFactor = 0;
	$MindFactor = 0;
	switch(GetCasterClass($class))
	{
		case 'I': 
			$WisInt = $int;

			if((( $WisInt - 199 ) / 2) > 0)
				$MindLesserFactor = ( $WisInt - 199 ) / 2;
			else
				$MindLesserFactor = 0;

			$MindFactor = $WisInt - $MindLesserFactor;
			if($WisInt > 100)
				$max_mana = (((5 * ($MindFactor + 20)) / 2) * 3 * $level / 40);
			else
				$max_mana = (((5 * ($MindFactor + 200)) / 2) * 3 * $level / 100);	
			$max_mana += $imana;
			break;

		case 'W':
			$WisInt = $wis;

			if((( $WisInt - 199 ) / 2) > 0)
				$MindLesserFactor = ( $WisInt - 199 ) / 2;
			else
				$MindLesserFactor = 0;

			$MindFactor = $WisInt - $MindLesserFactor;
			if($WisInt > 100)
				$max_mana = (((5 * ($MindFactor + 20)) / 2) * 3 * $level / 40);
			else
				$max_mana = (((5 * ($MindFactor + 200)) / 2) * 3 * $level / 100);	
			
			$max_mana += $imana;
			break;
				
		case 'N': {
			$max_mana = 0;
			break;
		}

	}

	return floor($max_mana);
}*/

//function copied/converted from EQEMU sourcecode oct 26, 2010 
function GetMaxMana($level,$class,$int,$wis,$imana) 
{ 
        $WisInt = 0; 
        $MindLesserFactor = 0; 
        $MindFactor = 0; 
        $max_m = 0; 
        $wisint_mana = 0; 
        $base_mana = 0; 
        $ConvertedWisInt = 0; 
        switch(GetCasterClass($class)) 
        { 
                case 'I': 
                        $WisInt = $int; 
                        if ($WisInt > 100) { 
                                $ConvertedWisInt = ((($WisInt - 100) * 5 / 2) + 100); 
                                if ($WisInt > 201) { 
                                        $ConvertedWisInt -= (($WisInt - 201) * 5 / 4); 
                                } 
                        } 
                        else { 
                                $ConvertedWisInt = $WisInt; 
                        } 
                        if ($level < 41) { 
                                $wisint_mana = ($level * 75 * $ConvertedWisInt / 1000); 
                                $base_mana = ($level * 15); 
                        } 
                        else if ($level < 81) { 
                                $wisint_mana = ((3 * $ConvertedWisInt) + (($level - 40) * 15 * $ConvertedWisInt / 100)); 
                                $base_mana = (600 + (($level - 40) * 30)); 
                        } 
                        else { 
                                $wisint_mana = (9 * $ConvertedWisInt); 
                                $base_mana = (1800 + (($level - 80) * 18)); 
                        } 
                        $max_mana = $base_mana + $wisint_mana; 
                        $max_mana += $imana; 
                        break; 

                case 'W': 
                        $WisInt = $wis; 
                        if ($WisInt > 100) { 
                                $ConvertedWisInt = ((($WisInt - 100) * 5 / 2) + 100); 
                                if ($WisInt > 201) { 
                                        $ConvertedWisInt -= (($WisInt - 201) * 5 / 4); 
                                } 
                        } 
                        else { 
                                $ConvertedWisInt = $WisInt; 
                        } 
                        if ($level < 41) { 
                                $wisint_mana = ($level * 75 * $ConvertedWisInt / 1000); 
                                $base_mana = ($level * 15); 
                        } 
                        else if ($level < 81) { 
                                $wisint_mana = ((3 * $ConvertedWisInt) + (($level - 40) * 15 * $ConvertedWisInt / 100)); 
                                $base_mana = (600 + (($level - 40) * 30)); 
                        } 
                        else { 
                                $wisint_mana = (9 * $ConvertedWisInt); 
                                $base_mana = (1800 + (($level - 80) * 18)); 
                        } 
                        $max_mana = $base_mana + $wisint_mana; 
                        $max_mana += $imana; 
                        break; 
                                
                case 'N': { 
                        $max_mana = 0; 
                        break; 
                } 

        } 

        return floor($max_mana); 
} 

/* replaced by new sod hp/mana/end calculations
function GetMaxEndurance($STR,$STA,$DEX,$AGI,$level,$iendurance)
{
	$Stats = $STR + $STA + $DEX + $AGI;

	$LevelBase = $level * 15;

	$at_most_800 = $Stats;
	if($at_most_800 > 800)
		$at_most_800 = 800;
	
	$Bonus400to800 = 0;
	$HalfBonus400to800 = 0;
	$Bonus800plus = 0;
	$HalfBonus800plus = 0;
	
	$BonusUpto800 = floor( $at_most_800 / 4 ) ;
	if($Stats > 400) {
		$Bonus400to800 = floor( ($at_most_800 - 400) / 4 );
		$HalfBonus400to800 = floor( max( ( $at_most_800 - 400 ), 0 ) / 8 );
		
		if($Stats > 800) {
			$Bonus800plus = floor( ($Stats - 800) / 8 ) * 2;
			$HalfBonus800plus = floor( ($Stats - 800) / 16 );
		}
	}
	$bonus_sum = $BonusUpto800 + $Bonus400to800 + $HalfBonus400to800 + $Bonus800plus + $HalfBonus800plus;
	
	$max_end = $LevelBase;

	//take all of the sums from above, then multiply by level*0.075
	$max_end += ( $bonus_sum * 3 * $level ) / 40;
	
	$max_end += $iendurance;
	return floor($max_end);
}*/

//function copied/converted from EQEMU sourcecode oct 26, 2010 
function GetMaxEndurance($STR,$STA,$DEX,$AGI,$level,$iendurance) 
{ 
        $Stats = ($STR + $STA + $DEX + $AGI)/4; 
        $base_endurance = 0; 
        $ConvertedStats = 0; 
        $sta_end = 0; 

        if (($Stats) > 100) { 
                $ConvertedStats = ((($Stats - 100) * 5 / 2) + 100); 
                if ($Stats > 201) { 
                        $ConvertedStats -= (($Stats - 201) * 5 / 4); 
                } 
        } 
        else { 
                $ConvertedStats = $Stats; 
        } 

        if ($level < 41) { 
                $sta_end = ($level * 75 * $ConvertedStats / 1000); 
                $base_endurance = ($level * 15); 
        } 
        else if ($level < 81) { 
                $sta_end = ((3 * $ConvertedStats) + (($level - 40) * 15 * $ConvertedStats / 100)); 
                $base_endurance = (600 + (($level - 40) * 30)); 
        } 
        else { 
                $sta_end = (9 * $ConvertedStats); 
                $base_endurance = (1800 + (($level - 80) * 18)); 
        } 
        $max_end = ($base_endurance + $sta_end); 

        
        $max_end += $iendurance; 
        return floor($max_end); 
} 

?>