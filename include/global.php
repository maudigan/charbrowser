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
 *   February 5, 2014 - Updated for Powersource (Maudigan c/o Natedog)
 *   February 25, 2014 - added heroic/aug (Maudigan c/o Kinglykrab)
 ***************************************************************************/
 																																	
 
 
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}


//few constants for allitems.type
define("EQUIPMENT", 1);
define("INVENTORY", 2);
define("BANK", 3);

//wether or not the server has GD and freetype installed
define("SERVER_HAS_GD", function_exists("imagecreatetruecolor"));
define("SERVER_HAS_FREETYPE", function_exists("imagettfbbox"));

include_once ( "template.php" );
//templates
$template = new Template("./templates");

// elements
$dbelements=array("Unknown","Magic","Fire","Cold","Poison","Disease");

$guildranks=array("Member", "Officer", "Leader");

// ItemClasses 2^(class-1)
$dbiclasses=array();
$dbiclasses[65535]="ALL";
$dbiclasses[32768]="BER";
$dbiclasses[16384]="BST";
$dbiclasses[8192]="ENC";
$dbiclasses[4096]="MAG";
$dbiclasses[2048]="WIZ";
$dbiclasses[1024]="NEC";
$dbiclasses[512]="SHM";
$dbiclasses[256]="ROG";
$dbiclasses[128]="BRD";
$dbiclasses[64]="MNK";
$dbiclasses[32]="DRU";
$dbiclasses[16]="SHD";
$dbiclasses[8]="RNG";
$dbiclasses[4]="PAL";
$dbiclasses[2]="CLR";
$dbiclasses[1]="WAR";




// races
$dbraces=array();
$dbraces[65535]="ALL";
$dbraces[32768]="DRK";
$dbraces[16384]="FRG";
$dbraces[8192]="VAH";
$dbraces[4096]="IKS";
$dbraces[2048]="GNM";
$dbraces[1024]="HFL";
$dbraces[512]="OGR";
$dbraces[256]="TRL";
$dbraces[128]="DWF";
$dbraces[64]="HEF";
$dbraces[32]="DEF";
$dbraces[16]="HIE";
$dbraces[8]="ELF";
$dbraces[4]="ERU";
$dbraces[2]="BAR";
$dbraces[1]="HUM";

// skills
$dbskills=array();
$dbskills[0]='1H_BLUNT';
$dbskills[1]='1H_SLASHING';
$dbskills[2]='2H_BLUNT';
$dbskills[3]='2H_SLASHING';
$dbskills[4]='ABJURATION';
$dbskills[5]='ALTERATION';
$dbskills[6]='APPLY_POISON';
$dbskills[7]='ARCHERY';
$dbskills[8]='BACKSTAB';
$dbskills[9]='BIND_WOUND';
$dbskills[10]='BASH';
$dbskills[11]='BLOCKSKILL';
$dbskills[12]='BRASS_INSTRUMENTS';
$dbskills[13]='CHANNELING';
$dbskills[14]='CONJURATION';
$dbskills[15]='DEFENSE';
$dbskills[16]='DISARM';
$dbskills[17]='DISARM_TRAPS';
$dbskills[18]='DIVINATION';
$dbskills[19]='DODGE';
$dbskills[20]='DOUBLE_ATTACK';
$dbskills[21]='DRAGON_PUNCH';
$dbskills[22]='DUEL_WIELD';
$dbskills[23]='EAGLE_STRIKE';
$dbskills[24]='EVOCATION';
$dbskills[25]='FEIGN_DEATH';
$dbskills[26]='FLYING_KICK';
$dbskills[27]='FORAGE';
$dbskills[28]='HAND_TO_HAND';
$dbskills[29]='HIDE';
$dbskills[30]='KICK';
$dbskills[31]='MEDITATE';
$dbskills[32]='MEND';
$dbskills[33]='OFFENSE';
$dbskills[34]='PARRY';
$dbskills[35]='PICK_LOCK';
$dbskills[36]='PIERCING';
$dbskills[37]='RIPOSTE';
$dbskills[38]='ROUND_KICK';
$dbskills[39]='SAFE_FALL';
$dbskills[40]='SENSE_HEADING';
$dbskills[41]='SINGING';
$dbskills[42]='SNEAK';
$dbskills[43]='SPECIALIZE_ABJURE';
$dbskills[44]='SPECIALIZE_ALTERATION';
$dbskills[45]='SPECIALIZE_CONJURATION';
$dbskills[46]='SPECIALIZE_DIVINATION';
$dbskills[47]='SPECIALIZE_EVOCATION';
$dbskills[48]='PICK_POCKETS';
$dbskills[49]='STRINGED_INSTRUMENTS';
$dbskills[50]='SWIMMING';
$dbskills[51]='THROWING';
$dbskills[52]='CLICKY';
$dbskills[53]='TRACKING';
$dbskills[54]='WIND_INSTRUMENTS';
$dbskills[55]='FISHING';
$dbskills[56]='POISON_MAKING';
$dbskills[57]='TINKERING';
$dbskills[58]='RESEARCH';
$dbskills[59]='ALCHEMY';
$dbskills[60]='BAKING';
$dbskills[61]='TAILORING';
$dbskills[62]='SENSE_TRAPS';
$dbskills[63]='BLACKSMITHING';
$dbskills[64]='FLETCHING';
$dbskills[65]='BREWING';
$dbskills[66]='ALCOHOL_TOLERANCE';
$dbskills[67]='BEGGING';
$dbskills[68]='JEWELRY_MAKING';
$dbskills[69]='POTTERY';
$dbskills[70]='PERCUSSION_INSTRUMENTS';
$dbskills[71]='INTIMIDATION';
$dbskills[72]='BERSERKING';
$dbskills[73]='TAUNT';

// damage bonuses 2Hands at 65
//http://lucy.allakhazam.com/dmgbonus.html
$dam2h=array( 0,14,14,14,14,14,14,14,14,14, // 0->9
             14,14,14,14,14,14,14,14,14,14, // 10->19
             14,14,14,14,14,14,14,14,35,35, // 20->29
             36,36,37,37,38,38,39,39,40,40, // 30->39
             42,42,42,45,45,47,48,49,49,51, // 40->49
             51,52,53,54,54,56,56,57,58,59, // 50->59
             59, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 60->69
             68, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 70->79
              0, 0, 0, 0, 0,80, 0, 0, 0, 0, // 80->89
              0, 0, 0, 0, 0,88, 0, 0, 0, 0, // 90->99
              0, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 100->109
              0, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 110->119
              0, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 120->129
              0, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 130->139
              0, 0, 0, 0, 0, 0, 0, 0, 0, 0, // 140->149
            132); // 150
            
// item types
$dbitypes=array();
$dbitypes[3]="1H Blunt";
$dbitypes[0]="1H Slashing";
$dbitypes[4]="2H Blunt";
$dbitypes[35]="2H Piercing";
$dbitypes[1]="2H Slashing";
$dbitypes[38]="Alcohol";
$dbitypes[5]="Archery";
$dbitypes[10]="Armor";
$dbitypes[27]="Arrow";
$dbitypes[54]="Augmentation";
$dbitypes[18]="Bandages";
$dbitypes[25]="Brass Instrument";
$dbitypes[52]="Charm";
$dbitypes[34]="Coin";
$dbitypes[17]="Combinable";
$dbitypes[40]="Compass";
$dbitypes[15]="Drink";
$dbitypes[37]="Fishing Bait";
$dbitypes[36]="Fishing Pole";
$dbitypes[14]="Food";
$dbitypes[11]="Gems";
$dbitypes[29]="Jewelry";
$dbitypes[33]="Key";
$dbitypes[39]="Key (bis)";
$dbitypes[16]="Light";
$dbitypes[12]="Lockpicks";
$dbitypes[45]="Martial";
$dbitypes[32]="Note";
$dbitypes[26]="Percussion Instrument";
$dbitypes[2]="Piercing";
$dbitypes[42]="Poison";
$dbitypes[21]="Potion";
$dbitypes[20]="Scroll";
$dbitypes[8]="Shield";
$dbitypes[30]="Skull";
$dbitypes[24]="Stringed Instrument";
$dbitypes[19]="Throwing";
$dbitypes[7]="Throwing range items";
$dbitypes[31]="Tome";
$dbitypes[23]="Wind Instrument";

$tbraces="races";
$tbspells="spells_new";

// Body types (bodytypes.h)
$dbbodytypes=array(
  "Unknown", // 0
  "Humanoid", // 1 
  "Lycanthrope", // 2
  "Undead",  // 3
  "Giant", // 4
  "Construct", // 5
  "Extra planar", //6
  "Magical", // 7
  "Summoned undead", // 8
  "Unknown", //9
  "Unknown", //10
  "No target", //11
  "Vampire", //12
  "Atenha Ra", // 13
  "Greater Akheva", // 14
  "Khati Sha", // 15
  "Unknown", //16
  "Unknown", //17
  "Unknown", //18
  "Zek", // 19
  "Unkownn", // 20
  "Animal", // 21
  "Insect", // 22 
  "Monster", // 23
  "Summoned", // 24
  "Plant", // 25
  "Dragon", // 26
  "Summoned 2", // 27
  "Summoned 3", // 28 
  "Unknown", //29
  "Velious Dragon", //30
  "Unknown", //31
  "Dragon 3", //32
  "Boxes", //33
  "Discord Mob"); //34
$dbbodytypes[60]="No Target 2"; 
$dbbodytypes[63]="Swarm pet"; 
$dbbodytypes[67]="Special"; 

$dbbardskills[23]="Wind Instruments";
$dbbardskills[24]="Stringed Instruments";
$dbbardskills[25]="Brass Instruments";
$dbbardskills[26]="Percussion Instruments";
$dbbardskills[51]="All instruments";

//classes
$dbclassnames=array("UNKNOWN","Warrior","Cleric","Paladin","Ranger","Shadowknight","Druid",
                       "Monk","Bard","Rogue","Shaman","Necromancer","Wizard","Magician",
                       "Enchanter","Beastlord","Berserker");
                       //classes






$dbracenames=array();
$dbracenames[1]="Human";
$dbracenames[2]="Barbarian";
$dbracenames[3]="Erudite";
$dbracenames[4]="Wood Elf";
$dbracenames[5]="High Elf";
$dbracenames[6]="Dark Elf";
$dbracenames[7]="Half Elf";
$dbracenames[8]="Dwarf";
$dbracenames[9]="Troll";
$dbracenames[10]="Ogre";
$dbracenames[11]="Halfling";
$dbracenames[12]="Gnome";
$dbracenames[14]="Werewolf";
$dbracenames[60]="Skeleton";
$dbracenames[75]="Elemental";
$dbracenames[108]="Eye of Zomm";
$dbracenames[120]="Wolf Elemental";
$dbracenames[128]="Iksar";
$dbracenames[130]="Vahshir";
$dbracenames[161]="Iksar Skeleton";
$dbracenames[330]="Froglok";
$dbracenames[74]="Froglok";
$dbracenames[65533]="EMU Race NPC";
$dbracenames[65534]="EMU Race Pet";
$dbracenames[65535]="EMU Race Unknown";

                                           
                       
// deities
$dbdeities=array();
$dbdeities[0]="Unknown";
$dbdeities[201]="Bertoxxulous";
$dbdeities[202]="Brell-Serilis";
$dbdeities[203]="Cazic-Thule";
$dbdeities[204]="Erollisi-Marr";
$dbdeities[205]="Bristlebane";
$dbdeities[206]="Innoruuk";
$dbdeities[207]="Karana";
$dbdeities[208]="Mithaniel-Marr";
$dbdeities[209]="Prexus";
$dbdeities[210]="Quellious";
$dbdeities[211]="Rallos-Zek";
$dbdeities[213]="Solusek-Ro";
$dbdeities[212]="Rodcet-Nife";
$dbdeities[215]="Tunare";
$dbdeities[214]="The-Tribunal";
$dbdeities[216]="Veeshan";
$dbdeities[140]="Agnostic";//EQEditor shows this as 140...
$dbdeities[396]="Agnostic";

// deities (items)
$dbideities=array();
$dbideities[65536]="Veeshan";
$dbideities[32768]="Tunare";
$dbideities[16384]="The Tribunal";
$dbideities[8192]="Solusek Ro";
$dbideities[4096]="Rodcet Nife";
$dbideities[2048]="Rallos Zek";
$dbideities[1024]="Quellious";
$dbideities[512]="Prexus";
$dbideities[256]="Mithaniel Marr";
$dbideities[128]="Karana";
$dbideities[64]="Innoruuk";
$dbideities[32]="Bristlebane";
$dbideities[16]="Erollisi Marr";
$dbideities[8]="Cazic Thule";
$dbideities[4]="Brell Serilis";
$dbideities[2]="Bertoxxulous";
                       

$dbslots=array(); $dbslotsid=array();
$dbslots[4194304]="Powersource";   // added line 2/5/2014 
$dbslots[2097152]="Ammo"; 
$dbslots[1048576]="Waist"; 
$dbslots[524288]="Feet"; 
$dbslots[262144]="Legs"; 
$dbslots[131072]="Chest"; 
$dbslots[98304]="Fingers";
$dbslots[65536]="Finger"; 
$dbslots[32768]="Finger"; 
$dbslots[16384]="Secondary"; 
$dbslots[8192]="Primary"; 
$dbslots[4096]="Hands";
$dbslots[2048]="Range"; 
$dbslots[1536]="Wrists"; 
$dbslots[1024]="Wrist"; 
$dbslots[512]="Wrist"; 
$dbslots[256]="Back";
$dbslots[128]="Arms";
$dbslots[64]="Shoulders"; 
$dbslots[32]="Neck";
$dbslots[18]="Ears"; 
$dbslots[16]="Ear"; 
$dbslots[8]="Face";
$dbslots[4]="Head";
$dbslots[2]="Ear"; 
$dbslots[1]="Charm"; 

//added all $augtypes 2/25/2014
$augtypes = array(); 
$augtypes[2147483647] = "All Types"; 
$augtypes[536870912] = "Type 30"; 
$augtypes[268435456] = "Type 29"; 
$augtypes[134217728] = "Type 28"; 
$augtypes[67108864] = "Type 27"; 
$augtypes[33554432] = "Type 26"; 
$augtypes[16777216] = "Type 25"; 
$augtypes[8388608] = "Type 24"; 
$augtypes[4194304] = "Type 23"; 
$augtypes[2097152] = "Type 22"; 
$augtypes[1048576] = "Type 21"; 
$augtypes[524288] = "Type 20"; 
$augtypes[262144] = "Type 19"; 
$augtypes[131072] = "Type 18"; 
$augtypes[65536] = "Type 17"; 
$augtypes[32768] = "Type 16"; 
$augtypes[16384] = "Type 15"; 
$augtypes[8192] = "Type 14"; 
$augtypes[4096] = "Type 13"; 
$augtypes[2048] = "Type 12"; 
$augtypes[1024] = "Type 11"; 
$augtypes[512] = "Type 10"; 
$augtypes[256] = "Type 9"; 
$augtypes[128] = "Type 8"; 
$augtypes[64] = "Type 7"; 
$augtypes[32] = "Type 6"; 
$augtypes[16] = "Type 5"; 
$augtypes[8] = "Type 4"; 
$augtypes[4] = "Type 3"; 
$augtypes[2] = "Type 2"; 
$augtypes[1] = "Type 1"; 
?>