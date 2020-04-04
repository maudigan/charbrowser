<div class='WindowComplex PositionHome CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_HOME}</div>
   <img src="{ROOT_URL}title.php">
   <p>You've enabled the custom home page by renaming home.template to home.php. To add custom data edit the queries and templates in home.php. To edit the layout, make changes to /templates/home_body.tpl.</p>
   <h2> Demo Data: {ROWS}</h2>
   <div class="WindowNestedBlue">
      <table class='CB_Table CB_Highlight_Rows'>
         <thead>
            <tr>
               <th>{L_COL1}</th>
               <th>{L_COL2}</th>
               <th>{L_COL3}</th>
            </tr>
         </thead>
         <tbody>
            <!-- BEGIN rows -->
            <tr>
               <td>{rows.FIRSTCOL}</td>
               <td>{rows.SECONDCOL}</td>
               <td>{rows.THIRDCOL}</td>
            </tr>
            <!-- END rows -->
         </tbody>
      </table>
   </div>
</div>
