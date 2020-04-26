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
 *
 ***************************************************************************/


use Magelo\Repositories\CharacterAlternateAbilityRepository;
use Magelo\Repositories\ItemRepository;
use Magelo\Repositories\SpellRepository;

if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

include_once(__DIR__ . "/statsclass.php");

define('PROF_NOROWS', false);


//constants to reference indexes in the locator array
define('LOCATOR_TABLE',  0);
define('LOCATOR_COLUMN', 1);
define('LOCATOR_INDEX',  2);


class profile {

   // Variables
   private $cached_tables = array();
   private $cached_records = array();
   private $account_id;
   private $char_id;
   private $race;
   private $class;
   private $level;
   private $items_populated;
   private $itemstats;
   private $allitems;
   private $base_data;
   private $db;
   private $db_content;
   private $language;
   private $aa_effects = array();

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
private $locator_pk = array (
   "character_alternate_abilities" => "aa_id",
   "character_skills" => "skill_id",
   "character_languages" => "lang_id",
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
private $locator = array (
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
   function __construct($name, &$db, &$db_content, &$language, $showsoftdelete = false, $charbrowser_is_admin_page = false)
   {
      //dont load characters items until we need to
      $this->items_populated = false;

      $this->db = $db;
      $this->db_content = $db_content;
      $this->language = $language;

      //we can't call the local query method as it assumes the character id
      //which we need to get in the first place
      $table_name = "character_data";

      //don't go sticking just anything in the database
      if (!IsAlphaSpace($name)) cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_NAME_ALPHA']);

      //build the query
      $tpl = <<<TPL
SELECT * 
FROM `%s` 
WHERE `name` = '%s'
TPL;
      $query = sprintf($tpl, $table_name, $name);

      //get the result/error
      $result = $this->db->query($query);

      //collect the data from returned row
      if($this->db->rows($result))
      {
         //fetch the row
         $row = $this->db->nextrow($result);
         //save it
         $this->cached_records[$table_name] = $row;
         $this->account_id = $row['account_id'];
         $this->char_id = $row['id'];
         $this->race = $row['race'];
         $this->class = $row['class'];
         $this->level = $row['level'];
      }
      else cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_NO_FIND']);

      //dont display deleted characters
      if (!$showsoftdelete && !$charbrowser_is_admin_page && $row['deleted_at']) cb_message_die($this->language['MESSAGE_ERROR'],$this->language['MESSAGE_NO_FIND']);

   }

   /********************************************
   **              DESTRUCTOR                 **
   ********************************************/
   function __destruct()
   {
      unset($this->db);
      unset($this->language);
   }


   /********************************************
   **            PUBLIC FUNCTIONS             **
   ********************************************/

   // Return Account ID
   public function accountid()
   {
      return $this->account_id;
   }


   // Return char ID
   public function char_id()
   {
      return $this->char_id;
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
      $table_name = $this->db->escape_string($table_name);

      return $this->_getRecordCache($table_name);
   }

   //uses the locator data to find the requested setting
   public function GetValue($data_key, $default = 0)
   {
      return $this->_getValue($data_key, $default);
   }

   //given an item type, fetch this chars skill for that item
   public function GetSkillValByItemType($type)
   {
      switch ($type) {
         case 0: // 1H Slashing
            return $this->_getValue('1h_slashing', 0);
         case 1: // 2H Slashing
            return $this->_getValue('2h_slashing', 0);
         case 2: // Piercing
            return $this->_getValue('piercing', 0);
         case 3: // 1H Blunt
            return $this->_getValue('1h_blunt', 0);
         case 4: // 2H Blunt
            return $this->_getValue('2h_blunt', 0);
         case 35: // 2H Piercing
            return $this->_getValue('2h_piercing', 0);
         case 45: // Martial/Hand to Hand
            return $this->_getValue('hand_to_hand', 0);
         default: // All other types default to Hand to Hand
            return $this->_getValue('hand_to_hand', 0);
      }
   }

   //given an item type, fetch this chars skill for that item
   public function GetSkillNameByItemType($type)
   {
      switch ($type) {
         case 0: // 1H Slashing
            return '1H Slashing';
         case 1: // 2H Slashing
            return '2H Slashing';
         case 2: // Piercing
            return 'Piercing';
         case 3: // 1H Blunt
            return '1H Blunt';
         case 4: // 2H Blunt
            return '2H Blunt';
         case 35: // 2H Piercing
            return '2H Piercing';
         case 45: // Martial/Hand to Hand
            return 'Hand to Hand';
         default: // All other types default to Hand to Hand
            return 'Hand to Hand';
      }
   }


   //return array of all the items for this character
   public function GetAllItems()
   {
      $this->_populateItems();
      return $this->allitems;
   }


   //gets the name, rank and mod for an AA effect
   public function GetAAModsByEffect($effectid)
   {
      //see if we already cached this effect id
      if (array_key_exists($effectid, $this->aa_effects)) {
         return $this->aa_effects[$effectid];
      }

      //this will load every rank of every aa with the $effect id.
      //this first rank of that aa will be joined to the characters
      //rank of that AA
      $tpl = <<<TPL
         SELECT aa_rank_effects.rank_id, aa_rank_effects.base1, 
                aa_ranks.next_id, aa_ability.name  
         FROM aa_rank_effects 
         LEFT JOIN aa_ranks 
            ON aa_ranks.id = aa_rank_effects.rank_id
         LEFT JOIN aa_ability 
            ON aa_ability.first_rank_id = aa_ranks.title_sid 
         WHERE effect_id = '%s'
TPL;
      $query = sprintf($tpl, $effectid);
      $result = $this->db_content->query($query);

      //no aa with this effect
      if(!$this->db_content->rows($result)) return array();

      //first pass is to load all the AA with this effect into a linked array
      //a secondary conditional will capture if the character has the aa and their rank
      $aa_ranks = array();
      $char_ranks = array();
        //build the query

//      $tpl = <<<TPL
//         SELECT aa_value
//         FROM character_alternate_abilities
//         WHERE id = '%s'
//         AND aa_id = '%s'
//TPL;

      while ($row = $this->db_content->nextrow($result)) {
         //'linked' list of rank modifiers

         $aa_rank = [
             'MODIFIER' => intval($row['base1']),
             'NEXT'     => intval($row['next_id']),
         ];

         $aa_ranks[intval($row['rank_id'])] = $aa_rank;

         //get characters rank for this aa
         $aa = CharacterAlternateAbilityRepository::getAbility($row['rank_id']);

         //this chars rank
         if ($aa['aa_value'] > 0) {
            $char_rank[intval($row['rank_id'])] = [
                'RELATIVE_RANK' => $aa['aa_value'],
                'NAME'          => $row['name'],
            ];
         }
      }

      if (count($char_rank) < 1) return array();


      //calculate this char's modifier
      $output = array();
      foreach ($char_rank as $aa_id => $aa_data) {
         //first rank
         $rank_id = $aa_id;

         //walk through the ranks to find the rank id
         //if they're rank 3, we should look 2 times to
         //jump forward 2 ranks to the 3rd rank modifier
         for ($i = $aa_data['RELATIVE_RANK']; $i > 1; $i--) {
            $rank_id = $aa_ranks[$rank_id]['NEXT'];
         }

         //log it if it worked
         if ($rank_id) {
            $output[] = array(
               'RELATIVE_RANK' => $aa_data['RELATIVE_RANK'],
               'AA_ID' => $aa_id,
               'RANK_ID' => $rank_id,
               'MODIFIER' => $aa_ranks[$rank_id]['MODIFIER'],
               'NAME' => $aa_data['NAME']
            );
         }
      }

      $this->aa_effects[$effectid] = $output;
      return $output;
   }

   //gets the mod total for an AA effect
   public function GetAAModTotalByEffect($effectid)
   {
      //get every rank for this effect
      $effects = $this->GetAAModsByEffect($effectid);

      $total = 0;
      foreach($effects as $value) {
         $total += $value['MODIFIER'];
      }

      return $total;
   }


   //function copied/converted from EQEMU sourcecode may 2, 2009
   //gets I/W/N for the type of int/wis casting this char does
   public function GetCasterClass(){
      switch($this->class)
      {
      case CB_CLASS_CLERIC:
      case CB_CLASS_PALADIN:
      case CB_CLASS_RANGER:
      case CB_CLASS_DRUID:
      case CB_CLASS_SHAMAN:
      case CB_CLASS_BEASTLORD:
         return 'W';
         break;

      case CB_CLASS_SHADOWKNIGHT:
      case CB_CLASS_BARD:
      case CB_CLASS_NECROMANCER:
      case CB_CLASS_WIZARD:
      case CB_CLASS_MAGICIAN:
      case CB_CLASS_ENCHANTER:
         return 'I';
         break;

      default:
         return 'N';
         break;
      }
   }


   //pulled from EQEMU 20200316
   //calculate the display mana
   public function CalcMaxMana(&$calculation_description = 'unset') {
      $base_val = $this->CalcBaseMana(); //base mana
      $item_val = $this->getItemMana(); //item mana
      $base_item_val = $base_val + $item_val;
      $caster_class = $this->GetCasterClass();

      if ($calculation_description == 'unset') {
         if ($caster_class == 'N') return 0;
         $max_mana = $this->GetAAModTotalByEffect(SE_MANAPOOL) + $base_item_val;  //mana bonus effect 97
         return $max_mana;
      }
      else {
         $max_val = $base_item_val;
         $aa_mods = $this->GetAAModsByEffect(SE_MANAPOOL);
         $aa_total_mod = 0;

         $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'mana', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'mana.row', 'DESCRIPTION' => 'Base Mana', 'VALUE' => number_format($base_val));
         $calculation_description[] = array('TYPE' => 'mana.row', 'DESCRIPTION' => 'Item Modifiers', 'VALUE' => number_format($item_val));
         $calculation_description[] = array('TYPE' => 'mana.footer', 'DESCRIPTION' => 'Equiped Subtotal', 'SUBTOTAL' => number_format($base_item_val), 'ROLLTOTAL' => number_format($max_val));


         $calculation_description[] = array('TYPE' => 'mana', 'TYPE_HEAD' => "AA Modifiers", 'VALUE_HEAD' => "Value");
         if (count($aa_mods) > 0) {
            foreach($aa_mods as $value) {
               $aa_total_mod += $value['MODIFIER'];
               $calculation_description[] = array('TYPE' => 'mana.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
            }
         }
         else {
            $calculation_description[] = array('TYPE' => 'mana.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
         }
         $max_val = max($aa_total_mod + $max_val, 0);
         $calculation_description[] = array('TYPE' => 'mana.footer', 'DESCRIPTION' => 'Mod Subtotal', 'SUBTOTAL' => number_format($aa_total_mod), 'ROLLTOTAL' => number_format($max_val));

         //non casters lose all their mana
         $class_mod = 0;
         $class_mod_desc = 'None';
         if ($caster_class == 'N') {
            $class_mod = -$max_val;
            $max_val = 0;
            $class_mod_desc = 'Non-Caster Class';
         }
         $calculation_description[] = array('TYPE' => 'mana', 'TYPE_HEAD' => "Class Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'mana.row', 'DESCRIPTION' => $class_mod_desc, 'VALUE' => number_format($class_mod));
         $calculation_description[] = array('TYPE' => 'mana.footer', 'DESCRIPTION' => 'Equiped Subtotal', 'SUBTOTAL' => number_format($class_mod), 'ROLLTOTAL' => number_format($max_val));
         return $max_val;
      }
   }

   //pulled from EQEMU 20200316
   //gets basic mana without gear/effects/etc
   public function CalcBaseMana()
   {
      $ConvertedWisInt = 0;
      $WisInt = 0;
      $max_m = 0;
      switch ($this->GetCasterClass()) {
         case 'I':
            $WisInt = $this->getINT();

            $ConvertedWisInt = $WisInt;
            $over200 = $WisInt;
            if ($WisInt > 100) {
               if ($WisInt > 200) {
                  $over200 = $this->_cppCastInt(($WisInt - 200) / -2 + $WisInt);
               }
               $ConvertedWisInt = $this->_cppCastInt((3 * $over200 - 300) / 2 + $over200);
            }
            $base_data = $this->_getBaseData();
            if ($base_data) {
               $max_m = $base_data['mana'] + ($ConvertedWisInt * $base_data['mana_fac']) + ($this->getHINT() * 10);
            }

            break;
         case 'W':
            $WisInt = $this->getWIS();
            $ConvertedWisInt = $WisInt;
            $over200 = $WisInt;
            if ($WisInt > 100) {
               if ($WisInt > 200) {
                  $over200 = $this->_cppCastInt(($WisInt - 200) / -2 + $WisInt);
               }
               $ConvertedWisInt = $this->_cppCastInt((3 * $over200 - 300) / 2 + $over200);
            }
            $base_data = $this->_getBaseData();
            if ($base_data) {
               $max_m = $base_data['mana'] + ($ConvertedWisInt * $base_data['mana_fac']) + ($this->getHWIS() * 10);
            }
            break;
         case 'N': {
               $max_m = 0;
               break;
            }
      }
      return $this->_cppCastInt($max_m);
   }


   //pulled from EQEMU 20200316
   //calculate the display endurance
   public function CalcMaxEndurance(&$calculation_description = 'unset') {
      $base_val = $this->CalcBaseEndurance(); //base endurance
      $item_val = $this->getItemEndurance(); //item endurance
      $base_item_val = $base_val + $item_val;
      if ($calculation_description == 'unset') {
         $max_val = $this->GetAAModTotalByEffect(SE_ENDURANCEPOOL) + $base_item_val; //aa effects 190
         return max(0, $max_val);
      }
      else {
         $max_val = $base_item_val;
         $aa_mods = $this->GetAAModsByEffect(SE_ENDURANCEPOOL);
         $aa_total_mod = 0;

         $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'endurance', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'endurance.row', 'DESCRIPTION' => 'Base Endurance', 'VALUE' => number_format($base_val));
         $calculation_description[] = array('TYPE' => 'endurance.row', 'DESCRIPTION' => 'Item Modifiers', 'VALUE' => number_format($item_val));
         $calculation_description[] = array('TYPE' => 'endurance.footer', 'DESCRIPTION' => 'Equiped Subtotal', 'SUBTOTAL' => number_format($base_item_val), 'ROLLTOTAL' => number_format($max_val));


         $calculation_description[] = array('TYPE' => 'endurance', 'TYPE_HEAD' => "AA Modifiers", 'VALUE_HEAD' => "Value");
         if (count($aa_mods) > 0) {
            foreach($aa_mods as $value) {
               $aa_total_mod += $value['MODIFIER'];
               $calculation_description[] = array('TYPE' => 'endurance.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
            }
         }
         else {
            $calculation_description[] = array('TYPE' => 'endurance.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
         }
         $max_val = max($aa_total_mod + $max_val, 0);
         $calculation_description[] = array('TYPE' => 'endurance.footer', 'DESCRIPTION' => 'Mod Subtotal', 'SUBTOTAL' => number_format($aa_total_mod), 'ROLLTOTAL' => number_format($max_val));
         return $max_val;
      }
   }


   //pulled from EQEMU 20200316
   //gets basic endurance without gear/effects/etc
   public function CalcBaseEndurance() {
      $base_end = 0;

      $heroic_stats = ($this->getHSTR() + $this->getHSTA() + $this->getHDEX() + $this->getHAGI()) / 4.0;
      $stats = ($this->getSTR() + $this->getSTA() + $this->getDEX() + $this->getAGI()) / 4.0;
      if ($stats > 201.0) {
         $stats = 1.25 * ($stats - 201.0) + 352.5;
      }
      else if ($stats > 100.0) {
         $stats = 2.5 * ($stats - 100.0) + 100.0;
      }
      $base_data = $this->_getBaseData();
      if ($base_data) {
         $base_end = $this->_cppCastInt($base_data['end'] + ($heroic_stats * 10.0) + ($base_data['end_fac'] * $stats));
      }

      return $this->_cppCastInt($base_end);
   }


   //pulled from EQEMU 20200316
   //calculate the display hp
   public function CalcMaxHP(&$calculation_description = 'unset') {
      $base_val = $this->CalcBaseHP(); //base HP
      $item_val = $this->getItemHP(); //item HP
      $base_item_val = $this->_cppCastInt($base_val + $item_val);
      $max_val = $base_item_val;
      if ($calculation_description == 'unset') {
         $nd = 10000;
         $nd += $this->GetAAModTotalByEffect(SE_MAXHPCHANGE);   //Natural Durability, Physical Enhancement, Planar Durability effect 214
         $max_val = $this->_cppCastInt((float)$max_val * (float)$nd / (float)10000); //this is to fix the HP-above-495k issue
         //leaving these out, these are HP buffs that can be added by item worn/focus effects
         //theres only 3 items that I can see that add HP by effect and its a whole lot more code
         //and load on the database for a few rare items (spell effect 69 & 214)
         //$max_val += spellbonuses.HP + aabonuses.HP;
         //$max_val += $max_val * ((spellbonuses.MaxHPChange + itembonuses.MaxHPChange) / 10000.0f);
         $max_val += $this->GetAAModTotalByEffect(SE_TOTALHP); //effect 69
         return $this->_cppCastInt($max_val);
      }
      else {
         $aa_mods_pct = $this->GetAAModsByEffect(SE_MAXHPCHANGE);
         $aa_mods = $this->GetAAModsByEffect(SE_TOTALHP);
         $aa_total_mod = 0;
         $aa_total_mod_pct = 10000;

         $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'hp', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => 'Base Hitpoints', 'VALUE' => number_format($base_val));
         $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => 'Item Modifiers', 'VALUE' => number_format($item_val));
         $calculation_description[] = array('TYPE' => 'hp.footer', 'DESCRIPTION' => 'Equiped Subtotal', 'SUBTOTAL' => number_format($base_item_val), 'ROLLTOTAL' => number_format($max_val));

         //percent aa mods
         $calculation_description[] = array('TYPE' => 'hp', 'TYPE_HEAD' => "AA Percent Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => "Basic Hitpoints", 'VALUE' => "100%");
         foreach($aa_mods_pct as $value) {
            $aa_total_mod_pct += $value['MODIFIER'];
            $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => ($value['MODIFIER']/100)."%");
         }
         $nd = $aa_total_mod_pct;
         $max_val = $this->_cppCastInt((float)$max_val * (float)$nd / (float)10000);
         $calculation_description[] = array('TYPE' => 'hp.footer', 'DESCRIPTION' => 'Percent Mod Subtotal', 'SUBTOTAL' => ($aa_total_mod_pct/100)."%", 'ROLLTOTAL' => number_format($max_val));

         //aa mods
         $calculation_description[] = array('TYPE' => 'hp', 'TYPE_HEAD' => "AA Modifiers", 'VALUE_HEAD' => "Value");
         if (count($aa_mods) > 0) {
            foreach($aa_mods as $value) {
               $aa_total_mod += $value['MODIFIER'];
               $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
            }
         }
         else {
            $calculation_description[] = array('TYPE' => 'hp.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
         }
         $max_val = $aa_total_mod + $max_val;
         $calculation_description[] = array('TYPE' => 'hp.footer', 'DESCRIPTION' => 'Mod Subtotal', 'SUBTOTAL' => number_format($aa_total_mod), 'ROLLTOTAL' => number_format($max_val));
         return $max_val;
      }
   }



   //pulled from EQEMU 20200316
   //gets basic hp without gear/effects/etc
   public function CalcBaseHP() {
      $stats = $this->getSTA();
      if ($stats > 255) {
         $stats = $this->_cppCastInt(($stats - 255) / 2);
         $stats += 255;
      }
      $base_hp = 5;
      $base_data = $this->_getBaseData();
      if ($base_data) {
         $base_hp += $base_data['hp'] + ($base_data['hp_fac'] * $stats);
         $base_hp += ($this->getHSTA() * 10);
      }

      return $this->_cppCastInt($base_hp);
   }


   //pulled from EQEMU 20200316
   //based on dev quotes
   public function compute_defense(&$calculation_description = 'unset') {
      $defense_mod = $this->_cppCastInt($this->_getValue('defense', 0) * 400 / 225);
      $agi_mod = $this->_cppCastInt((8000 * ($this->getAGI() - 40)) / 36000);
      $hagi_mod = $this->_cppCastInt($this->getHAGI() / 10);
      $def_total = $this->_cppCastInt($defense_mod + $agi_mod + $hagi_mod);
      $def_return = max(1, $def_total);
      if ($calculation_description == 'unset') {
         $defense = $this->_cppCastInt($this->_getValue('defense', 0) * 400 / 225);
         $defense += $this->_cppCastInt((8000 * ($this->getAGI() - 40)) / 36000);
         $defense += $this->_cppCastInt($this->getHAGI() / 10);
         //no items effects yet TODO
         //$defense += itembonuses.AvoidMeleeChance; // item mod2
         return $def_return;
      }
      else {
         $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'ac', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'ac.row', 'DESCRIPTION' => 'Defense x 16/9', 'VALUE' => number_format($defense_mod));
         $calculation_description[] = array('TYPE' => 'ac.row', 'DESCRIPTION' => '(Agility - 40) x 8/36', 'VALUE' => number_format($agi_mod));
         $calculation_description[] = array('TYPE' => 'ac.row', 'DESCRIPTION' => 'Heroic Agility / 10', 'VALUE' => number_format($hagi_mod));
         $calculation_description[] = array('TYPE' => 'ac.footer', 'DESCRIPTION' => 'Defense Mod', 'SUBTOTAL' => number_format($def_total), 'ROLLTOTAL' => number_format($def_return));

         return $def_return;
      }
   }


   //pulled from EQEMU 20200316
   //get the displayed attack value
   public function GetTotalATK(&$calculation_description = 'unset') {
      $AttackRating = 0;
      $mainhandSkillID = $this->GetMainhandSkillID();
      $mainhandSkill = $this->GetSkillValByItemType($mainhandSkillID);
      $atkoffense_mod = $this->_getValue('offense', 0) * 1.345;
      $atkitem_mod = $this->getItemATK() * 1.342;
      $atkstr_mod = ($this->getSTR() - 66) * 0.9;
      $atkmainhand_mod = $mainhandSkill * 2.69;

      $atksubtotal = $this->_cppCastInt($atkitem_mod + $atkoffense_mod + $atkstr_mod + $atkmainhand_mod);
      $max_val = $atksubtotal;

      if ($calculation_description == 'unset') {
         $max_val += $this->GetAAModTotalByEffect(SE_ATK); //aa attack mods by effectid 2
         return max(10, $max_val);
      }
      else {
         $aa_mods = $this->GetAAModsByEffect(SE_ATK);
         $mainhandSkillName = $this->GetSkillNameByItemType($mainhandSkillID);
         $aa_total_mod = 0;

         $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'attack', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => 'Item Base x 1.342', 'VALUE' => number_format($atkitem_mod));
         $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => 'Offense x 1.345', 'VALUE' => number_format($atkoffense_mod));
         $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => '(Strength - 66) x 0.9', 'VALUE' => number_format($atkstr_mod));
         $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => $mainhandSkillName.' x 2.69', 'VALUE' => number_format($atkmainhand_mod));
         $calculation_description[] = array('TYPE' => 'attack.footer', 'DESCRIPTION' => 'Equiped Subtotal', 'SUBTOTAL' => number_format($atksubtotal), 'ROLLTOTAL' => number_format($atksubtotal));

         $calculation_description[] = array('TYPE' => 'attack', 'TYPE_HEAD' => "AA Modifiers", 'VALUE_HEAD' => "Value");
         if (count($aa_mods) > 0) {
            foreach($aa_mods as $value) {
               $aa_total_mod += $value['MODIFIER'];
               $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
            }
         }
         else {
            $calculation_description[] = array('TYPE' => 'attack.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
         }
         $max_val = max($aa_total_mod + $max_val, 0);
         $calculation_description[] = array('TYPE' => 'attack.footer', 'DESCRIPTION' => 'Mod Subtotal', 'SUBTOTAL' => number_format($aa_total_mod), 'ROLLTOTAL' => number_format($max_val));
         return max(10, $max_val);
      }
   }


   //pulled from EQEMU 20200316
   //get the AC cap where returns diminish
   public function GetACSoftcap()
   {
      // from test server Resources/ACMitigation.txt
      $war_softcaps = array(
         312, 314, 316, 318, 320, 322, 324, 326, 328, 330, 332, 334, 336, 338, 340, 342, 344, 346, 348, 350, 352,
         354, 356, 358, 360, 362, 364, 366, 368, 370, 372, 374, 376, 378, 380, 382, 384, 386, 388, 390, 392, 394,
         396, 398, 400, 402, 404, 406, 408, 410, 412, 414, 416, 418, 420, 422, 424, 426, 428, 430, 432, 434, 436,
         438, 440, 442, 444, 446, 448, 450, 452, 454, 456, 458, 460, 462, 464, 466, 468, 470, 472, 474, 476, 478,
         480, 482, 484, 486, 488, 490, 492, 494, 496, 498, 500, 502, 504, 506, 508, 510, 512, 514, 516, 518, 520
      );

      $clrbrdmnk_softcaps = array(
         274, 276, 278, 278, 280, 282, 284, 286, 288, 290, 292, 292, 294, 296, 298, 300, 302, 304, 306, 308, 308,
         310, 312, 314, 316, 318, 320, 322, 322, 324, 326, 328, 330, 332, 334, 336, 336, 338, 340, 342, 344, 346,
         348, 350, 352, 352, 354, 356, 358, 360, 362, 364, 366, 366, 368, 370, 372, 374, 376, 378, 380, 380, 382,
         384, 386, 388, 390, 392, 394, 396, 396, 398, 400, 402, 404, 406, 408, 410, 410, 412, 414, 416, 418, 420,
         422, 424, 424, 426, 428, 430, 432, 434, 436, 438, 440, 440, 442, 444, 446, 448, 450, 452, 454, 454, 456
      );

      $palshd_softcaps = array(
         298, 300, 302, 304, 306, 308, 310, 312, 314, 316, 318, 320, 322, 324, 326, 328, 330, 332, 334, 336, 336,
         338, 340, 342, 344, 346, 348, 350, 352, 354, 356, 358, 360, 362, 364, 366, 368, 370, 372, 374, 376, 378,
         380, 382, 384, 384, 386, 388, 390, 392, 394, 396, 398, 400, 402, 404, 406, 408, 410, 412, 414, 416, 418,
         420, 422, 424, 426, 428, 430, 432, 432, 434, 436, 438, 440, 442, 444, 446, 448, 450, 452, 454, 456, 458,
         460, 462, 464, 466, 468, 470, 472, 474, 476, 478, 480, 480, 482, 484, 486, 488, 490, 492, 494, 496, 498
      );

      $rng_softcaps = array(
         286, 288, 290, 292, 294, 296, 298, 298, 300, 302, 304, 306, 308, 310, 312, 314, 316, 318, 320, 322, 322,
         324, 326, 328, 330, 332, 334, 336, 338, 340, 342, 344, 344, 346, 348, 350, 352, 354, 356, 358, 360, 362,
         364, 366, 368, 368, 370, 372, 374, 376, 378, 380, 382, 384, 386, 388, 390, 390, 392, 394, 396, 398, 400,
         402, 404, 406, 408, 410, 412, 414, 414, 416, 418, 420, 422, 424, 426, 428, 430, 432, 434, 436, 436, 438,
         440, 442, 444, 446, 448, 450, 452, 454, 456, 458, 460, 460, 462, 464, 466, 468, 470, 472, 474, 476, 478
      );

      $dru_softcaps = array(
         254, 256, 258, 260, 262, 264, 264, 266, 268, 270, 272, 272, 274, 276, 278, 280, 282, 282, 284, 286, 288,
         290, 290, 292, 294, 296, 298, 300, 300, 302, 304, 306, 308, 308, 310, 312, 314, 316, 318, 318, 320, 322,
         324, 326, 328, 328, 330, 332, 334, 336, 336, 338, 340, 342, 344, 346, 346, 348, 350, 352, 354, 354, 356,
         358, 360, 362, 364, 364, 366, 368, 370, 372, 372, 374, 376, 378, 380, 382, 382, 384, 386, 388, 390, 390,
         392, 394, 396, 398, 400, 400, 402, 404, 406, 408, 410, 410, 412, 414, 416, 418, 418, 420, 422, 424, 426
      );

      $rogshmbstber_softcaps = array(
         264, 266, 268, 270, 272, 272, 274, 276, 278, 280, 282, 282, 284, 286, 288, 290, 292, 294, 294, 296, 298,
         300, 302, 304, 306, 306, 308, 310, 312, 314, 316, 316, 318, 320, 322, 324, 326, 328, 328, 330, 332, 334,
         336, 338, 340, 340, 342, 344, 346, 348, 350, 350, 352, 354, 356, 358, 360, 362, 362, 364, 366, 368, 370,
         372, 374, 374, 376, 378, 380, 382, 384, 384, 386, 388, 390, 392, 394, 396, 396, 398, 400, 402, 404, 406,
         408, 408, 410, 412, 414, 416, 418, 418, 420, 422, 424, 426, 428, 430, 430, 432, 434, 436, 438, 440, 442
      );

      $necwizmagenc_softcaps = array(
         248, 250, 252, 254, 256, 256, 258, 260, 262, 264, 264, 266, 268, 270, 272, 272, 274, 276, 278, 280, 280,
         282, 284, 286, 288, 288, 290, 292, 294, 296, 296, 298, 300, 302, 304, 304, 306, 308, 310, 312, 312, 314,
         316, 318, 320, 320, 322, 324, 326, 328, 328, 330, 332, 334, 336, 336, 338, 340, 342, 344, 344, 346, 348,
         350, 352, 352, 354, 356, 358, 360, 360, 362, 364, 366, 368, 368, 370, 372, 374, 376, 376, 378, 380, 382,
         384, 384, 386, 388, 390, 392, 392, 394, 396, 398, 400, 400, 402, 404, 406, 408, 408, 410, 412, 414, 416
      );

      //max level and zero index fix
      $level = min(105, $this->level) - 1;

      switch ($this->class) {
         case CB_CLASS_WARRIOR:
            return $war_softcaps[$level];
         case CB_CLASS_CLERIC:
         case CB_CLASS_BARD:
         case CB_CLASS_MONK:
            return $clrbrdmnk_softcaps[$level];
         case CB_CLASS_PALADIN:
         case CB_CLASS_SHADOWKNIGHT:
            return $palshd_softcaps[$level];
         case CB_CLASS_RANGER:
            return $rng_softcaps[$level];
         case CB_CLASS_DRUID:
            return $dru_softcaps[$level];
         case CB_CLASS_ROGUE:
         case CB_CLASS_SHAMAN:
         case CB_CLASS_BEASTLORD:
         case CB_CLASS_BERSERKER:
            return $rogshmbstber_softcaps[$level];
         case CB_CLASS_NECROMANCER:
         case CB_CLASS_WIZARD:
         case CB_CLASS_MAGICIAN:
         case CB_CLASS_ENCHANTER:
            return $necwizmagenc_softcaps[$level];
         default:
            return 350;
      }
   }


   //pulled from EQEMU 20200316
   //get the percent of return after your pass the soft cap
   public function GetSoftcapReturns()
   {
      // These are based on the dev post, they seem to be correct for every level
      // AKA no more hard caps
      switch ($this->class) {
         case CB_CLASS_WARRIOR:
            return 0.35;
         case CB_CLASS_CLERIC:
         case CB_CLASS_BARD:
         case CB_CLASS_MONK:
            return 0.3;
         case CB_CLASS_PALADIN:
         case CB_CLASS_SHADOWKNIGHT:
            return 0.33;
         case CB_CLASS_RANGER:
            return 0.315;
         case CB_CLASS_DRUID:
            return 0.265;
         case CB_CLASS_ROGUE:
         case CB_CLASS_SHAMAN:
         case CB_CLASS_BEASTLORD:
         case CB_CLASS_BERSERKER:
            return 0.28;
         case CB_CLASS_NECROMANCER:
         case CB_CLASS_WIZARD:
         case CB_CLASS_MAGICIAN:
         case CB_CLASS_ENCHANTER:
            return 0.25;
         default:
            return 0.3;
      }
   }


   //pulled from EQEMU 20200316
   //fetch the AC bonus for this chars class
   public function GetClassRaceACBonus()
   {
      $ac_bonus = 0;
      if ($this->class == CB_CLASS_MONK) {
         $hardcap = 30;
         $softcap = 14;
         if ($this->level > 99) {
            $hardcap = 58;
            $softcap = 35;
         }
         else if ($this->level > 94) {
            $hardcap = 57;
            $softcap = 34;
         }
         else if ($this->level > 89) {
            $hardcap = 56;
            $softcap = 33;
         }
         else if ($this->level > 84) {
            $hardcap = 55;
            $softcap = 32;
         }
         else if ($this->level > 79) {
            $hardcap = 54;
            $softcap = 31;
         }
         else if ($this->level > 74) {
            $hardcap = 53;
            $softcap = 30;
         }
         else if ($this->level > 69) {
            $hardcap = 53;
            $softcap = 28;
         }
         else if ($this->level > 64) {
            $hardcap = 53;
            $softcap = 26;
         }
         else if ($this->level > 63) {
            $hardcap = 50;
            $softcap = 24;
         }
         else if ($this->level > 61) {
            $hardcap = 47;
            $softcap = 24;
         }
         else if ($this->level > 59) {
            $hardcap = 45;
            $softcap = 24;
         }
         else if ($this->level > 54) {
            $hardcap = 40;
            $softcap = 20;
         }
         else if ($this->level > 50) {
            $hardcap = 38;
            $softcap = 18;
         }
         else if ($this->level > 44) {
            $hardcap = 36;
            $softcap = 17;
         }
         else if ($this->level > 29) {
            $hardcap = 34;
            $softcap = 16;
         }
         else if ($this->level > 14) {
            $hardcap = 32;
            $softcap = 15;
         }
         $weight = $this->_cppCastInt($this->getWT()/10);
         if ($weight < $hardcap - 1) {
            $temp = $this->level + 5;
            if ($weight > $softcap) {
               $redux = ($weight - $softcap) * 6.66667;
               $redux = (100.0 - min(100.0, $redux)) * 0.01;
               $temp = max(0.0, $temp * $redux);
            }
            $ac_bonus = $this->_cppCastInt((4.0 * $temp) / 3.0);
         }
         else if ($weight > $hardcap + 1) {
            $temp = $this->level + 5;
            $multiplier = min(1.0, ($weight - ($hardcap - 10.0)) / 100.0);
            $temp = (4.0 * $temp) / 3.0;
            $ac_bonus -= $this->_cppCastInt($temp * $multiplier);
         }
      }

      if ($this->class == CB_CLASS_ROGUE) {
         $AGI = $this->getAGI();
         $level_scaler = $this->level - 26;
         if ($AGI < 80)
            $ac_bonus = $this->_cppCastInt($level_scaler / 4);
         else if ($AGI < 85)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 2) / 4);
         else if ($AGI < 90)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 3) / 4);
         else if ($AGI < 100)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 4) / 4);
         else if ($AGI >= 100)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 5) / 4);
         if ($ac_bonus > 12)
            $ac_bonus = 12;
      }

      if ($this->class == CB_CLASS_BEASTLORD) {
         $level_scaler = $this->level - 6;
         if ($AGI < 80)
            $ac_bonus = $this->_cppCastInt($level_scaler / 5);
         else if ($AGI < 85)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 2) / 5);
         else if ($AGI < 90)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 3) / 5);
         else if ($AGI < 100)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 4) / 5);
         else if ($AGI >= 100)
            $ac_bonus = $this->_cppCastInt(($level_scaler * 5) / 5);
         if ($ac_bonus > 16)
            $ac_bonus = 16;
      }

      if ($this->race == CB_RACE_IKSAR) {
         $ac_bonus += max(10, min($this->level, 35));
      }

      return $this->_cppCastInt($ac_bonus);
   }


   //pulled from https://gist.github.com/fe1c0e77a5f9c40d6ce037c8efe2cf9a
   //calculates the AC that is displayed in the inventory window
   public function GetDisplayAC(&$calculation_description = 'unset') {
      $ac_sum_skipcaps = $this->ACSum(true);
      $ac_defense = $this->compute_defense($calculation_description);
      $ac_def_total = $this->_cppCastInt(1000 * ($ac_sum_skipcaps + $ac_defense) / 847);
      if ($calculation_description == 'unset') {
         return $ac_def_total;
      }
      else {
         if (!is_array($calculation_description)) $calculation_description = array();
         $calculation_description[] = array('TYPE' => 'ac', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'ac.row', 'DESCRIPTION' => 'Uncapped Mitigation AC / 0.847', 'VALUE' => number_format($ac_sum_skipcaps/0.847));
         $calculation_description[] = array('TYPE' => 'ac.row', 'DESCRIPTION' => 'Defense Mod / 0.847', 'VALUE' => number_format($ac_defense/0.847));
         $calculation_description[] = array('TYPE' => 'ac.footer', 'DESCRIPTION' => 'Subtotal', 'SUBTOTAL' => number_format($ac_def_total), 'ROLLTOTAL' => number_format($ac_def_total));

         return $ac_def_total;
      }
   }

   //pulled from EQEMU 20200316
   //AKA mitigation AC, this is the value used for combat, not the one displayed (which isnt used)
   public function ACSumOriginal($skip_caps = false)
   {

      $ac = 0; // this should be base AC whenever shrouds come around
      $ac += $this->getItemAC(); // items + food + tribute

      $shield_ac = 0;

      /* need to implement shield modifier TODO
      if (HasShieldEquiped()) {
         auto client = CastToClient();
         auto inst = client->GetInv().GetItem(EQEmu::invslot::slotSecondary);
         if (inst) {
            if (inst->GetItemRecommendedLevel(true) <= GetLevel())
               shield_ac = inst->GetItemArmorClass(true);
            else
               shield_ac = client->CalcRecommendedLevelBonus(GetLevel(), inst->GetItemRecommendedLevel(true), inst->GetItemArmorClass(true));
         }
         shield_ac += client->GetHeroicSTR() / 10;
      }*/

      // EQ math
      $ac = $this->_cppCastInt(($ac * 4) / 3);

      // anti-twink,
      if (!$skip_caps && $this->level < 50) {
         $ac = $this->_cppCastInt(min($ac, 25 + 6 * $this->level));
      }

      $ac = $this->_cppCastInt(max(0, $ac + $this->GetClassRaceACBonus()));

      $spell_aa_ac = $this->GetAAModTotalByEffect(1) + $this->GetAAModTotalByEffect(416); //aa AC bonuses effect 1 a& 416
      if ($this->class >= CB_CLASS_NECROMANCER && $this->class <= CB_CLASS_ENCHANTER) {
         $ac += $this->_cppCastInt($this->_getValue('defense', 0) / 2 + $spell_aa_ac / 3);
      }
      else {
         $ac += $this->_cppCastInt($this->_getValue('defense', 0) / 3 + $spell_aa_ac / 4);
      }

      $AGI = $this->getAGI();
      if ($AGI > 70)
         $ac += $this->_cppCastInt($AGI / 20);

      if ($ac < 0)
         $ac = 0;


      if (!$skip_caps) { //pulled from https://gist.github.com/fe1c0e77a5f9c40d6ce037c8efe2cf9a
         $softcap = $this->GetACSoftcap();
         $returns = $this->GetSoftcapReturns();
         $total_aclimitmod = $this->GetAAModTotalByEffect(259); //combat stability effect 259;
         if ($total_aclimitmod) {
            $softcap = ($softcap * (100 + $total_aclimitmod)) / 100;
         }
         $softcap += $shield_ac;

         if ($ac > $softcap) {
            $over_cap = $ac - $softcap;
            $ac = $softcap + $this->_cppCastInt($over_cap * $returns);
         }
      }

      return $this->_cppCastInt($ac);
   }

   //pulled from EQEMU 20200316
   //AKA mitigation AC, this is the value used for combat, not the one displayed (which isnt used)
   public function ACSum($skip_caps = false, &$calculation_description = 'unset')
   {
      if (!is_array($calculation_description)) $calculation_description = array();

      $aa_mods1 = $this->GetAAModsByEffect(SE_ARMORCLASS); //effect 1
      $aa_mods2 = $this->GetAAModsByEffect(SE_ACV2); //effect 416
      $calculation_description[] = array('TYPE' => 'mit_ac', 'TYPE_HEAD' => "AC Alt Abilities", 'VALUE_HEAD' => "Value");
      if (count($aa_mods1) + count($aa_mods2) < 1) {
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
      }
      else {
         foreach($aa_mods1 as $value) {
            $aa_total_mod1 += $value['MODIFIER'];
            $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
         }
         foreach($aa_mods2 as $value) {
            $aa_total_mod1 += $value['MODIFIER'];
            $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
         }
      }
      $calculation_description[] = array('TYPE' => 'mit_ac.footer', 'DESCRIPTION' => 'AC AA Total', 'SUBTOTAL' => number_format($aa_total_mod1), 'ROLLTOTAL' => number_format($aa_total_mod1));


      $aa_mods3 = $this->GetAAModsByEffect(SE_COMBATSTABILITY); //effect 259
      $calculation_description[] = array('TYPE' => 'mit_ac', 'TYPE_HEAD' => "Stability Alt Abilities", 'VALUE_HEAD' => "Value");
      if (count($aa_mods3) < 1) {
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => 'None', 'VALUE' => '0');
      }
      else {
         foreach($aa_mods3 as $value) {
            $aa_total_mod2 += $value['MODIFIER'];
            $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => $value['NAME']." ".$value['RELATIVE_RANK'], 'VALUE' => $value['MODIFIER']);
         }
      }
      $calculation_description[] = array('TYPE' => 'mit_ac.footer', 'DESCRIPTION' => 'Stability AA Total', 'SUBTOTAL' => number_format($aa_total_mod2), 'ROLLTOTAL' => number_format($aa_total_mod2));

      //softcap vars
      if (!$skip_caps) { //pulled from https://gist.github.com/fe1c0e77a5f9c40d6ce037c8efe2cf9a
         $softcap = $this->GetACSoftcap();
         $original_softcap = $softcap;
         $softcapreturns = $this->GetSoftcapReturns();
         if ($aa_total_mod2) {
            $softcap = ($softcap * (100 + $aa_total_mod2)) / 100;
         }
         $softcap += $shield_ac;
         $calculation_description[] = array('TYPE' => 'mit_ac', 'TYPE_HEAD' => "Softcap Vars", 'VALUE_HEAD' => "Value");
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Base Softcap", 'VALUE' => number_format($original_softcap));
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Softcap After Stability AA", 'VALUE' => number_format($softcap));
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Softcap Returns", 'VALUE' => $softcapreturns);
      }

      $totalAC = 0; // this should be base AC whenever shrouds come around

      $shield_ac = 0;
      /* need to implement shield modifier TODO
      if (HasShieldEquiped()) {
         auto client = CastToClient();
         auto inst = client->GetInv().GetItem(EQEmu::invslot::slotSecondary);
         if (inst) {
            if (inst->GetItemRecommendedLevel(true) <= GetLevel())
               shield_ac = inst->GetItemArmorClass(true);
            else
               shield_ac = client->CalcRecommendedLevelBonus(GetLevel(), inst->GetItemRecommendedLevel(true), inst->GetItemArmorClass(true));
         }
         shield_ac += client->GetHeroicSTR() / 10;
      }*/


      $calculation_description[] = array('TYPE' => 'mit_ac', 'TYPE_HEAD' => "Modifiers", 'VALUE_HEAD' => "Value");

      // EQ math
      $item_mod = $this->_cppCastInt(($this->getItemAC() * 4) / 3); // items + food + tribute
      $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Item AC x 4/3", 'VALUE' => number_format($item_mod));
      $totalAC = $item_mod;

      // anti-twink,
      $level_cap_mod = 0;
      if (!$skip_caps && $this->level < 50) {
         $levelcap_ac = 25 + 6 * $this->level;
         if ($levelcap_ac  < $item_mod ) {
            $level_cap_mod = $item_mod - $levelcap_ac;
            $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Level Cap Mod", 'VALUE' => number_format($level_cap_mod));
         }
      }
      $totalAC += $level_cap_mod;

      //race class bonus
      $race_class_mod = $this->_cppCastInt($this->GetClassRaceACBonus());
      $temptotal = $item_mod + $level_cap_mod + $race_class_mod;
      if ($temptotal < 0) {
         $race_class_mod -= $temptotal;
      }
      $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Race-Class Bonus", 'VALUE' => number_format($race_class_mod));
      $totalAC += $race_class_mod;



      //int caster mod
      if ($this->class >= CB_CLASS_NECROMANCER && $this->class <= CB_CLASS_ENCHANTER) {
         $int_caster_mod = $this->_cppCastInt($this->_getValue('defense', 0) / 2 + $aa_total_mod1 / 3);
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Defense Skill / 2", 'VALUE' => number_format($this->_cppCastInt($this->_getValue('defense', 0) / 2)));
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "AC AA / 3", 'VALUE' => number_format($this->_cppCastInt($aa_total_mod1 / 3)));
      }
      else {
         $int_caster_mod = $this->_cppCastInt($this->_getValue('defense', 0) / 3 + $aa_total_mod1 / 4);
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Defense Skill / 3", 'VALUE' => number_format($this->_cppCastInt($this->_getValue('defense', 0) / 3)));
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "AC AA / 4", 'VALUE' => number_format($this->_cppCastInt($aa_total_mod1 / 4)));
      }
      $totalAC += $int_caster_mod;


      //agility bonus
      $AGI = $this->getAGI();
      $agi_bonus_mod = 0;
      if ($AGI > 70) {
         $agi_bonus_mod = $this->_cppCastInt($AGI / 20);
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "AGI / 20", 'VALUE' => number_format($agi_bonus_mod));
      }
      $totalAC += $agi_bonus_mod;

      //cant go below 0
      if ($totalAC < 0) {
         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Min AC Correction", 'VALUE' => "+".number_format($totalAC));
         $totalAC = 0;
      }


      if (!$skip_caps) { //pulled from https://gist.github.com/fe1c0e77a5f9c40d6ce037c8efe2cf9a

         if ($totalAC > $softcap) {
            $tempTotalAC = $totalAC;
            $over_cap = $totalAC - $softcap;
            $totalAC = $softcap + $this->_cppCastInt($over_cap * $softcapreturns);
            $softcap_mod = 0 - ($tempTotalAC - $totalAC);
         }

         $calculation_description[] = array('TYPE' => 'mit_ac.row', 'DESCRIPTION' => "Softcap Mod", 'VALUE' => number_format($softcap_mod));
      }

      $totalAC = $this->_cppCastInt($totalAC);
      $calculation_description[] = array('TYPE' => 'mit_ac.footer', 'DESCRIPTION' => 'Mitigation AC', 'SUBTOTAL' => number_format($totalAC), 'ROLLTOTAL' => number_format($totalAC));
      return $totalAC;
   }


   //get the skill id for your mainhand attack
   public function GetMainhandSkillID()
   {
      $mainhandslot = 13;
      $handtohand = 45;
      if (array_key_exists($mainhandslot, $this->allitems)) {
         return $this->allitems[$mainhandslot]->skill();
      }
      else {
         return $handtohand;
      }
   }


   //get stats including items
   public function getSTR()
   {
      $this->_populateItems();
      return $this->_getValue('str', 0) + $this->itemstats->STR();
   }

   public function getSTA()
   {
      $this->_populateItems();
      return $this->_getValue('sta', 0) + $this->itemstats->STA();
   }

   public function getDEX()
   {
      $this->_populateItems();
      return $this->_getValue('dex', 0) + $this->itemstats->DEX();
   }

   public function getAGI()
   {
      $this->_populateItems();
      return $this->_getValue('agi', 0) + $this->itemstats->AGI();
   }

   public function getINT()
   {
      $this->_populateItems();
      return $this->_getValue('int', 0) + $this->itemstats->INT();
   }

   public function getWIS()
   {
      $this->_populateItems();
      return $this->_getValue('wis', 0) + $this->itemstats->WIS();
   }

   public function getCHA()
   {
      $this->_populateItems();
      return $this->_getValue('cha', 0) + $this->itemstats->CHA();
   }

   public function getHSTR()
   {
      $this->_populateItems();
      return $this->itemstats->HSTR();
   }

   public function getHSTA()
   {
      $this->_populateItems();
      return $this->itemstats->HSTA();
   }

   public function getHDEX()
   {
      $this->_populateItems();
      return $this->itemstats->HDEX();
   }

   public function getHAGI()
   {
      $this->_populateItems();
      return $this->itemstats->HAGI();
   }

   public function getHINT()
   {
      $this->_populateItems();
      return $this->itemstats->HINT();
   }

   public function getHWIS()
   {
      $this->_populateItems();
      return $this->itemstats->HWIS();
   }

   public function getHCHA()
   {
      $this->_populateItems();
      return $this->itemstats->HCHA();
   }

   public function getPR()
   {
      $this->_populateItems();

      $byClass = array(0,0,0,0,4,0,0,0,8,0,0,0,0,0,0,0);

      if($this->race == 8) $retval =  20;
      else if($this->race == 330) $retval = 30;
      else if($this->race == 74) $retval =  30;
      else if($this->race == 11) $retval =  20;
      else $retval =  15;

      $retval += $byClass[$this->class] + $this->itemstats->PR();

      return $retval;
   }

   public function getMR()
   {
      $this->_populateItems();

      $byClass = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

      if($this->race == 8) $retval = 30;
      else if($this->race == 3) $retval = 30;
      else if($this->race == 330) $retval = 30;
      else if($this->race == 74) $retval = 30;
      else $retval = 25;

      $retval += $byClass[$this->class] + $this->itemstats->MR();

      return $retval;
   }

   public function getDR()
   {
      $this->_populateItems();

      $byClass = array(0,0,8,0,4,0,0,0,0,0,0,0,0,0,4,0);

      if($this->race == 3) $retval = 10;
      else if($this->race == 11) $retval = 20;
      else $retval = 15;

      $retval += $byClass[$this->class] + $this->itemstats->DR();

      return $retval;
   }

   public function getFR()
   {
      $this->_populateItems();

      $byClass = array(0,0,0,4,0,0,8,0,0,0,0,0,0,0,0,0);

      if($this->race == 128) $retval = 30;
      else if($this->race == 9) $retval = 5;
      else $retval = 25;

      $retval += $byClass[$this->class] + $this->itemstats->FR();

      return $retval;
   }

   public function getCR()
   {
      $this->_populateItems();

      $byClass = array(0,0,0,4,0,0,0,0,0,0,0,0,0,0,4,0);

      if($this->race == 2) $retval = 35;
      else if($this->race == 128) $retval = 15;
      else $retval = 25;

      $retval += $byClass[$this->class] + $this->itemstats->CR();

      return $retval;
   }

   public function getCOR()
   {
      $this->_populateItems();
      return $this->itemstats->COR();
   }

   public function getHPR()
   {
      $this->_populateItems();
      return $this->itemstats->HPR();
   }

   public function getHFR()
   {
      $this->_populateItems();
      return $this->itemstats->HFR();
   }

   public function getHMR()
   {
      $this->_populateItems();
      return $this->itemstats->HMR();
   }

   public function getHDR()
   {
      $this->_populateItems();
      return $this->itemstats->HDR();
   }

   public function getHCR()
   {
      $this->_populateItems();
      return $this->itemstats->HCR();
   }

   public function getHCOR()
   {
      $this->_populateItems();
      return $this->itemstats->HCOR();
   }

   public function getWT()
   {
      $this->_populateItems();
      return $this->itemstats->WT();
   }

   public function getFT()
   {
      $this->_populateItems();
      return $this->itemstats->FT();
   }

   public function getDS()
   {
      $this->_populateItems();
      return $this->itemstats->DS();
   }

   public function getHaste()
   {
      $this->_populateItems();
      return $this->itemstats->haste();
   }

   public function getRegen()
   {
      $this->_populateItems();
      return $this->itemstats->regen();
   }

   public function getItemAC()
   {
      $this->_populateItems();
      return $this->itemstats->AC();
   }

   public function getItemHP()
   {
      $this->_populateItems();
      return $this->itemstats->hp();
   }

   public function getItemATK()
   {
      $this->_populateItems();
      return $this->itemstats->attack();
   }

   public function getItemEndurance()
   {
      $this->_populateItems();
      return $this->itemstats->endurance();
   }

   public function getItemMana()
   {
      $this->_populateItems();
      return $this->itemstats->mana();
   }



/********************************************
**            PRIVATE FUNCTIONS            **
********************************************/

   //we have converted c++ code here
   //much of it stores floats in integers
   //causing an implicit conversion
   //this does that same conversion
   private function _cppCastInt($val)
   {
      return floor($val);
   }

   //fetches base data for this character
   private function _getBaseData()
   {
      if (is_array($this->base_data)) return $this->base_data;

      //load and cache the base data for this race/class
      $tpl = <<<TPL
      SELECT * 
      FROM base_data
      WHERE level = '%s' 
      AND class= '%s'
      LIMIT 1
TPL;
      $query = sprintf($tpl, $this->level, $this->class);
      $result = $this->db_content->query($query);

      if(!$this->db_content->rows($result)) {
         cb_message_die('profile.php', $this->language['MESSAGE_NO_BASE_DATA'], $this->language['MESSAGE_ERROR']);
      }

      $this->base_data = $this->db_content->nextrow($result);
      return $this->base_data;
   }


   //query this profiles items and add up all the stats
   private function _populateItems()
   {
      //only run it once
      if ($this->items_populated) return;
      $this->items_populated = true;

      //place where all the items stats are added up
      $this->itemstats = new stats();

      //holds all of the items and info about them
      $this->allitems = array();

      //FETCH INVENTORY
      // pull characters inventory slotid is loaded as
      // "myslot" since items table also has a slotid field.
      $tpl = <<<TPL
      SELECT itemid, augslot1, augslot2, 
             augslot3, augslot4, 
             augslot5, slotid AS myslot,
             charges
      FROM inventory
      WHERE charid = '%s'  
TPL;
      $query = sprintf($tpl, $this->char_id);
      $result = $this->db->query($query);
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later

      CharacterAlternateAbilityRepository::preloadAlternateAbilities($this->char_id);
      ItemRepository::preloadItemsByAccountCharacter($this->account_id, $this->char_id);
      SpellRepository::preloadSpellsUsedByCharacterId($this->char_id);

      while ($row = $this->db->nextrow($result)) {
         $itemrow = ItemRepository::findOne($row['itemid']);
         //merge the inventory and item row
         $row = array_merge($itemrow, $row);
         $tempitem = new item($row);
         for ($i = 1; $i <= 5; $i++) {
            if ($row["augslot" . $i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = ItemRepository::findOne($aug_item_id);
               $tempitem->addaug($augrow);
               //add stats only if it's equiped
               if ($tempitem->type() == EQUIPMENT) {
                  $this->itemstats->additem($augrow);
               }
            }
         }

         if ($tempitem->type() == EQUIPMENT)
            $this->itemstats->additem($row);

         if ($tempitem->type() == EQUIPMENT || $tempitem->type() == INVENTORY)
            $this->itemstats->addWT($row['weight']);

         $this->allitems[$tempitem->slot()] = &$tempitem;
         unset($tempitem);
      }

      //FETCH SHARED BANK
      // pull characters shared bank, slotid is loaded as
      // "myslot" since items table also has a slotid field.
      $tpl = <<<TPL
      SELECT itemid, augslot1, augslot2, 
             augslot3, augslot4, 
             augslot5, slotid AS myslot 
      FROM sharedbank
      WHERE acctid = '%s'  
TPL;
      $query = sprintf($tpl, $this->_getValue('account_id', $default));
      $result = $this->db->query($query);
      // loop through inventory results saving Name, Icon, and preload HTML for each
      // item to be pasted into its respective div later
      $tpl = <<<TPL
      SELECT * 
      FROM items 
      WHERE id = '%s' 
      LIMIT 1
TPL;
      while ($row = $this->db->nextrow($result)) {
         $itemrow = ItemRepository::findOne($row['itemid']);
         //merge the inventory and item row
         $row      = array_merge($itemrow, $row);
         $tempitem = new item($row);
         for ($i = 1; $i <= 5; $i++) {
            if ($row["augslot" . $i]) {
               $aug_item_id = $row["augslot" . $i];
               $augrow      = ItemRepository::findOne($aug_item_id);
               $tempitem->addaug($augrow);
            }
         }

         $this->allitems[$tempitem->slot()] = $tempitem;
      }
   }

   //uses the locator data to find the requested setting
   private function _getValue($data_key, $default)
   {
      // Pull Profile Info
      if (!array_key_exists($data_key, $this->locator))
      {
         cb_message_die('profile.php', sprintf($this->language['MESSAGE_PROF_NOKEY'], $data_key),$this->language['MESSAGE_ERROR']);
      }

      //get the locator data for this setting so we can find it
      $table_name  = $this->locator[$data_key][LOCATOR_TABLE];
      $column_name = $this->locator[$data_key][LOCATOR_COLUMN];
      $index       = $this->locator[$data_key][LOCATOR_INDEX];

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
            cb_message_die('profile.php', sprintf($this->language['MESSAGE_PROF_NOCACHE'], $data_key, $table_name, $column_name),$this->language['MESSAGE_ERROR']);
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
      if (!array_key_exists($table_name, $this->locator_pk))
      {
         cb_message_die('profile.php', sprintf($this->language['MESSAGE_PROF_NOTABKEY'], $table_name),$this->language['MESSAGE_ERROR']);
      }
      $second_column_name = $this->locator_pk[$table_name];

      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->cached_tables))
      {
         //since we are accessing the database, we'll go ahead and
         //load every column for the character and store it for later use
         $result = $this->_doCharacterQuery($table_name);

         //parse the result
         if($this->db->rows($result))
         {
            //this is a table with two primary keys, we need to load it
            //into a supporting array, indexed by it's second pk
            $temp_array = array();
            while($row = $this->db->nextrow($result))
            {
               $temp_array[$row[$second_column_name]] = $row;
            }

            $this->cached_tables[$table_name] = $temp_array;
         }
         else
         {
            return PROF_NOROWS;
         }
      }

      //hand the table/record over
      return $this->cached_tables[$table_name];
   }



   // gets a RECORD, it loads it into memory so the same RECORD
   // isnt double queried.
   private function _getRecordCache($table_name)
   {

      //if we haven't already loaded data from this table then load it
      if (!array_key_exists($table_name, $this->cached_records))
      {
         //since we are accessing the database, we'll go ahead and
         //load every column for the character and store it for later use
         $result = $this->_doCharacterQuery($table_name);

         //parse the result
         if($this->db->rows($result))
         {
            //this is a simple table with only 1 row per character
            //we just store it in the root structure
            $this->cached_records[$table_name] = $this->db->nextrow($result);
         }
         else cb_message_die('profile.php', sprintf($this->language['MESSAGE_PROF_NOROWS'], $table_name),$this->language['MESSAGE_ERROR']);
      }

      //hand the table/record over
      return $this->cached_records[$table_name];
   }

   //gets all the records from a table for this character instance
   //we even get ones we dont need; they'll get cached for later use
   private function _doCharacterQuery($table_name)
   {
      //build the query
      $tpl = <<<TPL
      SELECT * 
      FROM `%s` 
      WHERE `id` = '%d'
TPL;
      $query = sprintf($tpl, $table_name, $this->char_id);

      //get the result/error
      $result = $this->db->query($query);

      //serve em up
      return $result;
   }

}


?>
