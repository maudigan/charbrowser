<div class='WindowComplex PositionFlagsParent CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_FLAGS} - {NAME}</div>
      <!-- BEGIN mainhead -->
      <h2>{mainhead.TEXT}</h2>
      <!-- BEGIN main -->
      <div>
         <div class='CB_HoverParent CB_CheckBox_{mainhead.main.FLAG}' hoverChild='#flag{mainhead.main.ID}'>
            <a href='#'>{mainhead.main.TEXT}...</a>
         </div>
      </div>
      <!-- END main -->
      <!-- END mainhead -->
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
      
<br><br><br>

<!-- BEGIN head -->
<div class='WindowComplex PositionFlagsChild CB_Can_Drag CB_Should_ZOrder CB_HoverChild' id='flag{head.ID}'>
   <div class='WindowTitleBar'>
      {head.NAME}
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#flag{head.ID}");' title='click to close popup'></div>
   </div>
   <!-- BEGIN flags -->
   <div>
      <div class='CB_CheckBox_{head.flags.FLAG}'>
         <span>{head.flags.TEXT}</span>u
      </div>
   </div>
   <!-- END flags -->
</div>
<!-- END head -->
