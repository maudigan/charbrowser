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
 *   March 9, 2020 - added item slot globals (Maudigan)
 *   March 22, 2020 - relocated code to common.php (Maudigan)
 *                    added EQEmu style race/class constants (Maudigan)
 *   March 25, 2020 - added spell effect constants (Maudigan)
 *   April 6, 2020 - added constant for the server max bag slots (Maudigan)
 *   April 14, 2020 - the global template class is being created/included
 *                    in common.php, it shouldn't be double created here
 *                    the api enabled check was relocated to common.php too
 *   October 20, 2022 - added leader/group aa rank data (Maudigan)
 *   October 24, 2022 - removed the unnecessary tbspells (Maudigan)
 *                      added bag constants
 *   November 27, 2022 - added missing bagtype array (Maudigan)
 *   January 16, 2023 - converted bitmasked array indexes to binary
 *                      added array for identifying bag types
 *                      
 *
 ***************************************************************************/

 
 
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}

//max bag slots, this will cap larger bags
//to only display so many slots
define("MAX_BAG_SLOTS", 200);

//few constants for allitems.type
define("EQUIPMENT", 1);
define("INVENTORY", 2);
define("BANK", 3);
define("SHAREDBANK", 4);
define("CURSOR", 5);

//spell effect types
define("SE_ARMORCLASS", 1);
define("SE_ATK", 2);
define("SE_TOTALHP", 69);
define("SE_MANAPOOL", 97);
define("SE_ENDURANCEPOOL", 190);
define("SE_MAXHPCHANGE", 214);
define("SE_COMBATSTABILITY", 259);
define("SE_ACV2", 416);

//slot numbers
define("SLOT_EQUIPMENT_START", 0);
define("SLOT_EQUIPMENT_END", 22);
define("SLOT_INVENTORY_START", 23);
define("SLOT_INVENTORY_END", 32);
define("SLOT_INVENTORY_BAGS_START", 251);
define("SLOT_INVENTORY_BAGS_END", 350);
define("SLOT_BANK_START", 2000);
define("SLOT_BANK_END", 2023);
define("SLOT_BANK_BAGS_START", 2031);
define("SLOT_BANK_BAGS_END", 2270);
define("SLOT_SHAREDBANK_START", 2500);
define("SLOT_SHAREDBANK_END", 2501);
define("SLOT_SHAREDBANK_BAG_START", 2531);
define("SLOT_SHAREDBANK_BAG_END", 2550);

//EQEMU style class constants
define("CB_CLASS_WARRIOR", 1);
define("CB_CLASS_CLERIC", 2);
define("CB_CLASS_PALADIN", 3);
define("CB_CLASS_RANGER", 4);
define("CB_CLASS_SHADOWKNIGHT", 5);
define("CB_CLASS_DRUID", 6);
define("CB_CLASS_MONK", 7);
define("CB_CLASS_BARD", 8);
define("CB_CLASS_ROGUE", 9);
define("CB_CLASS_SHAMAN", 10);
define("CB_CLASS_NECROMANCER", 11);
define("CB_CLASS_WIZARD", 12);
define("CB_CLASS_MAGICIAN", 13);
define("CB_CLASS_ENCHANTER", 14);
define("CB_CLASS_BEASTLORD", 15);
define("CB_CLASS_BERSERKER", 16);

//EQEMU style race constants
define("CB_RACE_HUMAN", 1);
define("CB_RACE_BARBARIAN", 2);
define("CB_RACE_ERUDITE", 3);
define("CB_RACE_WOOD_ELF", 4);
define("CB_RACE_HIGH_ELF", 5);
define("CB_RACE_DARK_ELF", 6);
define("CB_RACE_HALF_ELF", 7);
define("CB_RACE_DWARF", 8);
define("CB_RACE_TROLL", 9);
define("CB_RACE_OGRE", 10);
define("CB_RACE_HALFLING", 11);
define("CB_RACE_GNOME", 12);
define("CB_RACE_IKSAR", 128);
define("CB_RACE_VAHSHIR", 130);
define("CB_RACE_FROGLOK", 330);
define("CB_RACE_DRAKKIN", 522); 

//wether or not the server has GD and freetype installed
define("SERVER_HAS_GD", function_exists("imagecreatetruecolor"));
define("SERVER_HAS_FREETYPE", function_exists("imagettfbbox"));


// Element Types
$dbelements = array("Unknown", "Magic", "Fire", "Cold", "Poison", "Disease", "Corruption");

// Guild Ranks
$guildranks = array("Member", "Officer", "Leader");

// Leadership Ranks
// taken from eqemu source:  /Server/zone/aa.h 
$dbleadershipranks = array(); 
$dbleadershipranks[0] = array( 1, 2, 3, 0, 0, 0 ); //groupAAMarkNPC
$dbleadershipranks[1] = array( 2, 0, 0, 0, 0, 0 ); //groupAANPCHealth
$dbleadershipranks[2] = array( 4, 0, 0, 0, 0, 0 ); //groupAADelegateMainAssist - Have seen DelegateMainAssist come in with two different codes.
$dbleadershipranks[3] = array( 4, 0, 0, 0, 0, 0 ); //groupAADelegateMainAssist
$dbleadershipranks[4] = array( 4, 0, 0, 0, 0, 0 ); //groupAADelegateMarkNPC
$dbleadershipranks[5] = array( 0, 0, 0, 0, 0, 0 ); //groupAA5
$dbleadershipranks[6] = array( 4, 6, 0, 0, 0, 0 ); //groupAAInspectBuffs
$dbleadershipranks[7] = array( 0, 0, 0, 0, 0, 0 ); //groupAA7
$dbleadershipranks[8] = array( 6, 0, 0, 0, 0, 0 ); //groupAASpellAwareness
$dbleadershipranks[9] = array( 4, 5, 6, 7, 8, 0 ); //groupAAOffenseEnhancement
$dbleadershipranks[10] = array( 4, 6, 8, 0, 0, 0 ); //groupAAManaEnhancement
$dbleadershipranks[11] = array( 4, 6, 8, 0, 0, 0 ); //groupAAHealthEnhancement
$dbleadershipranks[12] = array( 4, 6, 8, 0, 0, 0 ); //groupAAHealthRegeneration
$dbleadershipranks[13] = array( 4, 0, 0, 0, 0, 0 ); //groupAAFindPathToPC
$dbleadershipranks[14] = array( 7, 0, 0, 0, 0, 0 ); //groupAAHealthOfTargetsTarget
$dbleadershipranks[15] = array( 0, 0, 0, 0, 0, 0 ); //groupAA15

$dbleadershipranks[16] = array( 5, 6, 7, 0, 0, 0 ); //raidAAMarkNPC	//0x10
$dbleadershipranks[17] = array( 4, 0, 0, 0, 0, 0 ); //raidAANPCHealth
$dbleadershipranks[18] = array( 6, 7, 8, 0, 0, 0 ); //raidAADelegateMainAssist
$dbleadershipranks[19] = array( 6, 6, 6, 0, 0, 0 ); //raidAADelegateMarkNPC
$dbleadershipranks[20] = array( 6, 6, 6, 0, 0, 0 ); //raidAADelegateMarkNPC (works for SoD and Titanium)
$dbleadershipranks[21] = array( 0, 0, 0, 0, 0, 0 ); //raidAA5
$dbleadershipranks[22] = array( 0, 0, 0, 0, 0, 0 ); //raidAA6
$dbleadershipranks[23] = array( 8, 0, 0, 0, 0, 0 ); //raidAASpellAwareness
$dbleadershipranks[24] = array( 6, 7, 8, 9, 10, 0 ); //raidAAOffenseEnhancement
$dbleadershipranks[25] = array( 6, 8, 10, 0, 0, 0 ); //raidAAManaEnhancement
$dbleadershipranks[26] = array( 6, 8, 10, 0, 0, 0 ); //raidAAHealthEnhancement
$dbleadershipranks[27] = array( 6, 8, 10, 0, 0, 0 ); //raidAAHealthRegeneration
$dbleadershipranks[28] = array( 5, 0, 0, 0, 0, 0 ); //raidAAFindPathToPC
$dbleadershipranks[29] = array( 9, 0, 0, 0, 0, 0 ); //raidAAHealthOfTargetsTarget
$dbleadershipranks[30] = array( 0, 0, 0, 0, 0, 0 ); //raidAA14
$dbleadershipranks[31] = array( 0, 0, 0, 0, 0, 0 ); //raidAA15

// Class BItmasks
$dbiclasses = array();
$dbiclasses[0b01111111111111111] = "ALL";
$dbiclasses[0b01000000000000000] = "BER";
$dbiclasses[0b00100000000000000] = "BST";
$dbiclasses[0b00010000000000000] = "ENC";
$dbiclasses[0b00001000000000000] = "MAG";
$dbiclasses[0b00000100000000000] = "WIZ";
$dbiclasses[0b00000010000000000] = "NEC";
$dbiclasses[0b00000001000000000] = "SHM";
$dbiclasses[0b00000000100000000] = "ROG";
$dbiclasses[0b00000000010000000] = "BRD";
$dbiclasses[0b00000000001000000] = "MNK";
$dbiclasses[0b00000000000100000] = "DRU";
$dbiclasses[0b00000000000010000] = "SHD";
$dbiclasses[0b00000000000001000] = "RNG";
$dbiclasses[0b00000000000000100] = "PAL";
$dbiclasses[0b00000000000000010] = "CLR";
$dbiclasses[0b00000000000000001] = "WAR";

// Race Bitmasks
$dbraces = array();
$dbraces[0b01111111111111111] = "ALL";
$dbraces[0b01000000000000000] = "DRK";
$dbraces[0b00100000000000000] = "FRG";
$dbraces[0b00010000000000000] = "VAH";
$dbraces[0b00001000000000000] = "IKS";
$dbraces[0b00000100000000000] = "GNM";
$dbraces[0b00000010000000000] = "HFL";
$dbraces[0b00000001000000000] = "OGR";
$dbraces[0b00000000100000000] = "TRL";
$dbraces[0b00000000010000000] = "DWF";
$dbraces[0b00000000001000000] = "HEF";
$dbraces[0b00000000000100000] = "DEF";
$dbraces[0b00000000000010000] = "HIE";
$dbraces[0b00000000000001000] = "ELF";
$dbraces[0b00000000000000100] = "ERU";
$dbraces[0b00000000000000010] = "BAR";
$dbraces[0b00000000000000001] = "HUM";

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

$dbnpcraces =  array(
   1   => 'Human',
   2   => 'Barbarian',
   3   => 'Erudite',
   4   => 'Wood Elf',
   5   => 'High Elf',
   6   => 'Dark Elf',
   7   => 'Half Elf',
   8   => 'Dwarf',
   9   => 'Troll',
   10  => 'Ogre',
   11  => 'Halfling',
   12  => 'Gnome',
   13  => 'Aviak',
   14  => 'Werewolf',
   15  => 'Brownie',
   16  => 'Centaur',
   17  => 'Golem',
   18  => 'Giant/Cyclops',
   19  => 'Trakanon',
   20  => 'Venril Sathir',
   21  => 'Evil Eye',
   22  => 'Beetle',
   23  => 'Kerran',
   24  => 'Fish',
   25  => 'Fairy',
   26  => 'Froglok',
   27  => 'Froglok Ghoul',
   28  => 'Fungusman',
   29  => 'Gargoyle',
   30  => 'Gasbag',
   31  => 'Gelatinous Cube',
   32  => 'Ghost',
   33  => 'Ghoul',
   34  => 'Giant Bat',
   35  => 'Giant Eel',
   36  => 'Giant Rat',
   37  => 'Giant Snake',
   38  => 'Giant Spider',
   39  => 'Gnoll',
   40  => 'Goblin',
   41  => 'Gorilla',
   42  => 'Wolf',
   43  => 'Bear',
   44  => 'Freeport Guard',
   45  => 'Demi Lich',
   46  => 'Imp',
   47  => 'Griffin',
   48  => 'Kobold',
   49  => 'Lava Dragon',
   50  => 'Lion',
   51  => 'Lizard Man',
   52  => 'Mimic',
   53  => 'Minotaur',
   54  => 'Orc',
   55  => 'Human Beggar',
   56  => 'Pixie',
   57  => 'Dracnid',
   58  => 'Solusek Ro',
   59  => 'Bloodgill',
   60  => 'Skeleton',
   61  => 'Shark',
   62  => 'Tunare',
   63  => 'Tiger',
   64  => 'Treant',
   65  => 'Vampire',
   66  => 'Statue of Rallos Zek',
   67  => 'Highpass Citizen',
   68  => 'Tentacle',
   69  => 'Wisp',
   70  => 'Zombie',
   71  => 'Qeynos Citizen',
   72  => 'Ship',
   73  => 'Launch',
   74  => 'Piranha',
   75  => 'Elemental',
   76  => 'Puma',
   77  => 'Neriak Citizen',
   78  => 'Erudite Citizen',
   79  => 'Bixie',
   80  => 'Reanimated Hand',
   81  => 'Rivervale Citizen',
   82  => 'Scarecrow',
   83  => 'Skunk',
   84  => 'Snake Elemental',
   85  => 'Spectre',
   86  => 'Sphinx',
   87  => 'Armadillo',
   88  => 'Clockwork Gnome',
   89  => 'Drake',
   90  => 'Halas Citizen',
   91  => 'Alligator',
   92  => 'Grobb Citizen',
   93  => 'Oggok Citizen',
   94  => 'Kaladim Citizen',
   95  => 'Cazic Thule',
   96  => 'Cockatrice',
   97  => 'Daisy Man',
   98  => 'Elf Vampire',
   99  => 'Denizen',
   100 => 'Dervish',
   101 => 'Efreeti',
   102 => 'Froglok Tadpole',
   103 => 'Phinigel Autropos',
   104 => 'Leech',
   105 => 'Swordfish',
   106 => 'Felguard',
   107 => 'Mammoth',
   108 => 'Eye of Zomm',
   109 => 'Wasp',
   110 => 'Mermaid',
   111 => 'Harpie',
   112 => 'Fayguard',
   113 => 'Drixie',
   114 => 'Ghost Ship',
   115 => 'Clam',
   116 => 'Sea Horse',
   117 => 'Dwarf Ghost',
   118 => 'Erudite Ghost',
   119 => 'Sabertooth',
   120 => 'Wolf Elemental',
   121 => 'Gorgon',
   122 => 'Dragon Skeleton',
   123 => 'Innoruuk',
   124 => 'Unicorn',
   125 => 'Pegasus',
   126 => 'Djinn',
   127 => 'Invisible Man',
   128 => 'Iksar',
   129 => 'Scorpion',
   130 => 'Vah Shir',
   131 => 'Sarnak',
   132 => 'Draglock',
   133 => 'Lycanthrope',
   134 => 'Mosquito',
   135 => 'Rhino',
   136 => 'Xalgoz',
   137 => 'Kunark Goblin',
   138 => 'Yeti',
   139 => 'Iksar Citizen',
   140 => 'Forest Giant',
   141 => 'Boat',
   142 => 'Minor Illusion',
   143 => 'Tree Illusion',
   144 => 'Burynai',
   145 => 'Goo',
   146 => 'Spectral Sarnak',
   147 => 'Spectral Iksar',
   148 => 'Kunark Fish',
   149 => 'Iksar Scorpion',
   150 => 'Erollisi',
   151 => 'Tribunal',
   152 => 'Bertoxxulous',
   153 => 'Bristlebane',
   154 => 'Fay Drake',
   155 => 'Sarnak Skeleton',
   156 => 'Ratman',
   157 => 'Wyvern',
   158 => 'Wurm',
   159 => 'Devourer',
   160 => 'Iksar Golem',
   161 => 'Iksar Skeleton',
   162 => 'Man Eating Plant',
   163 => 'Raptor',
   164 => 'Sarnak Golem',
   165 => 'Water Dragon',
   166 => 'Iksar Hand',
   167 => 'Succulent',
   168 => 'Holgresh',
   169 => 'Brontotherium',
   170 => 'Snow Dervish',
   171 => 'Dire Wolf',
   172 => 'Manticore',
   173 => 'Totem',
   174 => 'Cold Spectre',
   175 => 'Enchanted Armor',
   176 => 'Snow Bunny',
   177 => 'Walrus',
   178 => 'Rock-gem Man',
   179 => 'Unknown179',
   180 => 'Unknown180',
   181 => 'Yak Man',
   182 => 'Faun',
   183 => 'Coldain',
   184 => 'Velious Dragon',
   185 => 'Hag',
   186 => 'Hippogriff',
   187 => 'Siren',
   188 => 'Frost Giant',
   189 => 'Storm Giant',
   190 => 'Otterman',
   191 => 'Walrus Man',
   192 => 'Clockwork Dragon',
   193 => 'Abhorrent',
   194 => 'Sea Turtle',
   195 => 'Black and White Dragon',
   196 => 'Ghost Dragon',
   197 => 'Ronnie Test',
   198 => 'Prismatic Dragon',
   199 => 'Shiknar',
   200 => 'Rockhopper',
   201 => 'Underbulk',
   202 => 'Grimling',
   203 => 'Vacuum Worm',
   204 => 'Evan Test',
   205 => 'Kahli Shah',
   206 => 'Owlbear',
   207 => 'Rhino Beetle',
   208 => 'Vampyre',
   209 => 'Earth Elemental',
   210 => 'Air Elemental',
   211 => 'Water Elemental',
   212 => 'Fire Elemental',
   213 => 'Wetfang Minnow',
   214 => 'Thought Horror',
   215 => 'Tegi',
   216 => 'Horse',
   217 => 'Shissar',
   218 => 'Fungal Fiend',
   219 => 'Vampire Volatalis',
   220 => 'StoneGrabber',
   221 => 'Scarlet Cheetah',
   222 => 'Zelniak',
   223 => 'Lightcrawler',
   224 => 'Shade',
   225 => 'Sunflower',
   226 => 'Khati Sha',
   227 => 'Shrieker',
   228 => 'Galorian',
   229 => 'Netherbian',
   230 => 'Akhevan',
   231 => 'Spire Spirit',
   232 => 'Sonic Wolf',
   233 => 'Ground Shaker',
   234 => 'Vah Shir Skeleton',
   235 => 'Mutant Humanoid',
   236 => 'Lord Inquisitor Seru',
   237 => 'Recuso',
   238 => 'Vah Shir King',
   239 => 'Vah Shir Guard',
   240 => 'Teleport Man',
   241 => 'Lujein',
   242 => 'Naiad',
   243 => 'Nymph',
   244 => 'Ent',
   245 => 'Wrinnfly',
   246 => 'Coirnav',
   247 => 'Solusek Ro',
   248 => 'Clockwork Golem',
   249 => 'Clockwork Brain',
   250 => 'Spectral Banshee',
   251 => 'Guard of Justice',
   252 => 'PoM Castle',
   253 => 'Disease Boss',
   254 => 'Solusek Ro Guard',
   255 => 'Bertoxxulous (New)',
   256 => 'Tribunal (New)',
   257 => 'Terris Thule',
   258 => 'Vegerog',
   259 => 'Crocodile',
   260 => 'Bat',
   261 => 'Slarghilug',
   262 => 'Tranquilion',
   263 => 'Tin Soldier',
   264 => 'Nightmare Wraith',
   265 => 'Malarian',
   266 => 'Knight of Pestilence',
   267 => 'Lepertoloth',
   268 => 'Bubonian Boss',
   269 => 'Bubonian Underling',
   270 => 'Pusling',
   271 => 'Water Mephit',
   272 => 'Stormrider',
   273 => 'Junk Beast',
   274 => 'Broken Clockwork',
   275 => 'Giant Clockwork',
   276 => 'Clockwork Beetle',
   277 => 'Nightmare Goblin',
   278 => 'Karana',
   279 => 'Blood Raven',
   280 => 'Nightmare Gargoyle',
   281 => 'Mouth of Insanity',
   282 => 'Skeletal Horse',
   283 => 'Saryrn',
   284 => 'Fennin Ro',
   285 => 'Tormentor',
   286 => 'Necromancer Priest',
   287 => 'Nightmare',
   288 => 'New Rallos Zek',
   289 => 'Vallon Zek',
   290 => 'Tallon Zek',
   291 => 'Air Mephit',
   292 => 'Earth Mephit',
   293 => 'Fire Mephit',
   294 => 'Nightmare Mephit',
   295 => 'Zebuxoruk',
   296 => 'Mithaniel Marr',
   297 => 'Knightmare Rider',
   298 => 'Rathe Councilman',
   299 => 'Xegony',
   300 => 'Demon/Fiend',
   301 => 'Test Object',
   302 => 'Lobster Monster',
   303 => 'Phoenix',
   304 => 'Quarm',
   305 => 'New Bear',
   306 => 'Earth Golem',
   307 => 'Iron Golem',
   308 => 'Storm Golem',
   309 => 'Air Golem',
   310 => 'Wood Golem',
   311 => 'Fire Golem',
   312 => 'Water Golem',
   313 => 'Veiled Gargoyle',
   314 => 'Lynx',
   315 => 'Squid',
   316 => 'Frog',
   317 => 'Flying Serpent',
   318 => 'Tactics Soldier',
   319 => 'Armored Boar',
   320 => 'Djinni',
   321 => 'Boar',
   322 => 'Knight of Marr',
   323 => 'Armor of Marr',
   324 => 'Nightmare Knight',
   325 => 'Rallos Ogre',
   326 => 'Arachnid',
   327 => 'Crystal Arachnid',
   328 => 'Tower Model',
   329 => 'Portal',
   330 => 'Froglok',
   331 => 'Troll Crew Member',
   332 => 'Pirate Deckhand',
   333 => 'Broken Skull Pirate',
   334 => 'Pirate Ghost',
   335 => 'One-armed Pirate',
   336 => 'Spiritmaster Nadox',
   337 => 'Broken Skull Taskmaster',
   338 => 'Gnome Pirate',
   339 => 'Dark Elf Pirate',
   340 => 'Ogre Pirate',
   341 => 'Human Pirate',
   342 => 'Erudite Pirate',
   343 => 'Frog',
   344 => 'Undead Pirate',
   345 => 'Luggald Worker',
   346 => 'Luggald Soldier',
   347 => 'Luggald Disciple',
   348 => 'Drogmor',
   349 => 'Froglok Skeleton',
   350 => 'Undead Froglok',
   351 => 'Knight of Hate',
   352 => 'Warlock of Hate',
   353 => 'Highborn',
   354 => 'Highborn Diviner',
   355 => 'Highborn Crusader',
   356 => 'Chokidai',
   357 => 'Undead Chokidai',
   358 => 'Undead Veksar',
   359 => 'Undead Vampire',
   360 => 'Vampire',
   361 => 'Rujarkian Orc',
   362 => 'Bone Golem',
   363 => 'Synarcana',
   364 => 'Sand Elf',
   365 => 'Master Vampire',
   366 => 'Master Orc',
   367 => 'New Skeleton',
   368 => 'Crypt Creeper',
   369 => 'New Goblin',
   370 => 'Burrower Bug',
   371 => 'Froglok Ghost',
   372 => 'Vortex',
   373 => 'Shadow',
   374 => 'Golem Beast',
   375 => 'Watchful Eye',
   376 => 'Box',
   377 => 'Barrel',
   378 => 'Chest',
   379 => 'Vase',
   380 => 'Frozen Table',
   381 => 'Weapon Rack',
   382 => 'Coffin',
   383 => 'Skull and Bones',
   384 => 'Jester',
   385 => 'Taelosian Native',
   386 => 'Taelosian Evoker',
   387 => 'Taelosian Golem',
   388 => 'Taelosian Wolf',
   389 => 'Taelosian Amphibian Creature',
   390 => 'Taelosian Mountain Beast',
   391 => 'Taelosian Stonemite',
   392 => 'Ukun War Hound',
   393 => 'Ixt Centaur',
   394 => 'Ikaav Snakewoman',
   395 => 'Aneuk',
   396 => 'Kyv Hunter',
   397 => 'Noc Sprayblood',
   398 => 'Ratuk Brute',
   399 => 'Ixt',
   400 => 'Huvul',
   401 => 'Mastruq Warfiend',
   402 => 'Mastruq',
   403 => 'Taelosian',
   404 => 'Ship',
   405 => 'New Golem',
   406 => 'Overlord Mata Muram',
   407 => 'Lighting warrior',
   408 => 'Succubus',
   409 => 'Bazu',
   410 => 'Feran',
   411 => 'Pyrilen',
   412 => 'Chimera',
   413 => 'Dragorn',
   414 => 'Murkglider',
   415 => 'Rat',
   416 => 'Bat',
   417 => 'Gelidran',
   418 => 'Discordling',
   419 => 'Girplan',
   420 => 'Minotaur',
   421 => 'Dragorn Box',
   422 => 'Runed Orb',
   423 => 'Dragon Bones',
   424 => 'Muramite Armor Pile',
   425 => 'Crystal Shard',
   426 => 'Portal',
   427 => 'Coin Purse',
   428 => 'Rock Pile',
   429 => 'Murkglider Egg Sack',
   430 => 'Drake',
   431 => 'Dervish',
   432 => 'Drake',
   433 => 'Goblin',
   434 => 'Kirin',
   435 => 'Dragon',
   436 => 'Basilisk',
   437 => 'Dragon',
   438 => 'Dragon',
   439 => 'Puma',
   440 => 'Spider',
   441 => 'Spider Queen',
   442 => 'Animated Statue',
   443 => 'Unknown443',
   444 => 'Unknown444',
   445 => 'Dragon Egg',
   446 => 'Dragon Statue',
   447 => 'Lava Rock',
   448 => 'Animated Statue',
   449 => 'Spider Egg Sack',
   450 => 'Lava Spider',
   451 => 'Lava Spider Queen',
   452 => 'Dragon',
   453 => 'Giant',
   454 => 'Werewolf',
   455 => 'Kobold',
   456 => 'Sporali',
   457 => 'Gnomework',
   458 => 'Orc',
   459 => 'Corathus',
   460 => 'Coral',
   461 => 'Drachnid',
   462 => 'Drachnid Cocoon',
   463 => 'Fungus Patch',
   464 => 'Gargoyle',
   465 => 'Witheran',
   466 => 'Dark Lord',
   467 => 'Shiliskin',
   468 => 'Snake',
   469 => 'Evil Eye',
   470 => 'Minotaur',
   471 => 'Zombie',
   472 => 'Clockwork Boar',
   473 => 'Fairy',
   474 => 'Witheran',
   475 => 'Air Elemental',
   476 => 'Earth Elemental',
   477 => 'Fire Elemental',
   478 => 'Water Elemental',
   479 => 'Alligator',
   480 => 'Bear',
   481 => 'Scaled Wolf',
   482 => 'Wolf',
   483 => 'Spirit Wolf',
   484 => 'Skeleton',
   485 => 'Spectre',
   486 => 'Bolvirk',
   487 => 'Banshee',
   488 => 'Banshee',
   489 => 'Elddar',
   490 => 'Forest Giant',
   491 => 'Bone Golem',
   492 => 'Horse',
   493 => 'Pegasus',
   494 => 'Shambling Mound',
   495 => 'Scrykin',
   496 => 'Treant',
   497 => 'Vampire',
   498 => 'Ayonae Ro',
   499 => 'Sullon Zek',
   500 => 'Banner',
   501 => 'Flag',
   502 => 'Rowboat',
   503 => 'Bear Trap',
   504 => 'Clockwork Bomb',
   505 => 'Dynamite Keg',
   506 => 'Pressure Plate',
   507 => 'Puffer Spore',
   508 => 'Stone Ring',
   509 => 'Root Tentacle',
   510 => 'Runic Symbol',
   511 => 'Saltpetter Bomb',
   512 => 'Floating Skull',
   513 => 'Spike Trap',
   514 => 'Totem',
   515 => 'Web',
   516 => 'Wicker Basket',
   517 => 'Nightmare/Unicorn',
   518 => 'Horse',
   519 => 'Nightmare/Unicorn',
   520 => 'Bixie',
   521 => 'Centaur',
   522 => 'Drakkin',
   523 => 'Giant',
   524 => 'Gnoll',
   525 => 'Griffin',
   526 => 'Giant Shade',
   527 => 'Harpy',
   528 => 'Mammoth',
   529 => 'Satyr',
   530 => 'Dragon',
   531 => 'Dragon',
   532 => 'DynLeth',
   533 => 'Boat',
   534 => 'Weapon Rack',
   535 => 'Armor Rack',
   536 => 'Honey Pot',
   537 => 'Jum Jum Bucket',
   538 => 'Plant',
   539 => 'Plant',
   540 => 'Plant',
   541 => 'Toolbox',
   542 => 'Wine Cask',
   543 => 'Stone Jug',
   544 => 'Elven Boat',
   545 => 'Gnomish Boat',
   546 => 'Barrel Barge Ship',
   547 => 'Goo',
   548 => 'Goo',
   549 => 'Goo',
   550 => 'Merchant Ship',
   551 => 'Pirate Ship',
   552 => 'Ghost Ship',
   553 => 'Banner',
   554 => 'Banner',
   555 => 'Banner',
   556 => 'Banner',
   557 => 'Banner',
   558 => 'Aviak',
   559 => 'Beetle',
   560 => 'Gorilla',
   561 => 'Kedge',
   562 => 'Kerran',
   563 => 'Shissar',
   564 => 'Siren',
   565 => 'Sphinx',
   566 => 'Human',
   567 => 'Campfire',
   568 => 'Brownie',
   569 => 'Dragon',
   570 => 'Exoskeleton',
   571 => 'Ghoul',
   572 => 'Clockwork Guardian',
   573 => 'Mantrap',
   574 => 'Minotaur',
   575 => 'Scarecrow',
   576 => 'Shade',
   577 => 'Rotocopter',
   578 => 'Tentacle Terror',
   579 => 'Wereorc',
   580 => 'Worg',
   581 => 'Wyvern',
   582 => 'Chimera',
   583 => 'Kirin',
   584 => 'Puma',
   585 => 'Boulder',
   586 => 'Banner',
   587 => 'Elven Ghost',
   588 => 'Human Ghost',
   589 => 'Chest',
   590 => 'Chest',
   591 => 'Crystal',
   592 => 'Coffin',
   593 => 'Guardian CPU',
   594 => 'Worg',
   595 => 'Mansion',
   596 => 'Floating Island',
   597 => 'Cragslither',
   598 => 'Wrulon',
   599 => 'Spell Particle 1',
   600 => 'Invisible Man of Zomm',
   601 => 'Robocopter of Zomm',
   602 => 'Burynai',
   603 => 'Frog',
   604 => 'Dracolich',
   605 => 'Iksar Ghost',
   606 => 'Iksar Skeleton',
   607 => 'Mephit',
   608 => 'Muddite',
   609 => 'Raptor',
   610 => 'Sarnak',
   611 => 'Scorpion',
   612 => 'Tsetsian',
   613 => 'Wurm',
   614 => 'Balrog',
   615 => 'Hydra Crystal',
   616 => 'Crystal Sphere',
   617 => 'Gnoll',
   618 => 'Sokokar',
   619 => 'Stone Pylon',
   620 => 'Demon Vulture',
   621 => 'Wagon',
   622 => 'God of Discord',
   623 => 'Wrulon Mount',
   624 => 'Ogre NPC - Male',
   625 => 'Sokokar Mount',
   626 => 'Giant (Rallosian mats)',
   627 => 'Sokokar (w saddle)',
   628 => '10th Anniversary Banner',
   629 => '10th Anniversary Cake',
   630 => 'Wine Cask',
   631 => 'Hydra Mount',
   632 => 'Hydra NPC',
   633 => 'Wedding Flowers',
   634 => 'Wedding Arbor',
   635 => 'Wedding Altar',
   636 => 'Powder Keg',
   637 => 'Apexus',
   638 => 'Bellikos',
   639 => 'Brells First Creation',
   640 => 'Brell',
   641 => 'Crystalskin Ambuloid',
   642 => 'Cliknar Queen',
   643 => 'Cliknar Soldier',
   644 => 'Cliknar Worker',
   645 => 'Coldain',
   646 => 'Coldain',
   647 => 'Crystalskin Sessiloid',
   648 => 'Genari',
   649 => 'Gigyn',
   650 => 'Greken - Young Adult',
   651 => 'Greken - Young',
   652 => 'Cliknar Mount',
   653 => 'Telmira',
   654 => 'Spider Mount',
   655 => 'Bear Mount',
   656 => 'Rat Mount',
   657 => 'Sessiloid Mount',
   658 => 'Morell Thule',
   659 => 'Marionette',
   660 => 'Book Dervish',
   661 => 'Topiary Lion',
   662 => 'Rotdog',
   663 => 'Amygdalan',
   664 => 'Sandman',
   665 => 'Grandfather Clock',
   666 => 'Gingerbread Man',
   667 => 'Beefeater',
   668 => 'Rabbit',
   669 => 'Blind Dreamer',
   670 => 'Cazic Thule',
   671 => 'Topiary Lion Mount',
   672 => 'Rot Dog Mount',
   673 => 'Goral Mount',
   674 => 'Selyran Mount',
   675 => 'Sclera Mount',
   676 => 'Braxy Mount',
   677 => 'Kangon Mount',
   678 => 'Erudite',
 );

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
//Retrieved from Server code /Sever/zone/bonuses.cpp
$dbbardskills[23] = "Wind Instruments";
$dbbardskills[24] = "Stringed Instruments";
$dbbardskills[25] = "Brass Instruments";
$dbbardskills[26] = "Percussion Instruments";
$dbbardskills[50] = "Singing";
$dbbardskills[51] = "All Instruments";

//Types of bags
//bag types, pulled from eqemu server: /Server/common/item_data.h
$dbbagtypes = array(); 
$dbbagtypes[0] = "Small Bag";
$dbbagtypes[1] = "Large Bag";
$dbbagtypes[2] = "Quiver";
$dbbagtypes[3] = "Belt Pouch";
$dbbagtypes[4] = "Wrist Pouch";
$dbbagtypes[5] = "Back Pack";
$dbbagtypes[6] = "Small Chest";
$dbbagtypes[7] = "Large Chest";
$dbbagtypes[8] = "Bandolier";// <*Database Reference Only>
$dbbagtypes[9] = "Medicine Bag";
$dbbagtypes[10] = "Tool Box";
$dbbagtypes[11] = "Lexicon";
$dbbagtypes[12] = "Mortar";
$dbbagtypes[13] = "Self Dusting";// Quest container (Auto-clear contents?)
$dbbagtypes[14] = "Mixing Bowl";
$dbbagtypes[15] = "Oven";
$dbbagtypes[16] = "Sewing Kit";
$dbbagtypes[17] = "Forge";
$dbbagtypes[18] = "Fletching Kit";
$dbbagtypes[19] = "Brew Barrel";
$dbbagtypes[20] = "Jewelers Kit";
$dbbagtypes[21] = "Pottery Wheel";
$dbbagtypes[22] = "Kiln";
$dbbagtypes[23] = "Keymaker";// (no database entries as of peq rev 69)
$dbbagtypes[24] = "Wizards Lexicon";
$dbbagtypes[25] = "Mages Lexicon";
$dbbagtypes[26] = "Necromancers Lexicon";
$dbbagtypes[27] = "Enchanters Lexicon";
$dbbagtypes[28] = "Unknown";// (a coin pouch/purse?) (no database entries as of peq rev 69)
$dbbagtypes[29] = "Concordance of Research";// <*Database Reference Only>
$dbbagtypes[30] = "Always Works";// Quest container (Never-fail combines?)
$dbbagtypes[31] = "Koada`Dal Forge";// High Elf
$dbbagtypes[32] = "Teir`Dal Forge";// Dark Elf
$dbbagtypes[33] = "Oggok Forge";// Ogre
$dbbagtypes[34] = "Stormguard Forge";// Dwarf
$dbbagtypes[35] = "Ak`Anon Forge";// Gnome
$dbbagtypes[36] = "Northman Forge";// Barbarian
$dbbagtypes[37] = "Unknown";// (no database entries as of peq rev 69)
$dbbagtypes[38] = "Cabilis Forge";// Iksar
$dbbagtypes[39] = "Freeport Forge";// Human 1
$dbbagtypes[40] = "Royal Qeynos Forge";// Human 2
$dbbagtypes[41] = "Halfling Tailoring Kit";
$dbbagtypes[42] = "Erudite Tailoring Kit";
$dbbagtypes[43] = "Fier`Dal Tailoring Kit";// Wood Elf
$dbbagtypes[44] = "Fier`Dal Fletching Kit";// Wood Elf
$dbbagtypes[45] = "Iksar Pottery Wheel";
$dbbagtypes[46] = "Tackle Box";
$dbbagtypes[47] = "Troll Forge";
$dbbagtypes[48] = "Fier`Dal Forge";// Wood Elf
$dbbagtypes[49] = "Vale Forge";// Halfling
$dbbagtypes[50] = "Erud Forge";
$dbbagtypes[51] = "Traders Satchel";// <*Database Reference Only> (db: Yellow Trader's Satchel Token?)
$dbbagtypes[52] = "Gukta Forge";// Froglok (no database entries as of peq rev 69)
$dbbagtypes[53] = "Augmentation Sealer";
$dbbagtypes[54] = "Ice Cream Churn";// <*Database Reference Only>
$dbbagtypes[55] = "Transformation Mold";// Ornamentation
$dbbagtypes[56] = "Detransformation Mold";// Ornamentation Stripper
$dbbagtypes[57] = "Unattuner";
$dbbagtypes[58] = "Tradeskill Bag";
$dbbagtypes[59] = "Collectible Bag";

//map bagtype to skill used
//bag types, pulled from eqemu server: /Server/zone/tradeskills.cpp, TypeToSkill() Method
$dbbagtypetoskill = array(); 
$dbbagtypetoskill[0] = false; //Small Bag
$dbbagtypetoskill[1] = false; //Large Bag
$dbbagtypetoskill[2] = false; //Quiver
$dbbagtypetoskill[3] = false; //Belt Pouch
$dbbagtypetoskill[4] = false; //Wrist Pouch
$dbbagtypetoskill[5] = false; //Back Pack
$dbbagtypetoskill[6] = false; //Small Chest
$dbbagtypetoskill[7] = false; //Large Chest
$dbbagtypetoskill[8] = false; //Bandolier
$dbbagtypetoskill[9] = 59; //Medicine Bag
$dbbagtypetoskill[10] = 57; //Tool Box
$dbbagtypetoskill[11] = 58; //Lexicon
$dbbagtypetoskill[12] = 56; //Mortar
$dbbagtypetoskill[13] = false; //Self Dusting// Quest container (Auto-clear contents?)
$dbbagtypetoskill[14] = 60; //Mixing Bowl
$dbbagtypetoskill[15] = 60; //Oven
$dbbagtypetoskill[16] = 61; //Sewing Kit
$dbbagtypetoskill[17] = 63; //Forge
$dbbagtypetoskill[18] = 64; //Fletching Kit
$dbbagtypetoskill[19] = 65; //Brew Barrel
$dbbagtypetoskill[20] = 68; //Jewelers Kit
$dbbagtypetoskill[21] = 69; //Pottery Wheel
$dbbagtypetoskill[22] = 69; //Kiln
$dbbagtypetoskill[23] = false; //Keymaker// (no database entries as of peq rev 69)
$dbbagtypetoskill[24] = 58; //Wizards Lexicon
$dbbagtypetoskill[25] = 58; //Mages Lexicon
$dbbagtypetoskill[26] = 58; //Necromancers Lexicon
$dbbagtypetoskill[27] = 58; //Enchanters Lexicon
$dbbagtypetoskill[28] = false; //Unknown// (a coin pouch/purse?) (no database entries as of peq rev 69)
$dbbagtypetoskill[29] = 58; //Concordance of Research// <*Database Reference Only>
$dbbagtypetoskill[30] = false; //Always Works// Quest container (Never-fail combines?)
$dbbagtypetoskill[31] = 63; //Koada`Dal Forge// High Elf
$dbbagtypetoskill[32] = 63; //Teir`Dal Forge// Dark Elf
$dbbagtypetoskill[33] = 63; //Oggok Forge// Ogre
$dbbagtypetoskill[34] = 63; //Stormguard Forge// Dwarf
$dbbagtypetoskill[35] = 63; //Ak`Anon Forge// Gnome
$dbbagtypetoskill[36] = 63; //Northman Forge// Barbarian
$dbbagtypetoskill[37] = false; //Unknown// (no database entries as of peq rev 69)
$dbbagtypetoskill[38] = 63; //Cabilis Forge// Iksar
$dbbagtypetoskill[39] = 63; //Freeport Forge// Human 1
$dbbagtypetoskill[40] = 63; //Royal Qeynos Forge// Human 2
$dbbagtypetoskill[41] = 61; //Halfling Tailoring Kit
$dbbagtypetoskill[42] = 61; //Erudite Tailoring Kit
$dbbagtypetoskill[43] = 61; //Fier`Dal Tailoring Kit// Wood Elf
$dbbagtypetoskill[44] = 64; //Fier`Dal Fletching Kit// Wood Elf
$dbbagtypetoskill[45] = 69; //Iksar Pottery Wheel
$dbbagtypetoskill[46] = 55; //Tackle Box
$dbbagtypetoskill[47] = 63; //Troll Forge
$dbbagtypetoskill[48] = 63; //Fier`Dal Forge// Wood Elf
$dbbagtypetoskill[49] = 63; //Vale Forge// Halfling
$dbbagtypetoskill[50] = 63; //Erud Forge
$dbbagtypetoskill[51] = false; //Traders Satchel// <*Database Reference Only> (db: Yellow Trader's Satchel Token?)
$dbbagtypetoskill[52] = 63; //Gukta Forge// Froglok (no database entries as of peq rev 69)
$dbbagtypetoskill[53] = false; //Augmentation Sealer
$dbbagtypetoskill[54] = 65; //Ice Cream Churn// <*Database Reference Only>
$dbbagtypetoskill[55] = false; //Transformation Mold// Ornamentation
$dbbagtypetoskill[56] = false; //Detransformation Mold// Ornamentation Stripper
$dbbagtypetoskill[57] = false; //Unattuner
$dbbagtypetoskill[58] = false; //Tradeskill Bag
$dbbagtypetoskill[59] = false; //Collectible Bag

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
$dbideities[0b10000000000000000] = "Veeshan";
$dbideities[0b01000000000000000] = "Tunare";
$dbideities[0b00100000000000000] = "The Tribunal";
$dbideities[0b00010000000000000] = "Solusek Ro";
$dbideities[0b00001000000000000] = "Rodcet Nife";
$dbideities[0b00000100000000000] = "Rallos Zek";
$dbideities[0b00000010000000000] = "Quellious";
$dbideities[0b00000001000000000] = "Prexus";
$dbideities[0b00000000100000000] = "Mithaniel Marr";
$dbideities[0b00000000010000000] = "Karana";
$dbideities[0b00000000001000000] = "Innoruuk";
$dbideities[0b00000000000100000] = "Bristlebane";
$dbideities[0b00000000000010000] = "Erollisi Marr";
$dbideities[0b00000000000001000] = "Cazic Thule";
$dbideities[0b00000000000000100] = "Brell Serilis";
$dbideities[0b00000000000000010] = "Bertoxxulous";

// Slot Bitmasks
$dbslots = array();
$dbslots[0b10000000000000000000000] = "Powersource";
$dbslots[0b01000000000000000000000] = "Ammo"; 
$dbslots[0b00100000000000000000000] = "Waist"; 
$dbslots[0b00010000000000000000000] = "Feet"; 
$dbslots[0b00001000000000000000000] = "Legs"; 
$dbslots[0b00000100000000000000000] = "Chest"; 
$dbslots[0b00000011000000000000000] = "Fingers"; 
$dbslots[0b00000010000000000000000] = "Finger"; 
$dbslots[0b00000001000000000000000] = "Finger"; 
$dbslots[0b00000000100000000000000] = "Secondary"; 
$dbslots[0b00000000010000000000000] = "Primary"; 
$dbslots[0b00000000001000000000000] = "Hands";
$dbslots[0b00000000000100000000000] = "Range"; 
$dbslots[0b00000000000011000000000] = "Wrists"; 
$dbslots[0b00000000000010000000000] = "SWrist"; 
$dbslots[0b00000000000001000000000] = "WWrist"; 
$dbslots[0b00000000000000100000000] = "Back";
$dbslots[0b00000000000000010000000] = "Arms";
$dbslots[0b00000000000000001000000] = "Shoulders"; 
$dbslots[0b00000000000000000100000] = "Neck";
$dbslots[0b00000000000000000010010] = "Ears"; 
$dbslots[0b00000000000000000010000] = "Ear"; 
$dbslots[0b00000000000000000001000] = "Face";
$dbslots[0b00000000000000000000100] = "Head";
$dbslots[0b00000000000000000000010] = "Ear"; 
$dbslots[0b00000000000000000000001] = "Charm"; 


// Augment Type Bitmasks
$augtypes = array(); 
$augtypes[0b1111111111111111111111111111111] = "ALL";
$augtypes[0b0100000000000000000000000000000] = "30"; 
$augtypes[0b0010000000000000000000000000000] = "29"; 
$augtypes[0b0001000000000000000000000000000] = "28"; 
$augtypes[0b0000100000000000000000000000000] = "27"; 
$augtypes[0b0000010000000000000000000000000] = "26"; 
$augtypes[0b0000001000000000000000000000000] = "25"; 
$augtypes[0b0000000100000000000000000000000] = "24"; 
$augtypes[0b0000000010000000000000000000000] = "23"; 
$augtypes[0b0000000001000000000000000000000] = "22"; 
$augtypes[0b0000000000100000000000000000000] = "21"; 
$augtypes[0b0000000000010000000000000000000] = "20"; 
$augtypes[0b0000000000001000000000000000000] = "19"; 
$augtypes[0b0000000000000100000000000000000] = "18"; 
$augtypes[0b0000000000000010000000000000000] = "17"; 
$augtypes[0b0000000000000001000000000000000] = "16"; 
$augtypes[0b0000000000000000100000000000000] = "15"; 
$augtypes[0b0000000000000000010000000000000] = "14"; 
$augtypes[0b0000000000000000001000000000000] = "13"; 
$augtypes[0b0000000000000000000100000000000] = "12"; 
$augtypes[0b0000000000000000000010000000000] = "11"; 
$augtypes[0b0000000000000000000001000000000] = "10"; 
$augtypes[0b0000000000000000000000100000000] = "9"; 
$augtypes[0b0000000000000000000000010000000] = "8"; 
$augtypes[0b0000000000000000000000001000000] = "7"; 
$augtypes[0b0000000000000000000000000100000] = "6"; 
$augtypes[0b0000000000000000000000000010000] = "5"; 
$augtypes[0b0000000000000000000000000001000] = "4"; 
$augtypes[0b0000000000000000000000000000100] = "3"; 
$augtypes[0b0000000000000000000000000000010] = "2"; 
$augtypes[0b0000000000000000000000000000001] = "1"; 
?>