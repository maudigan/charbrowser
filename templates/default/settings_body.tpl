<div class='WindowComplex PositionSettings CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_SETTINGS}</div>
   <table class='CB_Table CB_Highlight_Rows'>
      <thead>
      <!-- BEGIN headers -->
      <tr>
         <!-- BEGIN cols -->
         <th>{headers.cols.VALUE}</th>
         <!-- END cols -->
      </tr>
      <!-- END headers -->
      </thead>
      <tbody>
      <!-- BEGIN rows -->
      <tr>
         <!-- BEGIN cols -->
         <td class='cb_setting_val_{rows.cols.VALUENUM}'>{rows.cols.VALUE}</td>
         <!-- END cols -->
      </tr>
      <!-- END rows -->
      </tbody>
   </table>
   <br>
   {L_RESULTS}: <em>{S_RESULTS}</em><br>
   {L_BAZAAR}: <em>{S_BAZAAR}</em><br>
   {L_CHARMOVE}: <em>{S_CHARMOVE}</em><br>
   {L_GUILDVIEW}: <em>{S_GUILDVIEW}</em><br>
   {L_SERVERVIEW}: <em>{S_SERVERVIEW}</em><br>
   {L_BARTER}: <em>{S_BARTER}</em><br>
   {L_ADVENTURE}: <em>{S_ADVENTURE}</em><br>
   <!-- BEGIN switch_new_version -->
   <div class='CB_is_old'>
      <p>{switch_new_version.PUBLISHED}</p>
      <p>{L_UPDATES_EXIST}</p>
      <p>{switch_new_version.DESCRIPTION}</p>
      <p><a href='{switch_new_version.URL}'>{L_DOWNLOAD}: {switch_new_version.VERSION}</a></p>
   </div>
   <!-- END switch_new_version -->
   <div class='CB_Button' onclick="cb_GoBackOnePage();">{L_BACK}</div>
</div>