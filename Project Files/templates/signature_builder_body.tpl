<script type="text/javascript">
  function buildstring() {
    var name = 0;
    if (document.sigform.char.value) {
      name = document.sigform.char.value;
    } 
    else {
      alert ('{L_NEED_NAME}');
      return;
    }
    
    var sigdir = document.sigform.sigdir.value;																																								

    var path = sigdir + "cbsig/" + 
	document.sigform.fontone.value + "-" + 
	document.sigform.sizeone.value + "-" + 
	document.sigform.colorone.value + "-" + 
	document.sigform.shadowone.value + "/" + 
	document.sigform.fonttwo.value + "-" + 
	document.sigform.sizetwo.value + "-" + 
	document.sigform.colortwo.value + "-" + 
	document.sigform.shadowtwo.value + "/" + 
	document.sigform.epicborder.value + "/" + 
	document.sigform.statborder.value + "-" + 
	document.sigform.statcolor.value + "-" + 
	document.sigform.statone.value + "-" + 
	document.sigform.stattwo.value + "-" + 
	document.sigform.statthree.value + "-" + 
	document.sigform.statfour.value + "-" + 
	document.sigform.statfive.value + "/" +  
	document.sigform.border.value + "/" +  
	document.sigform.bgcolor.value + "-" +  
	document.sigform.mainbg.value + "-" +  
	document.sigform.mainscreen.value + "/" +  
	name + ".png";

    document.sigform.bbcode.value = "[url=" + sigdir + "character.php?char=" + name + "][img]" + path + "[/img][/url]";
    document.sigform.html.value = "<a href='" + sigdir + "character.php?char=" + name + "'><img src='" + path + "'></a>";
    document.preview.src = path;

  }
</script>
<script type="text/javascript" src="jscolor/jscolor.js"></script>

<center>
  <div class='FlagOuter'>
    <div class='FlagTitle'>
      <div class='FlagTitleLeft'></div>
      <div class='FlagTitleMid'>{L_SIGNATURE_BUILDER}</div>
      <div class='FlagTitleRight'></div>
    </div>
    <div class='FlagInner'>  
      <form name='sigform' onsubmit="return false;"> 
        <input type='hidden' name='sigdir' value='{SIGNATURE_DIR}'>
    	  <div class='FlagTabBox' >        
	    <table class='StatTable' style='width:90%;'>
	      <tr><td colspan='5'>
	      {L_NAME}<br><input class='SigBuilder' name='char'>
	      </td></tr>
	      <tr><td>&nbsp;</td></tr>
	      <tr><td colspan='5'>
	          {L_STATS}	          
	      </td></tr>
	      <tr>
	        <td>
	          <select class='SigBuilder' name='statone'>
	            <!-- BEGIN stats -->
	            <option value='{stats.VALUE}'>{stats.TEXT}</option>
	            <!-- END stats -->
	          </select>
	        </td>
	        <td>	
	          <select class='SigBuilder' name='stattwo'>
	            <!-- BEGIN stats -->
	            <option value='{stats.VALUE}'>{stats.TEXT}</option>
	            <!-- END stats -->
	          </select>
	        </td>
	        <td>	
	          <select class='SigBuilder' name='statthree'>
	            <!-- BEGIN stats -->
	            <option value='{stats.VALUE}'>{stats.TEXT}</option>
	            <!-- END stats -->
	          </select>
	        </td>
	        <td>	
	          <select class='SigBuilder' name='statfour'>
	            <!-- BEGIN stats -->
	            <option value='{stats.VALUE}'>{stats.TEXT}</option>
	            <!-- END stats -->
	          </select>
	        </td>
	        <td>
	          <select class='SigBuilder' name='statfive'>
	            <!-- BEGIN stats -->
	            <option value='{stats.VALUE}'>{stats.TEXT}</option>
	            <!-- END stats -->
	          </select>
	        </td>
	      </tr>	      
	      <tr><td>&nbsp;</td></tr>
              <tr>
                <td colspan='2'>
	          {L_EPIC_BORDER}<br>
	          <select class='SigBuilder' name='epicborder'>
	            <!-- BEGIN epicborders -->
	            <option value='{epicborders.VALUE}'>{epicborders.TEXT}</option>
	            <!-- END epicborders -->
	          </select>                
	        </td>
	        <td>
	          {L_STAT_BORDER}<br>
	          <select class='SigBuilder' name='statborder'>
	            <!-- BEGIN statborders -->
	            <option value='{statborders.VALUE}'>{statborders.TEXT}</option>
	            <!-- END statborders -->
	          </select>
	        </td>
	        <td colspan='2'>
                  {L_STAT_COLOR}<br><input class='color SigBuilder' name='statcolor' value='FFFFFF'>
	        </td>	        
	      </tr>	      
	      <tr><td>&nbsp;</td></tr>
	      <tr>
	        <td>
	          {L_FONT_ONE}<br>
	          <select class='SigBuilder' name='fontone'>
	            <!-- BEGIN font -->
	            <option value='{font.VALUE}'>{font.TEXT}</option>
	            <!-- END font -->
	          </select>
	        </td>
	        <td>
	          {L_FONT_SIZE_ONE}<br>
	          <select class='SigBuilder' name='sizeone' {CAN_CHANGE_FONT_SIZE}>
	            <!-- BEGIN fontsize -->
	            <option value='{fontsize.VALUE}'>{fontsize.TEXT}</option>
	            <!-- END fontsize -->
	          </select>
	        </td>
	        <td>
	          {L_FONT_SHADOW_ONE}<br>
	          <select class='SigBuilder' name='shadowone'>
	            <option value='0'>off</option>
	            <option value='1'>on</option>
	          </select>
	        </td>
	        <td colspan='2'>
	          {L_FONT_COLOR_ONE}<br><input class='color SigBuilder' name='colorone' value='FFFFFF'>
	        </td>	        
	      </tr>
	      <tr><td>&nbsp;</td></tr>
	      <tr>
	        <td>
	          {L_FONT_TWO}<br>
	          <select class='SigBuilder' name='fonttwo'>
	            <!-- BEGIN font -->
	            <option value='{font.VALUE}'>{font.TEXT}</option>
	            <!-- END font -->
	          </select>
	        </td>
	        <td>	          
	          {L_FONT_SIZE_TWO}<br>
	          <select class='SigBuilder' name='sizetwo' {CAN_CHANGE_FONT_SIZE}>
	            <!-- BEGIN fontsize -->
	            <option value='{fontsize.VALUE}'>{fontsize.TEXT}</option>
	            <!-- END fontsize -->
	          </select>
	        </td>
	        <td>
	          {L_FONT_SHADOW_TWO}<br>
	          <select class='SigBuilder' name='shadowtwo'>
	            <option value='0'>off</option>
	            <option value='1'>on</option>
	          </select>
	        </td>	
	        <td colspan='2'>	          
	          {L_FONT_COLOR_TWO}<br><input class='color SigBuilder' name='colortwo' value='FFFFFF'>
	        </td>	                	          
	      </tr>
	      <tr><td>&nbsp;</td></tr>
              <tr>
                <td>
	          {L_MAIN_BACKGROUND}<br>
	          <select class='SigBuilder' name='mainbg'>
	            <!-- BEGIN backgrounds -->
	            <option value='{backgrounds.VALUE}'>{backgrounds.TEXT}</option>
	            <!-- END backgrounds -->
	          </select>
	        </td>
                <td>
	          {L_MAIN_SCREEN}<br>
	          <select class='SigBuilder' name='mainscreen'>
	            <!-- BEGIN screens -->
	            <option value='{screens.VALUE}'>{screens.TEXT}</option>
	            <!-- END screens -->
	          </select>
	        </td>
                <td>
	          {L_MAIN_BORDER}<br>
	          <select class='SigBuilder' name='border'>
	            <!-- BEGIN borders -->
	            <option value='{borders.VALUE}'>{borders.TEXT}</option>
	            <!-- END borders -->
	          </select>
	        </td>
	        <td colspan='2'>
                  {L_MAIN_COLOR}<br><input class='color SigBuilder' name='bgcolor' value='2A2A2A'>
                </td>	        
	      </tr>
	    </table>
	    <br>
	  </div>

	    <center>
	    <br><br>
	    <div class='FreeButton' onclick="javascript:buildstring();" >{L_CREATE}</div>
	    <br><br>
	    </center>
	  <div class='FlagTabBox' align='center'> 
	    <table class='StatTable' style='width:auto;'>
	      <tr><td align='left'>
	        {L_BBCODE}<br>
	        <textarea name='bbcode' class='SigBuilder' style="width:500px;height:50px;"></textarea><br>
	        <br>
	        {L_HTML}<br>
	        <textarea name='html' class='SigBuilder' style="width:500px;height:50px;"></textarea><br>
	        <br>
	        {L_PREVIEW}<br>	        
	        <img name='preview' src="{SIGNATURE_DIR}cbsig/0/0/0/0/0/0/0.png">
	      </td></tr>
	    </table>
	  </div>
      </form>
    </div>
  </div>
  </center>