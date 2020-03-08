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
 *   September 23, 2018 - make the API able to be disabled (Maudigan)
 *   September 7, 2019 - Added Corruption
 *                       Cleaned up whitespace
 *                       Clarified comments
 *                       Added missing dbskills and cleaned up case
 *                       Put arrays in order
 *                       Added Drakkin
 *                       Fixed aug type description (Kinglykrab)
 *   March 8, 2020 - implement shared bank (Maudigan)
 *
 ***************************************************************************/

 
 
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}


//few constants for allitems.type
define("EQUIPMENT", 1);
define("INVENTORY", 2);
define("BANK", 3);
define("SHAREDBANK", 4);

//wether or not the server has GD and freetype installed
define("SERVER_HAS_GD", function_exists("imagecreatetruecolor"));
define("SERVER_HAS_FREETYPE", function_exists("imagettfbbox"));

include_once ( __DIR__ . "/template.php" );
//templates
$cb_template = new CB_Template(__DIR__ . "/../templates");

//the template class will allow data to be output as json
//if this is an API request and API is not enabled kill the api
//request and then show an error saying api is unavailable
if (isset($_GET['api']) && !$api_enabled) 
{
   unset($_GET['api']);
   cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NOAPI']); 
}

// Element Types
$dbelements = array("Unknown", "Magic", "Fire", "Cold", "Poison", "Disease", "Corruption");

// Guild Ranks
$guildranks = array("Member", "Officer", "Leader");

// Class BItmasks
$dbiclasses = array();
$dbiclasses[65535] = "ALL";
$dbiclasses[32768] = "BER";
$dbiclasses[16384] = "BST";
$dbiclasses[8192] = "ENC";
$dbiclasses[4096] = "MAG";
$dbiclasses[2048] = "WIZ";
$dbiclasses[1024] = "NEC";
$dbiclasses[512] = "SHM";
$dbiclasses[256] = "ROG";
$dbiclasses[128] = "BRD";
$dbiclasses[64] = "MNK";
$dbiclasses[32] = "DRU";
$dbiclasses[16] = "SHD";
$dbiclasses[8] = "RNG";
$dbiclasses[4] = "PAL";
$dbiclasses[2] = "CLR";
$dbiclasses[1] = "WAR";

// Race Bitmasks
$dbraces = array();
$dbraces[65535] = "ALL";
$dbraces[32768] = "DRK";
$dbraces[16384] = "FRG";
$dbraces[8192] = "VAH";
$dbraces[4096] = "IKS";
$dbraces[2048] = "GNM";
$dbraces[1024] = "HFL";
$dbraces[512] = "OGR";
$dbraces[256] = "TRL";
$dbraces[128] = "DWF";
$dbraces[64] = "HEF";
$dbraces[32] = "DEF";
$dbraces[16] = "HIE";
$dbraces[8] = "ELF";
$dbraces[4] = "ERU";
$dbraces[2] = "BAR";
$dbraces[1] = "HUM";

// Skills
$dbskills = array();
$dbskills[0] = '1H Blunt';
$dbskills[1] = '1H Slashing';
$dbskills[2] = '2H Blunt';
$dbskills[3] = '2H Slashing';
$dbskills[4] = 'Abjuration';
$dbskills[5] = 'Alteration';
$dbskills[6] = 'Apply Poison';
$dbskills[7] = 'Archery';
$dbskills[8] = 'Backstab';
$dbskills[9] = 'Bind Wound';
$dbskills[10] = 'Bash';
$dbskills[11] = 'Block';
$dbskills[12] = 'Brass Instruments';
$dbskills[13] = 'Channeling';
$dbskills[14] = 'Conjuration';
$dbskills[15] = 'Defense';
$dbskills[16] = 'Disarm';
$dbskills[17] = 'Disarm Traps';
$dbskills[18] = 'Divination';
$dbskills[19] = 'Dodge';
$dbskills[20] = 'Double Attack';
$dbskills[21] = 'Dragon Punch';
$dbskills[22] = 'Dual Wield';
$dbskills[23] = 'Eagle Strike';
$dbskills[24] = 'Evocation';
$dbskills[25] = 'Feign Death';
$dbskills[26] = 'Flying Kick';
$dbskills[27] = 'Forage';
$dbskills[28] = 'Hand to Hand';
$dbskills[29] = 'Hide';
$dbskills[30] = 'Kick';
$dbskills[31] = 'Meditate';
$dbskills[32] = 'Mend';
$dbskills[33] = 'Offense';
$dbskills[34] = 'Parry';
$dbskills[35] = 'Pick Lock';
$dbskills[36] = '1H Piercing';
$dbskills[37] = 'Riposte';
$dbskills[38] = 'Round Kick';
$dbskills[39] = 'Safe Fall';
$dbskills[40] = 'Sense Heading';
$dbskills[41] = 'Singing';
$dbskills[42] = 'Sneak';
$dbskills[43] = 'Specialize Abjuration';
$dbskills[44] = 'Specialize Alteration';
$dbskills[45] = 'Specialize Conjuration';
$dbskills[46] = 'Specialize Divination';
$dbskills[47] = 'Specialize Evocation';
$dbskills[48] = 'Pick Pocket';
$dbskills[49] = 'Stringed Instruments';
$dbskills[50] = 'Swimming';
$dbskills[51] = 'Throwing';
$dbskills[52] = 'Clicky';
$dbskills[53] = 'Tracking';
$dbskills[54] = 'Wind Instruments';
$dbskills[55] = 'Fishing';
$dbskills[56] = 'Poison Making';
$dbskills[57] = 'Tinkering';
$dbskills[58] = 'Research';
$dbskills[59] = 'Alchemy';
$dbskills[60] = 'Baking';
$dbskills[61] = 'Tailoring';
$dbskills[62] = 'Sense Traps';
$dbskills[63] = 'Blacksmithing';
$dbskills[64] = 'Fletching';
$dbskills[65] = 'Brewing';
$dbskills[66] = 'Alcohol Tolerance';
$dbskills[67] = 'Begging';
$dbskills[68] = 'Jewelry Making';
$dbskills[69] = 'Pottery';
$dbskills[70] = 'Percussion Instruments';
$dbskills[71] = 'Intimidation';
$dbskills[72] = 'Berserking';
$dbskills[73] = 'Taunt';
$dbskills[74] = 'Frenzy';
$dbskills[75] = 'Remove Traps';
$dbskills[76] = 'Triple Attack';
$dbskills[77] = '2H Piercing';

// Damage Bonus Array
//http://lucy.allakhazam.com/dmgbonus.html
$dam2h = array( 0,14,14,14,14,14,14,14,14,14, // 0->9
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
            
// Item Types
$dbitypes = array();
$dbitypes[0] = "1H Slashing";
$dbitypes[1] = "2H Slashing";
$dbitypes[2] = "1H Piercing";
$dbitypes[3] = "1H Blunt";
$dbitypes[4] = "2H Blunt";
$dbitypes[5] = "Archery";
$dbitypes[7] = "Throwing";
$dbitypes[8] = "Shield";
$dbitypes[10] = "Armor";
$dbitypes[11] = "Gem";
$dbitypes[12] = "Lockpick";
$dbitypes[14] = "Food";
$dbitypes[15] = "Drink";
$dbitypes[16] = "Light";
$dbitypes[17] = "Combinable";
$dbitypes[18] = "Bandages";
$dbitypes[19] = "Throwing";
$dbitypes[20] = "Scroll";
$dbitypes[21] = "Potion";
$dbitypes[23] = "Wind Instrument";
$dbitypes[24] = "Stringed Instrument";
$dbitypes[25] = "Brass Instrument";
$dbitypes[26] = "Percussion Instrument";
$dbitypes[27] = "Arrow";
$dbitypes[29] = "Jewelry";
$dbitypes[30] = "Skull";
$dbitypes[31] = "Tome";
$dbitypes[32] = "Note";
$dbitypes[33] = "Key";
$dbitypes[34] = "Coin";
$dbitypes[35] = "2H Piercing";
$dbitypes[36] = "Fishing Pole";
$dbitypes[37] = "Fishing Bait";
$dbitypes[38] = "Alcohol";
$dbitypes[39] = "Key (bis)";
$dbitypes[40] = "Compass";
$dbitypes[42] = "Poison";
$dbitypes[45] = "Martial";
$dbitypes[52] = "Charm";
$dbitypes[54] = "Augmentation";

$tbraces = "races";
$tbspells = "spells_new";

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
$dbbodytypes[60] = "No Target 2"; 
$dbbodytypes[63] = "Swarm pet"; 
$dbbodytypes[67] = "Special"; 


// Bard Skills
$dbbardskills[23] = "Wind Instruments";
$dbbardskills[24] = "Stringed Instruments";
$dbbardskills[25] = "Brass Instruments";
$dbbardskills[26] = "Percussion Instruments";
$dbbardskills[51] = "All Instruments";

// Class Names
$dbclassnames = array("UNKNOWN", "Warrior", "Cleric", "Paladin", "Ranger", "Shadowknight", "Druid", "Monk", "Bard", "Rogue", "Shaman", "Necromancer", "Wizard", "Magician", "Enchanter", "Beastlord", "Berserker");

// Race Names
$dbracenames = array();
$dbracenames[1] = "Human";
$dbracenames[2] = "Barbarian";
$dbracenames[3] = "Erudite";
$dbracenames[4] = "Wood Elf";
$dbracenames[5] = "High Elf";
$dbracenames[6] = "Dark Elf";
$dbracenames[7] = "Half Elf";
$dbracenames[8] = "Dwarf";
$dbracenames[9] = "Troll";
$dbracenames[10] = "Ogre";
$dbracenames[11] = "Halfling";
$dbracenames[12] = "Gnome";
$dbracenames[14] = "Werewolf";
$dbracenames[60] = "Skeleton";
$dbracenames[74] =  "Froglok";
$dbracenames[75] = "Elemental";
$dbracenames[108] = "Eye of Zomm";
$dbracenames[120] = "Wolf Elemental";
$dbracenames[128] = "Iksar";
$dbracenames[130] = "Vahshir";
$dbracenames[161] = "Iksar Skeleton";
$dbracenames[330] = "Froglok";
$dbracenames[522]  = "Drakkin";
$dbracenames[65533] = "EMU Race NPC";
$dbracenames[65534] = "EMU Race Pet";
$dbracenames[65535] = "EMU Race Unknown";

// Deity IDs
$dbdeities = array();
$dbdeities[0] = "Unknown";
$dbdeities[140] = "Agnostic";
$dbdeities[201] = "Bertoxxulous";
$dbdeities[202] = "Brell-Serilis";
$dbdeities[203] = "Cazic-Thule";
$dbdeities[204] = "Erollisi-Marr";
$dbdeities[205] = "Bristlebane";
$dbdeities[206] = "Innoruuk";
$dbdeities[207] = "Karana";
$dbdeities[208] = "Mithaniel-Marr";
$dbdeities[209] = "Prexus";
$dbdeities[210] = "Quellious";
$dbdeities[211] = "Rallos-Zek";
$dbdeities[213] = "Solusek-Ro";
$dbdeities[212] = "Rodcet-Nife";
$dbdeities[215] = "Tunare";
$dbdeities[214] = "The-Tribunal";
$dbdeities[216] = "Veeshan";
$dbdeities[396] = "Agnostic";

// Deity Bitmasks
$dbideities = array();
$dbideities[65536] = "Veeshan";
$dbideities[32768] = "Tunare";
$dbideities[16384] = "The Tribunal";
$dbideities[8192] = "Solusek Ro";
$dbideities[4096] = "Rodcet Nife";
$dbideities[2048] = "Rallos Zek";
$dbideities[1024] = "Quellious";
$dbideities[512] = "Prexus";
$dbideities[256] = "Mithaniel Marr";
$dbideities[128] = "Karana";
$dbideities[64] = "Innoruuk";
$dbideities[32] = "Bristlebane";
$dbideities[16] = "Erollisi Marr";
$dbideities[8] = "Cazic Thule";
$dbideities[4] = "Brell Serilis";
$dbideities[2] = "Bertoxxulous";

// Slot Bitmasks
$dbslots = array();
$dbslotsid = array();
$dbslots[4194304] = "Powersource";
$dbslots[2097152] = "Ammo"; 
$dbslots[1048576] = "Waist"; 
$dbslots[524288] = "Feet"; 
$dbslots[262144] = "Legs"; 
$dbslots[131072] = "Chest"; 
$dbslots[98304] = "Fingers";
$dbslots[65536] = "Finger"; 
$dbslots[32768] = "Finger"; 
$dbslots[16384] = "Secondary"; 
$dbslots[8192] = "Primary"; 
$dbslots[4096] = "Hands";
$dbslots[2048] = "Range"; 
$dbslots[1536] = "Wrists"; 
$dbslots[1024] = "Wrist"; 
$dbslots[512] = "Wrist"; 
$dbslots[256] = "Back";
$dbslots[128] = "Arms";
$dbslots[64] = "Shoulders"; 
$dbslots[32] = "Neck";
$dbslots[18] = "Ears"; 
$dbslots[16] = "Ear"; 
$dbslots[8] = "Face";
$dbslots[4] = "Head";
$dbslots[2] = "Ear"; 
$dbslots[1] = "Charm"; 

// Augment Type Bitmasks
$augtypes = array(); 
$augtypes[2147483647] = "ALL";
$augtypes[536870912] = "30"; 
$augtypes[268435456] = "29"; 
$augtypes[134217728] = "28"; 
$augtypes[67108864] = "27"; 
$augtypes[33554432] = "26"; 
$augtypes[16777216] = "25"; 
$augtypes[8388608] = "24"; 
$augtypes[4194304] = "23"; 
$augtypes[2097152] = "22"; 
$augtypes[1048576] = "21"; 
$augtypes[524288] = "20"; 
$augtypes[262144] = "19"; 
$augtypes[131072] = "18"; 
$augtypes[65536] = "17"; 
$augtypes[32768] = "16"; 
$augtypes[16384] = "15"; 
$augtypes[8192] = "14"; 
$augtypes[4096] = "13"; 
$augtypes[2048] = "12"; 
$augtypes[1024] = "11"; 
$augtypes[512] = "10"; 
$augtypes[256] = "9"; 
$augtypes[128] = "8"; 
$augtypes[64] = "7"; 
$augtypes[32] = "6"; 
$augtypes[16] = "5"; 
$augtypes[8] = "4"; 
$augtypes[4] = "3"; 
$augtypes[2] = "2"; 
$augtypes[1] = "1"; 
?>