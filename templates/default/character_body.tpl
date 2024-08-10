<div class='PositionCharacter'>

   <!-- INVENTORY WINDOW -->
   <div class='WindowSuperFancy {HIGHLIGHT_GM} PositionInventory CB_Can_Drag'> 
      <div class='CB_Avatar_Layer1'></div>
      <div class='CB_Avatar_Layer2' style='background-image: url({ROOT_URL}images/faces/{AVATAR_IMG});'></div>
      <div class='CB_Avatar_Layer3'></div>
      <div class='CB_Inv_Header'><!--{L_HEADER_INVENTORY}{DELETED}-->
         <p>{FIRST_NAME} {LAST_NAME}</p>
         <p>{LEVEL} {RACE} {CLASS} - {DEITY}</p>
         <p>{GUILD}</p>
      </div>
      <nav class='CB_Tab_Box'>
         <ul>
            <li id='tab1' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionInventory DIV.InventoryContents', '#tabbox1');">Gear</li> 
            <li id='tab2' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab2', '#charbrowser DIV.PositionInventory DIV.InventoryContents', '#tabbox2');">Augments</li> 
         </ul>
      </nav>      
      <div id='tabbox1' class='InventoryContents'>
      
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
               <td>{L_HP}<br>{L_MANA}<br>{L_ENDR}<br>{L_AC}<br>{L_MIT_AC}<br>{L_ATK}</td> 
               <td width='100%'>{HP}<br>{MANA}<br>{ENDR}<br>{AC}<br>{MIT_AC}<br>{ATK}</td> 
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

      <div id='tabbox2' class='InventoryContents CB_CalcBox'>    
         Coming Soon
      </div>  
   </div> 

   <!-- BUFF WINDOW -->
   <div class='WindowSimple PositionBuffs CB_Can_Drag CB_Should_ZOrder' buffcount='{BUFFCOUNT}'> 
      <div class="WindowTitleBar">Buffs</div>
      <!-- BEGIN buffs -->
      <a class="CB_SpellWrap Spell_{buffs.ICON}" title="{buffs.NAME} ({buffs.TIME})" href="{buffs.HREF}">
         <span>{buffs.TIME}</span>
      </a>
      <!-- END buffs -->
      <!-- BEGIN placeholderbuffs -->
      <div class="CB_SpellWrap"></div>
      <!-- END placeholderbuffs -->
   </div>

   <!-- BANK WINDOW -->
   <div class='WindowSuperFancy PositionBank CB_Can_Drag'> 
      <div class='WindowTitleBar'>{L_HEADER_BANK}</div> 
      <div class='BankContents'>
         <!-- SLOT IMAGES FOR THE BANK -->
         <!-- BEGIN bankslots -->
         <div class='Slot slotloc{bankslots.SLOT} slotimage'></div> 
         <!-- END bankslots -->   

         <!-- SLOT IMAGES FOR THE SHARED BANK -->
         <!-- BEGIN sharedbankslots -->
         <div class='Slot slotloc{sharedbankslots.SLOT} slotimage'></div> 
         <!-- END sharedbankslots -->   

         <!-- BEGIN bankitem --> 
         <div hoverChild='#slot{bankitem.SLOT}' class='Slot Item_{bankitem.ICON} slotloc{bankitem.SLOT} CB_HoverParent'><span>{bankitem.STACK}</span></div> 
         <!-- BEGIN switch_is_bag --> 
         <div hoverChild='#bag{bankitem.SLOT}' class='BagOpenSlot slotloc{bankitem.SLOT} CB_HoverParent'></div>
         <!-- END switch_is_bag --> 
         <!-- END bankitem --> 

         <div class='sharedbankheaderloc'>{L_SHARED_BANK}</div>
         <!-- BEGIN sharedbankitem --> 
         <div hoverChild='#slot{sharedbankitem.SLOT}' class='Slot Item_{sharedbankitem.ICON} slotloc{sharedbankitem.SLOT} CB_HoverParent'><span>{sharedbankitem.STACK}</span></div> 
         <!-- BEGIN switch_is_bag --> 
         <div hoverChild='#bag{sharedbankitem.SLOT}' class='BagOpenSlot slotloc{sharedbankitem.SLOT} CB_HoverParent'></div>
         <!-- END switch_is_bag --> 
         <!-- END sharedbankitem --> 

         <div class='Coin CoinPP coinlocsharedbankpp'>{SBPP}</div> 

         <div class='Coin CoinPP coinlocbankpp'>{BPP}</div> 
         <div class='Coin CoinGP coinlocbankgp'>{BGP}</div> 
         <div class='Coin CoinSP coinlocbanksp'>{BSP}</div> 
         <div class='Coin CoinCP coinlocbankcp'>{BCP}</div> 
      </div> 
   </div>
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
         <div class='Slot InspectAugSlot slotimage'></div> 
         <div class='InspectAugSlot ItemAugment_{item.augment.AUG_ICON}'></div> 
         {item.augment.AUG_HTML} 
      </div> 
      <!-- END augment -->   
   </div> 
</div>
<!-- END item --> 

<script type="text/javascript">
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionInventory DIV.InventoryContents', '#tabbox1');
   });
</script>