<div class='NavOuter'>
<div class='NavInner'>
  <div class='FreeButton' onclick="window.location='character.php?char={NAME}';" style="margin:3px">{L_INVENTORY}</div>
  <div class='FreeButton' onclick="window.location='aas.php?char={NAME}';" style="margin:3px">{L_AAS}</div>
  <div class='FreeButton' onclick="window.location='keys.php?char={NAME}';" style="margin:3px">{L_KEYS}</div>
  <div class='FreeButton' onclick="window.location='flags.php?char={NAME}';" style="margin:3px">{L_FLAGS}</div>
  <div class='FreeButton' onclick="window.location='skills.php?char={NAME}';" style="margin:3px">{L_SKILLS}</div>
  <div class='FreeButton' style="color:606060;margin:3px">{L_CORPSE}</div>
  <div class='FreeButton' onclick="window.location='factions.php?char={NAME}';" style="margin:3px">{L_FACTION}</div>
  <div class='FreeButton' onclick="window.location='charmove.php?char={NAME}';" style="margin:3px">{L_CHARMOVE}</div>
  <div class='FreeButton' onclick="window.external.AddFavorite(location.href, document.title);" style="margin:3px">{L_BOOKMARK}</div>
</div>
</div>
<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_CORPSES} - {NAME}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner'>
      <table class='StatTable' style='width:90%;'>
          <tr>
            <td width='25%' class='ColumnHead' align='center'>{L_REZZED}</td>	      
            <td width='25%' class='ColumnHead'>{L_TOD}</td>	
            <td width='25%' class='ColumnHead'>{L_LOC}</td>	
            <td width='25%' class='ColumnHead' align='center'>{L_MAP}</td>	
          </tr>
        <!-- BEGIN corpses -->
          <tr>
            <td align='center'><div class='check{corpses.REZZED}'></div></</td>
            <td>{corpses.TOD}</td>	      
            <td><a href='{corpses.LINK_ZONE}'>{corpses.ZONE}</a> {corpses.LOC}</td>
            <td align='center'><a href='{corpses.LINK_MAP}'>[map]</a></td>
          </tr>
        <!-- END corpses -->
      </table>
      <br><br>
      <div class='FreeButton' onclick="window.location='character.php?char={NAME}';">{L_DONE}</div>
    </div>
  </div>
</center>