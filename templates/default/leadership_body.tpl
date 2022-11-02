<div class='WindowComplex PositionLeadership CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_LEADERSHIP} - {NAME}</div>
   <nav class='CB_Tab_Box'>
      <ul>
         <!-- BEGIN tabs -->
         <li id='tab{tabs.ID}' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab{tabs.ID}', '#charbrowser DIV.PositionLeadershipLeft TABLE', '#tabbox{tabs.ID}');">{tabs.TEXT}</li> 
         <!-- END tabs -->
      </ul>
   </nav>
   <div class='WindowNestedBlue PositionLeadershipLeft'>
      <!-- BEGIN tabs -->
      <table id='tabbox{tabs.ID}' class='CB_Table CB_Highlight_Rows'>
         <thead>
            <tr>
               <th>{L_TITLE}</th>
               <th>{L_CUR_MAX}</th>
               <th>{L_COST}</th>
            </tr>
         </thead>
         <!-- BEGIN aas -->
         <tbody>
            <tr>
               <td>{tabs.aas.NAME}</td>
               <td>{tabs.aas.CUR} / {tabs.aas.MAX}</td>
               <td>{tabs.aas.COST}</td>
            </tr> 
         </tbody>
         <!-- END aas -->        
      </table>
      <!-- END tabs -->
   </div>
   <div class='PositionLeadershipRight'>
      <h2>{L_GROUP_POINTS}</h2>
      <div>{GROUP_LEADERSHIP_POINTS}</div>
      <h2>{L_RAID_POINTS}</h2>
      <div>{RAID_LEADERSHIP_POINTS}</div>
   </div>
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
<script type="text/javascript">
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionLeadershipLeft TABLE', '#tabbox1');
   });
</script>