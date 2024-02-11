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
 *   February 24, 2014 - Added backstab damage (Maudigan c/o Kinglykrab)
 *   February 24, 2014 - Spelling--uncommented (Maudigan c/o Kinglykrab)
 *   February 25, 2014 - added heroic and aug types (Maudigan c/o Kinglykrab)
 *   February 25, 2014 - fixed maxcharges condition (Maudigan c/o Kinglykrab)
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *   October 4, 2014 - Maudigan
 *      fixed call to nonexistent function mycb_message_die
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences.
 *      Implemented new database wrapper.
 *   October 3, 2016 - Maudigan
 *      Made the spell links customizable
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   September 7, 2019 - Kinglykrab
 *      Added a cleaner itemstats list view
 *   April 4, 2020 - Maudigan
 *     cap bag slot count with a constant
 *   April 25, 2020 - Maudigan
 *     relocated GetFieldByQuery to db.php
 *   March 16, 2022 - Maudigan
 *     added item type to item display
 *   November 27, 2022 - Maudigan
 *     corrected "hpregen" column to "regen" 
 *     added missing $dbbagtypes array
 *     clarified details about bag type and tradeskill containers
 *   January 16, 2023 - modified the "get" functions to use the new
 *                      breakout_bits function
 *
 ***************************************************************************/


if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}
include_once(__DIR__ . "/global.php");
include_once(__DIR__ . "/db.php");



function strtolower_ucfirst($txt) 
{
   if ($txt=="") 
   { 
      return $txt; 
   }
   else 
   {
      $txt=strtolower($txt);
      $txt[0]=strtoupper($txt[0]);
      return $txt;
   }
}

//convert the bitwise slot variable on item record
//into a string list of slot names
function getslots($slotbits)
{
   global $dbslots;
   return breakout_bits($slotbits, $dbslots, " ", "NONE");
}

//convert the bitwise race variable on item record
//into a string list of race names
function getraces($racebits)
{
   global $dbraces;
   return breakout_bits($racebits, $dbraces, " ", "NONE");
}

//convert the bitwise classes variable on item record
//into a string list of class names
function getclasses($classbits)
{
   global $dbiclasses;
   return breakout_bits($classbits, $dbiclasses, " ", "NONE");
}

//convert the bitwise deity variable on item record
//into a string list of deity names
function getdeities($deitybits)
{
   global $dbideities;
   return breakout_bits($deitybits, $dbideities, ", ", "");
}

//convert the bitwise augment types variable on item record
//into a string list of augment slots
function getaugtype($augbits)
{
   global $augtypes;
   return breakout_bits($augbits, $augtypes, ", ", "");
}


function sign($val) {
   if ($val > 0)
   {
      return "+" . $val;
   }
   else
   {
      return $val;
   }
}

function getsize($val) {
   switch($val) {
      case 0: return "Tiny"; break;
      case 1: return "Small"; break;
      case 2: return "Medium"; break;
      case 3: return "Large"; break;
      case 4: return "Giant"; break;
      default: return "$val?"; break;
   }
}

function getitemtype($val) {
   switch($val) {
      case 3: return "1H Blunt"; break;
      case 0: return "1H Slashing"; break;
      case 4: return "2H Blunt"; break;
      case 35: return "2H Piercing"; break;
      case 1: return "2H Slashing"; break;
      case 38: return "Alcohol"; break;
      case 5: return "Archery"; break;
      case 10: return "Armor"; break;
      case 27: return "Arrow"; break;
      case 54: return "Augmentation"; break;
      case 18: return "Bandages"; break;
      case 25: return "Brass Instrument"; break;
      case 52: return "Charm"; break;
      case 34: return "Coin"; break;
      case 17: return "Combinable"; break;
      case 40: return "Compass"; break;
      case 15: return "Drink"; break;
      case 37: return "Fishing Bait"; break;
      case 36: return "Fishing Pole"; break;
      case 14: return "Food"; break;
      case 11: return "Gems"; break;
      case 29: return "Jewelry"; break;
      case 33: return "Key"; break;
      case 39: return "Key (bis)"; break;
      case 16: return "Light"; break;
      case 12: return "Lockpicks"; break;
      case 45: return "Martial"; break;
      case 32: return "Note"; break;
      case 26: return "Percussion Instrument"; break;
      case 2: return "Piercing"; break;
      case 42: return "Poison"; break;
      case 21: return "Potion"; break;
      case 20: return "Scroll"; break;
      case 8: return "Shield"; break;
      case 30: return "Skull"; break;
      case 24: return "Stringed Instrument"; break;
      case 19: return "Throwing"; break;
      case 7: return "Throwing range items"; break;
      case 31: return "Tome"; break;
      case 23: return "Wind Instrument"; break;
      default: return "$val"; break;
   }
}


/** Returns an items stats formatted for display.
 */
function GetItem($item)
{
   global $cbsql_content;
   global $dbelements;
   global $dbskills;
   global $dam2h;
   global $dbitypes;
   global $dbbagtypes;
   global $tbspells;
   global $dbbodytypes;
   global $dbbagtypetoskill;
   global $dbbardskills;
   global $link_spell;
   global $itemstatsdisplay;
   global $cbspellcache;
   global $dbnpcraces;

   //return buffer, build item here
   $Output = "";

   //use the itemstat list view
   if ($itemstatsdisplay > 0) {
      // LORE AUGMENT NODROP NORENT MAGIC
      $spaceswitch = "";
      if($item["itemtype"] == 54) {
         $Output .= "Augmentation";
         $spaceswitch = ", ";
      }

      if($item["magic"] == 1) {
         $Output .= "$spaceswitch Magic";
         $spaceswitch = ", ";
      }

      if($item["loregroup"] == -1) {
         $Output .= "$spaceswitch Lore";
         $spaceswitch = ", ";
      }

      if($item["nodrop"] == 0) {
         $Output .= "$spaceswitch No Trade";
         $spaceswitch = ", ";
      }

      if($item["norent"] == 0) {
         $Output .= "$spaceswitch Temporary";
      }

      $Output .= "<br>";

      // Classes
      $Output .= "Class: " . getclasses($item["classes"]) . "<br>";

      // Races
      $Output .= "Race: " . getraces($item["races"]) . "<br>";

      //EXPENDABLE, Charges
      if($item["clicktype"] == 3)
         $Output .= "Expendable ";

      if($item["clicktype"] > 0 && $item["maxcharges"] != 0)
         $Output .= "Charges: " . (($item["maxcharges"] > 0) ? $item["maxcharges"] : "Infinite") . "<br>";

      // Augmentation type
      if($item["itemtype"] == 54) {
         if($item["augtype"] > 0)
            $Output .= "Augmentation Type: " . getaugtype($item["augtype"]) . "<br>";
         else
            $Output .= "Augmentation Type: All<br>";
      }

      // Slots
      if($item["slots"] > 0)
         $Output .= "Slot: " . getslots($item["slots"]) . "<br>";

      // Bag-specific information
      if($item["bagslots"] > 0) {
         $Output .= "Item Type: Container<br>";
         $Output .= "Bag Slots: " . min(MAX_BAG_SLOTS, $item["bagslots"]) . "<br>";
         if($dbbagtypetoskill[$item["bagtype"]])
            $Output .= "Tradeskill Container: " . $dbskills[$dbbagtypetoskill[$item["bagtype"]]] . "<br>";

         if($item["bagwr"] > 0)
            $Output .= "Weight Reduction: " . $item["bagwr"] . "%<br>";

         $Output .= "This can hold " . getsize($item["bagsize"]) . " and smaller items.<br>";
      }

      // Damage/Delay
      if($item["damage"] > 0) {
         $WepSkill = $dbitypes[$item["itemtype"]];
         if ($item["itemtype"] == 27)
            $WepSkill = "Archery";

         $Output .= "Skill: " . $WepSkill . "<br>";
         if ($item["delay"] > 0)
            $Output .= "Delay: " . $item["delay"] . "<br>";

         $Output .= "Damage: " . number_format($item["damage"]) . "<br>";
         switch($item["itemtype"]) {
            case 0: // 1HS
            case 2: // 1HP
            case 3: // 1HB
            case 45: // H2H
            case 27: //Arrow
               $dmgbonus = 13; // floor((65-25)/3)  main hand
               $Output .= "Damage Bonus: $dmgbonus <i>(Level 65)</i><br>";
               break;
            case 5: //archery
            case 1: // 2hs
            case 4: // 2hb
            case 35: // 2hp
               $dmgbonus = $dam2h[$item["delay"]];
               $Output .= "Damage Bonus: $dmgbonus <i>(Level 65)</i><br>";
               break;
         }
      }

      //backstab dmg
      if($item["backstabdmg"] > 0)
         $Output .= "Backstab Damage: " . number_format($item["backstabdmg"]) . "<br>";

      //AC
      if($item["ac"] != 0)
         $Output .= " Armor Class: " . number_format($item["ac"]) . "<br>";

      // Attack
      if ($item["attack"] > 0)
         $Output .= "Attack: " . number_format($item["attack"]) . "<br>";

      // Elemental DMG
      if ($item["elemdmgtype"] > 0 AND $item["elemdmgamt"] != 0)
         $Output .= $dbelements[$item["elemdmgtype"]] . " Damage: " . number_format($item["elemdmgamt"]) . "<br>";

      //Bane DMG
      if ($item["banedmgrace"] > 0 AND $item["banedmgraceamt"] != 0) {
         $Output .= "Bane Damage: ";
         $Output .= $dbnpcraces[$item["banedmgrace"]];
         $Output .= " " . number_format($item["banedmgraceamt"]) . "<br>";
      }

      if ($item["banedmgbody"] > 0 AND $item["banedmgamt"] != 0) {
         $Output .= "Bane Damage: " . $dbbodytypes[$item["banedmgbody"]];
         $Output .= " " . number_format($item["banedmgamt"]) . "<br>";
      }

      // Skill Mods
      if ($item["skillmodtype"] > 0 AND $item["skillmodvalue"] != 0)
         $Output .= "Skill Modifier: " . $dbskills[$item["skillmodtype"]] . " " . number_format($item["skillmodvalue"]) . "%<br>";

      // Proc Effect
      if ($item["proceffect"] > 0 AND $item["proceffect"] < 65535) {
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["proceffect"]));
         $itemrow = $cbspellcache->get_spell($item["proceffect"]);
         if ($itemrow !== false) {
            $Output .= "Proc Effect: <a href='" . $temp . "'>" . $itemrow['name'] . "</a>";
            if ($item["proclevel2"] > 0)
               $Output .= " <i>(Level " . $item["proclevel2"] . ")</i>";

            $Output .= "<br>";
         }
         else {
            $Output .= $tab."Proc Effect: UNKNOWN";
         }
      }

      // Worn Effect
      if ($item["worneffect"] > 0 AND $item["worneffect"] < 65535) {
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["worneffect"]));
         $itemrow = $cbspellcache->get_spell($item["worneffect"]);
         if ($itemrow !== false) {
            $Output .= "Worn Effect: <a href='" . $temp . "'>"  . $itemrow['name'] . "</a>";
            if ($item["wornlevel"] > 0)
               $Output .= " <i>(Level " . $item["wornlevel"] . ")</i>";

            $Output .= "<br>";
         }
         else {
            $Output .= $tab."Worn Effect: UNKNOWN";
         }
      }

      // Focus Effect
      if ($item["focuseffect"] > 0 AND $item["focuseffect"] < 65535) {
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["focuseffect"]));
         $itemrow = $cbspellcache->get_spell($item["focuseffect"]);
         if ($itemrow !== false) {
            $Output .= "Focus Effect: <a href='" . $temp . "'>" . $itemrow['name'] . "</a>";
            if ($item["focuslevel"] > 0)
               $Output .= " <i>(Level " . $item["focuslevel"] . ")</i>";

            $Output .= "<br>";
         }
         else {
            $Output .= $tab."Focus Effect: UNKNOWN";
         }
      }

      // Click Effect
      if ($item["clickeffect"] > 0 AND $item["clickeffect"] < 65535) {
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["clickeffect"]));
         $itemrow = $cbspellcache->get_spell($item["clickeffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Click Effect: <a href='".$temp."'>".$itemrow['name']."</a>";
            $Output .= "&nbsp;(";
            if ($item["clicktype"] == 1)
               $Output .= "Any Slot, ";

            if ($item["clicktype"] == 4)
               $Output .= "Must Equip, ";

            if ($item["clicktype"] == 5)
               $Output .= "Any Slot/Can Equip, ";

            $Output .= "Casting Time: ";
            if ($item["casttime"] > 0) {
               $casttime = sprintf("%.1f",$item["casttime"] / 1000);
               $Output .= $casttime;
            }
            else
               $Output .= "Instant";

            $Output .= ")";
            if ($item["clicklevel"] > 0)
               $Output .= " <i>(Level " . $item["clicklevel"] . ")</i>";

            $Output .= "<br>";
         }
         else {
            $Output .= $tab."Click Effect: UNKNOWN";
         }
      }

      // Stats / HP / Mana / Endurance
      $Stats = "";

      // AGI
      if($item["aagi"] != 0)
         $Stats .= " Agility: " . $item ["aagi"];

      if($item["heroic_agi"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_agi"]) . "</font>";

      if ($item["aagi"] != 0 OR $item["heroic_agi"] != 0)
         $Stats .= "<br>";

      // CHA
      if($item["acha"] != 0)
         $Stats .= " Charisma: " . $item ["acha"];

      if($item["heroic_cha"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_cha"]) . "</font>";

      if ($item["acha"] != 0 OR $item["heroic_cha"] != 0)
         $Stats .= "<br>";

      // DEX
      if($item["adex"] != 0)
         $Stats .= " Dexterity: " . $item ["adex"];

      if($item["heroic_dex"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_dex"]) . "</font>";

      if ($item["adex"] != 0 OR $item["heroic_dex"] != 0)
         $Stats .= "<br>";

      // INT
      if($item["aint"] != 0)
         $Stats .= " Intelligence: " . $item ["aint"];

      if($item["heroic_int"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_int"]) . "</font>";

      if ($item["aint"] != 0 OR $item["heroic_int"] != 0)
         $Stats .= "<br>";

      // STA
      if($item["asta"] != 0)
         $Stats .= " Stamina: " . $item ["asta"];

      if($item["heroic_sta"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_sta"]) . "</font>";

      if ($item["asta"] != 0 OR $item["heroic_sta"] != 0)
         $Stats .= "<br>";

      // STR
      if($item["astr"] != 0)
         $Stats .= " Strength: " . $item ["astr"];

      if($item["heroic_str"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_str"]) . "</font>";

      if ($item["astr"] != 0 OR $item["heroic_str"] != 0)
         $Stats .= "<br>";

      // WIS
      if($item["awis"] != 0)
         $Stats .= " Wisdom: " . $item ["awis"];

      if($item["heroic_wis"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_wis"]) . "</font>";

      if($Stats != "")
         $Output .= $Stats . "<br>";

      // Resists
      $Stats = "";

      // Cold
      if($item["cr"] != 0)
         $Stats .= " Cold: " . $item["cr"];

      if($item["heroic_cr"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_cr"]) . "</font>";

      if ($item["cr"] != 0 OR $item["heroic_cr"] != 0)
         $Stats .= "<br>";

      // Disease
      if($item["dr"] != 0)
         $Stats .= " Disease: " . $item["dr"];

      if($item["heroic_dr"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_dr"]) . "</font>";

      if ($item["dr"] != 0 OR $item["heroic_dr"] != 0)
         $Stats .= "<br>";

      // Fire
      if($item["fr"] != 0)
         $Stats .= " Fire: " . $item["fr"];

      if($item["heroic_fr"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_fr"]) . "</font>";

      if ($item["fr"] != 0 OR $item["heroic_fr"] != 0)
         $Stats .= "<br>";

      // Magic
      if($item["mr"] != 0)
         $Stats .= " Magic: " . $item["mr"];

      if($item["heroic_mr"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_mr"]) . "</font>";

      if ($item["mr"] != 0 OR $item["heroic_mr"] != 0)
         $Stats .= "<br>";

      // Poison
      if($item["pr"] != 0)
         $Stats .= " Poison: " . $item["pr"];

      if($item["heroic_pr"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_pr"]) . "</font>";

      if ($item["pr"] != 0 OR $item["heroic_pr"] != 0)
         $Stats .= "<br>";

      // Corruption
      if($item["svcorruption"] != 0)
         $Stats .= " Corruption: " . $item["cr"];

      if($item["heroic_svcorrup"] != 0)
         $Stats .= " <font color = 'gold'>" . sign($item["heroic_svcorrup"]) . "</font>";

      if ($item["svcorruption"] != 0 OR $item["heroic_svcorrup"] != 0)
         $Stats .= "<br>";

      // Health
      if($item["hp"] != 0)
         $Stats .= " Health: " . number_format($item["hp"]) . "<br>";

      // Health Regeneration
      if ($item["regen"] > 0)
         $Output .= "Health Regen: " . number_format($item["regen"]) . "<br>";

      // Mana
      if($item["mana"] != 0)
         $Stats .= " Mana: " . number_format($item["mana"]) . "<br>";

      // Mana Regeneration
      if ($item["manaregen"] > 0)
         $Output .= "Mana Regen: " . number_format($item["manaregen"]) . "<br>";

      // Endurance
      if($item["endur"] != 0)
         $Stats .= " Endurance: " . number_format($item["endur"]);

      // Endurance Regeneration
      if ($item["enduranceregen"] > 0)
         $Output .= "Endurance Regen: " . number_format($item["manaregen"]) . "<br>";

      if($Stats != "")
         $Output .= $Stats . "<br>";

      // Bonuses

      // Haste
      if ($item["haste"] > 0)
         $Output .= "Haste: " . $item["haste"] . "%<br>";

      // Avoidance
      if ($item["avoidance"] > 0)
         $Output .= "Avoidance: " . $item["avoidance"] . "<br>";

      // Extra Damage Amount
      if ($item["extradmgamt"] > 0)
         $Output .= $dbskills[$item["extradmgskill"]] . " Damage: " . number_format($item["extradmgamt"]) . "<br>";

      // Damage Shield
      if ($item["damageshield"] > 0)
         $Output .= "Damage Shield: " . $item["damageshield"] . "<br>";

      // Damage Over Time Shielding
      if ($item["dotshielding"] > 0)
         $Output .= "DOT Shielding: " . $item["dotshielding"] . "%<br>";

      // Shielding
      if ($item["shielding"] > 0)
         $Output .= "Shielding: " . $item["shielding"] . "%<br>";

      // Combat Effects
      if ($item["combateffects"] > 0)
         $Output .= "Combat Effects: " . $item["combateffects"] . "<br>";

      // Accuracy
      if ($item["accuracy"] > 0)
         $Output .= "Accuracy: " . $item["accuracy"] . "<br>";

      // Spell Shielding
      if ($item["spellshield"] > 0)
         $Output .= "Spell Shielding: " . $item["spellshield"] . "%<br>";

      // Strikethrough
      if ($item["strikethrough"] > 0)
         $Output .= "Strikethrough: " . $item["strikethrough"] . "%<br>";

      // Stun Resist
      if ($item["stunresist"] > 0)
         $Output .= "Stun Resist: " . $item["stunresist"] . "%<br>";

      // Heal Amount
      if ($item["healamt"] > 0)
         $Output .= "Heal Amount: " . number_format($item["healamt"]) . "<br>";

      // Spell Damage
      if ($item["spelldmg"] > 0)
         $Output .= "Spell Damage: " . number_format($item["spelldmg"]) . "<br>";

      // Bard Items
      if ($item["bardtype"] > 0) {
         $Output .= $dbbardskills[$item["bardtype"]] . ": " . $item["bardvalue"];
         $val = (($item["bardvalue"] * 10) - 100);
         if ($val > 0)
            $Output .= "<i> (" . $val . "%)</i>";

         $Output .= "<br>";
      }

      //Required Level
      if ($item["reqlevel"] > 0)
         $Output .= "Required Level: " . $item["reqlevel"] . "<br>";

      // Recommended Level
      if ($item["reclevel"] > 0)
         $Output .= "Recommended Level: " . $item["reclevel"] . "<br>";

      // Weight
      if ($item["weight"] > 0) {
         $weight= sprintf("%.1f", ($item["weight"] / 10));
         $Output .= "Weight: " . $weight . "<br>";
      }

      // Range (Bows/Thrown)
      if($item["range"] > 0)
         $Output .= "Range: " . $item["range"] . "<br>";

      // Size
      $Output .= "Size: ". getsize($item["size"]) . "<br>";

      // Deity
      if($item["deity"] > 0)
         $Output .= "Deity: " . getdeities($item["deity"]) . "<br>";

      // Augmentations
      for($i = 1; $i <= 6; $i++) {
         if($item["augslot" . $i . "type"] > 0)
            $Output .= "Slot " . $i . ": Type " . $item["augslot" . $i . "type"] . "<br>";
      }

      // Scroll Effect
      if ($item["scrolleffect"] > 0 AND $item["scrolleffect"] < 65535) {
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["scrolleffect"]));
         $itemrow = $cbspellcache->get_spell($item["scrolleffect"]);
         if ($itemrow !== false) {
            $Output .= "Scroll Effect: <a href='" . $temp . "'>" . $itemrow['name'] . "</a>";
            $Output .= "<br>";
         }
         else {
            $Output .= $tab."Scroll Effect: UNKNOWN";
         }
      }
      
      $Output .= "Item Type: ".getitemtype($item['itemtype'])."<br>";

   //else use legacy view
   } else {
      $tab = "           ";

      // LORE AUGMENT NODROP NORENT MAGIC
      $spaceswitch= "";
      if($item["itemtype"] == 54)  { $Output .= "$spaceswitch AUGMENTATION"; $spaceswitch= " "; }
      if($item["magic"] == 1)      { $Output .= "$spaceswitch MAGIC ITEM";   $spaceswitch= " "; }
      if($item["loregroup"] == -1)   { $Output .= "$spaceswitch LORE ITEM";    $spaceswitch= " "; }
      if($item["nodrop"] == 0)     { $Output .= "$spaceswitch NO TRADE";       $spaceswitch= " "; }
      if($item["norent"] == 0)     { $Output .= "$spaceswitch NO RENT";       $spaceswitch= " "; }
      $Output .= "<br>\n";

      //EXPENDABLE, Charges
      if($item["clicktype"] == 3) { $Output .= $tab."EXPENDABLE "; }
      if($item["clicktype"]>0 && $item["maxcharges"]!=0) { $Output .= "Charges: ".(($item["maxcharges"]>0) ? $item["maxcharges"]: "Infinite")."<br>\n"; }
      // Augmentation type
      if($item["itemtype"] == 54) {
         if($item["augtype"] > 0) { $Output .= $tab."Augmentation type: ".getaugtype($item["augtype"])."<br>\n"; }
         else { $Output .= $tab."Augmentation type: for all slots<br>\n"; }
      }

      // Slots
      if($item["slots"] > 0) {$Output .= $tab."Slot: ".strtoupper(getslots($item["slots"]))."<br>\n"; }

      // Bag-specific information
      if($item["bagslots"] > 0) {
         $Output .= $tab."Item type: Container<br>\n";
         $Output .= $tab."Bag type: ".$dbbagtypes[$item["bagtype"]]."<br>\n";
         $Output .= $tab."Number of slots: ".min(MAX_BAG_SLOTS, $item["bagslots"])."<br>\n";
         if($dbbagtypetoskill[$item["bagtype"]]) { $Output .= $tab."Trade skill container: ".$dbskills[$dbbagtypetoskill[$item["bagtype"]]]."<br>\n"; }
         if($item["bagwr"] > 0) { $Output .= $tab."Weight reduction: ".$item["bagwr"]."%<br>\n"; }
         $Output .= $tab."This can hold ".strtoupper(getsize($item["bagsize"]))." and smaller items.<br>\n";
      }

      // Damage/Delay
      if($item["damage"] > 0) {
         $WepSkill = $dbitypes[$item["itemtype"]];
         if ($item["itemtype"]==27) { $WepSkill = "Archery"; }
         $Output .= $tab."Skill: ".$WepSkill." ";
         $Output .= "Atk Delay: ".$item["delay"]."<br>\n".$tab."DMG:  ".$item["damage"]."";
         switch($item["itemtype"]) {
            case 0: // 1HS
            case 2: // 1HP
            case 3: // 1HB
            case 45: // H2H
            case 27: //Arrow
               $dmgbonus = 13; // floor((65-25)/3)  main hand
               $Output .= $tab."Dmg bonus:$dmgbonus <i>(lvl 65)</i>";
               if($item["ac"]==0)  { $Output .= "<br>\n"; }
               break;
            case 5: //archery
            case 1: // 2hs
            case 4: // 2hb
            case 35: // 2hp
               $dmgbonus = $dam2h[$item["delay"]];
               $Output .= $tab."Dmg bonus: $dmgbonus <i>(lvl 65)</i>";
               if($item["ac"]==0) { $Output .= "<br>\n"; }
               break;
         }
      }

      //backstab dmg
      if($item["backstabdmg"] > 0)
      {
         $Output .= "Backstab Damage: " . $item["backstabdmg"] . "<br>\n";
      }

      //AC
      if($item["ac"] != 0) { $Output .= $tab." AC: ".$item["ac"]."<br>\n"; }

      // Elemental DMG
      if (($item["elemdmgtype"]>0) AND ($item["elemdmgamt"]!=0)) { $Output .= $tab.strtolower_ucfirst($dbelements[$item["elemdmgtype"]])." DMG: ".sign($item["elemdmgamt"])."<br>\n"; }

      //Bane DMG
      if (($item["banedmgrace"]>0) AND ($item["banedmgraceamt"]!=0)) {
         $Output .= $tab."Bane DMG: ";
         $Output .= $dbnpcraces[$item["banedmgrace"]];
         $Output .= " ".sign($item["banedmgraceamt"])."<br>\n";
      }
      if (($item["banedmgbody"]>0) AND ($item["banedmgamt"]!=0)) {
         $Output .= $tab."Bane DMG: ".$dbbodytypes[$item["banedmgbody"]];
         $Output .= " ".sign($item["banedmgamt"])."<br>\n";
      }

      // Skill Mods
      if (($item["skillmodtype"]>0) AND ($item["skillmodvalue"]!=0)) { $Output .= $tab."Skill Mod: ".strtolower_ucfirst($dbskills[$item["skillmodtype"]])." ".sign($item["skillmodvalue"])."%<br>\n"; }


      //item proc
      if (($item["proceffect"]>0) AND ($item["proceffect"]<65535)) {
         //build the link from the spell template
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["proceffect"]));
         
         $itemrow = $cbspellcache->get_spell($item["proceffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Effect: <a href='".$temp."'>".$itemrow['name']."</a>";
            $Output .= "&nbsp;(Combat)";
            $Output .= " <i>(Level ".$item["proclevel2"].")</i>";
            $Output .= "<br>\n";
         }
         else {
            $Output .= $tab."Effect: UNKNOWN";
         }
      }

      // worn effect
      if (($item["worneffect"]>0) AND ($item["worneffect"]<65535)) {
         //build the link from the spell template
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["worneffect"]));
         $itemrow = $cbspellcache->get_spell($item["worneffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Effect: <a href='".$temp."'>".$itemrow['name']."</a>";
            $Output .= "&nbsp;(Worn)";
            $Output .= " <i>(Level ".$item["wornlevel"].")</i>";
            $Output .= "<br>\n";
         }
         else {
            $Output .= $tab."Effect: UNKNOWN";
         }
      }

      // focus effect
      if (($item["focuseffect"]>0) AND ($item["focuseffect"]<65535)) {
         //build the link from the spell template
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["focuseffect"]));
         $itemrow = $cbspellcache->get_spell($item["focuseffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Focus: <a href='".$temp."'>".$itemrow['name']."</a>";
            if ($item["focuslevel"]>0) { $Output .= " <i>(Level ".$item["focuslevel"].")</i>";  }
            $Output .= "<br>\n";
         }
         else {
            $Output .= $tab."Focus: UNKNOWN";
         }
      }

      // clicky effect
      if (($item["clickeffect"]>0) AND ($item["clickeffect"]<65535)) {
         //build the link from the spell template
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["clickeffect"]));
         $itemrow = $cbspellcache->get_spell($item["clickeffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Effect: <a href='".$temp."'>".$itemrow['name']."</a>";
            $Output .= "&nbsp;(";
            if ($item["clicktype"]==1) { $Output .= "Any Slot, "; }
            if ($item["clicktype"]==4) { $Output .= "Must Equip, ";   }
            if ($item["clicktype"]==5) { $Output .= "Any Slot/Can Equip, "; }
            $Output .= "Casting Time: ";
            if ($item["casttime"]>0) {
               $casttime = sprintf("%.1f",$item["casttime"]/1000);
               $Output .= $casttime;
            }
            else  { $Output .= "Instant"; }
            $Output .= ")";
            $Output .= " <i>(Level ".$item["clicklevel"].")</i>";
            $Output .= "<br>\n";
         }
         else {
            $Output .= $tab."Effect: UNKNOWN";
         }
      }

      // Stats / HP / Mana / Endurance
      $Stats = "";
      if($item[ "astr"] != 0)  $Stats        .= " STR: "            . $item ["astr"];
      if($item["heroic_str"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_str"]) . "</font>";
      if($item[ "asta"] != 0)  $Stats        .= " STA: "            . $item ["asta"];
      if($item["heroic_sta"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_sta"]) . "</font>";
      if($item[ "aagi"] != 0)  $Stats        .= " AGI: "            . $item ["aagi"];
      if($item["heroic_agi"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_agi"]) . "</font>";
      if($item[ "adex"] != 0)  $Stats        .= " DEX: "            . $item ["adex"];
      if($item["heroic_dex"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_dex"]) . "</font>";
      if($item[ "awis"] != 0)  $Stats        .= " WIS: "            . $item ["awis"];
      if($item["heroic_wis"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_wis"]) . "</font>";
      if($item[ "aint"] != 0)  $Stats        .= " INT: "            . $item ["aint"];
      if($item["heroic_int"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_int"]) . "</font>";
      if($item[ "acha"] != 0)  $Stats        .= " CHA: "            . $item ["acha"];
      if($item["heroic_cha"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_cha"]) . "</font>";
      if($Stats != "") { $Output .= $tab.$Stats."<br>\n"; }

      //resists
      $Stats = "";
      if($item[   "fr"] != 0)  $Stats .= " Fire: "           . $item["fr"] ;
      if($item["heroic_fr"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_fr"]) . "</font>";
      if($item[   "dr"] != 0)  $Stats .= " Disease: "        . $item["dr"] ;
      if($item["heroic_dr"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_dr"]) . "</font>";
      if($item[   "cr"] != 0)  $Stats .= " Cold: "           . $item["cr"] ;
      if($item["heroic_cr"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_cr"]) . "</font>";
      if($item[   "mr"] != 0)  $Stats .= " Magic: "          . $item["mr"] ;
      if($item["heroic_mr"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_mr"]) . "</font>";
      if($item[   "pr"] != 0)  $Stats .= " Poison: "         . $item["pr"];
      if($item["heroic_pr"] != 0) $Stats .= " <font color = 'gold'>" . sign($item["heroic_pr"]) . "</font>";
      if($item[   "hp"] != 0)  $Stats .= " HP: "            .sign($item   ["hp"]);
      if($item[ "mana"] != 0)  $Stats .= " MANA: "          .sign($item ["mana"]);
      if($item["endur"] != 0)  $Stats .= " Endurance: "     .sign($item["endur"]);
      if($Stats != "") { $Output .= $tab.$Stats."<br>\n"; }

      // bonuses
      if ($item["haste"]>0) {$Output .= $tab."Haste: ".$item["haste"]."%<br>\n";   }
      if ($item["avoidance"]>0) { $Output .= $tab."Avoidance: ".sign($item["avoidance"])."<br>\n";   }
      if ($item["attack"]>0) { $Output .= $tab."Attack: ".sign($item["attack"])."<br>\n";   }
      if ($item["extradmgamt"]>0) { $Output .= $tab.strtolower_ucfirst($dbskills[$item["extradmgskill"]])." DMG: ".sign($item["extradmgamt"])."<br>\n";   }
      if ($item["damageshield"]>0) { $Output .= $tab."Damage Shield: ".sign($item["damageshield"])."<br>\n";   }
      if ($item["dotshielding"]>0) { $Output .= $tab."Dot Shielding: ".sign($item["dotshielding"])."%<br>\n";   }
      if ($item["manaregen"]>0) { $Output .= $tab."Mana Regeneration: ".sign($item["manaregen"])."<br>\n";   }
      if ($item["shielding"]>0) { $Output .= $tab."Shielding: ".sign($item["shielding"])."%<br>\n";   }
      if ($item["regen"]>0) { $Output .= $tab."Regeneration: ".sign($item["regen"])."<br>\n";   }
      if ($item["combateffects"]>0) { $Output .= $tab."Combat Effects: ".sign($item["combateffects"])."<br>\n";   }
      if ($item["accuracy"]>0) { $Output .= $tab."Accuracy: ".sign($item["accuracy"])."<br>\n";   }
      if ($item["spellshield"]>0) { $Output .= $tab."Spell Shielding: ".sign($item["spellshield"])."%<br>\n";   }
      if ($item["strikethrough"]>0) { $Output .= $tab."Strikethrough: ".sign($item["strikethrough"])."%<br>\n";   }
      if ($item["stunresist"]>0) { $Output .= $tab."Stun Resist: ".sign($item["stunresist"])."%<br>\n";   }


      // bard item ?
      if ($item["bardtype"]>0) {
         $Output .= $tab.$dbbardskills[$item["bardtype"]].": ".$item["bardvalue"];
         $val=($item["bardvalue"]*10)-100;
         if ($val>0)
            $Output .= "<i> (".sign($val)."%)</i>";
         $Output .= "<br>\n";
      }

      //required level
      if ($item["reqlevel"]>0) {
         $Output .= $tab."Required level of ".$item["reqlevel"].".<br>\n";
      }

      //recomended level
      if ($item["reclevel"]>0) {
         $Output .= $tab."Recommended level of ".$item["reclevel"].".<br>\n";
      }

      // Weight
      $weight= sprintf("%.1f",($item["weight"]/10));
      $Output .= $tab."WT: ".$weight." ";

      // Item range
      if($item["range"] > 0) { $Output .= $tab."Range: ".$item["range"]." "; }

      //size
      $Output .= $tab."Size: ".strtoupper(getsize($item["size"]))."<br>\n";

      //classes
      $Output .= $tab."Class: ".getclasses($item["classes"])."<br>\n";

      //races
      $Output .= $tab."Race: ".getraces($item["races"])."<br>\n";

      // Deity
      if($item["deity"] > 0) { $Output .= $tab."Deity: ".getdeities($item["deity"])."<br>\n"; }

      // Augmentations
      for( $i = 1; $i <= 5; $i ++) {
         if($item["augslot".$i."type"] > 0) { $Output .= $tab."Slot ".$i.": Type ".$item["augslot".$i."type"]."<br>\n"; }
      }

      // scroll
      if (($item["scrolleffect"] > 0) AND ($item["scrolleffect"] < 65535)) {
         //build the link from the spell template
         $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["scrolleffect"]));
         $itemrow = $cbspellcache->get_spell($item["scrolleffect"]);
         if ($itemrow !== false) {
            $Output .= $tab."Effect: <a href='".$temp."'>".$itemrow['name']."</a>";
            $Output .= "<br>\n";
         }
         else {
            $Output .= $tab."Effect: UNKNOWN";
         }
      }
      
      
      $Output .= "<br><br>Item Type: ".getitemtype($item['itemtype'])."<br>";
   }


   return $Output;
}


?>