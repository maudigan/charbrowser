<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_RESULTS}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner'>
      <table class='StatTable' style='width:90%;'>
          <tr>
            <td width='25%' class='ColumnHead'><a href="{ORDER_LINK}&orderby=name" style='color:#8aa3ff ;'>{L_NAME}</a></td>	      
            <td width='25%' class='ColumnHead'><a href="{ORDER_LINK}&orderby=guildname" style='color:#8aa3ff ;'>{L_GUILD}</a></td>	      
            <td width='25%' class='ColumnHead'><a href="{ORDER_LINK}&orderby=level" style='color:#8aa3ff ;'>{L_LEVEL}</a></td>
            <td width='25%' class='ColumnHead'><a href="{ORDER_LINK}&orderby=class" style='color:#8aa3ff ;'>{L_CLASS}</a></td>
          </tr>
        <!-- BEGIN characters -->
          <tr>
            <td><a href="character.php?char={characters.NAME}">{characters.NAME}</a></td>
            <td>{characters.GUILD_NAME}</td>	      
            <td>{characters.LEVEL}</td>
            <td>{characters.CLASS}</td>
          </tr>
        <!-- END characters -->
      </table>
      <br>
      {PAGINATION}
    </div>
  </div>
</center>