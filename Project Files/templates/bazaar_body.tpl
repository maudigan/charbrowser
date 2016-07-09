<script type="text/javascript">
function display(type, id, prefix) {
  if (target = document.getElementById(prefix + id))
    if (type) target.style.display = (target.style.display == 'none') ? 'block' : 'none';
  else {
    for(var i=0; i < 2190; i++) if (hideme = document.getElementById(prefix + i)) hideme.style.display = 'none';	    
    target.style.display = 'block';
  }
}
</script>

<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_BAZAAR}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner'>
      <table class='StatTable' style='width:625px;'>
        <tr>
          <td align='center' width='150px' valign='top'>          
            <table class='StatTable' style='width:90%;'>
              <tr><td align='left' nowrap>
                <form method='GET' name='bazaar' action='bazaar.php'>
                  {L_SEARCH_NAME}<br>
                  <input class='Bazaar' name='item' value='{ITEM}'><br>
                  <br>
                  {L_SEARCH_CLASS}<br>
                  <select class='Bazaar' name='class'>
                    <!-- BEGIN select_class -->
                    <option value='{select_class.VALUE}' {select_class.SELECTED}>{select_class.OPTION}</option>
                    <!-- END select_class -->
                  </select>
                  <br>
                  <br>
                  {L_SEARCH_RACE}<br>
                  <select class='Bazaar' name='race'>
                    <!-- BEGIN select_race -->
                    <option value='{select_race.VALUE}' {select_race.SELECTED}>{select_race.OPTION}</option>
                    <!-- END select_race -->
                  </select>
                  <br>
                  <br>
                  {L_SEARCH_SLOT}<br>
                  <select class='Bazaar' name='slot'>
                    <!-- BEGIN select_slot -->
                    <option value='{select_slot.VALUE}' {select_slot.SELECTED}>{select_slot.OPTION}</option>
                    <!-- END select_slot -->
                  </select>
                  <br>
                  <br>
                  {L_SEARCH_TYPE}<br>
                  <select class='Bazaar' name='type'>
                    <!-- BEGIN select_type -->
                    <option value='{select_type.VALUE}' {select_type.SELECTED}>{select_type.OPTION}</option>
                    <!-- END select_type -->
                  </select>
                  <br>
                  <br>
                  {L_SEARCH_PRICE_MIN}<br>
                  <input class='Bazaar' name='pricemin' value='{PRICE_MIN}'><br>
                  <br>
                  {L_SEARCH_PRICE_MAX}<br>
                  <input class='Bazaar' name='pricemax' value='{PRICE_MAX}'><br>
                  <br>
                  <center>  
                    <div class='FreeButton' onclick="document.bazaar.submit();">{L_SEARCH}</div>
                  </center>
                </form>
              </td></tr>
            </table>
          </td>
          <td>
            <div class='FlagTabBox' > 
              <center>   
              <table class='StatTable' cellpadding='3px' style='width:90%;height:400px;'>
                <tr>                  
                  <td class='ColumnHead'><a href="{ORDER_LINK}&orderby=name" style='color:#8aa3ff ;'>{L_ITEM}</a></td>
                  <td class='ColumnHead'><a href="{ORDER_LINK}&orderby=tradercost" style='color:#8aa3ff ;'>{L_PRICE}</a></td>
                  <td class='ColumnHead'><a href="{ORDER_LINK}&orderby=charactername" style='color:#8aa3ff ;'>{L_NAME}</a></td>
                </tr>	
                <!-- BEGIN items -->
                <tr onMouseOver="this.style.background = '#7b714a '" onMouseOut ="this.style.background = 'none'" >
	          <td nowrap><a href=# onclick="display(0, {items.SLOT}, 'item');"  style='color:#8aa3ff ;'>{items.NAME}</a></td>
	          <td nowrap>{items.PRICE}</td>
                  <td nowrap><a href='character.php?char={items.SELLER}' style='color:#8aa3ff ;'>{items.SELLER}</a></td>
	        </tr>
                <!-- END items -->
                <tr>
                  <td height='100%' colspan='3' valign='bottom' align='center'>{PAGINATION}</td>
	        </tr>
              </table>
              </center>
            </div>
          </td>      
        </tr>
      </table>
    </div>
  </div>
  <br>
  <br>
  <br>
      <!-- BEGIN items -->
      <div class='ItemOuter' id='item{items.SLOT}' style='display:none;'>
	<div class='ItemTitle'>
	  <div class='ItemTitleLeft'></div>
	  <div class='ItemTitleMid'><a href='http://mqemulator.net/item.php?id={items.ID}'>{items.NAME}</a></div>
	  <div class='ItemTitleRight'></div>
	</div>
	<div class='ItemInner' style='text-align:left;'>
          {items.HTML}
        </div>
      </div>
      <!-- END items -->
</center>