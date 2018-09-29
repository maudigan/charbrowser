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
	 ***************************************************************************/




	if (!defined('INCHARBROWSER'))
	   die("Hacking attempt");

	include_once(__DIR__ . "/global.php");
	include_once(__DIR__ . "/db.php");



	/** Runs '$query' and returns the value of '$field' of the first (arbitrarily) found row
	 *  If no row is selected by '$query', returns an empty string
	 */
	function GetFieldByQuery($field, $query) {
	   global $cbsql;
	   $QueryResult = $cbsql->query($query);
	   if ($cbsql->rows($QueryResult) > 0) {
		  $rows = $cbsql->nextrow($QueryResult);
		  $Result = $rows[$field];
	   } else
		  $Result = "";
	   
	   return $Result;
	}

	function strtolower_ucfirst($txt) {
	   if ($txt == "") {
		  return $txt;
	   } else {
		  $txt = strtolower($txt);
		  $txt[0] = strtoupper($txt[0]);
		  return $txt;
	   }
	}

	// Slot List
	function getslots($val) {
	   global $dbslots;
	   reset($dbslots);
	   do {
		  $key = key($dbslots);
		  if ($key <= $val) {
			 $val -= $key;
			 $Result = current($dbslots) . $v . $Result;
			 $v = " ";
		  }
	   } while (next($dbslots));
	   return $Result;
	}

	function getraces($val) {
	   if ($val == 0)
		  return "NONE";

	   global $dbraces;
	   reset($dbraces);
	   do {
		  $key = key($dbraces);
		  if ($key <= $val) {
			 $val -= $key;
			 $res = current($dbraces) . $v . $res;
			 $v   = " ";
		  }
	   } while (next($dbraces));
	   return $res;
	}

	function getclasses($val) {
	   if ($val == 0)
		  return "NONE";

	   global $dbiclasses;
	   reset($dbiclasses);
	   do {
		  $key = key($dbiclasses);
		  if ($key <= $val) {
			 $val -= $key;
			 $res = current($dbiclasses) . $v . $res;
			 $v   = " ";
		  }
	   } while (next($dbiclasses));
	   return $res;
	}

	function getdeities($val) {
	   global $dbideities;
	   reset($dbideities);
	   do {
		  $key = key($dbideities);
		  if ($key <= $val) {
			 $val -= $key;
			 $res .= $v . current($dbideities);
			 $v = ", ";
		  }
	   } while (next($dbideities));
	   return $res;
	}


	function getaugtype($val) {
		$augtypes = array();
		if ($val == 2147483647) return "All Types;"; // All Augment Types
		for ($i = 30; $i >= 0; $i--) {
			if ((2 ** $i) <= $val) {
				$val -= $key;
				$augtypes[$i] = 1;
			}
		}
		return implode(", ", $augtypes);
	}


	function sign($val) {
	   if ($val > 0)
		  return "+$val";
	   else
		  return $val;
	}

	function getsize($val) {
	   switch ($val) {
		  case 0:
			 return "Tiny";
			 break;
		  case 1:
			 return "Small";
			 break;
		  case 2:
			 return "Medium";
			 break;
		  case 3:
			 return "Large";
			 break;
		  case 4:
			 return "Giant";
			 break;
		  default:
			 return "$val?";
			 break;
	   }
	}

	/** Returns an items stats formatted for display.
	 */
	function GetItem($item) {   
	   global $dbelements, $dbskills, $dam2h, $dbitypes, $tbspells, $tbraces, $dbbodytypes, $dbbardskills, $link_spell;   
	   //return buffer, build item here
	   $Output = "";
	   
	   // Augment / Magic / Lore / No Trade / Temporary
	   $spaceswitch = "";
	   if ($item["itemtype"] == 54) {
		  $Output .= "Augmentation";
		  $spaceswitch = ", ";
	   }

	   if ($item["magic"] == 1) {
		  $Output .= "$spaceswitch Magic";
		  $spaceswitch = ", ";
	   }

	   if ($item["loregroup"] == -1) {
		  $Output .= "$spaceswitch Lore";
		  $spaceswitch = ", ";
	   }

	   if ($item["nodrop"] == 0) {
		  $Output .= "$spaceswitch No Trade";
		  $spaceswitch = ", ";
	   }

	   if ($item["norent"] == 0) {
		  $Output .= "$spaceswitch Temporary";
		  $spaceswitch = ", ";
	   }
	   
	   if ($spaceswitch != "")
		  $Output .= "<br>";
	   
	   //Expendable/Charges
	   if ($item["clicktype"] == 3)
		  $Output .= $tab . "Expendable ";

	   if ($item["clicktype"] > 0 && $item["maxcharges"] != 0)
		  $Output .= "Charges: " . (($item["maxcharges"] > 0) ? $item["maxcharges"] : "Infinite") . "<br>";

	   // Augmentation Type
	   if ($item["itemtype"] == 54) {
		  if ($item["augtype"] > 0)
			 $Output .= "Augmentation Type: " . getaugtype($item["augtype"]) . "<br>";
		  else
			 $Output .= "Augmentation Type: All Types<br>";
	   }
	   
	   // Slots
	   if ($item["slots"] > 0)
		  $Output .= "Slot: " . getslots($item["slots"]) . "<br>";
	   
	   // Bag-specific information
	   if ($item["bagslots"] > 0) {
		  $Output .= "Type: Container<br>";
		  $Output .= "Slots: " . $item["bagslots"] . "<br>";
		  if ($item["bagtype"] > 0 && $dbbagtypes[$item["bagtype"]] != "")
			 $Output .= "Tradeskill Container: " . $dbbagtypes[$item["bagtype"]] . "<br>";

		  if ($item["bagwr"] > 0)
			 $Output .= "Weight Reduction: " . $item["bagwr"] . "%<br>";

		  $Output .= "This can hold " . getsize($item["bagsize"]) . " and smaller items.<br>";
	   }
	   
	   // Damage/Delay
	   if ($item["damage"] > 0) {
		  $weapon_skill = $dbitypes[$item["itemtype"]];
		  if ($item["itemtype"] == 27)
			 $weapon_skill = "Archery";

		  $Output .= "Skill: " . $weapon_skill . "<br>";
		  $Output .= "Attack Delay: " . $item["delay"] . "<br>";
		  $Output .= "Damage: " . $item["damage"];
		  switch ($item["itemtype"]) {
			 case 0: // 1H Slashing
			 case 2: // 1H Piercing
			 case 3: // 1H Blunt
			 case 45: // Hand to Hand
			 case 27: // Arrow
				$damage_bonus = 13; // floor((65-25)/3)  main hand
				$Output .= "Damage Bonus: $damage_bonus <i>(Level 65)</i><br>";
				break;
			 case 5: // Archery
			 case 1: // 2H Slashing
			 case 4: // 2H Blunt 
			 case 35: // 2H Piercing
				$damage_bonus = $dam2h[$item["delay"]];
				$Output .= "Damage Bonus: $damage_bonus <i>(Level 65)</i><br>";
				break;
		  }
	   }
	   
	   // Backstab Damage
	   if ($item["backstabdmg"] > 0)
		  $Output .= "Backstab Damage: " . number_format($item["backstabdmg"]) . "<br>";
	   
	   // Armor Class
	   if ($item["ac"] != 0)
		  $Output .= "Armor Class: " . number_format($item["ac"]) . "<br>";
	   
	   // Elemental Damage
	   if ($item["elemdmgtype"] > 0 AND $item["elemdmgamt"] != 0)
		  $Output .= $dbelements[$item["elemdmgtype"]] . " Damage: " . $item["elemdmgamt"] . "<br>";

	   // Bane Damage
	   if ($item["banedmgrace"] > 0 AND $item["banedmgraceamt"] != 0) {
		  $Output .= "Bane Damage: ";
		  $Output .= GetFieldByQuery("name", "SELECT name FROM $tbraces WHERE id = '" . $item["banedmgrace"] . "'");
		  $Output .= " " . $item["banedmgraceamt"] . "<br>";
	   }

	   if ($item["banedmgbody"] > 0 AND $item["banedmgamt"] != 0) {
		  $Output .= "Bane Damage: " . $dbbodytypes[$item["banedmgbody"]];
		  $Output .= " " . $item["banedmgamt"] . "<br>";
	   }
	   
	   // Skill Mods
	   if (($item["skillmodtype"] > 0) AND ($item["skillmodvalue"] != 0)) {
		  $Output .= "Skill Modifier: " . $dbskills[$item["skillmodtype"]] . " " . $item["skillmodvalue"] . "%<br>";
	   }
	   
	   
	   //item proc
	   if ($item["proceffect"] > 0 AND $item["proceffect"] < 65535) {
		  //build the link from the spell template
		  $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["proceffect"]));
		  $Output .= $tab . "Proc Effect: <a href='" . $temp . "'>" . GetFieldByQuery("name", "SELECT name FROM $tbspells WHERE id=" . $item["proceffect"]) . "</a>";
		  $Output .= "&nbsp;(Combat)";
		  $Output .= " <i>(Level " . $item["proclevel2"] . ")</i>";
		  $Output .= "<br>";
	   }
	   
	   // worn effect
	   if ($item["worneffect"] > 0 AND $item["worneffect"] < 65535) {
		  //build the link from the spell template
		  $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["worneffect"]));
		  $Output .= $tab . "Worn Effect: <a href='" . $temp . "'>" . GetFieldByQuery("name", "SELECT name FROM $tbspells WHERE id=" . $item["worneffect"]) . "</a>";
		  $Output .= "&nbsp;(Worn)";
		  $Output .= " <i>(Level " . $item["wornlevel"] . ")</i>";
		  $Output .= "<br>";
	   }
	   
	   // focus effect
	   if ($item["focuseffect"] > 0 AND $item["focuseffect"] < 65535) {
		  //build the link from the spell template 
		  $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["focuseffect"]));
		  $Output .= $tab . "Focus Effect: <a href='" . $temp . "'>" . GetFieldByQuery("name", "SELECT name FROM $tbspells WHERE id=" . $item["focuseffect"]) . "</a>";
		  if ($item["focuslevel"] > 0) {
			 $Output .= " <i>(Level " . $item["focuslevel"] . ")</i>";
		  }
		  $Output .= "<br>";
	   }
	   
	   // clicky effect
	   if ($item["clickeffect"] > 0 AND $item["clickeffect"] < 65535) {
		  //build the link from the spell template
		  $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["clickeffect"]));
		  $Output .= $tab . "Click Effect: <a href='" . $temp . "'>" . GetFieldByQuery("name", "SELECT name FROM $tbspells WHERE id=" . $item["clickeffect"]) . "</a>";
		  $Output .= "&nbsp;(";
		  if ($item["clicktype"] == 1)
			 $Output .= "Any Slot, ";
		  elseif ($item["clicktype"] == 4)
			 $Output .= "Must Equip, ";
		  elseif ($item["clicktype"] == 5)
			 $Output .= "Any Slot/Can Equip, ";

		  $Output .= "Casting Time: ";
		  if ($item["casttime"] > 0) {
			 $casttime = sprintf("%.1f", $item["casttime"] / 1000);
			 $Output .= $casttime;
		  } else {
			 $Output .= "Instant";
		  }
		  $Output .= ")";
		  $Output .= " <i>(Level " . $item["clicklevel"] . ")</i>";
		  $Output .= "<br>";
	   }
	   
	   // Stats / HP / Mana / Endurance
	   $Stats = "";

	   // STR Begin
	   if ($item["astr"] != 0)
		  $Stats .= "STR: " . $item["astr"];

	   if ($item["astr"] == 0 && $item["heroic_str"] != 0)
		  $Stats .= "STR: 0";

	   if ($item["heroic_str"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_str"]) . "</font>";

	   if ($item["astr"] != 0 || $item["heroic_str"] != 0)
		  $Stats .= "<br>";
	   // STR End

	   // STA Begin
	   if ($item["asta"] != 0)
		  $Stats .= "STA: " . $item["asta"];

	   if ($item["asta"] == 0 && $item["heroic_sta"] != 0)
		  $Stats .= "STA: 0";

	   if ($item["heroic_sta"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_sta"]) . "</font>";

	   if ($item["asta"] != 0 || $item["heroic_sta"] != 0)
		  $Stats .= "<br>";
	   // STA End

	   // AGI Begin
	   if ($item["aagi"] != 0)
		  $Stats .= "AGI: " . $item["aagi"];

	   if ($item["aagi"] == 0 && $item["heroic_agi"] != 0)
		  $Stats .= "AGI: 0";

	   if ($item["heroic_agi"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_agi"]) . "</font>";

	   if ($item["aagi"] != 0 || $item["heroic_agi"] != 0)
		  $Stats .= "<br>";
	   // AGI End

	   // DEX Begin
	   if ($item["adex"] != 0)
		  $Stats .= "DEX: " . $item["adex"];

	   if ($item["adex"] == 0 && $item["heroic_dex"] != 0)
		  $Stats .= "DEX: 0";

	   if ($item["heroic_dex"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_dex"]) . "</font>";

	   if ($item["adex"] != 0 || $item["heroic_dex"] != 0)
		  $Stats .= "<br>";
	   // DEX End

	   // WIS Begin
	   if ($item["awis"] != 0)
		  $Stats .= "WIS: " . $item["awis"];

	   if ($item["awis"] == 0 && $item["heroic_wis"] != 0)
		  $Stats .= "WIS: 0";

	   if ($item["heroic_wis"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_wis"]) . "</font>";

	   if ($item["awis"] != 0 || $item["heroic_wis"] != 0)
		  $Stats .= "<br>";
	   // WIS End

	   // INT Begin
	   if ($item["aint"] != 0)
		  $Stats .= "INT: " . $item["aint"];

	   if ($item["aint"] == 0 && $item["heroic_int"] != 0)
		  $Stats .= "INT: 0";

	   if ($item["heroic_int"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_int"]) . "</font>";

	   if ($item["aint"] != 0 || $item["heroic_int"] != 0)
		  $Stats .= "<br>";
	   // INT End

	   // CHA Begin
	   if ($item["acha"] != 0)
		  $Stats .= "CHA: " . $item["acha"];

	   if ($item["acha"] == 0 && $item["heroic_cha"] != 0)
		  $Stats .= "CHA: 0";

	   if ($item["heroic_cha"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_cha"]) . "</font>";
	   // CHA End

	   if ($Stats != "")
		  $Output .= $Stats . "<br>";
	   
	   // Resists
	   $Stats = "";

	   // FR Begin
	   if ($item["fr"] != 0)
		  $Stats .= "Fire: " . $item["fr"];

	   if ($item["fr"] == 0 && $item["heroic_fr"] != 0)
		  $Stats .= "Fire: 0";

	   if ($item["heroic_fr"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_fr"]) . "</font>";

	   if ($item["fr"] != 0 || $item["heroic_fr"] != 0)
		  $Stats .= "<br>";
	   // FR End

	   // DR Begin
	   if ($item["dr"] != 0)
		  $Stats .= "Disease: " . $item["dr"];

	   if ($item["dr"] == 0 && $item["heroic_dr"] != 0)
		  $Stats .= "Disease: 0";

	   if ($item["heroic_dr"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_dr"]) . "</font>";

	   if ($item["dr"] != 0 || $item["heroic_dr"] != 0)
		  $Stats .= "<br>";
	   // DR End

	   // CR Begin
	   if ($item["cr"] != 0)
		  $Stats .= "Cold: " . $item["cr"];

	   if ($item["cr"] == 0 && $item["heroic_cr"] != 0)
		  $Stats .= "Cold: 0";

	   if ($item["heroic_cr"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_cr"]) . "</font>";

	   if ($item["cr"] != 0 || $item["heroic_cr"] != 0)
		  $Stats .= "<br>";
	   // CR End

	   // MR Begin
	   if ($item["mr"] != 0)
		  $Stats .= "Magic: " . $item["mr"];

	   if ($item["mr"] == 0 && $item["heroic_mr"] != 0)
		  $Stats .= "Magic: 0";

	   if ($item["heroic_mr"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_mr"]) . "</font>";

	   if ($item["mr"] != 0 || $item["heroic_mr"] != 0)
		  $Stats .= "<br>";
	   // MR End

	   // PR Begin
	   if ($item["pr"] != 0)
		  $Stats .= "Poison: " . $item["pr"];

	   if ($item["pr"] == 0 && $item["heroic_pr"] != 0)
		  $Stats .= "Poison: 0";

	   if ($item["heroic_pr"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_pr"]) . "</font>";

	   if ($item["pr"] != 0 || $item["heroic_pr"] != 0)
		  $Stats .= "<br>";
	   // PR End
	   
	   // Corruption Begin	   
	   if ($item["svcorruption"] != 0)
		  $Stats .= "Corruption: " . $item["svcorruption"];

	   if ($item["svcorruption"] == 0 && $item["heroic_svcorrup"] != 0)
		  $Stats .= "Corruption: 0";

	   if ($item["heroic_svcorrup"] != 0)
		  $Stats .= " <font color = 'gold'>" . sign($item["heroic_svcorrup"]) . "</font>";

	   if ($item["svcorruption"] != 0 || $item["heroic_svcorrup"] != 0)
		  $Stats .= "<br>";	   
	   // Corruption End

	   if ($item["hp"] != 0)
		  $Stats .= "Health: " . number_format($item["hp"]) . "<br>";

	   if ($item["mana"] != 0)
		  $Stats .= "Mana: " . number_format($item["mana"]) . "<br>";

	   if ($item["endur"] != 0)
		  $Stats .= "Endurance: " . number_format($item["endur"]) . "<br>";

	   if ($Stats != "")
		  $Output .= $Stats;
	   
	   // Bonuses
	   if ($item["haste"] != 0)
		  $Output .= "Haste: " . $item["haste"] . "%<br>";

	   if ($item["avoidance"] != 0)
		  $Output .= "Avoidance: " . $item["avoidance"] . "<br>";

	   if ($item["attack"] != 0)
		  $Output .= "Attack: " . $item["attack"] . "<br>";

	   if ($item["extradmgamt"] != 0)
		  $Output .= $dbskills[$item["extradmgskill"]] . " Damage: " . $item["extradmgamt"] . "<br>";

	   if ($item["damageshield"] != 0)
		  $Output .= "Damage Shield: " . $item["damageshield"] . "<br>";

	   if ($item["dotshielding"] != 0)
		  $Output .= "Damage Over Time Shielding: " . $item["dotshielding"] . "%<br>";

	   if ($item["manaregen"] != 0)
		  $Output .= "Mana Regeneration: " . $item["manaregen"] . "<br>";

	   if ($item["shielding"] != 0)
		  $Output .= "Shielding: " . $item["shielding"] . "%<br>";

	   if ($item["hpregen"] != 0)
		  $Output .= "Regeneration: " . $item["hpregen"] . "<br>";

	   if ($item["combateffects"] != 0)
		  $Output .= "Combat Effects: " . $item["combateffects"] . "<br>";

	   if ($item["accuracy"] != 0)
		  $Output .= "Accuracy: " . $item["accuracy"] . "<br>";

	   if ($item["combatskill"] != 0)
		  $Output .= $dbskills[$item["combatskill"]] . " Damage: " . $item["combatskilldmg"] . "<br>";

	   if ($item["spellshield"] != 0)
		  $Output .= "Spell Shielding: " . $item["spellshield"] . "%<br>";

	   if ($item["strikethrough"] != 0)
		  $Output .= "Strikethrough: " . $item["strikethrough"] . "%<br>";

	   if ($item["stunresist"] != 0)
		  $Output .= "Stun Resist: " . $item["stunresist"] . "%<br>";   
	   
	   // Bard Type
	   if ($item["bardtype"] != 0) {
		  $Output .= $tab . $dbbardskills[$item["bardtype"]] . ": " . $item["bardvalue"];
		  $val = (($item["bardvalue"] * 10) - 100);
		  if ($val > 0)
			 $Output .= "<i> (" . $val . "%)</i>";

		  $Output .= "<br>";
	   }
	   
	   // Required Level
	   if ($item["reqlevel"] > 0)
		  $Output .= $tab . "Required level of " . $item["reqlevel"] . ".<br>";

	   // Recommended Level
	   if ($item["reclevel"] > 0)
		  $Output .= $tab . "Recommended level of " . $item["reclevel"] . ".<br>";
	   
	   // Weight
	   if ($item["weight"] > 0.0)
		  $Output .= $tab . "Weight: " . ($item["weight"] / 10) . "<br>";

	   // Range
	   if ($item["range"] > 0)
		  $Output .= $tab . "Range: " . $item["range"] . " ";
	   
	   // Size
	   $Output .= $tab . "Size: " . getsize($item["size"]) . "<br>";
	   
	   // Classes
	   $Output .= $tab . "Class: " . getclasses($item["classes"]) . "<br>";
	   
	   // Races
	   $Output .= $tab . "Race: " . getraces($item["races"]) . "<br>";
	   
	   // Deities
	   if ($item["deity"] > 0)
		  $Output .= $tab . "Deity: " . getdeities($item["deity"]) . "<br>";
	   
	   // Augmentations
	   for ($i = 1; $i <= 6; $i++) 
		  if ($item["augslot" . $i . "type"] > 0)
			 $Output .= $tab . "Slot " . $i . ": Type " . $item["augslot" . $i . "type"] . "<br>";

	   // Scroll Effect
	   if ($item["scrolleffect"] > 0 AND $item["scrolleffect"] < 65535) {
		  //build the link from the spell template
		  $temp = QuickTemplate($link_spell, array('SPELL_ID' => $item["scrolleffect"]));
		  $Output .= $tab . "Effect: <a href='" . $temp . "'>" . GetFieldByQuery("name", "SELECT name FROM $tbspells WHERE id=" . $item["scrolleffect"]) . "</a>";
		  $Output .= "<br>";
	   }   
	   
	   return $Output;
	}
?>