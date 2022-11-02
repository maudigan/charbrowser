<div class='WindowSimpleFancy PositionBarter CB_Can_Drag CB_Should_ZOrder'>
   <div class='WindowTitleBar'>{L_BARTER}{STORENAME}</div>
   <div class='PositionBarterTop'>
      <div class='PositionBarterLeft'>
         <div>
            <form method='GET' name='barter' action='{INDEX_URL}'>
               <input type='hidden' name='page' value='barter'>
               
               <label for='item'>{L_SEARCH_NAME}</label>
               <input name='item' id='item' type='text' value='{ITEM}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
               
               <label for='buyer'>{L_NAME}</label>
               <input name='buyer' id='buyer' type='text' value='{BUYER}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
               
               <label for='seller'>{L_SELLER}</label>
               <input name='char' id='seller' type='text' value='{SELLER}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

               <input class='CB_Button' type='submit' value='{L_SEARCH}'>
            </form>
         </div>
         <!-- BEGIN switch_seller_set -->
         <div>
            <h2>{L_SELLERS_INVENTORY}</h2>
         </div>
         <div class='WindowNestedBlue StaticTableHeadParent'>
            <table class='CB_Table CB_Highlight_Rows'>
               <thead> 
                  <tr>                  
                     <th><a href="{INV_ORDER_LINK}&invorderby=Name">{L_ITEM}</a></th>
                     <th><a href="{INV_ORDER_LINK}&invorderby=quantity">{L_QTY}</a></th>
                  </tr>
               </thead>
               </tbody>
                  <!-- BEGIN seller_items -->
                  <tr>
                     <td>
                        <a hoverChild='#item{switch_seller_set.seller_items.SLOT}' class='CB_HoverParent' href='#'>
                           <div class='TableSlot ItemSmall_{switch_seller_set.seller_items.ICON}'></div>
                           {switch_seller_set.seller_items.NAME}
                        </a>
                     </td>
                     <td>{switch_seller_set.seller_items.QUANTITY}</td>
                  </tr>
                  <!-- END seller_items -->
               </tbody>
            </table>
         </div>
         <!-- END switch_seller_set -->
      </div>
      <div class='PositionBarterRight'>
         <div>
            <h2>{L_MATCHING_BUYERS}</h2>
         </div>
         <div class='WindowNestedBlue StaticTableHeadParent'>
            <table class='CB_Table CB_Highlight_Rows'>
               <thead> 
                  <tr>                  
                     <th><a href="{ORDER_LINK}&orderby=Name">{L_ITEM}</a></th>
                     <th><a href="{ORDER_LINK}&orderby=quantity">{L_QTY}</a></th>
                     <th><a href="{ORDER_LINK}&orderby=buyerprice">{L_PRICE}</a></th>
                     <th><a href="{ORDER_LINK}&orderby=charactername">{L_NAME}</a></th>
                  </tr>
               </thead>
               </tbody>
                  <!-- BEGIN buyer_items -->
                  <tr>
                     <td>
                        <a hoverChild='#item{buyer_items.SLOT}' class='CB_HoverParent' href='#'>
                           <div class='TableSlot ItemSmall_{buyer_items.ICON}'></div>
                           {buyer_items.NAME}
                        </a>
                     </td>
                     <td>{buyer_items.QUANTITY}</td>
                     <td>{buyer_items.PRICE}</td>
                     <td><a href='{INDEX_URL}?page=character&char={buyer_items.BUYER}'>{buyer_items.BUYER}</a></td>
                  </tr>
                  <!-- END buyer_items -->
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class='PositionBarterBottom'>
      <div class='CB_Pagination'>{PAGINATION}</div>
   </div>
</div>

<!-- BUYER ITEM WINDOWS -->
<!-- BEGIN buyer_items --> 
<div class='WindowComplex PositionItem CB_Can_Drag CB_HoverChild CB_Should_ZOrder' id='item{buyer_items.SLOT}'> 
   <div class='WindowTitleBar'>
      <a href='{buyer_items.LINK}'>{buyer_items.NAME}</a>
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#item{buyer_items.SLOT}");' title='click to close this popup'></div>
   </div> 
   <div class='Stats'> 
      <div class='Slot slotlocinspect slotimage'></div> 
      <div class='Slot Item_{buyer_items.ICON} slotlocinspect'><span>{buyer_items.STACK}</span></div>        
      {buyer_items.HTML} 
   </div> 
</div> 
<!-- END buyer_items --> 


<!-- BEGIN switch_seller_set -->
<!-- SELLER ITEM WINDOWS -->
<!-- BEGIN seller_items --> 
<div class='WindowComplex PositionItem CB_Can_Drag CB_HoverChild CB_Should_ZOrder' id='item{switch_seller_set.seller_items.SLOT}'> 
   <div class='WindowTitleBar'>
      <a href='{switch_seller_set.seller_items.LINK}'>{switch_seller_set.seller_items.NAME}</a>
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#item{switch_seller_set.seller_items.SLOT}");' title='click to close this popup'></div>
   </div> 
   <div class='Stats'> 
      <div class='Slot slotlocinspect slotimage'></div> 
      <div class='Slot Item_{switch_seller_set.seller_items.ICON} slotlocinspect'><span>{switch_seller_set.seller_items.STACK}</span></div>        
      {switch_seller_set.seller_items.HTML} 
   </div> 
</div> 
<!-- END seller_items --> 
<!-- END switch_seller_set -->
