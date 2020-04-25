<!-- INVENTORY WINDOW -->
<div class='WindowSuperFancy {HIGHLIGHT_GM} PositionBot CB_Can_Drag'> 
   <div class='CB_Avatar_Layer1'></div>
   <div class='CB_Avatar_Layer2' style='background-image: url({ROOT_URL}images/faces/{AVATAR_IMG});'></div>
   <div class='CB_Avatar_Layer3'></div>
   <div class='CB_Inv_Header'><!--{L_HEADER_INVENTORY}{DELETED}-->
      <p>{FIRST_NAME} {LAST_NAME}</p>
      <p>{LEVEL} {RACE} {CLASS} - {DEITY}</p>
      <p>{GUILD}</p>
   </div>
   <hr>
   <div class='InventoryContents'>
   
      <!-- TOP RIGHT STATS -->
      <table class='CB_Table CB_Char_Stats2'> 
         <tbody>
         <tr> 
            <td> 
            {L_REGEN}<br>{L_FT}<br>{L_DS}<br>{L_HASTE} 
            </td> 
            <td> 
            {REGEN}<br>{FT}<br>{DS}<br>{HASTE}% 
            </td> 
         </tr> 
         </tbody>
      </table> 

      <!-- STATS ON LEFT -->
      <table class='CB_Table CB_Char_Stats'> 
         <tr> 
            <td>{L_HP}<br>{L_MANA}<br>{L_ENDR}<br>{L_AC}<br>{L_ATK}</td> 
            <td width='100%'>{HP}<br>{MANA}<br>{ENDR}<br>{AC}<br>{ATK}</td> 
         </tr> 
         <tr><td class='CB_Char_Stats_Divider' colspan='2'></td></tr> 
         <tr> 
            <td>{L_STR}<br>{L_STA}<br>{L_AGI}<br>{L_DEX}</td> 
            <td width='100%'><font color='#00FF00'>{STR} <font color='Gold'>+{HSTR}</font><br>{STA} <font color='Gold'>+{HSTA}</font><br>{AGI} <font color='Gold'>+{HAGI}</font><br>{DEX} <font color='Gold'>+{HDEX}</font></font></td>
         </tr> 
         <tr><td class='CB_Char_Stats_Divider' colspan='2'></td></tr> 
         <tr> 
            <td>{L_WIS}<br>{L_INT}<br>{L_CHA}</td> 
            <td width='100%'><font color='#00FF00'>{WIS} <font color='Gold'>+{HWIS}</font><br>{INT} <font color='Gold'>+{HINT}</font><br>{CHA} <font color='Gold'>+{HCHA}</font></font></td> 
         </tr> 
         <tr><td class='CB_Char_Stats_Divider' colspan='2'></td></tr> 
         <tr> 
            <td>{L_POISON}<br>{L_MAGIC}<br>{L_DISEASE}<br>{L_FIRE}<br>{L_COLD}<br>{L_CORRUPT}</td> 
            <td><font color='#00FF00'>{POISON} <font color='Gold'>+{HPOISON}</font><br>{MAGIC} <font color='Gold'>+{HMAGIC}</font><br>{DISEASE} <font color='Gold'>+{HDISEASE}</font><br>{FIRE} <font color='Gold'>+{HFIRE}</font><br>{COLD} <font color='Gold'>+{HCOLD}</font><br>{CORRUPT} <font color='Gold'>+{HCORRUPT}</font></font></td> 
         </tr> 
         <tr><td class='CB_Char_Stats_Divider' colspan='2'></td></tr> 
         <tr> 
            <td>{L_WEIGHT}</td> 
            <td nowrap>{WEIGHT} / {STR}</td> 
         </tr> 
      </table> 

      <!-- CLASS MONOGRAM -->
      <div class='CB_Char_Monogram'><img src='{ROOT_URL}images/monograms/{CLASS_NUM}.gif'></div> 

      <!-- SLOT IMAGES FOR WORN ITEMS -->
      <!-- BEGIN equipslots -->
      <div class='Slot slotloc{equipslots.SLOT} slotimage{equipslots.SLOT}'></div> 
      <!-- END equipslots -->

      <!-- SLOT ICONS FOR WORN ITEMS -->
      <!-- BEGIN equipitem --> 
      <div hoverChild='#slot{equipitem.SLOT}' class='Slot slotloc{equipitem.SLOT} CB_HoverParent' style='background-image: url({ROOT_URL}images/items/item_{equipitem.ICON}.png);'><span>{equipitem.STACK}</span></div>  
      <!-- END equipitem --> 
      
   </div>  
   <a class='CB_Button' href="{INDEX_URL}?page=bots&char={NAME}">{L_DONE}</a> 
</div>    

      
      
<!-- ITEM WINDOWS -->
<!-- BEGIN item --> 
<div class='WindowComplex PositionItem CB_Can_Drag CB_Should_ZOrder CB_HoverChild' id='slot{item.SLOT}'> 
   <div class='WindowTitleBar'>
      <a href='{item.LINK}'>{item.NAME}</a>
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#slot{item.SLOT}");' title='click to close popup'></div>
   </div> 
   <div class='Stats'>        
      <div class='Slot slotlocinspect slotimage'></div> 
      <div class='Slot slotlocinspect' style='background-image: url({ROOT_URL}images/items/item_{item.ICON}.png);'><span>{item.STACK}</span></div> 
      {item.HTML} 
      <!-- BEGIN augment --> 
      <div class='WindowNestedTan'>
         <div class='WindowNestedTanTitleBar'>
            <a href='{item.augment.AUG_LINK}'>{item.augment.AUG_NAME}</a>
         </div>   
         <div class='Slot slotlocinspectaug slotimage'></div> 
         <div class='Slot slotlocinspectaug' style='background-image: url({ROOT_URL}images/items/item_{item.augment.AUG_ICON}.png);'></div> 
         {item.augment.AUG_HTML} 
      </div> 
      <!-- END augment -->   
   </div> 
</div>
<!-- END item --> 
