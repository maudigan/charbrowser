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
 *   February 25, 2014 - add Drakkin/Powersource (Maudigan c/o Kinglykrab)
 *   September 28, 2014 - added profile class error messages (Maudigan)
 *   May 17, 2017 - added OOW flags (Maudigan)
 *   September 7, 2019 - added corruption (Kinglykrab)
 *   March 7, 2020 - added language for deleted chars and opening 
 *                   bags(Maudigan)
 *   March 9, 2020 - added language for shared bank(Maudigan)
 *   March 13, 2020 - added language for charmove result table(Maudigan)
 *   March 14, 2020 - added signature tabs(Maudigan)
 *                    added sig button to side menu
 *   March 16, 2020 - added guild page language(Maudigan)
 *   March 17, 2020 - added self version check language(Maudigan)
 *   March 22, 2020 - added config error message(Maudigan)
 *   March 23, 2020 - added base data error message(Maudigan)
 *   March 26, 2020 - added skill tab mod(Maudigan)
 *   March 26, 2020 - added server info language(Maudigan)
 *   April 2, 2020 - added store language(Maudigan)
 *   April 3, 2020 - added custom home page language(Maudigan)
 *   April 16, 2020 - added bots language(Maudigan)
 *   January 17, 2022 - Maudigan
 *     modified Vxed flags to support the data bucket changes
 *   October 19, 2022 - added leadership button 
 *                      add title bar to profile menu (Maudigan)
 *   October 24, 2022 - added spell and itemcache error messages (Maudigan)
 *                      added language for barter page
 *   October 28, 2022 - added adventure board language (maudigan)
 *   November 1, 2022 - added language for corpses page update (Maudigan)
 *   January 16, 2023 - added many missing and new messages
 *   September 2, 2023 - add column headers for stats in search window
 ***************************************************************************/ 
  
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
  
  
if ( !defined('INCHARBROWSER') ) 
{ 
        die("Hacking attempt"); 
} 
$language = array();

//pagination
$language['GOTO_PAGE'] = "Goto Page"; 
 
//header language 
$language['HEADER_GUILD'] = "Guild"; 
$language['HEADER_NAME'] = "Name"; 
$language['HEADER_SETTINGS'] = "Settings"; 
$language['HEADER_HOME'] = "Home"; 
$language['HEADER_SERVER'] = "Server"; 
$language['HEADER_BAZAAR'] = "Bazaar"; 
$language['HEADER_BARTER'] = "Barter"; 
$language['HEADER_LEADERBOARD'] = "LDON"; 
$language['HEADER_CHARMOVE'] = "CharMover"; 
$language['HEADER_SIGBUILD'] = "Signature"; 
$language['HEADER_REPORT_ERRORS'] = "Errors"; 
$language['HEADER_HELP'] = "Help"; 
$language['HEADER_NAVIGATE'] = "Navigate"; 

//page title languages 
$language['PAGE_TITLES_AAS'] ="'s Alternate Abilities"; 
$language['PAGE_TITLES_LEADERSHIP'] ="'s Leadership Abilities"; 
$language['PAGE_TITLES_BAZAAR'] ="The Bazaar"; 
$language['PAGE_TITLES_BARTER'] ="Barter";  
$language['PAGE_TITLES_ADVENTURE_LEADERBOARD'] ="Adventure Leaderboard"; 
$language['PAGE_TITLES_CHARACTER'] ="'s Profile"; 
$language['PAGE_TITLES_GUILD'] =" Guild Info"; 
$language['PAGE_TITLES_SERVER'] =" Server Info"; 
$language['PAGE_TITLES_CHARMOVE'] ="Character Mover"; 
$language['PAGE_TITLES_CORPSES'] ="'s Corpses"; 
$language['PAGE_TITLES_CORPSE'] ="'s Corpse"; 
$language['PAGE_TITLES_FACTIONS'] ="'s Factions"; 
$language['PAGE_TITLES_BOTS'] ="'s Bots"; 
$language['PAGE_TITLES_FLAGS'] ="'s Flags"; 
$language['PAGE_TITLES_HELP'] ="Help"; 
$language['PAGE_TITLES_SEARCH'] ="Profile Search Results"; 
$language['PAGE_TITLES_SETTINGS'] ="Settings"; 
$language['PAGE_TITLES_SIGBUILD'] ="Signature Builder"; 
$language['PAGE_TITLES_SKILLS'] ="'s Skills"; 
$language['PAGE_TITLES_KEYS'] ="'s Keys"; 



//charmove language 
$language['CHARMOVE_BLANKFIELDS'] = "One or more fields were left blank"; 
$language['CHARMOVE_NAME_ILLEGAL'] = "The character name contains illegal characters"; 
$language['CHARMOVE_LOGIN_ILLEGAL'] = "Login contains illegal characters"; 
$language['CHARMOVE_ZONE_ILLEGAL'] = "That zone contains illegal characters"; 
$language['CHARMOVE_BAD_ZONE'] = "That zone is not a legal selection"; 
$language['CHARMOVE_UNKNOWN_DB'] = "Unknown database error"; 
$language['CHARMOVE_BAD_NAMES'] = "Login or character name was not correct"; 
$language['CHARMOVE_MOVED'] = "%s - moved to %s"; 
$language['MESSAGE_MOVE_ARRAY_MISMATCH'] = "The number of logins, character, and zones provided doesn't match."; 
$language['CHARMOVE_CHARACTER_MOVER'] = "Character Mover"; 
$language['CHARMOVE_LOGIN'] = "Login"; 
$language['CHARMOVE_CHARNAME'] = "Name"; 
$language['CHARMOVE_ZONE'] = "Zone"; 
$language['CHARMOVE_RESULT'] = "Result"; 
$language['CHARMOVE_ADD_CHARACTER'] = "add row"; 
$language['CHARMOVE_BOOKMARK'] = "Click here to add a bookmark for this move!"; 

//signature language 
$language['SIGNATURE_NO_FILE'] = "There is no %s file named %s, and no default images could be located to replace it."; 
$language['SIGNATURE_SIGNATURE_BUILDER'] = "Signature Builder"; 
$language['SIGNATURE_NAME'] = "Name"; 
$language['SIGNATURE_FONT_ONE'] = "Name Font"; 
$language['SIGNATURE_FONT_SIZE_ONE'] = "Name Size"; 
$language['SIGNATURE_FONT_COLOR_ONE'] = "Name Color"; 
$language['SIGNATURE_FONT_SHADOW_ONE'] = "Name Shadow"; 
$language['SIGNATURE_FONT_TWO'] = "Sub Font"; 
$language['SIGNATURE_FONT_SIZE_TWO'] = "Sub Size"; 
$language['SIGNATURE_FONT_COLOR_TWO'] = "Sub Color"; 
$language['SIGNATURE_FONT_SHADOW_TWO'] = "Sub Shadow"; 
$language['SIGNATURE_EPIC_BORDER'] = "Epic BG"; 
$language['SIGNATURE_STAT_BORDER'] = "Stat BG"; 
$language['SIGNATURE_STAT_COLOR'] = "Stat Color"; 
$language['SIGNATURE_STATS'] = "Stats"; 
$language['SIGNATURE_MAIN_BORDER'] = "Border"; 
$language['SIGNATURE_MAIN_BACKGROUND'] = "Background"; 
$language['SIGNATURE_MAIN_COLOR'] = "BG Color"; 
$language['SIGNATURE_MAIN_SCREEN'] = "BG Filter"; 
$language['SIGNATURE_CREATE'] = "Create"; 
$language['SIGNATURE_PREVIEW'] = "Preview"; 
$language['SIGNATURE_BBCODE'] = "BBCode"; 
$language['SIGNATURE_HTML'] = "HTML"; 
$language['SIGNATURE_OPTION_EPIC'] = "No Epics"; 
$language['SIGNATURE_OPTION_STAT_ALL'] = "No Stats"; 
$language['SIGNATURE_OPTION_BORDER'] = "No Border"; 
$language['SIGNATURE_OPTION_STAT_IND'] = "NONE"; 
$language['SIGNATURE_OPTION_BACKGROUND'] = "No Background"; 
$language['SIGNATURE_OPTION_SCREEN'] = "No Filter"; 
$language['SIGNATURE_NEED_NAME'] = "You must at least enter a valid character name."; 
$language['SIGNATURE_TABS'] = array(
   1 => 'Character',
   2 => 'General',
   3 => 'Stats',
   4 => 'Output'
);


//index language 
$language['INDEX_INTRO'] = "<p>Check out the new Leadership Ability page. Open it using the \"Leader.\" button in the left-hand menu when viewing a character profile. It displays all your Group and Raid Abilities.</p>
<p>You can now browse the Barter Window from CharBrowser. You can access it with the \"Barter\" link in the header, or with the \"Barter\" button on the left when viewing a character profile. If you view it from a character profile it will cross-reference that character's inventory with all the items being bought, showing you just the matches. You can change/add the character being cross-referenced by adjusting the \"Seller\" field.</p>
<p>The LDON Adventure Leaderboard is now visible. You can see the generic board using the \"LDON\" link in the header, or if you'd like to see how a character stacks up to everyone else, use the \"LDON\" button in the left-hand menu when viewing a character profile. This will highlight that characters standing.</p>
<p>The corpses page has been redesigned with some new features. Each corpse shows as an avatar tile. If it's black and white, that character has been rezzed; in color means it needs a rez. If the corpse avatar shows as a gravestone, the corpse has been buried in Shadowhaven. If the avatar tile shows a round, bag icon, it means the corpse has cash or items on it. You can hover over these symbols to see a popup describing in more detail.</p>
<p>If you click on one of the corpse avatar tiles, you will now be brought to a detil view of the corpse, which shows you all the items and coin on the corpse.</p>
<p>There are also a handful of bug fixes and performance upgrades that should see improved page rendering times, particularly on the inventory page and any pages showing item popups.</p>";
$language['INDEX_VERSION'] = "Version"; 
$language['INDEX_BY'] = "By"; 

//search results language 
$language['SEARCH_RESULTS'] = "Results"; 
$language['SEARCH_LEVEL'] = "Level"; 
$language['SEARCH_CLASS'] = "Class"; 
$language["SEARCH_AA_POINTS"] = "AA";   
$language["SEARCH_HP"] = "HP"; 
$language["SEARCH_MANA"] = "Mana"; 
$language["SEARCH_ENDURANCE"] = "End";
$language["SEARCH_ATTACK"] = "ATK"; 
$language["SEARCH_AC"] = "AC"; 
$language["SEARCH_HASTE"] = "Haste"; 
$language["SEARCH_ACCURACY"] = "Accuracy"; 
$language["SEARCH_HP_REGEN"] = "HP Reg."; 
$language["SEARCH_MANA_REGEN"] = "Mana Reg."; 
$language['SEARCH_NAME'] = "Name"; 
$language['SEARCH_PREVIOUS'] = "Prev"; 
$language['SEARCH_NEXT'] = "Next"; 

//barter language 
$language['BARTER_BARTER'] = "Barter Window"; 
$language['BARTER_NAME'] = "Buyer"; 
$language['BARTER_SELLER'] = "Seller"; 
$language['BARTER_ITEM'] = "Item"; 
$language['BARTER_PRICE'] = "Price"; 
$language['BARTER_QUANTITY'] = "Qty"; 
$language['BARTER_SEARCH'] = "Search"; 
$language['BARTER_SEARCH_NAME'] = "Name"; 
$language['BARTER_SELLER_NOPERM'] = "That seller's inventory is private."; 
$language['BARTER_SELLERS_INVENTORY'] = "%s's Inventory";
$language['BARTER_MATCHING_BUYERS'] = "Matching Buyers";

//bazaar language 
$language['BAZAAR_BAZAAR'] = "The Bazaar"; 
$language['BAZAAR_NAME'] = "Seller"; 
$language['BAZAAR_ITEM'] = "Item"; 
$language['BAZAAR_PRICE'] = "Price"; 
$language['BAZAAR_SEARCH'] = "Search"; 
$language['BAZAAR_SEARCH_NAME'] = "Name"; 
$language['BAZAAR_SEARCH_PRICE_MIN'] = "Min Price"; 
$language['BAZAAR_SEARCH_PRICE_MAX'] = "Max Price"; 
$language['BAZAAR_SEARCH_CLASS'] = "Class"; 
$language['BAZAAR_SEARCH_RACE'] = "Race"; 
$language['BAZAAR_SEARCH_SLOT'] = "Slot"; 
$language['BAZAAR_SEARCH_STAT'] = "Stat"; 
$language['BAZAAR_SEARCH_TYPE'] = "Type"; 
//the following 4 arrays are for the dropdown select boxes on the bazaar search page 
$language['BAZAAR_ARRAY_SEARCH_TYPE'] = array ( 
  -1   => 'Any Type', 
  3   => '1H Blunt', 
  0   => '1H Slashing', 
  4   => '2H Blunt', 
  35   => '2H Piercing', 
  1   => '2H Slashing', 
  38   => 'Alcohol', 
  5   => 'Archery', 
  10   => 'Armor', 
  27   => 'Arrow', 
  54   => 'Augmentation', 
  18   => 'Bandages', 
  25   => 'Brass Instrument', 
  52   => 'Charm', 
  34   => 'Coin', 
  17   => 'Combinable', 
  40   => 'Compass', 
  15   => 'Drink', 
  37   => 'Fishing Bait', 
  36   => 'Fishing Pole', 
  14   => 'Food', 
  11   => 'Gems', 
  29   => 'Jewelry', 
  33   => 'Key', 
  39   => 'Key (bis)', 
  16   => 'Light', 
  12   => 'Lockpicks', 
  45   => 'Martial', 
  32   => 'Note', 
  26   => 'Percussion Instrument', 
  2   => 'Piercing', 
  42   => 'Poison', 
  21   => 'Potion', 
  20   => 'Scroll', 
  8   => 'Shield', 
  30   => 'Skull', 
  24   => 'Stringed Instrument', 
  19   => 'Throwing', 
  7   => 'Throwing range items', 
  31   => 'Tome', 
  23   => 'Wind Instrument' 
); 
$language['BAZAAR_ARRAY_SEARCH_SLOT'] = array ( 
  -1 => 'Any Slot', 
  4194304 => 'Powersource', //added 2/25/2014
  2097152 => 'Ammo', 
  1048576 => 'Waist', 
  524288 => 'Feet', 
  262144 => 'Legs', 
  131072 => 'Chest', 
  98304 => 'Fingers', 
  65536 => 'Finger', 
  32768 => 'Finger', 
  16384 => 'Secondary', 
  8192 => 'Primary', 
  4096 => 'Hands', 
  2048 => 'Range', 
  1536 => 'Wrists', 
  1024 => 'Wrist', 
  512 => 'Wrist', 
  256 => 'Back', 
  128 => 'Arms', 
  64 => 'Shoulders', 
  32 => 'Neck', 
  18 => 'Ears', 
  16 => 'Ear', 
  8 => 'Face', 
  4 => 'Head', 
  2 => 'Ear', 
  1 => 'Charm' 
); 
$language['BAZAAR_ARRAY_SEARCH_CLASS'] = array ( 
  -1 => 'Any Class', 
  1 => 'WAR', 
  2 => 'CLR', 
  4 => 'PAL', 
  8 => 'RNG', 
  16 => 'SHD', 
  32 => 'DRU', 
  64 => 'MNK', 
  128 => 'BRD', 
  256 => 'ROG', 
  512 => 'SHM', 
  1024 => 'NEC', 
  2048 => 'WIZ', 
  4096 => 'MAG', 
  8192 => 'ENC', 
  16384 => 'BST', 
  32768 => 'BER' 
); 
$language['BAZAAR_ARRAY_SEARCH_RACE'] = array ( 
  -1 => 'Any Race', 
  1 => 'HUM', 
  2 => 'BAR', 
  4 => 'ERU', 
  8 => 'ELF', 
  16 => 'HIE', 
  32 => 'DEF', 
  64 => 'HEF', 
  128 => 'DWF', 
  256 => 'TRL', 
  512 => 'OGR', 
  1024 => 'HFL', 
  2048 => 'GNM', 
  4096 => 'IKS', 
  8192 => 'VAH', 
  16384 => 'FRG',
  32768 => 'DRK' //added 2/25/2014
); 
$language['BAZAAR_ARRAY_SEARCH_STAT'] = array ( 
  -1 => 'Any Stat', 
  'ac' => 'Armor Class', 
  'aagi' => 'Agility', 
  'acha' => 'Charisma', 
  'adex' => 'Dexterity', 
  'aint' => 'Intelligence', 
  'asta' => 'Stamina', 
  'astr' => 'Strength', 
  'awis' => 'Wisdom', 
  'cr' => 'Vs Cold', 
  'dr' => 'Vs Disease', 
  'fr' => 'Vs Fire', 
  'mr' => 'Vs Magic', 
  'pr' => 'Vs Poison', 
  'hp' => 'Hit Points', 
  'mana' => 'Mana', 
  'endur' => 'Endurance', 
  'attack' => 'Attack', 
  'regen' => 'HP Regen', 
  'manaregen' => 'Mana Regen', 
  'haste' => 'Haste', 
  'damageshield' => 'Damage Shield', 
  'dsmitigation' => 'Damage Shield Mitig', 
  'healamt' => 'Heal Amount', 
  'spelldmg' => 'Spell Damage', 
  'clairvoyance' => 'Clairvoyance', 
  'heroic_agi' => 'Heroic Agility', 
  'heroic_cha' => 'Heroic Charisma', 
  'heroic_dex' => 'Heroic Dexterity', 
  'heroic_int' => 'Heroic Intelligence', 
  'heroic_sta' => 'Heroic Stamina', 
  'heroic_str' => 'Heroic Strength', 
  'heroic_wis' => 'Heroic Wisdom', 
  'backstabdmg' => 'Backstab', 
  'extradmgsamt' => 'Extra Damage'
);  

//spellcache language
$language['MESSAGE_SPELL_NOROWS'] = "Couldn't locate spell (%s).";

//itemcache language
$language['MESSAGE_ITEM_NOROWS'] = "Couldn't locate item (%s).";

//leadership language  
$language['LEADERSHIP'] = "Leadership"; 
$language['LEADERSHIP_TAB_1'] = "Group Abilities"; 
$language['LEADERSHIP_TAB_2'] = "Raid Abilities"; 
$language['GROUP_POINTS'] = "Group Points"; 
$language['RAID_POINTS'] = "Raid Points"; 
$language['GROUP_POINTS_OF'] = " of 8"; 
$language['RAID_POINTS_OF'] = " of 10"; 

//leadership aa names's
// the array index needs to match the ID of the AA
$groupaa = array();
$groupaa[0] = 'Mark NPC';
$groupaa[1] = 'NPC Health';
$groupaa[4] = 'Delegate Mark NPC';
$groupaa[6] = 'Insepect Buffs';
$groupaa[8] = 'Spell Awareness';
$groupaa[9] = 'Offense Enhancement';
$groupaa[10] = 'Mana Enhancement';
$groupaa[11] = 'Health Enhancement';
$groupaa[12] = 'Health Regeneration';
$groupaa[13] = 'Find Path to PC';
$groupaa[14] = 'Health of Target\'s Target';

$raidaa = array();
$raidaa[16] = 'Mark NPC';
$raidaa[17] = 'NPC Health';
$raidaa[19] = 'Delegate Main Assist';
$raidaa[20] = 'Delegate Mark NPC';
$raidaa[23] = 'Spell Awareness';
$raidaa[24] = 'Offense Enhancement';
$raidaa[25] = 'Mana Enhancement';
$raidaa[26] = 'Health Enhancement';
$raidaa[27] = 'Health Regeneration';
$raidaa[28] = 'Find Path to PC';
$raidaa[29] = 'Health of Target\'s Target';


//alternate abilities language 
$language['AAS_ALTERNATE_ABILITIES'] = "Alternate Abilities"; 
$language['AAS_TITLE'] = "Title"; 
$language['AAS_CUR_MAX'] = "Cur/Max"; 
$language['AAS_COST'] = "Cost"; 
$language['AAS_AA_POINTS'] = "AA Points"; 
$language['AAS_POINTS_SPENT'] = "Point Spent"; 
$language['AAS_TAB_1'] = "General"; 
$language['AAS_TAB_2'] = "Archetype"; 
$language['AAS_TAB_3'] = "Class"; 
$language['AAS_TAB_4'] = "Special"; 

//help language 
$language['HELP_HELP'] = "Help"; 
$language['HELP_VERSION'] = "Version"; 
$language['HELP_BY'] = "By"; 
$language['HELP_TEXT'] = "<h2>Note</h2> 
                          <p>Great efforts have been made for this software to function as close to <i>in-game</i> as possible. Some noteable things will be covered here, for further assitance, ask the guy sitting next to you first, if he doesnt know please visit the 'Magelo Clone' forum on MQEmulator.net.</p> 
                          <h2>Blocking your profile</h2>
                          <p>When installed this software can be setup to hide the accounts of role players and/or anonymous players. To view if either of these options are enabled click the settings link in the top right. </p>
                          <h2>Information Hiding</h2>
                          <p>Just like blocked profiles the server op can choose to block a number of other sections for ALL players such as factions, aas, skills, etc. Again, to view the settings for your server, and to see the full list of options click the settings link in the top right. </p>
                          <h2>Searching</h2>
                          <p>Searching no longer requires the use of wildcards as any spaces are now translated into wildcards. A blank search will return all characters. A search for 'lon ar' will return 'lonestar'. Search results can be sorted by clicking the column headers(only ascending order). </p>
                          <h2>Inventory, AA, Skills</h2> 
                          <p>These should all be strikingly similar to the use in game.</p>
                          <h2>Corpses</h2>
                          <p>In the rezzed column, a filled radio button indicates the corpse has been rezzed. If the radio buttons are displayed incorrectly with a white background then you are using IE and a fix for IE's problem can be found <a href='http://www.mozilla.com/en-US/'>here</a>.  The zone-name link now takes you to a page with information about the zone instead of a map. Now the [map] link will display a map and attempt to pinpoint your corpse on it for easier location. </p>
                          <h2>Flags</h2>
                          <p>The top flags box will display the flag for each zone you have access to. A filled radio button indicates you have access to that zone. If the radio buttons are displayed incorrectly with a white background then you are using IE and a fix for IE's problem can be found <a href='http://www.mozilla.com/en-US/'>here</a>.  Clicking the name on one of these flags will open a subwindow of all the flags required to get access to that zone. This truth table was created from the planar projection quest file and because of this some flags are repeated even when verified in a prerequisite. For instance Askr's flags are checked to get into bot, and then checked again to get into Elementals. </p>
                          <h2>Faction</h2>
                          <p>Faction display can vary based on the server admins settings. A basic view shows the faction name, and your faction level in text. In advanced mode your entire faction table is broken down. Base will show the starting value of the faction, char shows your modifier(from killing/questing/etc), race shows your particular races modifer(frogs hate trolls), class shows your classes modifer for that faction, and deity shows your deity's modifier.  </p>
                          "; 
                        

//factions language 
$language['FACTION_FACTIONS'] = "Factions"; 
$language['FACTION_ALLY'] = "Ally"; 
$language['FACTION_WARMLY'] = "Warmly"; 
$language['FACTION_KINDLY'] = "Kindly"; 
$language['FACTION_AMIABLE'] = "Amiable"; 
$language['FACTION_INDIFF'] = "Indifferent"; 
$language['FACTION_APPR'] = "Apprehensive"; 
$language['FACTION_DUBIOUS'] = "Dubious"; 
$language['FACTION_THREAT'] = "Threatenly"; 
$language['FACTION_SCOWLS'] = "Scowls"; 
$language['FACTION_NAME'] = "Name"; 
$language['FACTION_FACTION'] = "Faction"; 
$language['FACTION_BASE'] = "Base"; 
$language['FACTION_CHAR'] = "Char"; 
$language['FACTION_CLASS'] = "Class"; 
$language['FACTION_RACE'] = "Race"; 
$language['FACTION_DEITY'] = "Deity"; 
$language['FACTION_TOTAL'] = "Total"; 

//corpses language 
$language['CORPSES_CORPSES'] = "Corpses"; 
$language['CORPSES_STUFF'] = "This corpse has %s remaining.";
$language['CORPSES_ITEM'] = "an item";
$language['CORPSES_ITEMS'] = "items";
$language['CORPSES_COIN'] = "coin";
$language['CORPSES_AND'] = "and";
$language['CORPSES_BURIED'] = " and has been buried in Shadowrest.";
$language['CORPSES_REZZED'] = "This corpse has already been resurrected";
$language['CORPSES_UNREZZED'] = "This corpse has NOT been resurrected";

//corpse language 
$language['CORPSE_WEIGHT_MAX'] = "UNLTD."; 
$language['CORPSE_REZZED_YES'] = "Resurrected"; 
$language['CORPSE_REZZED_NO'] = "Not Resurrected"; 
$language['CORPSE_BURIED'] = "Buried"; 
$language['CORPSE_VIEW_ON_MAP'] = "View on Map"; 
$language['CORPSE_STATUS'] = "Status"; 
$language['CORPSE_BURIED_PREAMBLE'] = "This corpse is buried in "; 
$language['CORPSE_TOD'] = "Time/Place of Death";

//bots language
$language['BOTS_BOTS'] = "Bots"; 

//guild language
$language['GUILD_GUILD'] = "Guild"; 
$language['GUILD_MEMBERS'] = "Members"; 
$language['GUILD_CLASS'] = "Class"; 
$language['GUILD_CLASSES'] = "Classes"; 
$language['GUILD_LEVELS'] = "Level Distribution"; 
$language['GUILD_RACE'] = "Race"; 
$language['GUILD_RANK'] = "Rank"; 
$language['GUILD_AVG_LEVEL'] = "Avg Level"; 
$language['GUILD_LEADER'] = "Leader"; 
$language['GUILD_PERCENT'] = "Percent"; 
$language['GUILD_COUNT'] = "Count"; 
$language['GUILD_LEVEL'] = "Level"; 
$language['GUILD_NAME'] = "Name"; 




//home language
$language['HOME_HOME'] = "Home"; 
$language['HOME_COL1'] = "Col 1"; 
$language['HOME_COL2'] = "Col 2"; 
$language['HOME_COL3'] = "Col 3"; 

//adventure language
$language['ADVENTURE_LEADERBOARD'] = "Adventure Leaderboard";
$language['ADVENTURE_RANK'] = "Rank";
$language['ADVENTURE_NAME'] = "Name";
$language['ADVENTURE_SUCCESS'] = "Success";
$language['ADVENTURE_FAILURE'] = "Failure";
$language['ADVENTURE_PERCENT'] = "Percent";
$language['ADVENTURE_SELECT_CATEGORY'] = array ( 
   0  => "Total wins", 
   1  => "Total win %", 
   2  => "Deepest Guk wins", 
   3  => "Deepest Guk win %", 
   4  => "Miragul's wins", 
   5  => "Miragul's win %", 
   6  => "Mistmoore wins", 
   7  => "Mistmoore win %", 
   8  => "Rujarkian wins", 
   9  => "Rujarkian win %", 
   10 => "Takish wins", 
   11 => "Takish win %"
); 

//server language
$language['SERVER_MIN_LEVEL'] = "Minimum Level";
$language['SERVER_MAX_LEVEL'] = "Maximum Level";
$language['SERVER_AVG_LEVEL'] = "Average Level";
$language['SERVER_CHAR_COUNT'] = "Character Count";
$language['SERVER_NONE'] = "None";
$language['SERVER_SERVER'] = "Server";
$language['SERVER_CLASSES'] = "Classes";
$language['SERVER_CLASSES_CUTOFF'] = "Classes (Last %s Days)"; 
$language['SERVER_CLASS'] = "Class"; 
$language['SERVER_PERCENT'] = "Percent";
$language['SERVER_COUNT'] = "Count";   
$language['SERVER_LEVELS'] = "Level Distribution"; 
$language['SERVER_LEVELS_CUTOFF'] = "Level Distribution (Last %s Days)"; 
$language['SERVER_ALL_TIME'] = "All Time"; 
$language['SERVER_CUTOFF'] = "Last %s Days"; 
   
//key language
$language['KEYS_KEY'] = "Keys"; 

//flag section info 
$language['FLAG_FLAGS'] = "Flags"; 
$language['FLAG_PoP'] = "Planes of Power &nbsp;&nbsp;&nbsp;(click for details)"; 
$language['FLAG_GoD'] = "Gates of Discord &nbsp;&nbsp;&nbsp;(click for details)"; 
$language['FLAG_OOW'] = "Omens of War &nbsp;&nbsp;&nbsp;(click for details)"; 
$language['FLAG_DON'] = "Dragons of Norrath &nbsp;&nbsp;&nbsp;(click for details)"; 

//flags language 
$language['FLAG_PoP_PoNB'] = "Lair of Terris Thule (Plane of Nightmare B)"; 
$language['FLAG_PoP_Hedge'] = "You have killed the construct of nightmares in the Hedge event in the Plane of Nightmare."; 
$language['FLAG_PoP_PreHedge'] = "You have said 'Tortured by nightmares' to Adroha Jezith, in the Plane of Tranquility sick bay."; 
$language['FLAG_PoP_PoTactics'] = "Drunder, Fortress of Zek (Plane of Tactics)"; 
$language['FLAG_PoP_Xana'] = "(optional) You have killed Xanamech Nezmirthafen and hailed Nitram Anizok in the Plane of Innovation."; 
$language['FLAG_PoP_PreMB'] = "You have told Giwin Mirakon, 'I will test the machine' within the Plane of Innovation factory."; 
$language['FLAG_PoP_MB'] = "You have defeated the Behemoth within Plane of Innovation and then QUICKLY hailed Giwin Mirakon in the factory."; 
$language['FLAG_PoP_CoD'] = "Ruins of Lxanvom (Crypt of Decay)"; 
$language['FLAG_PoP_PreGrummus'] = "You have talked to Adler Fuirstel outside of the Plane of Disease."; 
$language['FLAG_PoP_Grummus'] = "You have defeated Grummus"; 
$language['FLAG_PoP_PostGrummus'] = "You have talked to Elder Fuirstel in the plane of Tranquility sick bay."; 
$language['FLAG_PoP_PoSPoV'] = "Plane of Valor & Plane of Storms"; 
$language['FLAG_PoP_PreTrial'] = "You have talked to Mavuin, and have agreed to plea his case to The Tribunal."; 
$language['FLAG_PoP_Trial'] = "You have showed the Tribunal the mark from the trail you have completed."; 
$language['FLAG_PoP_PostTrial'] = "You have returned to Mavuin, letting him know the tribunal will hear his case."; 
$language['FLAG_PoP_HoHA'] = "Halls of Honor"; 
$language['FLAG_PoP_AD'] = "You have defeated the prysmatic dragon, Aerin`Dar within the Plane of Valor."; 
$language['FLAG_PoP_BoT'] = "Bastion of Thunder"; 
$language['FLAG_PoP_Askr1'] = "You have shown your prowess in battle to Askr, now you must make strides to get to the Bastion of Thunder."; 
$language['FLAG_PoP_Askr2'] = "You have obtained the Talisman of Thunderous Foyer from Askr."; 
$language['FLAG_PoP_HoHB'] = "Temple of Marr"; 
$language['FLAG_PoP_Faye'] = "You have completed Trydan Faye's trial by defeating Rydda'Dar."; 
$language['FLAG_PoP_Trell'] = "You have completed Rhaliq Trell's trial by saving the villagers."; 
$language['FLAG_PoP_Garn'] = "You have completed Alekson Garn's trial by protecting the maidens."; 
$language['FLAG_PoP_PoTorment'] = "Plane of Torment"; 
$language['FLAG_PoP_TT'] = "You have killed Terris Thule."; 
$language['FLAG_PoP_PostTerris'] = "You have hailed Elder Poxbourne in the Plane of Tranquility after defeating Terris Thule."; 
$language['FLAG_PoP_Carpin'] = "You have completed the Carpryn cycle within Ruins of Lxanvom."; 
$language['FLAG_PoP_Bertox'] = "You have killed Bertox within the Crypt of Decay."; 
$language['FLAG_PoP_PostBertox'] = "You have hailed Elder Fuirstel in the Plane of Tranquility after defeating Bertox."; 
$language['FLAG_PoP_SolRoTower'] = "Tower of Solusek Ro"; 
$language['FLAG_PoP_TZ'] = "You have killed Tallon Zek."; 
$language['FLAG_PoP_VZ'] = "You have killed Vallon Zek."; 
$language['FLAG_PoP_PostSaryrn'] = "You have hailed Fahlia Shadyglade after defeating The Keeper of Sorrows and Saryrn."; 
$language['FLAG_PoP_Saryrn'] = "You have killed Saryrn."; 
$language['FLAG_PoP_MM'] = "You have defeated Lord Mithaniel Marr within his temple."; 
$language['FLAG_PoP_KoS'] = "You have killed The Keeper of Sorrows."; 
$language['FLAG_PoP_PoFire'] = "Plane of Fire"; 
$language['FLAG_PoP_RZ'] = "You have killed Ralloz Zek the Warlord."; 
$language['FLAG_PoP_Arlyxir'] = "You have defeated Arlyxir within the Tower of Solusk Ro."; 
$language['FLAG_PoP_Dresolik'] = "You have defeated The Protector of Dresolik within the Tower of Solusk Ro."; 
$language['FLAG_PoP_Jiva'] = "You have defeated Jiva within the Tower of Solusk Ro."; 
$language['FLAG_PoP_Rizlona'] = "You have defeated Rizlona within the Tower of Solusk Ro."; 
$language['FLAG_PoP_Xusl'] = "You have defeated Xuzl within the Tower of Solusk Ro."; 
$language['FLAG_PoP_SolRo'] = "You have defeated Soluesk Ro within his own tower."; 
$language['FLAG_PoP_PoAirEarthWater'] = "Planes of Air, Earth and Water"; 
$language['FLAG_PoP_Agnarr'] = "You have defeated Agnarr, the Storm Lord."; 
$language['FLAG_PoP_PreSaryrn'] = "You have said 'I will go' to Fahlia Shadyglade in the Plane of Tranquility"; 
$language['FLAG_PoP_Maelin'] = "You have spoken with the grand librarian to receive access to the Elemental Planes."; 
$language['FLAG_PoP_PoTime'] = "Plane of Time"; 
$language['FLAG_PoP_Fennin'] = "You have defeated Fennin Ro, the Tyrant of Fire."; 
$language['FLAG_PoP_Xegony'] = "You have defeated Xegony, the Queen of Air."; 
$language['FLAG_PoP_Coirnav'] = "You have defeated Coirnav, the Avatar of Water."; 
$language['FLAG_PoP_Arbitor'] = "You have defeated the arbitor within Plane of Earth A."; 
$language['FLAG_PoP_Rathe'] = "You have defeated the Rathe Council within Plane of Earth B"; 

/************* GoD Flags for Progression ************* 
   Updated by Sorvani 
   April 15, 2011 
   Complete  
******************************************************/ 
$language['FLAG_GoD_Sewer_1_1'] = "You have completed the Purifying Plant trial!"; 
$language['FLAG_GoD_Sewer_2_1'] = "You have completed the Crematory trial!"; 
$language['FLAG_GoD_Sewer_3_1'] = "You have completed the Lair of Trapped Ones trial!"; 
$language['FLAG_GoD_Sewer_4_1'] = "You have completed the Pool of Sludge trial!"; 
$language['FLAG_GoD_Sewer_1_T'] = "You have completed the Purifying Plant trial OUT OF ORDER! Talk to the scribe to fix it!"; 
$language['FLAG_GoD_Sewer_2_T'] = "You have completed the Crematory trial OUT OF ORDER! Talk to the scribe to fix it!"; 
$language['FLAG_GoD_Sewer_3_T'] = "You have completed the Lair of Trapped Ones trial OUT OF ORDER! Talk to the scribe to fix it!"; 
$language['FLAG_GoD_Sewer_4_T'] = "You have completed the Pool of Sludge trial OUT OF ORDER! Talk to the scribe to fix it!"; 
$language['FLAG_GoD_Vxed'] = "Vxed, The Crumbling Caverns"; 
$language['FLAG_GoD_Tipt'] = "Tipt, Treacherous Crags"; 
$language['FLAG_GoD_KT_1'] = "Kod'Taz, Broken Trial Grounds"; 
$language['FLAG_GoD_KT_2'] = "You have completed the 4 sewer trials or defeated Smith Rondo!"; 
$language['FLAG_GoD_KT_3'] = "You have hailed Stonespiritist Ekikoa in Vxed!"; 
$language['FLAG_GoD_KT_4'] = "You have hailed Master Stonespiritist Okkanu in Tipt!"; 
$language['FLAG_GoD_Ikky_R3'] = "(optional) Able to request the three raid trials"; 
$language['FLAG_GoD_Ikky_2'] = "You have completed the trial at the Temple of Singular Might!"; 
$language['FLAG_GoD_Ikky_3'] = "You have completed the trial at the Temple of Twin Struggles!"; 
$language['FLAG_GoD_Ikky_4'] = "You have completed the trial at the Temple of the Tri-Fates!"; 
$language['FLAG_GoD_Ikky_5'] = "You've returned four relics from the Martyrs Passage!";      
$language['FLAG_GoD_Ikky_6'] = "You've recovered important glyphs from the Temple of the Damned!";              
$language['FLAG_GoD_Ikky_7'] = "You've successfully translated the glyphs you found in the Temple of the Damned!"; 
$language['FLAG_GoD_Ikky_8'] = "You've recovered the four flesh scraps from the small temple south of the summoners!"; 
$language['FLAG_GoD_Ikky_9'] = "You've sewn the flesh scraps together to make the Sewn Flesh Parchment!"; 
$language['FLAG_GoD_Ikky_10'] = "You've found the three clues from the three trial temples!"; 
$language['FLAG_GoD_Ikky_11'] = "You've collected the Minor Relics of Power from the Pit of the Lost!"; 
$language['FLAG_GoD_Ikky_12'] = "You've rescued the artifact from the Ageless Relic Protector in the Pit of the Lost!"; 
$language['FLAG_GoD_Ikky_R4'] = "(optional) Able to request Ikkinz: Chambers of Destruction"; 
$language['FLAG_GoD_Ikky_13'] = "You have completed the three raid trials!"; 
$language['FLAG_GoD_Ikky_14'] = "You have crafted the Icon of the Altar!";  
$language['FLAG_GoD_Qvic_1'] = "Qvic, Prayer Grounds of Calling"; 
$language['FLAG_GoD_Qvic_2'] = "You have given the Sliver of the High Temple to Tublik Narwethar after defeating Vrex Barxt Qurat in Uqua."; 
$language['FLAG_GoD_Txevu_1'] = "Txevu, Lair of the Elite"; 
$language['FLAG_GoD_Txevu_2'] = "You have given the three pieces of the high temple to Brevik Kalaner."; 
/* End GoD Flags */ 

//OOW flags
$language['FLAG_OOW_MPG'] = "Muramite Proving Grounds Group Trials"; 
$language['FLAG_OOW_MPG_FEAR'] = "You have completed The Mastery of Fear trial."; 
$language['FLAG_OOW_MPG_INGENUITY'] = "You have completed The Mastery of Ingenuity trial.";
$language['FLAG_OOW_MPG_WEAPONRY'] = "You have completed The Mastery of Weaponry trial."; 
$language['FLAG_OOW_MPG_SUBVERSION'] = "You have completed The Mastery of Subversion trial."; 
$language['FLAG_OOW_MPG_EFFICIENCY'] = "You have completed The Mastery of Efficiency trial.";  
$language['FLAG_OOW_MPG_DESTRUCTION'] = "You have completed The Mastery of Destruction trial."; 
$language['FLAG_OOW_COA'] = "The Citadel of Anguish"; 
$language['FLAG_OOW_COA_HATE'] = "You have completed The Mastery of Hate trial.";
$language['FLAG_OOW_COA_ENDURANCE'] = "You have completed The Mastery of Endurance trial.";
$language['FLAG_OOW_COA_FORESIGHT'] = "You have completed The Mastery of Foresight trial.";
$language['FLAG_OOW_COA_SPECIALIZATION'] = "You have completed The Mastery of Specialization trial."; 
$language['FLAG_OOW_COA_ADAPTATION'] = "You have completed The Mastery of Adaptation trial.";
$language['FLAG_OOW_COA_CORRUPTION'] = "You have completed The Mastery of Corruption trial.";
$language['FLAG_OOW_COA_TAROMANI'] = "You have turned the seven signets into Taromani.";


//DON FLAGS 
$language['FLAG_DON_GOOD'] = "Norrath's Keepers flags"; 
$language['FLAG_DON_GOOD_1'] = "(T1) Said 'help' for access to solo quests";
$language['FLAG_DON_GOOD_2'] = "(T1) Hailed after finishing 3 solo quests for access to mission";
$language['FLAG_DON_GOOD_3'] = "(T1) Finished the group mission (Children of Gimblax)";
$language['FLAG_DON_GOOD_4'] = "(T1) Complete (hailed after completing mission)";
$language['FLAG_DON_GOOD_5'] = "(T2) Said 'work' for access to solo quests";
$language['FLAG_DON_GOOD_6'] = "(T2) Hailed after finishing 3 solo quests for access to mission and raid";
$language['FLAG_DON_GOOD_7'] = "(T2) Finished the group mission (Sickness of the Spirit)";
$language['FLAG_DON_GOOD_8'] = "(T2) Turned in 'Glowing Stone Fragment' from 'Calling Emoush' raid";
$language['FLAG_DON_GOOD_9'] = "(T2) Complete (finished group mission and turned in raid item)";
$language['FLAG_DON_GOOD_10'] = "(T3) Hailed for access to solo quests";
$language['FLAG_DON_GOOD_11'] = "(T3) Hailed after finishing 3 solo quests for access to mission and raids";
$language['FLAG_DON_GOOD_12'] = "(T3) Finished the group mission (History of the Isle)";
$language['FLAG_DON_GOOD_13'] = "(T3) Turned in 'Goblin Warlord's Beads' from 'Trial of Perseverance' raid";
$language['FLAG_DON_GOOD_14'] = "(T3) Turned in 'Lava Spider Spinners' from 'Volkara's Bite' raid";
$language['FLAG_DON_GOOD_15'] = "(T3) Complete (finished group mission and turned in both raid items)";
$language['FLAG_DON_GOOD_16'] = "(T4) Hailed for access to raids";
$language['FLAG_DON_GOOD_17'] = "(T4) Turned in 'Quintessence of Sand' from 'Guardian of the Sands' raid";
$language['FLAG_DON_GOOD_18'] = "(T4) Turned in 'Meditation Stone' from 'Goblin Dojo' raid";
$language['FLAG_DON_GOOD_19'] = "(T4) Said 'defend' for access to 'An End to the Storms' raid";
$language['FLAG_DON_GOOD_20'] = "(T4) Turned in 'Yar`lir's Fang' from 'An End to the Storms' raid";
$language['FLAG_DON_GOOD_21'] = "(T4) Complete (turned in 'Yar`lir's Fang' from 'An End To the Storms' raid)";
$language['FLAG_DON_GOOD_22'] = "(T5) Hailed for access to group mission and 'The Curse of Ju`rek' raid";
$language['FLAG_DON_GOOD_23'] = "(T5) Finished the group mission (Origins of the Curse)";
$language['FLAG_DON_GOOD_24'] = "(T5) Turned in 'Diseased Wing Fragment' from 'The Curse of Ju`rek' raid";
$language['FLAG_DON_GOOD_25'] = "(T5) Said 'dragon' for access to 'In the Shadows' raid";
$language['FLAG_DON_GOOD_26'] = "(T5) Turned in 'Shadowscale of Vishimtar' from 'In the Shadows' raid";
$language['FLAG_DON_GOOD_27'] = "(T5) Complete (turned in 'Shadowscale of Vishim_tar')";
$language['FLAG_DON_EVIL'] = "Dark Reign flags"; 
$language['FLAG_DON_EVIL_1'] = "(T1) Said 'help' for access to solo quests";
$language['FLAG_DON_EVIL_2'] = "(T1) Hailed after finishing 3 solo quests for access to mission";
$language['FLAG_DON_EVIL_3'] = "(T1) Finished the group mission (Have Note Will Travel)";
$language['FLAG_DON_EVIL_4'] = "(T1) Complete (hailed after completing mission)";
$language['FLAG_DON_EVIL_5'] = "(T2) Said 'work' for access to solo quests";
$language['FLAG_DON_EVIL_6'] = "(T2) Hailed after finishing 3 solo quests for access to mission and raid";
$language['FLAG_DON_EVIL_7'] = "(T2) Finished the group mission (Drake Eggs)";
$language['FLAG_DON_EVIL_8'] = "(T2) Turned in 'Glowing Stone Fragment' from 'Calling Emoush' raid";
$language['FLAG_DON_EVIL_9'] = "(T2) Complete (finished group mission and turned in raid item)";
$language['FLAG_DON_EVIL_10'] = "(T3) Hailed for access to solo quests";
$language['FLAG_DON_EVIL_11'] = "(T3) Hailed after finishing 3 solo quests for access to mission and raids";
$language['FLAG_DON_EVIL_12'] = "(T3) Finished the group mission (The Gilded Scroll)";
$language['FLAG_DON_EVIL_13'] = "(T3) Turned in 'Goblin Warlord's Beads' from 'Trial of Perseverance' raid";
$language['FLAG_DON_EVIL_14'] = "(T3) Turned in 'Lava Spider Spinners' from 'Volkara's Bite' raid";
$language['FLAG_DON_EVIL_15'] = "(T3) Complete (finished group mission and turned in both raid items)";
$language['FLAG_DON_EVIL_16'] = "(T4) Hailed for access to raids";
$language['FLAG_DON_EVIL_17'] = "(T4) Turned in 'Quintessence of Sand' from 'Guardian of the Sands' raid";
$language['FLAG_DON_EVIL_18'] = "(T4) Turned in 'Meditation Stone' from 'Goblin Dojo' raid";
$language['FLAG_DON_EVIL_19'] = "(T4) Said 'prove' for access to 'An End to the Storms' raid";
$language['FLAG_DON_EVIL_20'] = "(T4) Turned in 'Yar`lir's Fang' from 'An End to the Storms' raid";
$language['FLAG_DON_EVIL_21'] = "(T4) Complete (turned in 'Yar`lir's Fang' from 'An End To the Storms' raid)";
$language['FLAG_DON_EVIL_22'] = "(T5) Hailed for access to group mission and 'The Curse of Ju`rek' raid";
$language['FLAG_DON_EVIL_23'] = "(T5) Finished the group mission (Rival Party)";
$language['FLAG_DON_EVIL_24'] = "(T5) Turned in 'Diseased Wing Fragment' from 'The Curse of Ju`rek' raid";
$language['FLAG_DON_EVIL_25'] = "(T5) Finished mission and turned in raid item for access to 'In the Shadows' raid";
$language['FLAG_DON_EVIL_26'] = "(T5) Turned in 'Shadowscale of Vishimtar' from 'In the Shadows' raid";
$language['FLAG_DON_EVIL_27'] = "(T5) Complete (turned in 'Shadowscale of Vishimtar')";

//skills language 
$language['SKILLS_SKILLS'] = "Skills"; 
$language['SKILLS_TRADE'] = "Trade"; 
$language['SKILLS_OTHER'] = "Other"; 
$language['SKILLS_CLASS'] = "Class"; 
$language['SKILLS_CASTING'] = "Cast."; 
$language['SKILLS_COMBAT'] = "Combat"; 
$language['SKILLS_LANGUAGE'] = "Lang."; 

//profile menu
$language['PROFILE_MENU_TITLE'] = "Actions"; 

//settings language 
$language['SETTINGS_SETTINGS'] = "Settings"; 
$language['SETTINGS_RESULTS'] = "Search results per page"; 
$language['SETTINGS_HIGHLIGHT_GM'] = "Highlight GM Inventory"; 
$language['SETTINGS_CHARMOVE'] = "Character Mover"; 
$language['SETTINGS_GUILDVIEW'] = "Guild View"; 
$language['SETTINGS_SERVERVIEW'] = "Server View"; 
$language['SETTINGS_BAZAAR'] = "The Bazaar"; 
$language['SETTINGS_BARTER'] = "Barter Window"; 
$language['SETTINGS_ADVENTURE'] = "LDON Leaderboard";
$language['SETTINGS_USERS_GM'] = "GMs"; 
$language['SETTINGS_USERS_ANON'] = "Anonymous"; 
$language['SETTINGS_USERS_RP'] = "Role Players"; 
$language['SETTINGS_USERS_ALL'] = "Others"; 
$language['SETTINGS_USERS_PUBLIC'] = "Public"; 
$language['SETTINGS_USERS_PRIVATE'] = "Private"; 
$language['SETTINGS_INVENTORY'] = "Inv. Page"; 
$language['SETTINGS_ICOIN'] = "Inv. Coin"; 
$language['SETTINGS_BCOIN'] = "Bank Coin"; 
$language['SETTINGS_SCOIN'] = "Shrd. Coin"; 
$language['SETTINGS_BAGS'] = "Bags Area"; 
$language['SETTINGS_BANK'] = "Bank"; 
$language['SETTINGS_SHRDBANK'] = "Shrd. Bank"; 
$language['SETTINGS_CORPSES'] = "Corpses"; 
$language['SETTINGS_CORPSE'] = "Corpse Dtl."; 
$language['SETTINGS_FLAGS'] = "Flags"; 
$language['SETTINGS_AAS'] = "AAs"; 
$language['SETTINGS_LEADERSHIP'] = "Leadership"; 
$language['SETTINGS_KEYS'] = "Keys"; 
$language['SETTINGS_FACTIONS'] = "Factions"; 
$language['SETTINGS_ADVFACTIONS'] = "Adv Factions"; 
$language['SETTINGS_SKILLS'] = "Skills"; 
$language['SETTINGS_LSKILLS'] = "Lang. Skills"; 
$language['SETTINGS_SIGNATURES'] = "Signatures"; 
$language['SETTINGS_ENABLED'] = "Enabled"; 
$language['SETTINGS_DISABLED'] = "Disabled"; 
$language['SETTINGS_UPDATES_EXIST'] = "UPDATES EXIST"; 
$language['SETTINGS_DOWNLOAD'] = "Download"; 


//character 
$language['CHAR_INVENTORY'] = "Inventory"; 
$language['CHAR_BANK'] = "Bank"; 
$language['CHAR_SHARED_BANK'] = "Shared Bank"; 
$language['CHAR_CONTAINER'] = "Container"; 
$language['CHAR_REGEN'] = "Regen "; 
$language['CHAR_FT'] = "FT"; 
$language['CHAR_DS'] = "DS"; 
$language['CHAR_HASTE'] = "Haste"; 
$language['CHAR_HP'] = "HP "; 
$language['CHAR_MANA'] = "MANA"; 
$language['CHAR_ENDR'] = "ENDR"; 
$language['CHAR_AC'] = "AC"; 
$language['CHAR_MIT_AC'] = "MIT AC"; 
$language['CHAR_ATK'] = "ATK"; 
$language['CHAR_STR'] = "STR"; 
$language['CHAR_STA'] = "STA"; 
$language['CHAR_DEX'] = "DEX"; 
$language['CHAR_AGI'] = "AGI"; 
$language['CHAR_INT'] = "INT"; 
$language['CHAR_WIS'] = "WIS"; 
$language['CHAR_CHA'] = "CHA"; 
$language['CHAR_POISON'] = "POISON"; 
$language['CHAR_MAGIC'] = "MAGIC"; 
$language['CHAR_DISEASE'] = "DISEASE  "; 
$language['CHAR_FIRE'] = "FIRE"; 
$language['CHAR_COLD'] = "COLD"; 
$language['CHAR_CORRUPT'] = "CORRUPT"; 
$language['CHAR_WEIGHT'] = "WEIGHT"; 
$language['CHAR_DELETED'] = "DELETED"; 
$language['CHAR_OPEN_BAG'] = "Inspect this bag's contents"; 


//buttons 
$language['BUTTON_BACK'] = "Back"; 
$language['BUTTON_DONE'] = "Done"; 
$language['BUTTON_CLEAR'] = "Clear"; 
$language['BUTTON_INVENTORY'] = "Profile"; 
$language['BUTTON_AAS'] = "AAs"; 
$language['BUTTON_LEADERSHIP'] = "Leader."; 
$language['BUTTON_FLAGS'] = "Flags"; 
$language['BUTTON_SKILLS'] = "Skills"; 
$language['BUTTON_CORPSES'] = "Corpses"; 
$language['BUTTON_FACTION'] = "Faction"; 
$language['BUTTON_CHARMOVE'] = "Move"; 
$language['BUTTON_STORE'] = "Store";
$language['BUTTON_BARTER'] = "Barter"; 
$language['BUTTON_ADVENTURE'] = "LDON"; 
$language['BUTTON_BOTS'] = "Bots"; 
$language['BUTTON_BOOKMARK'] = "Link"; 
$language['BUTTON_SIG'] = "Sig"; 
$language['BUTTON_KEYS'] = "Keys"; 

//messages 
$language['MESSAGE_ERROR'] = "Error"; 
$language['MESSAGE_ERROR_TEMPLATE'] = "%s in %s on line %s."; 
$language['MESSAGE_LOAD_ORDER'] = "The %s class can't be loaded prior to %s."; 
$language['MESSAGE_WARNING'] = "Warning"; 
$language['MESSAGE_NOTICE'] = "Notice"; 
$language['MESSAGE_DEBUG'] = "Debug"; 
$language['MESSAGE_FATAL_ERROR'] = "Fatal Error"; 
$language['MESSAGE_PARSER_ERROR'] = "Parser Error"; 
$language['MESSAGE_DISABLED'] = "Disabled"; 
$language['MESSAGE_GENERIC'] = "A fatal error was encountered."; 
$language['MESSAGE_NO_CHAR'] = "You must specify a valid character name using only letters."; 
$language['MESSAGE_NO_BOT'] = "You must specify a valid bot name using only letters."; 
$language['MESSAGE_NO_CORPSE'] = "You must specify a valid corpse id using only numbers."; 
$language['MESSAGE_NO_GUILD'] = "You must specify a guild."; 
$language['MESSAGE_NO_RESULTS_GUILD'] = "No guilds matched your search.";
$language['MESSAGE_CORPSE_NON_NUMERIC'] = "A non numeric corpse id was provided."; 
$language['MESSAGE_NAME_ALPHA'] = "A characters name can only contain alphabetic characters."; 
$language['MESSAGE_ITEM_ALPHA'] = "An item name search can only contain alphabetic, numeric, spaces, apostrophe and dashes for security."; 
$language['MESSAGE_NO_RESULTS'] = "No characters matched your search."; 
$language['MESSAGE_NO_RESULTS_ITEMS'] = "No items matched your search."; 
$language['MESSAGE_GUILD_ALPHA'] = "A guild search can only contain alphabetic, spaces, apostrophe and dashes for security."; 
$language['MESSAGE_ORDER_ALPHA'] = "A searches order by field can only contain alphabetic characters."; 
$language['MESSAGE_START_NUMERIC'] = "A searches start field can only contain numeric characters."; 
$language['MESSAGE_PRICE_NUMERIC'] = "A searches price field can only contain numeric characters."; 
$language['MESSAGE_STAT_INVALID'] = "The selected stat is invalid."; 
$language['MESSAGE_CLASS_NUMERIC'] = "A searches class field can only contain numeric characters."; 
$language['MESSAGE_CATEGORY_INVALID'] = "The provided LDON leaderboard category is invalid."; 
$language['MESSAGE_RACE_NUMERIC'] = "A searches race field can only contain numeric characters."; 
$language['MESSAGE_SLOT_NUMERIC'] = "A searches slot field can only contain numeric characters."; 
$language['MESSAGE_TYPE_NUMERIC'] = "A searches type field can only contain numeric characters."; 
$language['MESSAGE_NO_FIND'] = "Could not find that character."; 
$language['MESSAGE_ITEM_NO_VIEW'] = "Server settings prevent you from viewing this item."; 
$language['MESSAGE_NO_CORPSES'] = "This character has no corpses"; 
$language['MESSAGE_NO_BOTS'] = "This character has no bots"; 
$language['MESSAGE_NO_KEYS'] = "This character has no keys on the keyring"; 
$language['MESSAGE_NO_GD'] = "This server does not appear to have GD installed, and it is required for image generation. Please contact your system admin."; 
$language['MESSAGE_PROF_NOKEY'] = "Profile value '%s' requested but does not exist in the locator."; //added 4 "PROF" rows for new profile class rewrite 9/26/2014
$language['MESSAGE_PROF_NOROWS'] = "The '%s' table was requested but no rows were found."; 
$language['MESSAGE_NO_BASE_DATA'] = "Failed to locate the base data for this characters class/level combination."; 
$language['MESSAGE_PROF_NOCACHE'] = "Profile value '%s' requested from the '%s' table. A matching row is cached but the '%s' column is not present.";
$language['MESSAGE_PROF_NOTABKEY'] = "Table '%s' requested but does not exist in the locator.";
$language['MESSAGE_DB_CONNECT'] = "The database host/user/password supplied were invalid.";
$language['MESSAGE_DB_NODB'] = "Could not find designated database.";
$language['MESSAGE_NOAPI'] = "The API is unavailable on this page.";
$language['MESSAGE_BAD_CONFIG'] = "<h2>Configuration Error</h2>The config version stamp in your config file does not match the stamp in your software. This likely means that you've installed a new version of the software but kept your old config file. This stamp only changes when important changes have beeen made to the config file that require generating a new config. Backup your old config.php and replace it with config.template. Then edit the new file and reset all your settings in it.";
$language['MESSAGE_ILLEGAL_PAGE'] = "You've requested a page with ilelgal characters in the name. Not cool.";
$language['MESSAGE_NO_PAGE'] = "You've requested a page that doesn't exist.";
?>