<div class='WindowSimple PositionAdventure CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_ADVENTURE}</div>
   <div>
      <form method='GET' name='adventure' action='{INDEX_URL}'>
         <input type='hidden' name='page' value='adventure'>
         <input type='hidden' name='char' value='{CHARACTER}'>
         <label for='category'>Select Category:</label>
         <select name='category' id='category' onchange='this.form.submit();'>
            <!-- BEGIN select_category -->
            <option value='{select_category.VALUE}' {select_category.SELECTED}>{select_category.OPTION}</option>
            <!-- END select_category -->
         </select>
      </form>
   </div>
   <div class='WindowNestedBlue StaticTableHeadParent'>
      <table class='CB_Table CB_Highlight_Rows'>
         <thead>
            <tr>
               <th>{L_RANK}</th> 
               <th>{L_NAME}</th> 
               <th>{L_SUCCESS}</th> 
               <th>{L_FAILURE}</th> 
               <th>{L_PERCENT}</th>  
            </tr>
         </thead>
         <tbody>
         <!-- BEGIN leaders -->
            <tr>
               <td style='color: {leaders.COLOR};'>{leaders.RANK}</td>
               <td style='color: {leaders.COLOR};'><a style='color: {leaders.COLOR};' href='{INDEX_URL}?page=character&char={leaders.NAME}'>{leaders.NAME}</a></td>
               <td style='color: {leaders.COLOR};'>{leaders.SUCCESS}</td>
               <td style='color: {leaders.COLOR};'>{leaders.FAILURE}</td>
               <td style='color: {leaders.COLOR};'>{leaders.PERCENT}%</td>
            </tr>
         <!-- END leaders -->
         </tbody>
      </table> 
   </div>
   <div>
      <!-- BEGIN current -->
      <table class='CB_Table'>
         <thead>
            <tr>
               <th>{L_RANK}</th> 
               <th>{L_NAME}</th> 
               <th>{L_SUCCESS}</th> 
               <th>{L_FAILURE}</th> 
               <th>{L_PERCENT}</th>  
            </tr>
         </thead>
         <tbody>
            <tr>
               <td style='color: {current.COLOR};'>{current.RANK}</td>
               <td style='color: {current.COLOR};'><a style='color: {current.COLOR};' href='{INDEX_URL}?page=character&char={current.NAME}'>{current.NAME}</a></td>
               <td style='color: {current.COLOR};'>{current.SUCCESS}</td>
               <td style='color: {current.COLOR};'>{current.FAILURE}</td>
               <td style='color: {current.COLOR};'>{current.PERCENT}%</td>
            </tr>
         </tbody>
      </table> 
      <!-- END current -->
   </div>
   <div class='CB_Button' onclick="cb_GoBackOnePage();">{L_BACK}</div>
</div>
