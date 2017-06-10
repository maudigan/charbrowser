<div class='NavOuter'>
<div class='NavInner'>
  <div class='FreeButton' onclick="window.location='character.php?char={NAME}';" style="margin:3px">{L_INVENTORY}</div>
  <div class='FreeButton' onclick="window.location='aas.php?char={NAME}';" style="margin:3px">{L_AAS}</div>
  <div class='FreeButton' onclick="window.location='keys.php?char={NAME}';" style="margin:3px">{L_KEYS}</div>
  <div class='FreeButton' onclick="window.location='flags.php?char={NAME}';" style="margin:3px">{L_FLAGS}</div>
  <div class='FreeButton' style="color:606060;margin:3px">{L_SKILLS}</div>
  <div class='FreeButton' onclick="window.location='corpse.php?char={NAME}';" style="margin:3px">{L_CORPSE}</div>
  <div class='FreeButton' onclick="window.location='factions.php?char={NAME}';" style="margin:3px">{L_FACTION}</div>
  <div class='FreeButton' onclick="window.location='charmove.php?char={NAME}';" style="margin:3px">{L_CHARMOVE}</div>
  <div class='FreeButton' onclick="window.external.AddFavorite(location.href, document.title);" style="margin:3px">{L_BOOKMARK}</div>
</div>
</div>
<center>
  <div class='ItemOuter'>
    <div class='ItemTitle'>
      <div class='ItemTitleLeft'></div>
      <div class='ItemTitleMid'>{L_SKILLS} - {NAME}</div>
      <div class='ItemTitleRight'></div>
    </div>
    <div class='ItemInner'>
      <center>
        <table class='StatTable' style='width:90%;'>
	  <!-- BEGIN switch_language -->
	  <tr>    <td colspan='2' class='ColumnHead' align='center'>{L_LANGUAGE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Common Tongue</td>    <td> {switch_language.COMMON_TONGUE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Barbarian</td>    <td> {switch_language.BARBARIAN}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Erudian</td>    <td> {switch_language.ERUDIAN}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Elvish</td>    <td> {switch_language.ELVISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dark Elvish</td>    <td> {switch_language.DARK_ELVISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dwarvish</td>    <td> {switch_language.DWARVISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Troll</td>    <td> {switch_language.TROLL}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Ogre</td>    <td> {switch_language.OGRE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Gnomish</td>    <td> {switch_language.GNOMISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Halfling</td>    <td> {switch_language.HALFLING}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Thieves Cant</td>    <td> {switch_language.THIEVES_CANT}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Old Erudian</td>    <td> {switch_language.OLD_ERUDIAN}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Elder Elvish</td>    <td> {switch_language.ELDER_ELVISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Froglok</td>    <td> {switch_language.FROGLOK}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Goblin</td>    <td> {switch_language.GOBLIN}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Gnoll</td>    <td> {switch_language.GNOLL}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Combine Tongue</td>    <td> {switch_language.COMBINE_TONGUE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Elder Tier`dal</td>    <td> {switch_language.ELDER_TEIRDAL}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>LizardMan</td>    <td> {switch_language.LIZARDMAN}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Orcish</td>    <td> {switch_language.ORCISH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Faerie</td>    <td> {switch_language.FAERIE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dragon</td>    <td> {switch_language.DRAGON}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Elder Dragon</td>    <td> {switch_language.ELDER_DRAGON}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dark Speech</td>    <td> {switch_language.DARK_SPEECH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Vah Shir</td>    <td> {switch_language.VAH_SHIR}</td>  </tr> 
	  <!-- END switch_language -->
	  <tr>    <td colspan='2' class='ColumnHead' align='center'><br>{L_COMBAT}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>1H Blunt</td>    <td> {1H_BLUNT}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>1H Slashing</td>    <td> {1H_SLASHING}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>2H Blunt</td>    <td> {2H_BLUNT}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>2H Slashing</td>    <td> {2H_SLASHING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Archery</td>    <td> {ARCHERY}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Bash</td>    <td> {BASH}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Block</td>    <td> {BLOCK}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Defense</td>    <td> {DEFENSE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Disarm</td>    <td> {DISARM}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dodge</td>    <td> {DODGE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Double Attack</td>    <td> {DOUBLE_ATTACK}</td>  </tr>  
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dual Wield</td>    <td> {DUAL_WIELD}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Hand to Hand</td>    <td> {HAND_TO_HAND}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Kick</td>    <td> {KICK}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Offense</td>    <td> {OFFENSE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Parry</td>    <td> {PARRY}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Piercing</td>    <td> {PIERCING}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Riposte</td>    <td> {RIPOSTE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Throwing</td>    <td> {THROWING}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Intimidation</td>    <td> {INTIMIDATION}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Taunt</td>    <td> {TAUNT}</td>  </tr>
	
	  <tr>    <td colspan='2' class='ColumnHead' align='center'><br>{L_CASTING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Abjuration</td>    <td> {ABJURATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Alteration</td>    <td> {ALTERATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Channeling</td>    <td> {CHANNELING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Conjuration</td>    <td> {CONJURATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Divination</td>    <td> {DIVINATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Evocation</td>    <td> {EVOCATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Specialize Abjure</td>    <td> {SPECIALIZE_ABJURE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Specialize Alteration</td>    <td> {SPECIALIZE_ALTERATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Specialize Conjuration</td>    <td> {SPECIALIZE_CONJURATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Specialize Divination</td>    <td> {SPECIALIZE_DIVINATION}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Specialize Evocation</td>    <td> {SPECIALIZE_EVOCATION}</td>  </tr>
	
	  <tr>    <td colspan='2' class='ColumnHead' align='center'><br>{L_CLASS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Dragon Punch</td>    <td> {DRAGON_PUNCH}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Eagle Strike</td>    <td> {EAGLE_STRIKE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Round Kick</td>    <td> {ROUND_KICK}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Tiger Claw</td>    <td> {TIGER_CLAW}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Flying Kick</td>    <td> {FLYING_KICK}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Mend</td>    <td> {MEND}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Feign Death</td>    <td> {FEIGN_DEATH}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Pick Lock</td>    <td> {PICK_LOCK}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Apply Poison</td>    <td> {APPLY_POISON}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Backstab</td>    <td> {BACKSTAB}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Disarm Traps</td>    <td> {DISARM_TRAPS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Pick Pockets</td>    <td> {PICK_POCKETS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Sense Traps</td>    <td> {SENSE_TRAPS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Berserking</td>    <td> {BERSERKING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Frenzy</td>    <td> {FRENZY}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Brass Instruments</td>    <td> {BRASS_INSTRUMENTS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Singing</td>    <td> {SINGING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Stringed Instruments</td>    <td> {STRINGED_INSTRUMENTS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Wind Instruments</td>    <td> {WIND_INSTRUMENTS}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Percussion Instruments</td>    <td> {PERCUSSION_INSTRUMENTS}</td>  </tr>
	
	  <tr>    <td colspan='2' class='ColumnHead' align='center'><br>{L_OTHER}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Bind Wound</td>    <td> {BIND_WOUND}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Forage</td>    <td> {FORAGE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Hide</td>    <td> {HIDE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Meditate</td>    <td> {MEDITATE}</td>  </tr> 
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Safe Fall</td>    <td> {SAFE_FALL}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Sense Heading</td>    <td> {SENSE_HEADING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Sneak</td>    <td> {SNEAK}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Swimming</td>    <td> {SWIMMING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Tracking</td>    <td> {TRACKING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Fishing</td>    <td> {FISHING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Alcohol Tolerance</td>    <td> {ALCOHOL_TOLERANCE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Begging</td>    <td> {BEGGING}</td>  </tr>
	
	  <tr>    <td colspan='2' class='ColumnHead' align='center'><br>{L_TRADE}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Make Poison</td>    <td> {MAKE_POISON}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Tinkering</td>    <td> {TINKERING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Research</td>    <td> {RESEARCH}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Alchemy</td>    <td> {ALCHEMY}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Baking</td>    <td> {BAKING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Tailoring</td>    <td> {TAILORING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Blacksmithing</td>    <td> {BLACKSMITHING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Fletching</td>    <td> {FLETCHING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Brewing</td>    <td> {BREWING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Jewelry Making</td>    <td> {JEWELRY_MAKING}</td>  </tr>
	  <tr onMouseOver="this.style.background = '#7b714a'" onMouseOut="this.style.background = 'none'" >    <td>Pottery</td>    <td> {POTTERY}</td>  </tr>  
	</table>        
	<br><br>
	<div class='FreeButton' onclick="window.location='character.php?char={NAME}';">{L_DONE}</div>
      </center>
    </div>
  </div>
</center>