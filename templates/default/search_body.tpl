<div class='WindowComplex PositionSearch CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_RESULTS}</div>
   <table class='CB_Table CB_Highlight_Rows'>
      <thead>
         <tr>
            <th><a href="{ORDER_LINK}&orderby=name">{L_NAME}</a></th>   
            <th><a href="{ORDER_LINK}&orderby=guildname">{L_GUILD}</a></th>     
            <th><a href="{ORDER_LINK}&orderby=level">{L_LEVEL}</a></th>
            <th><a href="{ORDER_LINK}&orderby=class">{L_CLASS}</a></th>
         </tr>
      </thead>
      <tbody>
         <!-- BEGIN characters -->
         <tr>
            <td><a href="{INDEX_URL}?page=character&char={characters.NAME}">{characters.NAME}</a>{characters.DELETED}</td>
            <td>{characters.GUILD_NAME}</td>      
            <td>{characters.LEVEL}</td>
            <td>{characters.CLASS}</td>
         </tr>
         <!-- END characters -->
      </tbody>
   </table>
   <div class='CB_Pagination'>{PAGINATION}</div>
</div>