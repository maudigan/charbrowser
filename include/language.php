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
 ***************************************************************************/ 
  
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
  
  
if ( !defined('INCHARBROWSER') ) 
{ 
        die("Hacking attempt"); 
} 
$language = array(); 
//header language 
$language['HEADER_GUILD'] = "Guild"; 
$language['HEADER_NAME'] = "Name"; 
$language['HEADER_SETTINGS'] = "Settings"; 
$language['HEADER_HOME'] = "Home"; 
$language['HEADER_BAZAAR'] = "The Bazaar"; 
$language['HEADER_CHARMOVE'] = "Character Mover"; 
$language['HEADER_SIGBUILD'] = "Signature Builder"; 
$language['HEADER_REPORT_ERRORS'] = "Report Errors"; 
$language['HEADER_HELP'] = "Help"; 

//page title languages 
$language['PAGE_TITLES_AAS'] ="'s Alternate Abilities"; 
$language['PAGE_TITLES_BAZAAR'] ="The Bazaar"; 
$language['PAGE_TITLES_CHARACTER'] ="'s Profile"; 
$language['PAGE_TITLES_CHARMOVE'] ="Character Mover"; 
$language['PAGE_TITLES_CORPSE'] ="'s Corpses"; 
$language['PAGE_TITLES_FACTIONS'] ="'s Factions"; 
$language['PAGE_TITLES_FLAGS'] ="'s Flags"; 
$language['PAGE_TITLES_HELP'] ="Help"; 
$language['PAGE_TITLES_SEARCH'] ="Profile Search Results"; 
$language['PAGE_TITLES_SETTINGS'] ="Settings"; 
$language['PAGE_TITLES_SIGBUILD'] ="Signature Builder"; 
$language['PAGE_TITLES_SKILLS'] ="'s Skills"; 
$language['PAGE_TITLES_KEYS'] ="'s Keys"; 



//charmove language 
$language['CHARMOVE_CHARACTER_MOVER'] = "Character Mover"; 
$language['CHARMOVE_LOGIN'] = "Login"; 
$language['CHARMOVE_CHARNAME'] = "Name"; 
$language['CHARMOVE_ZONE'] = "Zone"; 
$language['CHARMOVE_ADD_CHARACTER'] = "add row"; 
$language['CHARMOVE_BOOKMARK'] = "Click here to add a bookmark for this move!"; 

//signature language 
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

//index language 
$language['INDEX_INTRO'] ="Welcome to the newest build of Character Browser. Many new features have been added to streamline the software to make customazation even easier than before. Languages have been modularized to better serve our growing Chinese community. Modifying the language.php file can now rapidly customize the tool in a way that used to take days. 
                           <br> 
                   <br> 
                   In addition to languages, all of the HTML has been modularized using a system you may recognize from phpbb. System admins can now edit easy to understand templates to quickly get a custom feel for their install. 
                   <br> 
                   <br> 
                   Various other navigational upgrades have been made, and security upgrades making it safer for the whole community. For more details check out the readme."; 
$language['INDEX_VERSION'] = "Version"; 
$language['INDEX_BY'] = "By"; 

//search results language 
$language['SEARCH_RESULTS'] = "Results"; 
$language['SEARCH_LEVEL'] = "Level"; 
$language['SEARCH_CLASS'] = "Class"; 
$language['SEARCH_NAME'] = "Name"; 
$language['SEARCH_PREVIOUS'] = "Prev"; 
$language['SEARCH_NEXT'] = "Next"; 

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
$language['HELP_TEXT'] = "<b>Note:</b><br> 
                          Great efforts have been made for this software to function as close to <i>in-game</i> as possible. Some noteable things will be covered here, for further assitance, ask the guy sitting next to you first, if he doesnt know please visit the 'Magelo Clone' forum on MQEmulator.net. 
                          <br><br> 
                          <b>Blocking your profile:</b><br> 
                          When installed this software can be setup to hide the accounts of role players and/or anonymous players. To view if either of these options are enabled click the settings link in the top right. 
                          <br><br> 
                          <b>Information Hiding:</b><br> 
                          Just like blocked profiles the server op can choose to block a number of other sections for ALL players such as factions, aas, skills, etc. Again, to view the settings for your server, and to see the full list of options click the settings link in the top right. 
                          <br><br> 
                          <b>Searching:</b><br> 
                          Searching no longer requires the use of wildcards as any spaces are now translated into wildcards. A blank search will return all characters. A search for 'lon ar' will return 'lonestar'. Search results can be sorted by clicking the column headers(only ascending order). 
                          <br><br> 
                          <b>Inventory, AA, Skills:</b><br> 
                          These should all be strikingly similar to the use in game.<br> 
                          <br><br> 
                          <b>Corpses:</b><br> 
                          In the rezzed column, a filled radio button indicates the corpse has been rezzed. If the radio buttons are displayed incorrectly with a white background then you are using IE and a fix for IE's problem can be found <a href='http://www.mozilla.com/en-US/'>here</a>.  The zone-name link now takes you to a page with information about the zone instead of a map. Now the [map] link will display a map and attempt to pinpoint your corpse on it for easier location. 
                          <br><br> 
                          <b>Flags:</b><br> 
                          The top flags box will display the flag for each zone you have access to. A filled radio button indicates you have access to that zone. If the radio buttons are displayed incorrectly with a white background then you are using IE and a fix for IE's problem can be found <a href='http://www.mozilla.com/en-US/'>here</a>.  Clicking the name on one of these flags will open a subwindow of all the flags required to get access to that zone. This truth table was created from the planar projection quest file and because of this some flags are repeated even when verified in a prerequisite. For instance Askr's flags are checked to get into bot, and then checked again to get into Elementals. 
                          <br><br> 
                          <b>Faction:</b><br> 
                          Faction display can vary based on the server admins settings. A basic view shows the faction name, and your faction level in text. In advanced mode your entire faction table is broken down. Base will show the starting value of the faction, char shows your modifier(from killing/questing/etc), race shows your particular races modifer(frogs hate trolls), class shows your classes modifer for that faction, and deity shows your deity's modifier.  
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

//corpse language 
$language['CORPSE_REZZED'] = "Rezurrected"; 
$language['CORPSE_TOD'] = "Time of Death"; 
$language['CORPSE_LOC'] = "Corpse Loc"; 
$language['CORPSE_MAP'] = "Map Link"; 
$language['CORPSE_CORPSES'] = "Corpses"; 

//key language
$language['KEYS_KEY'] = "Keys"; 

//flag section info 
$language['FLAG_FLAGS'] = "Flags"; 
$language['FLAG_PoP'] = "Planes of Power &nbsp;&nbsp;&nbsp;(click for details)"; 
$language['FLAG_GoD'] = "Gates of Discord &nbsp;&nbsp;&nbsp;(click for details)"; 
$language['FLAG_OOW'] = "Omens of War &nbsp;&nbsp;&nbsp;(click for details)"; 

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
$language['FLAG_GoD_Sewer_1_1'] = "You have hailed Ansharu after defeating the Ancient Kayserops in the Purifying Plant!"; 
$language['FLAG_GoD_Sewer_1_2'] = "You have hailed Scribe Gurru in Barindu after completing the Purifying Plant!"; 
$language['FLAG_GoD_Sewer_2_1'] = "You have turned in the 4 remains to to Gzifa the Pure in the Crematory!"; 
$language['FLAG_GoD_Sewer_2_2'] = "You have hailed Scribe Gurru in Barindu after completing the Crematory!"; 
$language['FLAG_GoD_Sewer_3_1'] = "You have returned Alej's tools to him in the Lair of Trapped Ones!"; 
$language['FLAG_GoD_Sewer_3_2'] = "You have hailed Scribe Gurru in Barindu after completing the Lair of Trapped Ones!"; 
$language['FLAG_GoD_Sewer_4_1'] = "You have returned the map pieces to Utandi in the Pool of Sludge!"; 
$language['FLAG_GoD_Sewer_4_2'] = "You have hailed Scribe Gurru in Barindu after completing the Pool of Sludge!"; 
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

//skills language 
$language['SKILLS_SKILLS'] = "Skills"; 
$language['SKILLS_TRADE'] = "Trade Skills"; 
$language['SKILLS_OTHER'] = "Other Skills"; 
$language['SKILLS_CLASS'] = "Class Skills"; 
$language['SKILLS_CASTING'] = "Casting Skills"; 
$language['SKILLS_COMBAT'] = "Combat Skills"; 
$language['SKILLS_LANGUAGE'] = "Language Skills"; 

//settings language 
$language['SETTINGS_SETTINGS'] = "Settings"; 
$language['SETTINGS_RESULTS'] = "Search results per page"; 
$language['SETTINGS_HIGHLIGHT_GM'] = "Highlight GM Inventory"; 
$language['SETTINGS_CHARMOVE'] = "Character Mover"; 
$language['SETTINGS_BAZAAR'] = "The Bazaar"; 
$language['SETTINGS_USERS_GM'] = "GMs"; 
$language['SETTINGS_USERS_ANON'] = "Anonymous"; 
$language['SETTINGS_USERS_RP'] = "Role Players"; 
$language['SETTINGS_USERS_ALL'] = "Others"; 
$language['SETTINGS_USERS_PUBLIC'] = "Public"; 
$language['SETTINGS_USERS_PRIVATE'] = "Private"; 
$language['SETTINGS_INVENTORY'] = "Inv. Page"; 
$language['SETTINGS_ICOIN'] = "Inv. Coin"; 
$language['SETTINGS_BCOIN'] = "Bank Coin"; 
$language['SETTINGS_BAGS'] = "Bags Area"; 
$language['SETTINGS_BANK'] = "Bank"; 
$language['SETTINGS_CORPSES'] = "Corpses"; 
$language['SETTINGS_FLAGS'] = "Flags"; 
$language['SETTINGS_AAS'] = "AAs"; 
$language['SETTINGS_KEYS'] = "Keys"; 
$language['SETTINGS_FACTIONS'] = "Factions"; 
$language['SETTINGS_ADVFACTIONS'] = "Adv Factions"; 
$language['SETTINGS_SKILLS'] = "Skills"; 
$language['SETTINGS_LSKILLS'] = "Lang. Skills"; 
$language['SETTINGS_SIGNATURES'] = "Signatures"; 
$language['SETTINGS_ENABLED'] = "Enabled"; 
$language['SETTINGS_DISABLED'] = "Disabled"; 


//character 
$language['CHAR_INVENTORY'] = "Inventory"; 
$language['CHAR_BANK'] = "Bank"; 
$language['CHAR_CONTAINER'] = "Container"; 
$language['CHAR_REGEN'] = "Regen "; 
$language['CHAR_FT'] = "FT"; 
$language['CHAR_DS'] = "DS"; 
$language['CHAR_HASTE'] = "Haste"; 
$language['CHAR_HP'] = "HP "; 
$language['CHAR_MANA'] = "MANA"; 
$language['CHAR_ENDR'] = "ENDR"; 
$language['CHAR_AC'] = "AC"; 
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
$language['CHAR_WEIGHT'] = "WEIGHT"; 


//buttons 
$language['BUTTON_BACK'] = "Back"; 
$language['BUTTON_DONE'] = "Done"; 
$language['BUTTON_INVENTORY'] = "Profile"; 
$language['BUTTON_AAS'] = "AAs"; 
$language['BUTTON_FLAGS'] = "Flags"; 
$language['BUTTON_SKILLS'] = "Skills"; 
$language['BUTTON_CORPSE'] = "Corpse"; 
$language['BUTTON_FACTION'] = "Faction"; 
$language['BUTTON_CHARMOVE'] = "Move"; 
$language['BUTTON_BOOKMARK'] = "Link"; 
$language['BUTTON_KEYS'] = "Keys"; 

//messages 
$language['MESSAGE_ERROR'] = "Error"; 
$language['MESSAGE_DISABLED'] = "Disabled"; 
$language['MESSAGE_NO_CHAR'] = "You must specify a character."; 
$language['MESSAGE_NAME_ALPHA'] = "A characters name can only contain alphabetic characters."; 
$language['MESSAGE_ITEM_ALPHA'] = "An item search can only contain alphabetic characters for security."; 
$language['MESSAGE_NO_RESULTS'] = "No characters matched your search."; 
$language['MESSAGE_NO_RESULTS_ITEMS'] = "No items matched your search."; 
$language['MESSAGE_GUILD_ALPHA'] = "A guild search can only contain alphabetic characters."; 
$language['MESSAGE_ORDER_ALPHA'] = "A searches order by field can only contain alphabetic characters."; 
$language['MESSAGE_START_NUMERIC'] = "A searches start field can only contain numeric characters."; 
$language['MESSAGE_PRICE_NUMERIC'] = "A searches price field can only contain numeric characters."; 
$language['MESSAGE_CLASS_NUMERIC'] = "A searches class field can only contain numeric characters."; 
$language['MESSAGE_RACE_NUMERIC'] = "A searches race field can only contain numeric characters."; 
$language['MESSAGE_SLOT_NUMERIC'] = "A searches slot field can only contain numeric characters."; 
$language['MESSAGE_TYPE_NUMERIC'] = "A searches type field can only contain numeric characters."; 
$language['MESSAGE_NO_FIND'] = "Could not find that character."; 
$language['MESSAGE_ITEM_NO_VIEW'] = "Server settings prevent you from viewing this item."; 
$language['MESSAGE_NO_CORPSES'] = "This character has no corpses"; 
$language['MESSAGE_NO_KEYS'] = "This character has no keys on the keyring"; 
$language['MESSAGE_NO_GD'] = "This server does not appear to have GD installed, and it is required for image generation. Please contact your system admin."; 
$language['MESSAGE_PROF_NOKEY'] = "Profile value '%s' requested but does not exist in the locator."; //added 4 "PROF" rows for new profile class rewrite 9/26/2014
$language['MESSAGE_PROF_NOROWS'] = "The '%s' table was requested but no rows were found."; 
$language['MESSAGE_PROF_NOCACHE'] = "Profile value '%s' requested from the '%s' table. A matching row is cached but the '%s' column is not present.";
$language['MESSAGE_PROF_NOTABKEY'] = "Table '%s' requested but does not exist in the locator.";
$language['MESSAGE_DB_CONNECT'] = "The database host/user/password supplied were invalid.";
$language['MESSAGE_DB_NODB'] = "Could not find designated database.";
$language['MESSAGE_NOAPI'] = "The API is unavailable on this page.";

?>