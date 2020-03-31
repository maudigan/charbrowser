<div class='WindowComplex PositionFactionAdvanced CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_FACTIONS} - {NAME}</div>
   <table class='CB_Table CB_Highlight_Rows'>
      <thead>
         <tr>
            <th>{L_NAME}</th>
            <th>{L_FACTION}</th>
            <th>{L_BASE}</th>
            <th>{L_CHAR}</th>
            <th>{L_CLASS}</th>
            <th>{L_RACE}</th>
            <th>{L_DEITY}</th> 
            <th>{L_TOTAL}</th>   
         </tr>
      </thead>
      <tbody>
         <!-- BEGIN factions -->
         <tr>
            <td><a href='{factions.LINK}'>{factions.NAME}</a></td>
            <td>{factions.FACTION}</td>
            <td>{factions.BASE}</td>
            <td>{factions.CHAR}</td>
            <td>{factions.CLASS}</td>
            <td>{factions.RACE}</td>
            <td>{factions.DEITY}</td>
            <td>{factions.TOTAL}</td>
         </tr>
         <!-- END factions -->
      </tbody>
   </table>
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
