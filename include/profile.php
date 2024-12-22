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
 *   September 29, 2014 - Maudigan
 *       This is a new rewrite to accomodate the retirement of the
 *       character blob. The load function was removed and a constructor
 *       was added instead.
 *   October 4, 2014 - Maudigan
 *      renamed sql $cb_template to $query_tpl so as to not interfere with
 *      the html template object it shouldn't be a problem here, just
 *      done to be consistent.
 *   May 24, 2016 - maudigan
 *      added a second parameter to GetValue, it lets you specify the
 *      value that is returned when no record is found. The default
 *      was set to 0 for the skills.php page to display correctly
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   September 23, 2018 - Maudigan
 *      Added 2h piercing, remove traps and tripple attack.
 *   September 7, 2019 - Kinglykrab
 *      fixed typo tripple => triple
 *   March 7, 2020 - Maudigan
 *      modified to accommodate soft deletes
 *   March 8, 2020 - Maudigan
 *      make soft deletes display if this is a wrapped install
 *      and the admin flag is turned on
 *   March 24, 2020 - Maudigan
 *      got rid of all the global refs in the profile class
 *      relocated the locator inside of the class
 *      pulled the code from calculatestats.php and put it here
 *      that code was recopied from eqemu from the 20200316 build
 *   April 2, 2020 - Maudigan
 *     show stack size code
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 3, 2020 - Maudigan
 *     allow construction with id or name
 *   October 20, 2022 - Maudigan
 *     added leadership table locator data
 *   January 11, 2023 - Maudigan
 *     renamed this class to Charbrowser_Character the match the bot/corpse
 *     classes
 *     moved character permissions into this class
 *   January 16, 2023 - Maudigan
 *      added _ prefix to private properties
 *      modified contructor to fetch global vars on its own
 *
 ***************************************************************************/


if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/statsclass.php");
include_once(__DIR__ . "/spellcache.php");
include_once(__DIR__ . "/itemcache.php");

if (!defined('PROF_NOROWS')) define('PROF_NOROWS', false);


//constants to reference indexes in the locator array
if (!defined('LOCATOR_TABLE')) define('LOCATOR_TABLE',  0);
if (!defined('LOCATOR_COLUMN')) define('LOCATOR_COLUMN', 1);
if (!defined('LOCATOR_INDEX')) define('LOCATOR_INDEX',  2);


class Charbrowser_Character 
{

   // Variables
   private $_cached_tables = array();
   private $_cached_records = array();
   private $_account_id;
   private $_char_id;
   private $_race;
   private $_class;
   private $_level;
   private $_items_populated;
   private $_itemstats;
   private $_allitems;
   private $_base_data;
   private $_aa_effects = array();
   private $_permissions = false;
   
   //local references to external classes
   //imported using "global" in the constructor
   private $_error;
   private $_language;
   private $_sql;
   private $_sql_content;

   /********************************************
   **           DATA LOCATOR ARRAYS           **
   ** these describe where different types    **
   ** of character data are found             **
   ********************************************/

   // the name of the second pk of a table
   // --------------------------------------------------------------
   // SYNTAX:   "<TABLE>" => "<COLUMN>",
   // --------------------------------------------------------------
   // <TABLE>  = the name of the table
   // <COLUMN> = the name of the tables secondary pk
   private $_locator_pk = array (
      "character_alternate_abilities" => "aa_id",
      "character_skills" => "skill_id",
      "character_languages" => "lang_id",
      "character_leadership_abilities" => "slot",
      "character_buffs" => "slot_id",
   );
   
   
   
   // the name of the primary charcter id pk of tables
   // if not included, it is assumed the PK is "id"
   // --------------------------------------------------------------
   // SYNTAX:   "<TABLE>" => "<COLUMN>",
   // --------------------------------------------------------------
   // <TABLE>  = the name of the table
   // <COLUMN> = the name of the tables character id column
   private $_char_id_col = array (
      "character_stats_record" => "character_id",
      "character_buffs" => "character_id"
   );


   // the table, column, and index of where to find a value
   // --------------------------------------------------------------
   // SYNTAX:  "<DATA>" => array("<TABLE>", "<COLUMN>", "<INDEX>"),
   // --------------------------------------------------------------
   // <DATA>   = The shortname reference for the value,
   //            it usually matches the column name.
   // <TABLE>  = the name of the table the data comes from
   // <COLUMN> = the column the data appears in
   // <INDEX>  = if there are multiple rows for the character
   //            because of a second PK, then this is the
   //            value of that second PK, otherwise its false.
   private $_locator = array (
      "id" => array("character_data", "id", false),
      "account_id" => array("character_data", "account_id", false),
      "name" => array("character_data", "name", false),
      "last_name" => array("character_data", "last_name", false),
      "title" => array("character_data", "title", false),
      "suffix" => array("character_data", "suffix", false),
      "zone_id" => array("character_data", "zone_id", false),
      "zone_instance" => array("character_data", "zone_instance", false),
      "y" => array("character_data", "y", false),
      "x" => array("character_data", "x", false),
      "z" => array("character_data", "z", false),
      "heading" => array("character_data", "heading", false),
      "gender" => array("character_data", "gender", false),
      "race" => array("character_data", "race", false),
      "class" => array("character_data", "class", false),
      "level" => array("character_data", "level", false),
      "deity" => array("character_data", "deity", false),
      "birthday" => array("character_data", "birthday", false),
      "last_login" => array("character_data", "last_login", false),
      "time_played" => array("character_data", "time_played", false),
      "level2" => array("character_data", "level2", false),
      "anon" => array("character_data", "anon", false),
      "gm" => array("character_data", "gm", false),
      "face" => array("character_data", "face", false),
      "hair_color" => array("character_data", "hair_color", false),
      "hair_style" => array("character_data", "hair_style", false),
      "beard" => array("character_data", "beard", false),
      "beard_color" => array("character_data", "beard_color", false),
      "eye_color_1" => array("character_data", "eye_color_1", false),
      "eye_color_2" => array("character_data", "eye_color_2", false),
      "drakkin_heritage" => array("character_data", "drakkin_heritage", false),
      "drakkin_tattoo" => array("character_data", "drakkin_tattoo", false),
      "drakkin_details" => array("character_data", "drakkin_details", false),
      "ability_time_seconds" => array("character_data", "ability_time_seconds", false),
      "ability_number" => array("character_data", "ability_number", false),
      "ability_time_minutes" => array("character_data", "ability_time_minutes", false),
      "ability_time_hours" => array("character_data", "ability_time_hours", false),
      "exp" => array("character_data", "exp", false),
      "aa_points_spent" => array("character_data", "aa_points_spent", false),
      "aa_points_spent_old" => array("character_data", "aa_points_spent_old", false),
      "aa_exp" => array("character_data", "aa_exp", false),
      "aa_exp_old" => array("character_data", "aa_exp_old", false),
      "aa_points" => array("character_data", "aa_points", false),
      "group_leadership_exp" => array("character_data", "group_leadership_exp", false),
      "raid_leadership_exp" => array("character_data", "raid_leadership_exp", false),
      "group_leadership_points" => array("character_data", "group_leadership_points", false),
      "raid_leadership_points" => array("character_data", "raid_leadership_points", false),
      "points" => array("character_data", "points", false),
      "cur_hp" => array("character_data", "cur_hp", false),
      "mana" => array("character_data", "mana", false),
      "endurance" => array("character_data", "endurance", false),
      "intoxication" => array("character_data", "intoxication", false),
      "str" => array("character_data", "str", false),
      "sta" => array("character_data", "sta", false),
      "cha" => array("character_data", "cha", false),
      "dex" => array("character_data", "dex", false),
      "int" => array("character_data", "int", false),
      "agi" => array("character_data", "agi", false),
      "wis" => array("character_data", "wis", false),
      "zone_change_count" => array("character_data", "zone_change_count", false),
      "toxicity" => array("character_data", "toxicity", false),
      "hunger_level" => array("character_data", "hunger_level", false),
      "thirst_level" => array("character_data", "thirst_level", false),
      "ability_up" => array("character_data", "ability_up", false),
      "ldon_points_guk" => array("character_data", "ldon_points_guk", false),
      "ldon_points_mir" => array("character_data", "ldon_points_mir", false),
      "ldon_points_mmc" => array("character_data", "ldon_points_mmc", false),
      "ldon_points_ruj" => array("character_data", "ldon_points_ruj", false),
      "ldon_points_tak" => array("character_data", "ldon_points_tak", false),
      "ldon_points_available" => array("character_data", "ldon_points_available", false),
      "tribute_time_remaining" => array("character_data", "tribute_time_remaining", false),
      "career_tribute_points" => array("character_data", "career_tribute_points", false),
      "tribute_points" => array("character_data", "tribute_points", false),
      "tribute_active" => array("character_data", "tribute_active", false),
      "pvp_status" => array("character_data", "pvp_status", false),
      "pvp_kills" => array("character_data", "pvp_kills", false),
      "pvp_deaths" => array("character_data", "pvp_deaths", false),
      "pvp_current_points" => array("character_data", "pvp_current_points", false),
      "pvp_career_points" => array("character_data", "pvp_career_points", false),
      "pvp_best_kill_streak" => array("character_data", "pvp_best_kill_streak", false),
      "pvp_worst_death_streak" => array("character_data", "pvp_worst_death_streak", false),
      "pvp_current_kill_streak" => array("character_data", "pvp_current_kill_streak", false),
      "pvp2" => array("character_data", "pvp2", false),
      "pvp_type" => array("character_data", "pvp_type", false),
      "show_helm" => array("character_data", "show_helm", false),
      "group_auto_consent" => array("character_data", "group_auto_consent", false),
      "raid_auto_consent" => array("character_data", "raid_auto_consent", false),
      "guild_auto_consent" => array("character_data", "guild_auto_consent", false),
      "leadership_exp_on" => array("character_data", "leadership_exp_on", false),
      "RestTimer" => array("character_data", "RestTimer", false),
      "air_remaining" => array("character_data", "air_remaining", false),
      "autosplit_enabled" => array("character_data", "autosplit_enabled", false),
      "lfp" => array("character_data", "lfp", false),
      "lfg" => array("character_data", "lfg", false),
      "mailkey" => array("character_data", "mailkey", false),
      "xtargets" => array("character_data", "xtargets", false),
      "firstlogon" => array("character_data", "firstlogon", false),
      "e_aa_effects" => array("character_data", "e_aa_effects", false),
      "e_percent_to_aa" => array("character_data", "e_percent_to_aa", false),
      "e_expended_aa_spent" => array("character_data", "e_expended_aa_spent", false),
      "deleted_at" => array("character_data", "deleted_at", false),
      "calculated_level" => array("character_stats_record", "level", false),
      "calculated_class" => array("character_stats_record", "class", false),
      "calculated_race" => array("character_stats_record", "race", false),
      "calculated_aa_points" => array("character_stats_record", "aa_points", false),
      "calculated_hp" => array("character_stats_record", "hp", false),
      "calculated_mana" => array("character_stats_record", "mana", false),
      "calculated_endurance" => array("character_stats_record", "endurance", false),
      "calculated_ac" => array("character_stats_record", "ac", false),
      "calculated_strength" => array("character_stats_record", "strength", false),
      "calculated_stamina" => array("character_stats_record", "stamina", false),
      "calculated_dexterity" => array("character_stats_record", "dexterity", false),
      "calculated_agility" => array("character_stats_record", "agility", false),
      "calculated_intelligence" => array("character_stats_record", "intelligence", false),
      "calculated_wisdom" => array("character_stats_record", "wisdom", false),
      "calculated_charisma" => array("character_stats_record", "charisma", false),
      "calculated_magic_resist" => array("character_stats_record", "magic_resist", false),
      "calculated_fire_resist" => array("character_stats_record", "fire_resist", false),
      "calculated_cold_resist" => array("character_stats_record", "cold_resist", false),
      "calculated_poison_resist" => array("character_stats_record", "poison_resist", false),
      "calculated_disease_resist" => array("character_stats_record", "disease_resist", false),
      "calculated_corruption_resist" => array("character_stats_record", "corruption_resist", false),
      "calculated_heroic_strength" => array("character_stats_record", "heroic_strength", false),
      "calculated_heroic_stamina" => array("character_stats_record", "heroic_stamina", false),
      "calculated_heroic_dexterity" => array("character_stats_record", "heroic_dexterity", false),
      "calculated_heroic_agility" => array("character_stats_record", "heroic_agility", false),
      "calculated_heroic_intelligence" => array("character_stats_record", "heroic_intelligence", false),
      "calculated_heroic_wisdom" => array("character_stats_record", "heroic_wisdom", false),
      "calculated_heroic_charisma" => array("character_stats_record", "heroic_charisma", false),
      "calculated_heroic_magic_resist" => array("character_stats_record", "heroic_magic_resist", false),
      "calculated_heroic_fire_resist" => array("character_stats_record", "heroic_fire_resist", false),
      "calculated_heroic_cold_resist" => array("character_stats_record", "heroic_cold_resist", false),
      "calculated_heroic_poison_resist" => array("character_stats_record", "heroic_poison_resist", false),
      "calculated_heroic_disease_resist" => array("character_stats_record", "heroic_disease_resist", false),
      "calculated_heroic_corruption_resist" => array("character_stats_record", "heroic_corruption_resist", false),
      "calculated_haste" => array("character_stats_record", "haste", false),
      "calculated_accuracy" => array("character_stats_record", "accuracy", false),
      "calculated_attack" => array("character_stats_record", "attack", false),
      "calculated_avoidance" => array("character_stats_record", "avoidance", false),
      "calculated_clairvoyance" => array("character_stats_record", "clairvoyance", false),
      "calculated_combat_effects" => array("character_stats_record", "combat_effects", false),
      "calculated_damage_shield_mitigation" => array("character_stats_record", "damage_shield_mitigation", false),
      "calculated_damage_shield" => array("character_stats_record", "damage_shield", false),
      "calculated_dot_shielding" => array("character_stats_record", "dot_shielding", false),
      "calculated_hp_regen" => array("character_stats_record", "hp_regen", false),
      "calculated_mana_regen" => array("character_stats_record", "mana_regen", false),
      "calculated_endurance_regen" => array("character_stats_record", "endurance_regen", false),
      "calculated_shielding" => array("character_stats_record", "shielding", false),
      "calculated_spell_damage" => array("character_stats_record", "spell_damage", false),
      "calculated_spell_shielding" => array("character_stats_record", "spell_shielding", false),
      "calculated_strikethrough" => array("character_stats_record", "strikethrough", false),
      "calculated_stun_resist" => array("character_stats_record", "stun_resist", false),
      "calculated_backstab" => array("character_stats_record", "backstab", false),
      "calculated_wind" => array("character_stats_record", "wind", false),
      "calculated_brass" => array("character_stats_record", "brass", false),
      "calculated_string" => array("character_stats_record", "string", false),
      "calculated_percussion" => array("character_stats_record", "percussion", false),
      "calculated_singing" => array("character_stats_record", "singing", false),
      "calculated_baking" => array("character_stats_record", "baking", false),
      "calculated_alchemy" => array("character_stats_record", "alchemy", false),
      "calculated_tailoring" => array("character_stats_record", "tailoring", false),
      "calculated_blacksmithing" => array("character_stats_record", "blacksmithing", false),
      "calculated_fletching" => array("character_stats_record", "fletching", false),
      "calculated_brewing" => array("character_stats_record", "brewing", false),
      "calculated_jewelry" => array("character_stats_record", "jewelry", false),
      "calculated_pottery" => array("character_stats_record", "pottery", false),
      "calculated_research" => array("character_stats_record", "research", false),
      "calculated_alcohol" => array("character_stats_record", "alcohol", false),
      "calculated_fishing" => array("character_stats_record", "fishing", false),
      "calculated_tinkering" => array("character_stats_record", "tinkering", false),
      "calculated_created_at" => array("character_stats_record", "created_at", false),
      "calculated_updated_at" => array("character_stats_record", "updated_at", false),
      "id" => array("character_currency", "id", false),
      "platinum" => array("character_currency", "platinum", false),
      "gold" => array("character_currency", "gold", false),
      "silver" => array("character_currency", "silver", false),
      "copper" => array("character_currency", "copper", false),
      "platinum_bank" => array("character_currency", "platinum_bank", false),
      "gold_bank" => array("character_currency", "gold_bank", false),
      "silver_bank" => array("character_currency", "silver_bank", false),
      "copper_bank" => array("character_currency", "copper_bank", false),
      "platinum_cursor" => array("character_currency", "platinum_cursor", false),
      "gold_cursor" => array("character_currency", "gold_cursor", false),
      "silver_cursor" => array("character_currency", "silver_cursor", false),
      "copper_cursor" => array("character_currency", "copper_cursor", false),
      "radiant_crystals" => array("character_currency", "radiant_crystals", false),
      "career_radiant_crystals" => array("character_currency", "career_radiant_crystals", false),
      "ebon_crystals" => array("character_currency", "ebon_crystals", false),
      "career_ebon_crystals" => array("character_currency", "career_ebon_crystals", false),
      "1h_blunt" => array("character_skills", "value", 0),
      "1h_slashing" => array("character_skills", "value", 1),
      "2h_blunt" => array("character_skills", "value", 2),
      "2h_slashing" => array("character_skills", "value", 3),
      "abjuration" => array("character_skills", "value", 4),
      "alteration" => array("character_skills", "value", 5),
      "apply_poison" => array("character_skills", "value", 6),
      "archery" => array("character_skills", "value", 7),
      "backstab" => array("character_skills", "value", 8),
      "bind_wound" => array("character_skills", "value", 9),
      "bash" => array("character_skills", "value", 10),
      "block" => array("character_skills", "value", 11),
      "brass_instruments" => array("character_skills", "value", 12),
      "channeling" => array("character_skills", "value", 13),
      "conjuration" => array("character_skills", "value", 14),
      "defense" => array("character_skills", "value", 15),
      "disarm" => array("character_skills", "value", 16),
      "disarm_traps" => array("character_skills", "value", 17),
      "divination" => array("character_skills", "value", 18),
      "dodge" => array("character_skills", "value", 19),
      "double_attack" => array("character_skills", "value", 20),
      "dragon_punch" => array("character_skills", "value", 21),
      "dual_wield" => array("character_skills", "value", 22),
      "eagle_strike" => array("character_skills", "value", 23),
      "evocation" => array("character_skills", "value", 24),
      "feign_death" => array("character_skills", "value", 25),
      "flying_kick" => array("character_skills", "value", 26),
      "forage" => array("character_skills", "value", 27),
      "hand_to_hand" => array("character_skills", "value", 28),
      "hide" => array("character_skills", "value", 29),
      "kick" => array("character_skills", "value", 30),
      "meditate" => array("character_skills", "value", 31),
      "mend" => array("character_skills", "value", 32),
      "offense" => array("character_skills", "value", 33),
      "parry" => array("character_skills", "value", 34),
      "pick_lock" => array("character_skills", "value", 35),
      "piercing" => array("character_skills", "value", 36),
      "riposte" => array("character_skills", "value", 37),
      "round_kick" => array("character_skills", "value", 38),
      "safe_fall" => array("character_skills", "value", 39),
      "sense_heading" => array("character_skills", "value", 40),
      "sing" => array("character_skills", "value", 41),
      "sneak" => array("character_skills", "value", 42),
      "specialize_abjure" => array("character_skills", "value", 43),
      "specialize_alteration" => array("character_skills", "value", 44),
      "specialize_conjuration" => array("character_skills", "value", 45),
      "specialize_divinatation" => array("character_skills", "value", 46),
      "specialize_evocation" => array("character_skills", "value", 47),
      "pick_pockets" => array("character_skills", "value", 48),
      "stringed_instruments" => array("character_skills", "value", 49),
      "swimming" => array("character_skills", "value", 50),
      "throwing" => array("character_skills", "value", 51),
      "tiger_claw" => array("character_skills", "value", 52),
      "tracking" => array("character_skills", "value", 53),
      "wind_instruments" => array("character_skills", "value", 54),
      "fishing" => array("character_skills", "value", 55),
      "make_poison" => array("character_skills", "value", 56),
      "tinkering" => array("character_skills", "value", 57),
      "research" => array("character_skills", "value", 58),
      "alchemy" => array("character_skills", "value", 59),
      "baking" => array("character_skills", "value", 60),
      "tailoring" => array("character_skills", "value", 61),
      "sense_traps" => array("character_skills", "value", 62),
      "blacksmithing" => array("character_skills", "value", 63),
      "fletching" => array("character_skills", "value", 64),
      "brewing" => array("character_skills", "value", 65),
      "alcohol_tolerance" => array("character_skills", "value", 66),
      "begging" => array("character_skills", "value", 67),
      "jewelry_making" => array("character_skills", "value", 68),
      "pottery" => array("character_skills", "value", 69),
      "percussion_instruments" => array("character_skills", "value", 70),
      "intimidation" => array("character_skills", "value", 71),
      "berserking" => array("character_skills", "value", 72),
      "taunt" => array("character_skills", "value", 73),
      "frenzy" => array("character_skills", "value", 74),
      "remove_traps" => array("character_skills", "value", 75),
      "triple_attack" => array("character_skills", "value", 76),
      "2h_piercing" => array("character_skills", "value", 77),
      "common_tongue" => array("character_languages", "value", 0),
      "barbarian" => array("character_languages", "value", 1),
      "erudian" => array("character_languages", "value", 2),
      "elvish" => array("character_languages", "value", 3),
      "dark_elvish" => array("character_languages", "value", 4),
      "dwarvish" => array("character_languages", "value", 5),
      "troll" => array("character_languages", "value", 6),
      "ogre" => array("character_languages", "value", 7),
      "gnomish" => array("character_languages", "value", 8),
      "halfling" => array("character_languages", "value", 9),
      "thieves_cant" => array("character_languages", "value", 10),
      "old_erudian" => array("character_languages", "value", 11),
      "elder_elvish" => array("character_languages", "value", 12),
      "froglok" => array("character_languages", "value", 13),
      "goblin" => array("character_languages", "value", 14),
      "gnoll" => array("character_languages", "value", 15),
      "combine_tongue" => array("character_languages", "value", 16),
      "elder_teirdal" => array("character_languages", "value", 17),
      "lizardman" => array("character_languages", "value", 18),
      "orcish" => array("character_languages", "value", 19),
      "faerie" => array("character_languages", "value", 20),
      "dragon" => array("character_languages", "value", 21),
      "elder_dragon" => array("character_languages", "value", 22),
      "dark_speech" => array("character_languages", "value", 23),
      "vah_shir" => array("character_languages", "value", 24),
      "aa_id_0" => array("character_alternate_abilities", "aa_id", 0),
      "aa_id_1" => array("character_alternate_abilities", "aa_id", 1),
      "aa_id_2" => array("character_alternate_abilities", "aa_id", 2),
      "aa_id_3" => array("character_alternate_abilities", "aa_id", 3),
      "aa_id_4" => array("character_alternate_abilities", "aa_id", 4),
      "aa_id_5" => array("character_alternate_abilities", "aa_id", 5),
      "aa_id_6" => array("character_alternate_abilities", "aa_id", 6),
      "aa_id_7" => array("character_alternate_abilities", "aa_id", 7),
      "aa_id_8" => array("character_alternate_abilities", "aa_id", 8),
      "aa_id_9" => array("character_alternate_abilities", "aa_id", 9),
      "aa_id_10" => array("character_alternate_abilities", "aa_id", 10),
      "aa_id_11" => array("character_alternate_abilities", "aa_id", 11),
      "aa_id_12" => array("character_alternate_abilities", "aa_id", 12),
      "aa_id_13" => array("character_alternate_abilities", "aa_id", 13),
      "aa_id_14" => array("character_alternate_abilities", "aa_id", 14),
      "aa_id_15" => array("character_alternate_abilities", "aa_id", 15),
      "aa_id_16" => array("character_alternate_abilities", "aa_id", 16),
      "aa_id_17" => array("character_alternate_abilities", "aa_id", 17),
      "aa_id_18" => array("character_alternate_abilities", "aa_id", 18),
      "aa_id_19" => array("character_alternate_abilities", "aa_id", 19),
      "aa_id_20" => array("character_alternate_abilities", "aa_id", 20),
      "aa_id_21" => array("character_alternate_abilities", "aa_id", 21),
      "aa_id_22" => array("character_alternate_abilities", "aa_id", 22),
      "aa_id_23" => array("character_alternate_abilities", "aa_id", 23),
      "aa_id_24" => array("character_alternate_abilities", "aa_id", 24),
      "aa_id_25" => array("character_alternate_abilities", "aa_id", 25),
      "aa_id_26" => array("character_alternate_abilities", "aa_id", 26),
      "aa_id_27" => array("character_alternate_abilities", "aa_id", 27),
      "aa_id_28" => array("character_alternate_abilities", "aa_id", 28),
      "aa_id_29" => array("character_alternate_abilities", "aa_id", 29),
      "aa_id_30" => array("character_alternate_abilities", "aa_id", 30),
      "aa_id_31" => array("character_alternate_abilities", "aa_id", 31),
      "aa_id_32" => array("character_alternate_abilities", "aa_id", 32),
      "aa_id_33" => array("character_alternate_abilities", "aa_id", 33),
      "aa_id_34" => array("character_alternate_abilities", "aa_id", 34),
      "aa_id_35" => array("character_alternate_abilities", "aa_id", 35),
      "aa_id_36" => array("character_alternate_abilities", "aa_id", 36),
      "aa_id_37" => array("character_alternate_abilities", "aa_id", 37),
      "aa_id_38" => array("character_alternate_abilities", "aa_id", 38),
      "aa_id_39" => array("character_alternate_abilities", "aa_id", 39),
      "aa_id_40" => array("character_alternate_abilities", "aa_id", 40),
      "aa_id_41" => array("character_alternate_abilities", "aa_id", 41),
      "aa_id_42" => array("character_alternate_abilities", "aa_id", 42),
      "aa_id_43" => array("character_alternate_abilities", "aa_id", 43),
      "aa_id_44" => array("character_alternate_abilities", "aa_id", 44),
      "aa_id_45" => array("character_alternate_abilities", "aa_id", 45),
      "aa_id_46" => array("character_alternate_abilities", "aa_id", 46),
      "aa_id_47" => array("character_alternate_abilities", "aa_id", 47),
      "aa_id_48" => array("character_alternate_abilities", "aa_id", 48),
      "aa_id_49" => array("character_alternate_abilities", "aa_id", 49),
      "aa_id_50" => array("character_alternate_abilities", "aa_id", 50),
      "aa_id_51" => array("character_alternate_abilities", "aa_id", 51),
      "aa_id_52" => array("character_alternate_abilities", "aa_id", 52),
      "aa_id_53" => array("character_alternate_abilities", "aa_id", 53),
      "aa_id_54" => array("character_alternate_abilities", "aa_id", 54),
      "aa_id_55" => array("character_alternate_abilities", "aa_id", 55),
      "aa_id_56" => array("character_alternate_abilities", "aa_id", 56),
      "aa_id_57" => array("character_alternate_abilities", "aa_id", 57),
      "aa_id_58" => array("character_alternate_abilities", "aa_id", 58),
      "aa_id_59" => array("character_alternate_abilities", "aa_id", 59),
      "aa_id_60" => array("character_alternate_abilities", "aa_id", 60),
      "aa_id_61" => array("character_alternate_abilities", "aa_id", 61),
      "aa_id_62" => array("character_alternate_abilities", "aa_id", 62),
      "aa_id_63" => array("character_alternate_abilities", "aa_id", 63),
      "aa_id_64" => array("character_alternate_abilities", "aa_id", 64),
      "aa_id_65" => array("character_alternate_abilities", "aa_id", 65),
      "aa_id_66" => array("character_alternate_abilities", "aa_id", 66),
      "aa_id_67" => array("character_alternate_abilities", "aa_id", 67),
      "aa_id_68" => array("character_alternate_abilities", "aa_id", 68),
      "aa_id_69" => array("character_alternate_abilities", "aa_id", 69),
      "aa_id_70" => array("character_alternate_abilities", "aa_id", 70),
      "aa_id_71" => array("character_alternate_abilities", "aa_id", 71),
      "aa_id_72" => array("character_alternate_abilities", "aa_id", 72),
      "aa_id_73" => array("character_alternate_abilities", "aa_id", 73),
      "aa_id_74" => array("character_alternate_abilities", "aa_id", 74),
      "aa_id_75" => array("character_alternate_abilities", "aa_id", 75),
      "aa_id_76" => array("character_alternate_abilities", "aa_id", 76),
      "aa_id_77" => array("character_alternate_abilities", "aa_id", 77),
      "aa_id_78" => array("character_alternate_abilities", "aa_id", 78),
      "aa_id_79" => array("character_alternate_abilities", "aa_id", 79),
      "aa_id_80" => array("character_alternate_abilities", "aa_id", 80),
      "aa_id_81" => array("character_alternate_abilities", "aa_id", 81),
      "aa_id_82" => array("character_alternate_abilities", "aa_id", 82),
      "aa_id_83" => array("character_alternate_abilities", "aa_id", 83),
      "aa_id_84" => array("character_alternate_abilities", "aa_id", 84),
      "aa_id_85" => array("character_alternate_abilities", "aa_id", 85),
      "aa_id_86" => array("character_alternate_abilities", "aa_id", 86),
      "aa_id_87" => array("character_alternate_abilities", "aa_id", 87),
      "aa_id_88" => array("character_alternate_abilities", "aa_id", 88),
      "aa_id_89" => array("character_alternate_abilities", "aa_id", 89),
      "aa_id_90" => array("character_alternate_abilities", "aa_id", 90),
      "aa_id_91" => array("character_alternate_abilities", "aa_id", 91),
      "aa_id_92" => array("character_alternate_abilities", "aa_id", 92),
      "aa_id_93" => array("character_alternate_abilities", "aa_id", 93),
      "aa_id_94" => array("character_alternate_abilities", "aa_id", 94),
      "aa_id_95" => array("character_alternate_abilities", "aa_id", 95),
      "aa_id_96" => array("character_alternate_abilities", "aa_id", 96),
      "aa_id_97" => array("character_alternate_abilities", "aa_id", 97),
      "aa_id_98" => array("character_alternate_abilities", "aa_id", 98),
      "aa_id_99" => array("character_alternate_abilities", "aa_id", 99),
      "aa_id_100" => array("character_alternate_abilities", "aa_id", 100),
      "aa_id_101" => array("character_alternate_abilities", "aa_id", 101),
      "aa_id_102" => array("character_alternate_abilities", "aa_id", 102),
      "aa_id_103" => array("character_alternate_abilities", "aa_id", 103),
      "aa_id_104" => array("character_alternate_abilities", "aa_id", 104),
      "aa_id_105" => array("character_alternate_abilities", "aa_id", 105),
      "aa_id_106" => array("character_alternate_abilities", "aa_id", 106),
      "aa_id_107" => array("character_alternate_abilities", "aa_id", 107),
      "aa_id_108" => array("character_alternate_abilities", "aa_id", 108),
      "aa_id_109" => array("character_alternate_abilities", "aa_id", 109),
      "aa_id_110" => array("character_alternate_abilities", "aa_id", 110),
      "aa_id_111" => array("character_alternate_abilities", "aa_id", 111),
      "aa_id_112" => array("character_alternate_abilities", "aa_id", 112),
      "aa_id_113" => array("character_alternate_abilities", "aa_id", 113),
      "aa_id_114" => array("character_alternate_abilities", "aa_id", 114),
      "aa_id_115" => array("character_alternate_abilities", "aa_id", 115),
      "aa_id_116" => array("character_alternate_abilities", "aa_id", 116),
      "aa_id_117" => array("character_alternate_abilities", "aa_id", 117),
      "aa_id_118" => array("character_alternate_abilities", "aa_id", 118),
      "aa_id_119" => array("character_alternate_abilities", "aa_id", 119),
      "aa_id_120" => array("character_alternate_abilities", "aa_id", 120),
      "aa_id_121" => array("character_alternate_abilities", "aa_id", 121),
      "aa_id_122" => array("character_alternate_abilities", "aa_id", 122),
      "aa_id_123" => array("character_alternate_abilities", "aa_id", 123),
      "aa_id_124" => array("character_alternate_abilities", "aa_id", 124),
      "aa_id_125" => array("character_alternate_abilities", "aa_id", 125),
      "aa_id_126" => array("character_alternate_abilities", "aa_id", 126),
      "aa_id_127" => array("character_alternate_abilities", "aa_id", 127),
      "aa_id_128" => array("character_alternate_abilities", "aa_id", 128),
      "aa_id_129" => array("character_alternate_abilities", "aa_id", 129),
      "aa_id_130" => array("character_alternate_abilities", "aa_id", 130),
      "aa_id_131" => array("character_alternate_abilities", "aa_id", 131),
      "aa_id_132" => array("character_alternate_abilities", "aa_id", 132),
      "aa_id_133" => array("character_alternate_abilities", "aa_id", 133),
      "aa_id_134" => array("character_alternate_abilities", "aa_id", 134),
      "aa_id_135" => array("character_alternate_abilities", "aa_id", 135),
      "aa_id_136" => array("character_alternate_abilities", "aa_id", 136),
      "aa_id_137" => array("character_alternate_abilities", "aa_id", 137),
      "aa_id_138" => array("character_alternate_abilities", "aa_id", 138),
      "aa_id_139" => array("character_alternate_abilities", "aa_id", 139),
      "aa_id_140" => array("character_alternate_abilities", "aa_id", 140),
      "aa_id_141" => array("character_alternate_abilities", "aa_id", 141),
      "aa_id_142" => array("character_alternate_abilities", "aa_id", 142),
      "aa_id_143" => array("character_alternate_abilities", "aa_id", 143),
      "aa_id_144" => array("character_alternate_abilities", "aa_id", 144),
      "aa_id_145" => array("character_alternate_abilities", "aa_id", 145),
      "aa_id_146" => array("character_alternate_abilities", "aa_id", 146),
      "aa_id_147" => array("character_alternate_abilities", "aa_id", 147),
      "aa_id_148" => array("character_alternate_abilities", "aa_id", 148),
      "aa_id_149" => array("character_alternate_abilities", "aa_id", 149),
      "aa_id_150" => array("character_alternate_abilities", "aa_id", 150),
      "aa_id_151" => array("character_alternate_abilities", "aa_id", 151),
      "aa_id_152" => array("character_alternate_abilities", "aa_id", 152),
      "aa_id_153" => array("character_alternate_abilities", "aa_id", 153),
      "aa_id_154" => array("character_alternate_abilities", "aa_id", 154),
      "aa_id_155" => array("character_alternate_abilities", "aa_id", 155),
      "aa_id_156" => array("character_alternate_abilities", "aa_id", 156),
      "aa_id_157" => array("character_alternate_abilities", "aa_id", 157),
      "aa_id_158" => array("character_alternate_abilities", "aa_id", 158),
      "aa_id_159" => array("character_alternate_abilities", "aa_id", 159),
      "aa_id_160" => array("character_alternate_abilities", "aa_id", 160),
      "aa_id_161" => array("character_alternate_abilities", "aa_id", 161),
      "aa_id_162" => array("character_alternate_abilities", "aa_id", 162),
      "aa_id_163" => array("character_alternate_abilities", "aa_id", 163),
      "aa_id_164" => array("character_alternate_abilities", "aa_id", 164),
      "aa_id_165" => array("character_alternate_abilities", "aa_id", 165),
      "aa_id_166" => array("character_alternate_abilities", "aa_id", 166),
      "aa_id_167" => array("character_alternate_abilities", "aa_id", 167),
      "aa_id_168" => array("character_alternate_abilities", "aa_id", 168),
      "aa_id_169" => array("character_alternate_abilities", "aa_id", 169),
      "aa_id_170" => array("character_alternate_abilities", "aa_id", 170),
      "aa_id_171" => array("character_alternate_abilities", "aa_id", 171),
      "aa_id_172" => array("character_alternate_abilities", "aa_id", 172),
      "aa_id_173" => array("character_alternate_abilities", "aa_id", 173),
      "aa_id_174" => array("character_alternate_abilities", "aa_id", 174),
      "aa_id_175" => array("character_alternate_abilities", "aa_id", 175),
      "aa_id_176" => array("character_alternate_abilities", "aa_id", 176),
      "aa_id_177" => array("character_alternate_abilities", "aa_id", 177),
      "aa_id_178" => array("character_alternate_abilities", "aa_id", 178),
      "aa_id_179" => array("character_alternate_abilities", "aa_id", 179),
      "aa_id_180" => array("character_alternate_abilities", "aa_id", 180),
      "aa_id_181" => array("character_alternate_abilities", "aa_id", 181),
      "aa_id_182" => array("character_alternate_abilities", "aa_id", 182),
      "aa_id_183" => array("character_alternate_abilities", "aa_id", 183),
      "aa_id_184" => array("character_alternate_abilities", "aa_id", 184),
      "aa_id_185" => array("character_alternate_abilities", "aa_id", 185),
      "aa_id_186" => array("character_alternate_abilities", "aa_id", 186),
      "aa_id_187" => array("character_alternate_abilities", "aa_id", 187),
      "aa_id_188" => array("character_alternate_abilities", "aa_id", 188),
      "aa_id_189" => array("character_alternate_abilities", "aa_id", 189),
      "aa_id_190" => array("character_alternate_abilities", "aa_id", 190),
      "aa_id_191" => array("character_alternate_abilities", "aa_id", 191),
      "aa_id_192" => array("character_alternate_abilities", "aa_id", 192),
      "aa_id_193" => array("character_alternate_abilities", "aa_id", 193),
      "aa_id_194" => array("character_alternate_abilities", "aa_id", 194),
      "aa_id_195" => array("character_alternate_abilities", "aa_id", 195),
      "aa_id_196" => array("character_alternate_abilities", "aa_id", 196),
      "aa_id_197" => array("character_alternate_abilities", "aa_id", 197),
      "aa_id_198" => array("character_alternate_abilities", "aa_id", 198),
      "aa_id_199" => array("character_alternate_abilities", "aa_id", 199),
      "aa_value_0" => array("character_alternate_abilities", "aa_value", 0),
      "aa_value_1" => array("character_alternate_abilities", "aa_value", 1),
      "aa_value_2" => array("character_alternate_abilities", "aa_value", 2),
      "aa_value_3" => array("character_alternate_abilities", "aa_value", 3),
      "aa_value_4" => array("character_alternate_abilities", "aa_value", 4),
      "aa_value_5" => array("character_alternate_abilities", "aa_value", 5),
      "aa_value_6" => array("character_alternate_abilities", "aa_value", 6),
      "aa_value_7" => array("character_alternate_abilities", "aa_value", 7),
      "aa_value_8" => array("character_alternate_abilities", "aa_value", 8),
      "aa_value_9" => array("character_alternate_abilities", "aa_value", 9),
      "aa_value_10" => array("character_alternate_abilities", "aa_value", 10),
      "aa_value_11" => array("character_alternate_abilities", "aa_value", 11),
      "aa_value_12" => array("character_alternate_abilities", "aa_value", 12),
      "aa_value_13" => array("character_alternate_abilities", "aa_value", 13),
      "aa_value_14" => array("character_alternate_abilities", "aa_value", 14),
      "aa_value_15" => array("character_alternate_abilities", "aa_value", 15),
      "aa_value_16" => array("character_alternate_abilities", "aa_value", 16),
      "aa_value_17" => array("character_alternate_abilities", "aa_value", 17),
      "aa_value_18" => array("character_alternate_abilities", "aa_value", 18),
      "aa_value_19" => array("character_alternate_abilities", "aa_value", 19),
      "aa_value_20" => array("character_alternate_abilities", "aa_value", 20),
      "aa_value_21" => array("character_alternate_abilities", "aa_value", 21),
      "aa_value_22" => array("character_alternate_abilities", "aa_value", 22),
      "aa_value_23" => array("character_alternate_abilities", "aa_value", 23),
      "aa_value_24" => array("character_alternate_abilities", "aa_value", 24),
      "aa_value_25" => array("character_alternate_abilities", "aa_value", 25),
      "aa_value_26" => array("character_alternate_abilities", "aa_value", 26),
      "aa_value_27" => array("character_alternate_abilities", "aa_value", 27),
      "aa_value_28" => array("character_alternate_abilities", "aa_value", 28),
      "aa_value_29" => array("character_alternate_abilities", "aa_value", 29),
      "aa_value_30" => array("character_alternate_abilities", "aa_value", 30),
      "aa_value_31" => array("character_alternate_abilities", "aa_value", 31),
      "aa_value_32" => array("character_alternate_abilities", "aa_value", 32),
      "aa_value_33" => array("character_alternate_abilities", "aa_value", 33),
      "aa_value_34" => array("character_alternate_abilities", "aa_value", 34),
      "aa_value_35" => array("character_alternate_abilities", "aa_value", 35),
      "aa_value_36" => array("character_alternate_abilities", "aa_value", 36),
      "aa_value_37" => array("character_alternate_abilities", "aa_value", 37),
      "aa_value_38" => array("character_alternate_abilities", "aa_value", 38),
      "aa_value_39" => array("character_alternate_abilities", "aa_value", 39),
      "aa_value_40" => array("character_alternate_abilities", "aa_value", 40),
      "aa_value_41" => array("character_alternate_abilities", "aa_value", 41),
      "aa_value_42" => array("character_alternate_abilities", "aa_value", 42),
      "aa_value_43" => array("character_alternate_abilities", "aa_value", 43),
      "aa_value_44" => array("character_alternate_abilities", "aa_value", 44),
      "aa_value_45" => array("character_alternate_abilities", "aa_value", 45),
      "aa_value_46" => array("character_alternate_abilities", "aa_value", 46),
      "aa_value_47" => array("character_alternate_abilities", "aa_value", 47),
      "aa_value_48" => array("character_alternate_abilities", "aa_value", 48),
      "aa_value_49" => array("character_alternate_abilities", "aa_value", 49),
      "aa_value_50" => array("character_alternate_abilities", "aa_value", 50),
      "aa_value_51" => array("character_alternate_abilities", "aa_value", 51),
      "aa_value_52" => array("character_alternate_abilities", "aa_value", 52),
      "aa_value_53" => array("character_alternate_abilities", "aa_value", 53),
      "aa_value_54" => array("character_alternate_abilities", "aa_value", 54),
      "aa_value_55" => array("character_alternate_abilities", "aa_value", 55),
      "aa_value_56" => array("character_alternate_abilities", "aa_value", 56),
      "aa_value_57" => array("character_alternate_abilities", "aa_value", 57),
      "aa_value_58" => array("character_alternate_abilities", "aa_value", 58),
      "aa_value_59" => array("character_alternate_abilities", "aa_value", 59),
      "aa_value_60" => array("character_alternate_abilities", "aa_value", 60),
      "aa_value_61" => array("character_alternate_abilities", "aa_value", 61),
      "aa_value_62" => array("character_alternate_abilities", "aa_value", 62),
      "aa_value_63" => array("character_alternate_abilities", "aa_value", 63),
      "aa_value_64" => array("character_alternate_abilities", "aa_value", 64),
      "aa_value_65" => array("character_alternate_abilities", "aa_value", 65),
      "aa_value_66" => array("character_alternate_abilities", "aa_value", 66),
      "aa_value_67" => array("character_alternate_abilities", "aa_value", 67),
      "aa_value_68" => array("character_alternate_abilities", "aa_value", 68),
      "aa_value_69" => array("character_alternate_abilities", "aa_value", 69),
      "aa_value_70" => array("character_alternate_abilities", "aa_value", 70),
      "aa_value_71" => array("character_alternate_abilities", "aa_value", 71),
      "aa_value_72" => array("character_alternate_abilities", "aa_value", 72),
      "aa_value_73" => array("character_alternate_abilities", "aa_value", 73),
      "aa_value_74" => array("character_alternate_abilities", "aa_value", 74),
      "aa_value_75" => array("character_alternate_abilities", "aa_value", 75),
      "aa_value_76" => array("character_alternate_abilities", "aa_value", 76),
      "aa_value_77" => array("character_alternate_abilities", "aa_value", 77),
      "aa_value_78" => array("character_alternate_abilities", "aa_value", 78),
      "aa_value_79" => array("character_alternate_abilities", "aa_value", 79),
      "aa_value_80" => array("character_alternate_abilities", "aa_value", 80),
      "aa_value_81" => array("character_alternate_abilities", "aa_value", 81),
      "aa_value_82" => array("character_alternate_abilities", "aa_value", 82),
      "aa_value_83" => array("character_alternate_abilities", "aa_value", 83),
      "aa_value_84" => array("character_alternate_abilities", "aa_value", 84),
      "aa_value_85" => array("character_alternate_abilities", "aa_value", 85),
      "aa_value_86" => array("character_alternate_abilities", "aa_value", 86),
      "aa_value_87" => array("character_alternate_abilities", "aa_value", 87),
      "aa_value_88" => array("character_alternate_abilities", "aa_value", 88),
      "aa_value_89" => array("character_alternate_abilities", "aa_value", 89),
      "aa_value_90" => array("character_alternate_abilities", "aa_value", 90),
      "aa_value_91" => array("character_alternate_abilities", "aa_value", 91),
      "aa_value_92" => array("character_alternate_abilities", "aa_value", 92),
      "aa_value_93" => array("character_alternate_abilities", "aa_value", 93),
      "aa_value_94" => array("character_alternate_abilities", "aa_value", 94),
      "aa_value_95" => array("character_alternate_abilities", "aa_value", 95),
      "aa_value_96" => array("character_alternate_abilities", "aa_value", 96),
      "aa_value_97" => array("character_alternate_abilities", "aa_value", 97),
      "aa_value_98" => array("character_alternate_abilities", "aa_value", 98),
      "aa_value_99" => array("character_alternate_abilities", "aa_value", 99),
      "aa_value_100" => array("character_alternate_abilities", "aa_value", 100),
      "aa_value_101" => array("character_alternate_abilities", "aa_value", 101),
      "aa_value_102" => array("character_alternate_abilities", "aa_value", 102),
      "aa_value_103" => array("character_alternate_abilities", "aa_value", 103),
      "aa_value_104" => array("character_alternate_abilities", "aa_value", 104),
      "aa_value_105" => array("character_alternate_abilities", "aa_value", 105),
      "aa_value_106" => array("character_alternate_abilities", "aa_value", 106),
      "aa_value_107" => array("character_alternate_abilities", "aa_value", 107),
      "aa_value_108" => array("character_alternate_abilities", "aa_value", 108),
      "aa_value_109" => array("character_alternate_abilities", "aa_value", 109),
      "aa_value_110" => array("character_alternate_abilities", "aa_value", 110),
      "aa_value_111" => array("character_alternate_abilities", "aa_value", 111),
      "aa_value_112" => array("character_alternate_abilities", "aa_value", 112),
      "aa_value_113" => array("character_alternate_abilities", "aa_value", 113),
      "aa_value_114" => array("character_alternate_abilities", "aa_value", 114),
      "aa_value_115" => array("character_alternate_abilities", "aa_value", 115),
      "aa_value_116" => array("character_alternate_abilities", "aa_value", 116),
      "aa_value_117" => array("character_alternate_abilities", "aa_value", 117),
      "aa_value_118" => array("character_alternate_abilities", "aa_value", 118),
      "aa_value_119" => array("character_alternate_abilities", "aa_value", 119),
      "aa_value_120" => array("character_alternate_abilities", "aa_value", 120),
      "aa_value_121" => array("character_alternate_abilities", "aa_value", 121),
      "aa_value_122" => array("character_alternate_abilities", "aa_value", 122),
      "aa_value_123" => array("character_alternate_abilities", "aa_value", 123),
      "aa_value_124" => array("character_alternate_abilities", "aa_value", 124),
      "aa_value_125" => array("character_alternate_abilities", "aa_value", 125),
      "aa_value_126" => array("character_alternate_abilities", "aa_value", 126),
      "aa_value_127" => array("character_alternate_abilities", "aa_value", 127),
      "aa_value_128" => array("character_alternate_abilities", "aa_value", 128),
      "aa_value_129" => array("character_alternate_abilities", "aa_value", 129),
      "aa_value_130" => array("character_alternate_abilities", "aa_value", 130),
      "aa_value_131" => array("character_alternate_abilities", "aa_value", 131),
      "aa_value_132" => array("character_alternate_abilities", "aa_value", 132),
      "aa_value_133" => array("character_alternate_abilities", "aa_value", 133),
      "aa_value_134" => array("character_alternate_abilities", "aa_value", 134),
      "aa_value_135" => array("character_alternate_abilities", "aa_value", 135),
      "aa_value_136" => array("character_alternate_abilities", "aa_value", 136),
      "aa_value_137" => array("character_alternate_abilities", "aa_value", 137),
      "aa_value_138" => array("character_alternate_abilities", "aa_value", 138),
      "aa_value_139" => array("character_alternate_abilities", "aa_value", 139),
      "aa_value_140" => array("character_alternate_abilities", "aa_value", 140),
      "aa_value_141" => array("character_alternate_abilities", "aa_value", 141),
      "aa_value_142" => array("character_alternate_abilities", "aa_value", 142),
      "aa_value_143" => array("character_alternate_abilities", "aa_value", 143),
      "aa_value_144" => array("character_alternate_abilities", "aa_value", 144),
      "aa_value_145" => array("character_alternate_abilities", "aa_value", 145),
      "aa_value_146" => array("character_alternate_abilities", "aa_value", 146),
      "aa_value_147" => array("character_alternate_abilities", "aa_value", 147),
      "aa_value_148" => array("character_alternate_abilities", "aa_value", 148),
      "aa_value_149" => array("character_alternate_abilities", "aa_value", 149),
      "aa_value_150" => array("character_alternate_abilities", "aa_value", 150),
      "aa_value_151" => array("character_alternate_abilities", "aa_value", 151),
      "aa_value_152" => array("character_alternate_abilities", "aa_value", 152),
      "aa_value_153" => array("character_alternate_abilities", "aa_value", 153),
      "aa_value_154" => array("character_alternate_abilities", "aa_value", 154),
      "aa_value_155" => array("character_alternate_abilities", "aa_value", 155),
      "aa_value_156" => array("character_alternate_abilities", "aa_value", 156),
      "aa_value_157" => array("character_alternate_abilities", "aa_value", 157),
      "aa_value_158" => array("character_alternate_abilities", "aa_value", 158),
      "aa_value_159" => array("character_alternate_abilities", "aa_value", 159),
      "aa_value_160" => array("character_alternate_abilities", "aa_value", 160),
      "aa_value_161" => array("character_alternate_abilities", "aa_value", 161),
      "aa_value_162" => array("character_alternate_abilities", "aa_value", 162),
      "aa_value_163" => array("character_alternate_abilities", "aa_value", 163),
      "aa_value_164" => array("character_alternate_abilities", "aa_value", 164),
      "aa_value_165" => array("character_alternate_abilities", "aa_value", 165),
      "aa_value_166" => array("character_alternate_abilities", "aa_value", 166),
      "aa_value_167" => array("character_alternate_abilities", "aa_value", 167),
      "aa_value_168" => array("character_alternate_abilities", "aa_value", 168),
      "aa_value_169" => array("character_alternate_abilities", "aa_value", 169),
      "aa_value_170" => array("character_alternate_abilities", "aa_value", 170),
      "aa_value_171" => array("character_alternate_abilities", "aa_value", 171),
      "aa_value_172" => array("character_alternate_abilities", "aa_value", 172),
      "aa_value_173" => array("character_alternate_abilities", "aa_value", 173),
      "aa_value_174" => array("character_alternate_abilities", "aa_value", 174),
      "aa_value_175" => array("character_alternate_abilities", "aa_value", 175),
      "aa_value_176" => array("character_alternate_abilities", "aa_value", 176),
      "aa_value_177" => array("character_alternate_abilities", "aa_value", 177),
      "aa_value_178" => array("character_alternate_abilities", "aa_value", 178),
      "aa_value_179" => array("character_alternate_abilities", "aa_value", 179),
      "aa_value_180" => array("character_alternate_abilities", "aa_value", 180),
      "aa_value_181" => array("character_alternate_abilities", "aa_value", 181),
      "aa_value_182" => array("character_alternate_abilities", "aa_value", 182),
      "aa_value_183" => array("character_alternate_abilities", "aa_value", 183),
      "aa_value_184" => array("character_alternate_abilities", "aa_value", 184),
      "aa_value_185" => array("character_alternate_abilities", "aa_value", 185),
      "aa_value_186" => array("character_alternate_abilities", "aa_value", 186),
      "aa_value_187" => array("character_alternate_abilities", "aa_value", 187),
      "aa_value_188" => array("character_alternate_abilities", "aa_value", 188),
      "aa_value_189" => array("character_alternate_abilities", "aa_value", 189),
      "aa_value_190" => array("character_alternate_abilities", "aa_value", 190),
      "aa_value_191" => array("character_alternate_abilities", "aa_value", 191),
      "aa_value_192" => array("character_alternate_abilities", "aa_value", 192),
      "aa_value_193" => array("character_alternate_abilities", "aa_value", 193),
      "aa_value_194" => array("character_alternate_abilities", "aa_value", 194),
      "aa_value_195" => array("character_alternate_abilities", "aa_value", 195),
      "aa_value_196" => array("character_alternate_abilities", "aa_value", 196),
      "aa_value_197" => array("character_alternate_abilities", "aa_value", 197),
      "aa_value_198" => array("character_alternate_abilities", "aa_value", 198),
      "aa_value_199" => array("character_alternate_abilities", "aa_value", 199),
   );

   /********************************************
   **              CONSTRUCTOR                **
   ********************************************/
   // get the basic data, like char id.
   function __construct($name, $showsoftdelete = false, $charbrowser_is_admin_page = false)
   {      
      global $permissions;
      global $charbrowser_is_admin_page;

      global $cb_error;
      global $language;
      global $cbsql;
      global $cbsql_content;
      
      //make sure the error class exists, store pointer
      if (!isset($cb_error)) 
      {
         die("The Charbrowser_Character class can't be initialized prior to the error class (error.php) being created.");
      }
      else
      {
         $this->_error = $cb_error;
      }
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_Character class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //make sure the database classes exist, store pointers
      if (!isset($cbsql)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Character', 'db.php'));
      }
      else
      {
         $this->_sql = $cbsql;
      }
      if (!isset($cbsql_content)) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Character', 'db.php'));
      }
      else
      {
         $this->_sql_content = $cbsql_content;
      }
      
      //dont load characters items until we need to
      $this->_items_populated = false;

      //we can't call the local query method as it assumes the character id
      //which we need to get in the first place
      $table_name = "character_data";

      //don't go sticking just anything in the database, check for alpha or numeric since we may get an id or name
      $name = preg_validate($name, '/^[a-zA-Z0-9]*$/', false);
      if (!$name) $this->_error->message_die($this->_language['MESSAGE_ERROR'],$this->_language['MESSAGE_NAME_ALPHA']);

      //initializing with name or id?
      if (is_numeric($name)) {
         $column_name = 'id';
      }
      else {
         $column_name = 'name';
      }
      
      //build the query
      $tpl = <<<TPL
SELECT * 
FROM `%s` 
WHERE `%s` = '%s'
TPL;
      $query = sprintf($tpl, $table_name, $column_name, $name);

      //get the result/error
      $result = $this->_sql->query($query);

      //collect the data from returned row
      if($this->_sql->rows($result))
      {
         //fetch the row
         $row = $this->_sql->nextrow($result);
         //save it
         $this->_cached_records[$table_name] = $row;
         $this->_account_id = $row['account_id'];
         $this->_char_id = $row['id'];
         $this->_race = $row['race'];
         $this->_class = $row['class'];
         $this->_level = $row['level'];
      }
      else $this->_error->message_die($this->_language['MESSAGE_ERROR'],$this->_language['MESSAGE_NO_FIND']);

      //dont display deleted characters
      if (!$showsoftdelete && !$charbrowser_is_admin_page && $row['deleted_at']) 
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'],$this->_language['MESSAGE_NO_FIND']);
      }

      //INITIALIZE THE PERMISSIONS FOR THIS CHARACTER
      //if your wrap charbrowser in your own sites header
      //and footer. You can have your site override the
      //default permissions to always be enabled by setting 
      //$charbrowser_is_admin_page = true;
      //the intent of this is for charbrowser to inherit
      //your sites admin privileges
      //if it's set, return a permission array with 
      //everything enabled
      if ($charbrowser_is_admin_page) {
         $this->_permissions = array(
            'inventory'         => 0,
            'coininventory'     => 0,
            'coinbank'          => 0,
            'coinsharedbank'    => 0,
            'bags'              => 0,
            'bank'              => 0,
            'sharedbank'        => 0,
            'corpses'           => 0,
            'corpse'            => 0,
            'bots'              => 0,
            'bot'               => 0,
            'flags'             => 0,
            'AAs'               => 0,
            'leadership'        => 0,
            'factions'          => 0,
            'advfactions'       => 0,
            'skills'            => 0,
            'languageskills'    => 0,
            'keys'              => 0,
            'signatures'        => 0);
      }
      
      //if not admin, determine it based on their account state
      else
      {
         
         $tpl = <<<TPL
SELECT `value`
FROM `quest_globals` 
WHERE `charid` = %d 
AND `name` = 'charbrowser_profile';
TPL;
         $query = sprintf($tpl, $this->_char_id);
         $result = $this->_sql->query($query);
         
         //first try to set their permissions based on their
         //settings from the charbrowser quest NPC--which may
         //or may not exist
         if($this->_sql->rows($result))
         { 
            $questrow = $this->_sql->nextrow($result);
            if ($questrow['value'] == 1) 
            {
               $this->_permissions = $permissions['PUBLIC'];
            }
            elseif ($questrow['value'] == 2) 
            {
               $this->_permissions = $permissions['PRIVATE'];
            }
         }
         
         //if that didn't set their permissions default to using
         //GM/ROLEPLAY/ANON as a guide
         if ($this->_permissions === false)
         {
            if ($this->GetValue('gm')) 
            {
               $this->_permissions = $permissions['GM'];
            }
            elseif ($this->GetValue('anon') == 2)  
            {
               $this->_permissions = $permissions['ROLEPLAY'];
            }
            elseif ($this->GetValue('anon') == 1)  
            {
               $this->_permissions = $permissions['ANON'];
            }
            else
            {
               $this->_permissions = $permissions['ALL'];
            }
         }
      }
   }
   


   /********************************************
   **              DESTRUCTOR                 **
   ********************************************/
   function __destruct()
   {
      unset($this->_sql);
      unset($this->_language);
   }


   /********************************************
   **            PUBLIC FUNCTIONS             **
   ********************************************/

   //returns if anyone is allowed to view a named aspect
   //of this characters profile, blocked = 1, allowed = 0
   //these correlate to the permission arrays in the config
   function Permission($key)
   {
      //default to blocked
      if (!is_array($this->_permissions)) return 1;

      //if a bogus value is requested, block it
      if (!array_key_exists($key, $this->_permissions)) return 1;
      
      return $this->_permissions[$key];
   }
   
   // Return Account ID
   public function accountid()
   {
      return $this->_account_id;
   }


   // Return char ID
   public function char_id()
   {
      return $this->_char_id;
   }

   //gets all the records for a double pk character from a table
   public function GetTable($table_name)
   {
      //we don't need to clean the name up before
      //handing it to the private function because
      //it has to bee in the locator arrays
      return $this->_getTableCache($table_name);
   }

   //gets a single record for a character from a table
   public function GetRecord($table_name)
   {
      //table name goes straight into a query
      // so we need to escape it
      $table_name = $this->_sql->escape_string($table_name);

      return $this->_getRecordCache($table_name);
   }

   //uses the locator data to find the requested setting
   public function GetValue($data_key, $default = 0)
   {
      return $this->_getValue($data_key, $default);
   }
   

   //return array of all the items for this character
   public function GetAllItems()
   {
      $this->_populateItems();
      return $this->_allitems;
   }


   //get stats including items
   public function getSTR()
   {
      return $this->_getValue('calculated_strength', 0);
   }

   public function getSTA()
   {
      return $this->_getValue('calculated_stamina', 0);
   }

   public function getDEX()
   {
      return $this->_getValue('calculated_dexterity', 0);
   }

   public function getAGI()
   {
      return $this->_getValue('calculated_agility', 0);
   }

   public function getINT()
   {
      return $this->_getValue('calculated_intelligence', 0);
   }

   public function getWIS()
   {
      return $this->_getValue('calculated_wisdom', 0);
   }

   public function getCHA()
   {
      return $this->_getValue('calculated_charisma', 0);
   }

   public function getHSTR()
   {
      return $this->_getValue('calculated_heroic_strength', 0);
   }

   public function getHSTA()
   {
      return $this->_getValue('calculated_heroic_stamina', 0);
   }

   public function getHDEX()
   {
      return $this->_getValue('calculated_heroic_dexterity', 0);
   }

   public function getHAGI()
   {
      return $this->_getValue('calculated_heroic_agility', 0);
   }

   public function getHINT()
   {
      return $this->_getValue('calculated_heroic_intelligence', 0);
   }

   public function getHWIS()
   {
      return $this->_getValue('calculated_heroic_wisdom', 0);
   }

   public function getHCHA()
   {
      return $this->_getValue('calculated_heroic_charisma', 0);
   }

   public function getPR()
   {
      return $this->_getValue('calculated_poison_resist', 0);
   }

   public function getMR()
   {
      return $this->_getValue('calculated_magic_resist', 0);
   }

   public function getDR()
   {
      return $this->_getValue('calculated_disease_resist', 0);
   }

   public function getFR()
   {
      return $this->_getValue('calculated_fire_resist', 0);
   }

   public function getCR()
   {
      return $this->_getValue('calculated_cold_resist', 0);
   }

   public function getCOR()
   {
      return $this->_getValue('calculated_corruption_resist', 0);
   }

   public function getHPR()
   {
      return $this->_getValue('calculated_heroic_poison_resist', 0);
   }

   public function getHFR()
   {
      return $this->_getValue('calculated_heroic_fire_resist', 0);
   }

   public function getHMR()
   {
      return $this->_getValue('calculated_heroic_magic_resist', 0);
   }

   public function getHDR()
   {
      return $this->_getValue('calculated_heroic_disease_resist', 0);
   }

   public function getHCR()
   {
      return $this->_getValue('calculated_heroic_cold_resist', 0);
   }


   public function getHCOR()
   {
      return $this->_getValue('calculated_heroic_corruption_resist', 0);
   }

   public function getWT()
   {
      $this->_populateItems();
      return $this->_itemstats->WT();
   }

   public function getFT()
   {
      $this->_populateItems();
      return $this->_itemstats->FT();
   }

   public function getDS()
   {
      $this->_populateItems();
      return $this->_itemstats->DS();
   }

   public function getHaste()
   {
      $this->_populateItems();
      return $this->_itemstats->haste();
   }

   public function getRegen()
   {
      $this->_populateItems();
      return $this->_itemstats->regen();
   }

   public function getItemAC()
   {
      $this->_populateItems();
      return $this->_itemstats->AC();
   }

   public function getItemHP()
   {
      $this->_populateItems();
      return $this->_itemstats->hp();
   }

   public function getItemATK()
   {
      $this->_populateItems();
      return $this->_itemstats->attack();
   }

   public function getItemEndurance()
   {
      $this->_populateItems();
      return $this->_itemstats->endurance();
   }

   public function getItemMana()
   {
      $this->_populateItems();
      return $this->_itemstats->mana();
   }



/********************************************
**            PRIVATE FUNCTIONS            **
********************************************/


   //fetches base data for this character
   private function _getBaseData()
   {
      if (is_array($this->_base_data)) return $this->_base_data;

      //load and cache the base data for this race/class
      $tpl = <<<TPL
      SELECT * 
      FROM base_data
      WHERE level = '%s' 
      AND class= '%s'
      LIMIT 1
TPL;
      $query = sprintf($tpl, $this->_level, $this->_class);
      $result = $this->_sql_content->query($query);

      if(!$this->_sql_content->rows($result)) {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], $this->_language['MESSAGE_NO_BASE_DATA']);
      }

      $this->_base_data = $this->_sql_content->nextrow($result);
      return $this->_base_data;
   }


   //query this character's items and add up all the stats
   private function _populateItems()
   {  
      global $cbspellcache;
      global $cbitemcache;
   
      //only run it once
      if ($this->_items_populated) return;
      $this->_items_populated = true;

      //place where all the items stats are added up
      $this->_itemstats = new Charbrowser_Stats();

      //holds all of the items and info about them
      $this->_allitems = array();

      //FETCH INVENTORY ROWS
      $tpl = <<<TPL
      SELECT item_id AS itemid, 
             augment_one   AS augslot1, 
             augment_two   AS augslot2, 
             augment_three AS augslot3, 
             augment_four  AS augslot4, 
             augment_five  AS augslot5,
             augment_six   AS augslot6,
             slot_id AS myslot,
             charges
      FROM inventory
      WHERE character_id = '%s'  
TPL;
      $query = sprintf($tpl, $this->_char_id);
      $result = $this->_sql->query($query);
      $inventory_results = $this->_sql->fetch_all($result);
      
      
      //FETCH SHARED BANK ROWS
      $tpl = <<<TPL
      SELECT item_id AS itemid, 
             augment_one   AS augslot1, 
             augment_two   AS augslot2, 
             augment_three AS augslot3, 
             augment_four  AS augslot4, 
             augment_five  AS augslot5,
             augment_six   AS augslot6,
             slot_id AS myslot,
             charges
      FROM sharedbank
      WHERE account_id = '%s'  
TPL;
      $query = sprintf($tpl, $this->_getValue('account_id', 0));
      $result = $this->_sql->query($query);
      $bank_results = $this->_sql->fetch_all($result);
      
      
      //CACHE ITEMS
      //preload all the items on the inventory using the item set
      $full_results = array_merge($inventory_results, $bank_results);
      $cbitemcache->build_cache_inventory($full_results);
      
      
      //CACHE SPELLS
      //preload all the spells that are on all the preloaded items
      $item_list = $cbitemcache->fetch_cache();
      $cbspellcache->build_cache_itemset($item_list);
      

      //PROCESS INVENTORY ROWS
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later
      foreach ($inventory_results as $row)
      {
         $itemrow = $cbitemcache->get_item($row['itemid']);
         //merge the inventory and item row
         $row = array_merge($itemrow, $row);
         $tempitem = new Charbrowser_Item($row);
         for ($i = 1; $i <= 5; $i++) {
            if ($row["augslot" . $i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = $cbitemcache->get_item($aug_item_id);
               $tempitem->addaug($augrow);
               //add stats only if it's equiped
               if ($tempitem->type() == EQUIPMENT) {
                  $this->_itemstats->additem($augrow);
               }
            }
         }

         if ($tempitem->type() == EQUIPMENT)
            $this->_itemstats->additem($row);

         if ($tempitem->type() == EQUIPMENT || $tempitem->type() == INVENTORY)
            $this->_itemstats->addWT($row['weight']);

         $this->_allitems[$tempitem->slot()] = &$tempitem;
         unset($tempitem);
      }

      //PROCESS SHARED BANK ROWS
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later
      foreach ($bank_results as $row)
      {
         $itemrow = $cbitemcache->get_item($row['itemid']);
         //merge the inventory and item row
         $row      = array_merge($itemrow, $row);
         $tempitem = new Charbrowser_Item($row);
         for ($i = 1; $i <= 5; $i++) {
            if ($row["augslot" . $i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = $cbitemcache->get_item($aug_item_id);
               $tempitem->addaug($augrow);
            }
         }

         $this->_allitems[$tempitem->slot()] = $tempitem;
      }
   }

   //uses the locator data to find the requested setting
   private function _getValue($data_key, $default)
   {
      // Pull Profile Info
      if (!array_key_exists($data_key, $this->_locator))
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_PROF_NOKEY'], $data_key));
      }

      //get the locator data for this setting so we can find it
      $table_name  = $this->_locator[$data_key][LOCATOR_TABLE];
      $column_name = $this->_locator[$data_key][LOCATOR_COLUMN];
      $index       = $this->_locator[$data_key][LOCATOR_INDEX];

      //if the locator lists a strict index of false then there
      //will only be 1 record
      if ($index === false)
      {
         //fetch the cached record
         $cached_record = $this->_getRecordCache($table_name);
      }

      //otherwise the locator lists a numeric value representing
      //the value of the second pk
      else
      {
         //fetch this table from the db/cache
         $cached_table = $this->_getTableCache($table_name);

         //this table has no rows at all
         if ($cached_table == PROF_NOROWS)
         {
            return false;
         }

         //this is not a failure, this just means the character doesn't have a record
         //for this skill, or whatever is being requested
         if (!array_key_exists($index, $cached_table))
         {
            return $default;
         }

         $cached_record = $cached_table[$index];
      }


      //make sure our column exists in the record
      if (!array_key_exists($column_name, $cached_record))
      {
         return $default;
      }

      //return the value
      return $cached_record[$column_name];
   }



   // gets a TABLE, it loads it into memory so the same TABLE
   // isnt double queried. It keeps every record and uses the
   // second column as the array index
   private function _getTableCache($table_name)
   {
      //get the name of the second pk on the table
      if (!array_key_exists($table_name, $this->_locator_pk))
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_PROF_NOTABKEY'], $table_name));
      }
      $second_column_name = $this->_locator_pk[$table_name];

      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->_cached_tables))
      {
         //since we are accessing the database, we'll go ahead and
         //load every column for the character and store it for later use
         $result = $this->_doCharacterQuery($table_name);

         //parse the result
         if($this->_sql->rows($result))
         {
            //this is a table with two primary keys, we need to load it
            //into a supporting array, indexed by it's second pk
            $temp_array = array();
            while($row = $this->_sql->nextrow($result))
            {
               $temp_array[$row[$second_column_name]] = $row;
            }

            $this->_cached_tables[$table_name] = $temp_array;
         }
         else
         {
            return PROF_NOROWS;
         }
      }

      //hand the table/record over
      return $this->_cached_tables[$table_name];
   }



   // gets a RECORD, it loads it into memory so the same RECORD
   // isnt double queried.
   private function _getRecordCache($table_name)
   {

      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->_cached_records))
      {
         //since we are accessing the database, we'll go ahead and
         //load every column for the character and store it for later use
         $result = $this->_doCharacterQuery($table_name);

         //parse the result
         if($this->_sql->rows($result))
         {
            //this is a simple table with only 1 row per character
            //we just store it in the root structure
            $this->_cached_records[$table_name] = $this->_sql->nextrow($result);
         }
         else $this->_cached_records[$table_name] = array();
      }

      //hand the table/record over
      return $this->_cached_records[$table_name];
   }

   //gets all the records from a table for this character instance
   //we even get ones we dont need; they'll get cached for later use
   private function _doCharacterQuery($table_name)
   {
      //TODO: fix this cludge
      //the new character stats table doesn't use 'id' as the PK, it uses
      //character_id. This is hacky for now, hoping to get the column name
      //changed. If that doesn't happen maybe there needs to be a second
      //locator array with the name of the character ID column for
      //each table
      $id_column = "id";
      if (array_key_exists($table_name, $this->_char_id_col)) {
         $id_column = $this->_char_id_col[$table_name];
      }
      
      
      //build the query
      $tpl = <<<TPL
      SELECT * 
      FROM `%s` 
      WHERE `%s` = '%d'
TPL;
      $query = sprintf($tpl, $table_name, $id_column, $this->_char_id);


      //get the result/error
      $result = $this->_sql->query($query);

      //serve em up
      return $result;
   }

}

?>