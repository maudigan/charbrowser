<div class='WindowComplex PositionBazaar CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_BAZAAR}</div>
   <div class='PositionBazaarLeft'>
      <form method='GET' name='bazaar' action='{INDEX_URL}'>
         <input type='hidden' name='page' value='bazaar'>
         
         <label for='item'>{L_SEARCH_NAME}</label>
         <input name='item' id='item' type='text' value='{ITEM}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

         <label for='class'>{L_SEARCH_CLASS}</label>
         <select name='class' id='class'>
            <!-- BEGIN select_class -->
            <option value='{select_class.VALUE}' {select_class.SELECTED}>{select_class.OPTION}</option>
            <!-- END select_class -->
         </select>

         <label for='race'>{L_SEARCH_RACE}</label>
         <select name='race' id='race'>
            <!-- BEGIN select_race -->
            <option value='{select_race.VALUE}' {select_race.SELECTED}>{select_race.OPTION}</option>
            <!-- END select_race -->
         </select>

         <label for='slot'>{L_SEARCH_SLOT}</label>
         <select name='slot' id='slot'>
            <!-- BEGIN select_slot -->
            <option value='{select_slot.VALUE}' {select_slot.SELECTED}>{select_slot.OPTION}</option>
            <!-- END select_slot -->
         </select>

         <label for='type'>{L_SEARCH_TYPE}</label>
         <select name='type' id='type'>
            <!-- BEGIN select_type -->
            <option value='{select_type.VALUE}' {select_type.SELECTED}>{select_type.OPTION}</option>
            <!-- END select_type -->
         </select>

         <label for='item'>{L_SEARCH_PRICE_MIN}</label>
         <input name='pricemin' id='pricemin' type='text' value='{PRICE_MIN}'>

         <label for='item'>{L_SEARCH_PRICE_MAX}</label>
         <input name='pricemax' id='pricemax' type='text' value='{PRICE_MAX}'>
         <input class='CB_Button' type='submit' value='{L_SEARCH}'>
      </form>
   </div>
   <div class='WindowNestedBlue PositionBazaarRight'>
      <table class='CB_Table CB_Highlight_Rows'>
         <thead> 
            <tr>                  
               <th><a href="{ORDER_LINK}&orderby=name">{L_ITEM}</a></th>
               <th><a href="{ORDER_LINK}&orderby=tradercost">{L_PRICE}</a></th>
               <th><a href="{ORDER_LINK}&orderby=charactername">{L_NAME}</a></th>
            </tr>
         </thead>
         </tbody>
            <!-- BEGIN items -->
            <tr>
               <td><a hoverChild='#item{items.SLOT}' class='CB_HoverParent' href='#'>{items.NAME}</a></td>
               <td>{items.PRICE}</td>
               <td><a href='{INDEX_URL}?page=character&char={items.SELLER}'>{items.SELLER}</a></td>
            </tr>
            <!-- END items -->
         </tbody>
      </table>
   </div>
   <div class='CB_Pagination'>{PAGINATION}</div>
</div>

<!-- ITEM WINDOWS -->
<!-- BEGIN items --> 
<div class='WindowComplex PositionItem CB_Can_Drag CB_HoverChild' id='item{items.SLOT}' onmousedown='cbPopup_ZOrder("#slot{items.SLOT}");'> 
   <div class='WindowTitleBar'>
      <a href='{items.LINK}'>{items.NAME}</a>
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#item{items.SLOT}");' title='click to close this popup'></div>
   </div> 
   <div style='text-align:left;'>        
      {items.HTML} 
   </div> 
</div> 
<!-- END items --> 

