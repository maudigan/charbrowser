<div class='WindowComplex PositionSkills CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_SKILLS} - {NAME}</div>
   <nav class='CB_Tab_Box'>
      <ul>
         <!-- BEGIN section -->
         <li id='tab{section.INDEX}' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab{section.INDEX}', '#charbrowser DIV.PositionSkills TABLE.CB_Table', '#tabbox{section.INDEX}');">{section.TAB}</li> 
         <!-- END section -->
      </ul>
   </nav>    
   <!-- BEGIN section -->
   <table id='tabbox{section.INDEX}' class='CB_Table CB_Highlight_Rows'>
      <thead>
         <tr>
            <th colspan='2'>{section.TEXT}</th>  
         </tr>
      </thead>
      <tbody>
      <!-- BEGIN skillrow -->
         <tr>
            <td>{section.skillrow.NAME}</td>
            <td>{section.skillrow.VALUE}</td>
         </tr>
      <!-- END skillrow -->
      </tbody>
   </table>  
   <!-- END section -->
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>

<script type="text/javascript">
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab0', '#charbrowser DIV.PositionSkills TABLE.CB_Table', '#tabbox0');
   });
</script>