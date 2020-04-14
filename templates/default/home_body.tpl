<div class='WindowComplex PositionHome CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_HOME}</div>
   <img src="{ROOT_URL}title.php">
   <p>You've enabled the custom home page by renaming home.template to home.php. To add custom data edit the queries and templates in home.php. To edit the layout, copy /templates/default/home_body.tpl into /templates/custom/home_body.tpl (or whatever directory you've created and set in config.php). You can then edit that template and it will overwrite the default template.</p>
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
