<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_CHARACTER_MOVER}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner' align='left'>
      <center>
        <table class='StatTable' style='width:90%'>
     <!-- BEGIN rows -->
     <tr onMouseOver="this.style.background = '#7b714a' onMouseOut =this.style.background = 'none' ">
       <!-- BEGIN cols -->
       <td align='center'>{rows.cols.VALUE}</td>
       <!-- END cols -->
     </tr>
     <!-- END rows -->
   </table>
   <br>
   <br>
   <table class='StatTable' style='width:90%'>
     <tr onMouseOver="this.style.background = '#7b714a' onMouseOut =this.style.background = 'none' "><td>{L_RESULTS}:</td><td>{S_RESULTS}</td></tr>
     <tr onMouseOver="this.style.background = '#7b714a' onMouseOut =this.style.background = 'none' "><td>{L_HIGHLIGHT_GM}:</td><td>{S_HIGHLIGHT_GM}</td></tr>
     <tr onMouseOver="this.style.background = '#7b714a' onMouseOut =this.style.background = 'none' "><td>{L_BAZAAR}:</td><td>{S_BAZAAR}</td></tr>
     <tr onMouseOver="this.style.background = '#7b714a' onMouseOut =this.style.background = 'none' "><td>{L_CHARMOVE}:</td><td>{S_CHARMOVE}</td></tr>
   </table>
        <br><br>
   <div class='FreeButton' onclick="history.go( -1 );return true;">{L_BACK}</div>
      </center>
    </div>
  </div>
</center>