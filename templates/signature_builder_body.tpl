<div class='WindowComplexFancy PositionSignatureBuilder CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_SIGNATURE_BUILDER}</div> 
   <nav class='CB_Tab_Box'>
      <ul>
         <!-- BEGIN tabs -->
         <li id='tab{tabs.ID}' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab{tabs.ID}', '#charbrowser DIV.CB_SigTabBoxes', '#tabbox{tabs.ID}');">{tabs.TEXT}</li> 
         <!-- END tabs -->
      </ul>
   </nav>   
   <form name='sigform' onsubmit="return false;"> 
      <div class='WindowNestedBlue'>
         <div id='tabbox1' class='CB_SigTabBoxes'> 
            <div>
               <label for='char'>{L_NAME}</label>
               <input id='char' name='char' type='text' onkeyup="$('#nameerror').html('');" value='{CHARNAME}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><span id='nameerror'></span>   
            </div>
            
            <div> 
               <label for='fontone'>{L_FONT_ONE}</label>
               <select id='fontone' name='fontone'>
                  <!-- BEGIN font -->
                  <option value='{font.VALUE}'>{font.TEXT}</option>
                  <!-- END font -->
               </select> 
            </div>
            
            <div> 
               <label for='sizeone'>{L_FONT_SIZE_ONE}</label>
               <select id='sizeone' name='sizeone' {CAN_CHANGE_FONT_SIZE}>
                  <!-- BEGIN fontsize -->
                  <option value='{fontsize.VALUE}'>{fontsize.TEXT}</option>
                  <!-- END fontsize -->
               </select>    
            </div> 
            
            <div> 
               <label for='shadowone'>{L_FONT_SHADOW_ONE}</label>
               <select id='shadowone' name='shadowone'>
                  <option value='0'>off</option>
                  <option value='1'>on</option>
               </select>         
            </div> 
            
            <div> 
               <label for='colorone'>{L_FONT_COLOR_ONE}</label>
               <input class='color' id='colorone' name='colorone' value='FFFFFF' type='text'> 
            </div> 
            
            <div>
               <label for='fonttwo'>{L_FONT_TWO}</label>
               <select id='fonttwo' name='fonttwo'>
                  <!-- BEGIN font -->
                  <option value='{font.VALUE}'>{font.TEXT}</option>
                  <!-- END font -->
               </select>   
            </div>
                  
            <div>
               <label for='sizetwo'>{L_FONT_SIZE_TWO}</label>
               <select id='sizetwo' name='sizetwo' {CAN_CHANGE_FONT_SIZE}>
                  <!-- BEGIN fontsize -->
                  <option value='{fontsize.VALUE}'>{fontsize.TEXT}</option>
                  <!-- END fontsize -->
               </select>   
            </div>
                  
            <div>
               <label for='shadowtwo'>{L_FONT_SHADOW_TWO}</label>
               <select id='shadowtwo' name='shadowtwo'>
                  <option value='0'>off</option>
                  <option value='1'>on</option>
               </select>         
            </div>
            
            <div>
               <label for='colortwo'>{L_FONT_COLOR_TWO}</label>
               <input class='color' id='colortwo' name='colortwo' value='FFFFFF' type='text'>        
            </div>
         </div>
         
         <div id='tabbox2' class='CB_SigTabBoxes'>  
            <div>
               <label for='mainbg'>{L_MAIN_BACKGROUND}</label>
               <select id='mainbg' name='mainbg'>
                  <!-- BEGIN backgrounds -->
                  <option value='{backgrounds.VALUE}'>{backgrounds.TEXT}</option>
                  <!-- END backgrounds -->
               </select>    
            </div>
                       
            <div>
               <label for='mainscreen'>{L_MAIN_SCREEN}</label>
               <select id='mainscreen' name='mainscreen'>
                  <!-- BEGIN screens -->
                  <option value='{screens.VALUE}'>{screens.TEXT}</option>
                  <!-- END screens -->
               </select>   
            </div>
                 
            <div>
               <label for='bgcolor'>{L_MAIN_COLOR}</label>
               <input class='color' id='bgcolor' name='bgcolor' value='2A2A2A' type='text'>    
            </div>
            
            <div>
               <label for='border'>{L_MAIN_BORDER}</label>
               <select id='border' name='border'>
                  <!-- BEGIN borders -->
                  <option value='{borders.VALUE}'>{borders.TEXT}</option>
                  <!-- END borders -->
               </select>         
            </div>
            
            <div>
               <label for='epicborder'>{L_EPIC_BORDER}</label>
               <select id='epicborder' name='epicborder'>
                  <!-- BEGIN epicborders -->
                  <option value='{epicborders.VALUE}'>{epicborders.TEXT}</option>
                  <!-- END epicborders -->
               </select>  
            </div>
         </div>
         
         <div id='tabbox3' class='CB_SigTabBoxes'>        
            <div>
               <label for='statone'>{L_STATS} 1</label>
               <select id='statone' name='statone'>
                  <!-- BEGIN stats -->
                  <option value='{stats.VALUE}'>{stats.TEXT}</option>
                  <!-- END stats -->
               </select> 
            </div>
            <div>            
               <label for='stattwo'>{L_STATS} 2</label>
               <select id='stattwo' name='stattwo'>
                  <!-- BEGIN stats -->
                  <option value='{stats.VALUE}'>{stats.TEXT}</option>
                  <!-- END stats -->
               </select> 
            </div>
            <div>            
               <label for='statthree'>{L_STATS} 3</label>
               <select id='statthree' name='statthree'>
                  <!-- BEGIN stats -->
                  <option value='{stats.VALUE}'>{stats.TEXT}</option>
                  <!-- END stats -->
               </select> 
            </div>
                   
            <div>
               <label for='statfour'>{L_STATS} 4</label>
               <select id='statfour' name='statfour'>
                  <!-- BEGIN stats -->
                  <option value='{stats.VALUE}'>{stats.TEXT}</option>
                  <!-- END stats -->
               </select> 
            </div>
                  
            <div>
               <label for='statfive'>{L_STATS} 5</label>
               <select id='statfive' name='statfive'>
                  <!-- BEGIN stats -->
                  <option value='{stats.VALUE}'>{stats.TEXT}</option>
                  <!-- END stats -->
               </select>
            </div>
                   
            <div>
               <label for='statborder'>{L_STAT_BORDER}</label>
               <select id='statborder' name='statborder'>
                  <!-- BEGIN statborders -->
                  <option value='{statborders.VALUE}'>{statborders.TEXT}</option>
                  <!-- END statborders -->
               </select>     
            </div>
                       
            <div>
               <label for='statcolor'>{L_STAT_COLOR}</label>
               <input class='color' id='statcolor' name='statcolor' value='FFFFFF' type='text'> 
            </div>
         </div>
         <div id='tabbox4' class='CB_SigTabBoxes'> 
            <label for='bbcode'>{L_BBCODE}</label>
            <textarea id='bbcode' name='bbcode'></textarea><br>
            <label for='html'>{L_HTML}</label>
            <textarea id='html' name='html'></textarea><br>
            <label for='preview'>{L_PREVIEW}</label>           
            <img id='preview' name='preview' src="{SIGNATURE_ROOT_URL}cbsig/0/0/0/0/0/0/0.png">
         </div>
      </div>
           
      <div class='CB_Button' onclick="buildstring();" >{L_CREATE}</div>

   </form>
</div>
<script type="text/javascript">
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.CB_SigTabBoxes', '#tabbox1');
   });

   function buildstring() {
  
      //we need at least a name
      var name = 0;
      if (document.sigform.char.value) {
         name = document.sigform.char.value;
      } 
      else {
         $('#nameerror').html('{L_NEED_NAME}');
         CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.CB_SigTabBoxes', '#tabbox1');
         return;
      }

      //build the image path
      var path = "{SIGNATURE_ROOT_URL}cbsig/" + 
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

      //populate the output tab
      document.sigform.bbcode.value = "[url={SIGNATURE_INDEX_URL}?page=character&char=" + name + "][img]" + path + "[/img][/url]";
      document.sigform.html.value = "<a href='{SIGNATURE_INDEX_URL}?page=character&char=" + name + "'><img src='" + path + "'></a>";
      document.preview.src = path;

      //swap to the output tab
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab4', '#charbrowser DIV.CB_SigTabBoxes', '#tabbox4');
  }
</script>
<script type="text/javascript" src="{ROOT_URL}jscolor/jscolor.js"></script>  