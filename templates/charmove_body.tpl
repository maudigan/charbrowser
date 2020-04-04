<div class='WindowComplexFancy PositionCharMove CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_CHARACTER_MOVER}</div>
   <form name='charmoveform' method='GET' action='{INDEX_URL}'>
      <input type='hidden' name='page' value='charmove'> 
      <div class='CB_Charmove_addrow'><a href='#' onclick="CB_NewCharMoveRow();">+ {L_ADD_CHARACTER}</a></div>
      <div class='CB_Charmove_row'></div>
      <input class='CB_Button' type='Submit' value='{L_MOVE}'>
   </form>
</div>

<script type="text/javascript">
   function CB_NewCharMoveRow(name = '') {
      $('DIV.CB_Charmove_row:first').after("\
         <div class='CB_Charmove_row'>\
            <label for='login'>{L_LOGIN}:</label>\
            <input type='text' id='login' name='login[]' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false'>\
            <label for='name'>{L_CHARNAME}:</label>\
            <input type='text' id='name' name='name[]' value='" + name + "' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false'>\
            <label for='zone'>{L_ZONE}:</label>\
            <select id='zone' name='zone[]'>\
               <!-- BEGIN zones -->
               <option value='{zones.VALUE}'>{zones.VALUE}</option>\
               <!-- END zones -->
            </select>\
         </div>\
      ");
   }

   //add first row
   CB_NewCharMoveRow('{CHARNAME}');
</script>