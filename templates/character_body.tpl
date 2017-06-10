
<script type="text/javascript"> 

function display(type, id, prefix) { 
  if (target = document.getElementById(prefix + id)) 
    if (type) target.style.display = (target.style.display == 'none') ? 'block' : 'none'; 
  else {      
    for(var i=0; i <= 2270; i++)    if (hideme = document.getElementById(prefix + i)) hideme.style.display = 'none';    
    for(var i=9999; i < 10000; i++) if (hideme = document.getElementById(prefix + i)) hideme.style.display = 'none';           
    target.style.display = 'block'; 
  } 
} 
</script> 
<div class='NavOuter'> 
<div class='NavInner'> 
  <div class='FreeButton' style="color:606060;margin:3px">{L_INVENTORY}</div> 
  <div class='FreeButton' onclick="window.location='aas.php?char={FIRST_NAME}';" style="margin:3px">{L_AAS}</div> 
  <div class='FreeButton' onclick="window.location='keys.php?char={FIRST_NAME}';" style="margin:3px">{L_KEYS}</div>
  <div class='FreeButton' onclick="window.location='flags.php?char={FIRST_NAME}';" style="margin:3px">{L_FLAGS}</div> 
  <div class='FreeButton' onclick="window.location='skills.php?char={FIRST_NAME}';" style="margin:3px">{L_SKILLS}</div> 
  <div class='FreeButton' onclick="window.location='corpse.php?char={FIRST_NAME}';" style="margin:3px">{L_CORPSE}</div> 
  <div class='FreeButton' onclick="window.location='factions.php?char={FIRST_NAME}';" style="margin:3px">{L_FACTION}</div> 
  <div class='FreeButton' onclick="window.location='charmove.php?char={FIRST_NAME}';" style="margin:3px">{L_CHARMOVE}</div> 
  <div class='FreeButton' onclick="window.external.AddFavorite(location.href, document.title);" style="margin:3px">{L_BOOKMARK}</div> 
</div> 
</div> 
<center> 
<table> 
  <tr> 
    <td width='200px' align='right'> 
      <!-- BEGIN bags --> 
      <div class='BagOuter bagrow{bags.ROWS}' id='bag{bags.SLOT}'> 
        <div class='BagTitle'> 
          <div class='BagTitleLeft'></div> 
          <div class='BagTitleMid'>{L_CONTAINER}</div> 
          <div class='BagTitleRight'></div> 
        </div> 
         
        <!-- BEGIN bagslots --> 
        <div class='Slot bagslotloc{bags.bagslots.BS_SLOT} slotimage'></div> 
        <!-- END bagslots --> 
        <!-- BEGIN bagitems --> 
        <div onclick="display(0, {bags.bagitems.BI_SLOT}, 'slot');" class='Slot bagslotloc{bags.bagitems.BI_RELATIVE_SLOT}' style='background-image: url(images/items/item_{bags.bagitems.BI_ICON}.png);'></div> 
        <!-- END bagitems --> 

        <div class='Button bagbuttonrow{bags.ROWS}' onclick="document.getElementById('bag{bags.SLOT}').style.display = 'none';">{L_DONE}</div> 
      </div> 
      <!-- END bags --> 
    </td> 
    <td width='460px' align='center'> 
      <div class='IventoryOuter{HIGHLIGHT_GM}'> 
        <div class='IventoryTitle'> 
          <div class='IventoryTitleLeft'></div> 
          <div class='IventoryTitleMid'>{L_HEADER_INVENTORY}</div> 
          <div class='IventoryTitleRight'></div> 
        </div> 
        <div class='InventoryInner'> 
        
          <div class='InventoryStats2'> 
            <table class='StatTable'> 
              <tr> 
                <td nowrap> 
                  {L_REGEN}<br>{L_FT}<br>{L_DS}<br>{L_HASTE} 
                </td> 
                <td> 
                  {REGEN}<br>{FT}<br>{DS}<br>{HASTE}% 
                </td> 
              </tr> 
            </table> 
          </div> 

          <div class='InventoryStats'> 
            <table class='StatTable'> 
              <tr><td colspan='2'>{FIRST_NAME} {LAST_NAME}</td></tr> 
              <tr><td colspan='2' style='height: 3px'></td></tr> 
              <tr><td colspan='2'>{RACE}</td></tr> 
              <tr><td colspan='2' style='height: 3px'></td></tr> 
              <tr><td colspan='2'>{LEVEL} {CLASS}<br>{DEITY}</td></tr> 
              <tr><td colspan='2' style='height: 3px'></td></tr> 
              <tr> 
                <td>{L_HP}<br>{L_MANA}<br>{L_ENDR}<br>{L_AC}<br>{L_ATK}</td> 
                <td width='100%'>{HP}<br>{MANA}<br>{ENDR}<br>{AC}<br>{ATK}</td> 
              </tr> 
              <tr><td class='Divider' colspan='2'></td></tr> 
              <tr> 
                <td>{L_STR}<br>{L_STA}<br>{L_AGI}<br>{L_DEX}</td> 
                <!-- <td width='100%'>{STR}<br>{STA}<br>{AGI}<br>{DEX}</td> -->
                <td width='100%'><font color='#00FF00'>{STR} <font color='Gold'>+{HSTR}</font><br>{STA} <font color='Gold'>+{HSTA}</font><br>{AGI} <font color='Gold'>+{HAGI}</font><br>{DEX} <font color='Gold'>+{HDEX}</font></font></td>
              </tr> 
              <tr><td class='Divider' colspan='2'></td></tr> 
              <tr> 
                <td>{L_WIS}<br>{L_INT}<br>{L_CHA}</td> 
                <!-- <td width='100%'>{WIS}<br>{INT}<br>{CHA}</td> -->
                <td width='100%'><font color='#00FF00'>{WIS} <font color='Gold'>+{HWIS}</font><br>{INT} <font color='Gold'>+{HINT}</font><br>{CHA} <font color='Gold'>+{HCHA}</font></font></td> 
              </tr> 
              <tr><td class='Divider' colspan='2'></td></tr> 
              <tr> 
                <td>{L_POISON}<br>{L_MAGIC}<br>{L_DISEASE}<br>{L_FIRE}<br>{L_COLD}</td> 
                <!-- <td>{POISON}<br>{MAGIC}<br>{DISEASE}<br>{FIRE}<br>{COLD}</td> -->
                <td><font color='#00FF00'>{POISON} <font color='Gold'>+{HPOISON}</font><br>{MAGIC} <font color='Gold'>+{HMAGIC}</font><br>{DISEASE} <font color='Gold'>+{HDISEASE}</font><br>{FIRE} <font color='Gold'>+{HFIRE}</font><br>{COLD} <font color='Gold'>+{HCOLD}</font></font></td> 
              </tr> 
              <tr><td class='Divider' colspan='2'></td></tr> 
              <tr> 
                <td>{L_WEIGHT}</td> 
                <td nowrap>{WEIGHT} / {STR}</td> 
              </tr> 
            </table> 
          </div> 

          <div class='InventoryMonogram'><img src='images/monograms/{CLASS_NUM}.gif'></div> 

          <div class='Coin' style='top: 116px;left: 317px;'><table class='StatTable'><tr><td align='left'><img src='images/pp.gif'></td><td align='center' width='100%'>{PP}</td></tr></table></div> 
          <div class='Coin' style='top: 144px;left: 317px;'><table class='StatTable'><tr><td align='left'><img src='images/gp.gif'></td><td align='center' width='100%'>{GP}</td></tr></table></div> 
          <div class='Coin' style='top: 172px;left: 317px;'><table class='StatTable'><tr><td align='left'><img src='images/sp.gif'></td><td align='center' width='100%'>{SP}</td></tr></table></div> 
          <div class='Coin' style='top: 200px;left: 317px;'><table class='StatTable'><tr><td align='left'><img src='images/cp.gif'></td><td align='center' width='100%'>{CP}</td></tr></table></div> 

          <div class='Slot slotloc0 slotimage0'></div> 
          <div class='Slot slotloc1 slotimage1'></div> 
          <div class='Slot slotloc2 slotimage2'></div> 
          <div class='Slot slotloc3 slotimage3'></div> 
          <div class='Slot slotloc4 slotimage4'></div> 
          <div class='Slot slotloc5 slotimage5'></div> 
          <div class='Slot slotloc6 slotimage6'></div> 
          <div class='Slot slotloc7 slotimage7'></div> 
          <div class='Slot slotloc8 slotimage8'></div> 
          <div class='Slot slotloc9 slotimage9'></div> 
          <div class='Slot slotloc10 slotimage10'></div> 
          <div class='Slot slotloc11 slotimage11'></div> 
          <div class='Slot slotloc12 slotimage12'></div> 
          <div class='Slot slotloc13 slotimage13'></div> 
          <div class='Slot slotloc14 slotimage14'></div> 
          <div class='Slot slotloc15 slotimage15'></div> 
          <div class='Slot slotloc16 slotimage16'></div> 
          <div class='Slot slotloc17 slotimage17'></div> 
          <div class='Slot slotloc18 slotimage18'></div> 
          <div class='Slot slotloc19 slotimage19'></div> 
          <div class='Slot slotloc20 slotimage20'></div> 
          <div class='Slot slotloc21 slotimage21'></div> 
          <div class='Slot slotloc22 slotimage'></div> 
          <div class='Slot slotloc23 slotimage'></div> 
          <div class='Slot slotloc24 slotimage'></div> 
          <div class='Slot slotloc25 slotimage'></div> 
          <div class='Slot slotloc26 slotimage'></div> 
          <div class='Slot slotloc27 slotimage'></div> 
          <div class='Slot slotloc28 slotimage'></div> 
          <div class='Slot slotloc29 slotimage'></div> 
          <div class='Slot slotloc9999 slotimage9999'></div> 


          <!-- BEGIN invitem --> 
          <div onclick="display(0, {invitem.SLOT}, 'slot');if ({invitem.ISBAG}) display(0, {invitem.SLOT}, 'bag');" class='Slot slotloc{invitem.SLOT}' style='background-image: url(images/items/item_{invitem.ICON}.png);'></div> 
          <!-- END invitem --> 
        </div> 
      </div> 
    </td> 
    <td width='300px' align='left'> 
      <div class='BankOuter'> 
        <div class='BankTitle'> 
          <div class='BankTitleLeft'></div> 
          <div class='BankTitleMid'>{L_HEADER_BANK}</div> 
          <div class='BankTitleRight'></div> 
        </div> 
        <div class='Slot slotloc2000 slotimage'></div> 
        <div class='Slot slotloc2001 slotimage'></div> 
        <div class='Slot slotloc2002 slotimage'></div> 
        <div class='Slot slotloc2003 slotimage'></div> 
        <div class='Slot slotloc2004 slotimage'></div> 
        <div class='Slot slotloc2005 slotimage'></div> 
        <div class='Slot slotloc2006 slotimage'></div> 
        <div class='Slot slotloc2007 slotimage'></div> 
        <div class='Slot slotloc2008 slotimage'></div> 
        <div class='Slot slotloc2009 slotimage'></div> 
        <div class='Slot slotloc2010 slotimage'></div> 
        <div class='Slot slotloc2011 slotimage'></div> 
        <div class='Slot slotloc2012 slotimage'></div> 
        <div class='Slot slotloc2013 slotimage'></div> 
        <div class='Slot slotloc2014 slotimage'></div> 
        <div class='Slot slotloc2015 slotimage'></div> 
        <div class='Slot slotloc2016 slotimage'></div> 
        <div class='Slot slotloc2017 slotimage'></div> 
        <div class='Slot slotloc2018 slotimage'></div> 
        <div class='Slot slotloc2019 slotimage'></div> 
        <div class='Slot slotloc2020 slotimage'></div> 
        <div class='Slot slotloc2021 slotimage'></div> 
        <div class='Slot slotloc2022 slotimage'></div> 
        <div class='Slot slotloc2023 slotimage'></div> 

        <!-- BEGIN bankitem --> 
        <div onclick="display(0, {bankitem.SLOT}, 'slot'); if ({bankitem.ISBAG}) display(0, {bankitem.SLOT}, 'bag');" class='Slot slotloc{bankitem.SLOT}' style='background-image: url(images/items/item_{bankitem.ICON}.png);'></div> 
        <!-- END bankitem --> 
        

        <div class='Coin' style='top: 200px;left: 6px;'><table class='StatTable'><tr><td align='left'><img src='images/pp.gif'></td><td align='center' width='100%'>{BPP}</td></tr></table></div> 
        <div class='Coin' style='top: 228px;left: 6px;'><table class='StatTable'><tr><td align='left'><img src='images/gp.gif'></td><td align='center' width='100%'>{BGP}</td></tr></table></div> 
        <div class='Coin' style='top: 256px;left: 6px;'><table class='StatTable'><tr><td align='left'><img src='images/sp.gif'></td><td align='center' width='100%'>{BSP}</td></tr></table></div> 
        <div class='Coin' style='top: 284px;left: 6px;'><table class='StatTable'><tr><td align='left'><img src='images/cp.gif'></td><td align='center' width='100%'>{BCP}</td></tr></table></div> 

      </div> 
    </td> 
  </tr> 
  <tr> 
    <td colspan='3' align='center'> 
      <br> 
      <br> 
      <br> 
      <!-- BEGIN item --> 
      <div class='ItemOuter' id='slot{item.SLOT}' style='display:none;'> 
        <div class='ItemTitle'> 
          <div class='ItemTitleLeft'></div> 
          <div class='ItemTitleMid'><a href='{item.LINK}'>{item.NAME}</a></div> 
          <div class='ItemTitleRight'></div> 
        </div> 
        <div class='ItemInner' style='text-align:left;'>        
          {item.HTML} 
          <!-- BEGIN augment --> 
          <center> 
            <br> 
            <table class='AugTable'> 
              <tr> 
                <td align='center'> 
                  <a href='{item.augment.AUG_LINK}'>{item.augment.AUG_NAME}</a> 
                </td> 
              </tr> 
              <tr> 
                <td align='left'> 
                  {item.augment.AUG_HTML} 
                </td> 
              </tr> 
            </table> 
          </center> 
          <!-- END augment --> 
        </div> 
      </div> 
      <!-- END item --> 
    </td> 
  </tr> 
</table> 
</center> 
