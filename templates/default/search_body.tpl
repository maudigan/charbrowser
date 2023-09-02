<div class='WindowComplex PositionSearch CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_RESULTS}</div>
   <table class='CB_Table CB_Highlight_Rows'>
      <thead>
         <tr>
            <th><a href="{ORDER_LINK}&orderby=name">{L_NAME}</a></th>   
            <th><a href="{ORDER_LINK}&orderby=guildname">{L_GUILD}</a></th>     
            <th><a href="{ORDER_LINK}&orderby=level">{L_LEVEL}</a></th>
            <th><a href="{ORDER_LINK}&orderby=class">{L_CLASS}</a></th>
            <th><a href="{ORDER_LINK}&orderby=aa_points">{L_AA_POINTS}</a></th>
            <th><a href="{ORDER_LINK}&orderby=hp">{L_HP}</a></th>
            <th><a href="{ORDER_LINK}&orderby=mana">{L_MANA}</a></th>
            <th><a href="{ORDER_LINK}&orderby=endurance">{L_ENDURANCE}</a></th>
            <th><a href="{ORDER_LINK}&orderby=attack">{L_ATTACK}</a></th>
            <th><a href="{ORDER_LINK}&orderby=ac">{L_AC}</a></th>
            <th><a href="{ORDER_LINK}&orderby=haste">{L_HASTE}</a></th>
            <th><a href="{ORDER_LINK}&orderby=accuracy">{L_ACCURACY}</a></th>
            <th><a href="{ORDER_LINK}&orderby=hp_regen">{L_HP_REGEN}</a></th>
            <th><a href="{ORDER_LINK}&orderby=mana_regen">{L_MANA_REGEN}</a></th>
         </tr>
      </thead>
      <tbody>
         <!-- BEGIN characters -->
         <tr>
            <td><a href="{INDEX_URL}?page=character&char={characters.NAME}">{characters.NAME}</a>{characters.DELETED}</td>
            <td>{characters.GUILD_NAME}</td>      
            <td>{characters.LEVEL}</td>
            <td>{characters.CLASS}</td>
            <td>{characters.AA_POINTS}</td>
            <td>{characters.HP}</td>
            <td>{characters.MANA}</td>
            <td>{characters.ENDURANCE}</td>
            <td>{characters.ATTACK}</td>
            <td>{characters.AC}</td>
            <td>{characters.HASTE}</td>
            <td>{characters.ACCURACY}</td>
            <td>{characters.HP_REGEN}</td>
            <td>{characters.MANA_REGEN}</td>
         </tr>
         <!-- END characters -->
      </tbody>
   </table>
   <div class='CB_Pagination'>{PAGINATION}</div>
</div>  