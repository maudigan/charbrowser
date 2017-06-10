<div class='NavOuter'>
<div class='NavInner'>
  <div class='FreeButton' onclick="window.location='character.php?char={NAME}';" style="margin:3px">{L_INVENTORY}</div>
  <div class='FreeButton' onclick="window.location='aas.php?char={NAME}';" style="margin:3px">{L_AAS}</div>
  <div class='FreeButton' onclick="window.location='keys.php?char={NAME}';" style="margin:3px">{L_KEYS}</div>
  <div class='FreeButton' onclick="window.location='flags.php?char={NAME}';" style="margin:3px">{L_FLAGS}</div>
  <div class='FreeButton' onclick="window.location='skills.php?char={NAME}';" style="margin:3px">{L_SKILLS}</div>
  <div class='FreeButton' onclick="window.location='corpse.php?char={NAME}';" style="margin:3px">{L_CORPSE}</div>
  <div class='FreeButton' style="color:606060;margin:3px">{L_FACTION}</div>
  <div class='FreeButton' onclick="window.location='charmove.php?char={NAME}';" style="margin:3px">{L_CHARMOVE}</div>
  <div class='FreeButton' onclick="window.external.AddFavorite(location.href, document.title);" style="margin:3px">{L_BOOKMARK}</div>
</div>
</div>
<center>
  <div class='ItemOuter'>
    <div class='ItemTitle'>
      <div class='ItemTitleLeft'></div>
      <div class='ItemTitleMid'>{L_FACTIONS} - {NAME}</div>
      <div class='ItemTitleRight'></div>
    </div>
    <div class='ItemInner'>
        <table class='StatTable' cellpadding='3px' style='width:90%'>
        <tr>
          <td class='ColumnHead'>{L_NAME}</td>
          <td class='ColumnHead'>{L_FACTION}</td>  
        </tr>	
        <!-- BEGIN factions -->
        <tr onMouseOver="this.style.background = '#7b714a '" onMouseOut ="this.style.background = 'none'" >
          <td nowrap><a href='{factions.LINK}' style='color:#8aa3ff ;'>{factions.NAME}</a></td>
	  <td nowrap>{factions.FACTION}</td>
	</tr>
        <!-- END factions -->
      </table>
      <br><br>
      <div class='FreeButton' onclick="window.location='character.php?char={NAME}';">{L_DONE}</div>
    </div>
  </div>
</center>