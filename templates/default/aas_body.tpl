<div class='WindowComplex PositionAAs CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_ALTERNATE_ABILITIES} - {NAME}</div>
   <nav class='CB_Tab_Box'>
      <ul>
         <!-- BEGIN tabs -->
         <li id='tab{tabs.ID}' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab{tabs.ID}', '#charbrowser DIV.PositionAALeft TABLE', '#tabbox{tabs.ID}');">{tabs.TEXT}</li> 
         <!-- END tabs -->
      </ul>
   </nav>
   <div class='WindowNestedBlue PositionAALeft'>
      <!-- BEGIN boxes -->
      <table id='tabbox{boxes.ID}' class='CB_Table CB_Highlight_Rows'>
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
               <td>{boxes.aas.NAME}</td>
               <td>{boxes.aas.CUR} / {boxes.aas.MAX}</td>
               <td>{boxes.aas.COST}</td>
            </tr> 
         </tbody>
         <!-- END aas -->        
      </table>
      <!-- END boxes -->
   </div>
   <div class='PositionAARight'>
      <table class='CB_Table'>
         <tbody>
            <tr><td>{L_AA_POINTS}:</td><td>{AA_POINTS}</td></tr>
            <tr><td>{L_POINTS_SPENT}:</td><td>{POINTS_SPENT}</td></tr>
         </tbody>
      </table>
   </div>
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
<script type="text/javascript">
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionAALeft TABLE', '#tabbox1');
   });
</script>