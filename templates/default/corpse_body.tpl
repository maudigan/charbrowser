<div class='WindowComplex PositionCorpse CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_CORPSES} - {NAME}</div>
   <table class='CB_Table'>
      <thead>
         <tr>
            <th>{L_REZZED}</th>     
            <th>{L_TOD}</th>
            <th>{L_LOC}</th>
            <th>{L_MAP}</th>
         </tr>
      </thead>
      <tbody>
      <!-- BEGIN corpses -->
         <tr>
            <td><div class='CB_CheckBox_{corpses.REZZED}'></div></td>
            <td>{corpses.TOD}</td>	      
            <td><a href='{corpses.LINK_ZONE}'>{corpses.ZONE}</a> {corpses.LOC}</td>
            <td><a href='{corpses.LINK_MAP}'>[map]</a></td>
         </tr>
      <!-- END corpses -->
      </tbody>
   </table>
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
