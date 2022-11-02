<!-- INVENTORY WINDOW -->
<div class='WindowSuperFancy PositionCorpse CB_Can_Drag'> 
   <div class='CB_Avatar_Layer1 {REZZED_STYLE}'></div>
   <div class='CB_Avatar_Layer2 {REZZED_STYLE}' style='background-image: url({ROOT_URL}images/faces/{AVATAR_IMG});'></div>
   <div class='CB_Avatar_Layer3 {REZZED_STYLE}'></div>
   <div class='CB_Inv_Header'><!--{L_HEADER_INVENTORY}-->
      <p>{FIRST_NAME}</p>
      <p>{LEVEL} {RACE} {CLASS} - {DEITY}</p>
   </div>
   <hr>
   <div class='InventoryContents'>
   
      <!-- TOP RIGHT STATS -->
      <table class='CB_Table CB_Char_Stats2'> 
         <tbody>
         <tr> 
            <td> 
            <!-- nothing to display here for corpse yet --> 
            </td> 
         </tr> 
         </tbody>
      </table> 

      <!-- STATS ON LEFT -->
      <div class='CB_Char_Stats'> 
         <div>
            <p>{L_TOD}</p>
            <p>
               {TOD_DAY}<br>
               {TOD_DATE}<br>
               {TOD_TIME}
            </p>
            <p><a href='{DIED_LINK_ZONE}'>{DIED_ZONE_LONG_NAME}</a> {DIED_LOC}</p>
            <p><a href='{LINK_MAP}'>{L_VIEW_ON_MAP}</a></p>
         </div> 
         <div>
            <p>{L_STATUS}</p>
            <p>{REZZED_TEXT}</p>         
            <!-- BEGIN switch_is_buried -->
            <p>{L_BURIED_PREAMBLE}<a href='{switch_is_buried.LINK_TO_ZONE_BURIED}'>{switch_is_buried.ZONE_LONG_NAME}</a></p>
         <!-- END switch_is_buried -->
         </div> 


         <div> 
            {L_WEIGHT} {WEIGHT} / {L_WEIGHT_MAX}
         </div> 
      </div> 

      <!-- CLASS MONOGRAM -->
      <div class='CB_Char_Monogram'><img src='{ROOT_URL}images/monograms/{CLASS_NUM}.gif'></div> 

      <!-- COINS -->
      <div class='Coin CoinPP coinlocinvpp'>{PP}</div> 
      <div class='Coin CoinGP coinlocinvgp'>{GP}</div> 
      <div class='Coin CoinSP coinlocinvsp'>{SP}</div> 
      <div class='Coin CoinCP coinlocinvcp'>{CP}</div> 

      <!-- SLOT IMAGES FOR WORN ITEMS -->
      <!-- BEGIN equipslots -->
      <div class='Slot slotloc{equipslots.SLOT} slotimage{equipslots.SLOT}'></div> 
      <!-- END equipslots -->

      <!-- SLOT IMAGES FOR INVENTORY -->
      <!-- BEGIN invslots -->
      <div class='Slot slotloc{invslots.SLOT} slotimage'></div> 
      <!-- END invslots -->

      <!-- SLOT ICONS FOR WORN ITEMS -->
      <!-- BEGIN equipitem --> 
      <div hoverChild='#slot{equipitem.SLOT}' class='Slot Item_{equipitem.ICON} slotloc{equipitem.SLOT} CB_HoverParent'><span>{equipitem.STACK}</span></div>  
      <!-- END equipitem --> 
         
      <!-- SLOT ICONS FOR INVENTORY -->
      <!-- BEGIN invitem --> 
      <div hoverChild='#slot{invitem.SLOT}' class='Slot Item_{invitem.ICON} slotloc{invitem.SLOT} CB_HoverParent'><span>{invitem.STACK}</span></div> 
      <!-- BEGIN switch_is_bag --> 
      <div hoverChild='#bag{invitem.SLOT}' class='BagOpenSlot slotloc{invitem.SLOT} CB_HoverParent'></div>
      <!-- END switch_is_bag --> 
      <!-- END invitem --> 
      
   </div>  
   <a class='CB_Button' href="{INDEX_URL}?page=corpses&char={NAME}">{L_DONE}</a> 
</div>    
    

<!-- BAG WINDOWS -->
<!-- BEGIN bags --> 
<div class='WindowSimple PositionBag CB_Should_ZOrder CB_Can_Drag CB_HoverChild' slotcount='{bags.SLOTCOUNT}' id='bag{bags.SLOT}'> 
   <div class='WindowTitleBar'>{L_CONTAINER}</div>

   <!-- BEGIN bagslots --> 
   <div class='BagSlot slotimage'>
      <!-- BEGIN bagitems --> 
      <div hoverChild='#slot{bags.bagslots.bagitems.BI_SLOT}' class='BagSlot Item_{bags.bagslots.bagitems.BI_ICON} CB_HoverParent'>
         <span>{bags.bagslots.bagitems.STACK}</span>
      </div> 
      <!-- END bagitems -->    
   </div> 
   <!-- END bagslots --> 

   <div class='CB_Button' onclick='cbPopup_closeItem("#bag{bags.SLOT}");'>{L_DONE}</div> 
</div> 
<!-- END bags -->       
      
      
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
      <div class='Slot Item_{item.ICON} slotlocinspect'><span>{item.STACK}</span></div> 
      {item.HTML} 
      <!-- BEGIN augment --> 
      <div class='WindowNestedTan'>
         <div class='WindowNestedTanTitleBar'>
            <a href='{item.augment.AUG_LINK}'>{item.augment.AUG_NAME}</a>
         </div>   
         <div class='InspectAugSlot slotimage'></div> 
         <div class='InspectAugSlot ItemAugment_{item.augment.AUG_ICON} slotlocinspectaug'></div> 
         {item.augment.AUG_HTML} 
      </div> 
      <!-- END augment -->   
   </div> 
</div>
<!-- END item --> 
