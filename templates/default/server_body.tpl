<div class='WindowComplexFancy PositionServer CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_SERVER}</div>  
   <nav class='CB_Tab_Box CB_TopTabs'>
      <ul>
         <li id='toptab1' onclick="CB_displayTab('#charbrowser NAV.CB_TopTabs UL LI', '#toptab1', '#charbrowser DIV.PositionServer TABLE.CB_TopTable', '#toptabbox1');">{L_ALL_TIME}</li> 
         <li id='toptab2' onclick="CB_displayTab('#charbrowser NAV.CB_TopTabs UL LI', '#toptab2', '#charbrowser DIV.PositionServer TABLE.CB_TopTable', '#toptabbox2');">{L_CUTOFF}</li> 
      </ul>
   </nav>  
   <table id='toptabbox1' class='CB_Table CB_TopTable'>
      <thead>
         <tr>
            <th>{L_MIN_LEVEL}</th>     
            <th>{L_MAX_LEVEL}</th>  
            <th>{L_AVG_LEVEL}</th>
            <th>{L_CHAR_COUNT}</th>
         </tr>
      </thead>
      <tbody>   
         <tr>
            <td>{MIN_LEVEL}</td>    
            <td>{MAX_LEVEL}</td>
            <td>{AVG_LEVEL}</td>
            <td>{CHAR_COUNT}</td>
         </tr>
      </tbody>
   </table>  
   <table id='toptabbox2' class='CB_Table CB_TopTable'>
      <thead>
         <tr>
            <th>{L_MIN_LEVEL}</th>     
            <th>{L_MAX_LEVEL}</th>  
            <th>{L_AVG_LEVEL}</th>
            <th>{L_CHAR_COUNT}</th>
         </tr>
      </thead>
      <tbody>   
         <tr>
            <td>{MIN_LEVEL_CUTOFF}</td>    
            <td>{MAX_LEVEL_CUTOFF}</td>
            <td>{AVG_LEVEL_CUTOFF}</td>
            <td>{CHAR_COUNT_CUTOFF}</td>
         </tr>
      </tbody>
   </table>      
   <nav class='CB_Tab_Box CB_BottomTabs'>
      <ul>
         <li id='tab1' onclick="CB_displayTab('#charbrowser NAV.CB_BottomTabs UL LI', '#tab1', '#charbrowser DIV.PositionServer DIV.CB_ServerTabBoxes', '#tabbox1');">{L_CLASSES}</li> 
         <li id='tab2' onclick="CB_displayTab('#charbrowser NAV.CB_BottomTabs UL LI', '#tab2', '#charbrowser DIV.PositionServer DIV.CB_ServerTabBoxes', '#tabbox2');populateChart();">{L_LEVELS}</li> 
         <li id='tab3' onclick="CB_displayTab('#charbrowser NAV.CB_BottomTabs UL LI', '#tab3', '#charbrowser DIV.PositionServer DIV.CB_ServerTabBoxes', '#tabbox3');populateChart_Cutoff();">{L_LEVELS_CUTOFF}</li> 
      </ul>
   </nav>   
   <div id='tabbox1' class='CB_ServerTabBoxes'>
      <table class='CB_Table'>
         <thead>
            <tr>
               <th>{L_CLASS}</th>    
               <th>{L_PERCENT}</th> 
               <th>{L_COUNT}</th> 
               <th>{L_AVG_LEVEL}</th>
            </tr>
         </thead>
         <tbody>
         <!-- BEGIN classes -->
            <tr>
               <td>{classes.CLASS}</td>
               <td class='CB_Chart_Bar'><div style='width:{classes.RELATIVE_CLEAN_PERCENT}%;'><span>{classes.ROUNDED_PERCENT}%</span></div></td>   
               <td>{classes.COUNT}</td>    
               <td>{classes.LEVEL}</td>
            </tr>
         <!-- END classes -->
         </tbody>
      </table>
   </div>
   <div id='tabbox2' class='CB_ServerTabBoxes'>
      <canvas id="myChart" width="400" height="200">Your browser does not support the HTML5 canvas tag.</canvas>
   </div>
   <div id='tabbox3' class='CB_ServerTabBoxes'>
      <canvas id="myChart_Cutoff" width="400" height="200">Your browser does not support the HTML5 canvas tag.</canvas>
   </div>
   <div class='CB_Button' onclick="cb_GoBackOnePage();">{L_BACK}</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>
   var myNewChart = 0;
   function populateChart() {   
      if (!myNewChart) {
         myNewChart = new Chart(document.getElementById("myChart"), {
           type: 'line',
           data: {
             labels: [
               <!-- BEGIN levels -->
               {levels.LEVEL},
               <!-- END levels -->
             ],
             datasets: [{ 
                 data: [
                   <!-- BEGIN levels -->
                   {levels.COUNT},
                   <!-- END levels -->
                 ],
                 label: "Characters of this level: ",
                 borderColor: "rgba(138, 163, 255, 1)",
                 fillOpacity: .3,
                 backgroundColor: "rgba(138, 163, 255, 0.5)",
                 fill: true,
                 borderWidth: 1,
                 pointRadius: 2,
                 pointHoverRadius: 4
               }
             ]
           },
           options: {
             tooltips: {
               custom: function(tooltip) {
                 if (!tooltip) return;
                 // disable displaying the color box;
                 tooltip.displayColors = false;
               },          
               callbacks: {
                 title: function(tooltipItem, data) {
                   return "";
                   return "Level: " + data.labels[tooltipItem[0]['index']];
                 },
                 label: function(tooltipItem, data) {
                   return data.datasets[0].data[tooltipItem['index']] + ' of {CHAR_COUNT} characters are level ' + data.labels[tooltipItem['index']];
                 }
               }
             },
             elements: {
                line: {
                   tension: .600001
                }
             },        
             title: {
               display: false,
               text: 'Level Distribution'
             },
             legend: {
               display: false,
               labels: {
                 fontColor: 'rgb(255, 99, 132)'
               }
             },
             scales: {
               xAxes: [{
                 ticks: {
                   autoSkip: true,
                   maxTicksLimit: 20,
                   fontColor: 'white'
                 },
                 display: true,
                 scaleLabel: {
                   display: true,
                   labelString: 'Level',
                   fontColor: 'white'
                 }              
               }],
               yAxes: [{
                 ticks: {
                   fontColor: 'white'
                 },
                 display: true,
                 scaleLabel: {
                   display: true,
                   labelString: 'Count',
                   fontColor: 'white'
                 }
               }]
             }
           }
        });
      }
   }
   
   var myNewChart_Cutoff = 0;
   function populateChart_Cutoff() {   
      if (!myNewChart_Cutoff) {
         myNewChart_Cutoff = new Chart(document.getElementById("myChart_Cutoff"), {
           type: 'line',
           data: {
             labels: [
               <!-- BEGIN levels_cutoff -->
               {levels_cutoff.LEVEL},
               <!-- END levels_cutoff -->
             ],
             datasets: [{ 
                 data: [
                   <!-- BEGIN levels_cutoff -->
                   {levels_cutoff.COUNT},
                   <!-- END levels_cutoff -->
                 ],
                 label: "Characters of this level: ",
                 borderColor: "rgba(138, 163, 255, 1)",
                 fillOpacity: .3,
                 backgroundColor: "rgba(138, 163, 255, 0.5)",
                 fill: true,
                 borderWidth: 1,
                 pointRadius: 2,
                 pointHoverRadius: 4
               }
             ]
           },
           options: {
             tooltips: {
               custom: function(tooltip) {
                 if (!tooltip) return;
                 // disable displaying the color box;
                 tooltip.displayColors = false;
               },          
               callbacks: {
                 title: function(tooltipItem, data) {
                   return "";
                   return "Level: " + data.labels[tooltipItem[0]['index']];
                 },
                 label: function(tooltipItem, data) {
                   return data.datasets[0].data[tooltipItem['index']] + ' of {CHAR_COUNT_CUTOFF} characters are level ' + data.labels[tooltipItem['index']];
                 }
               }
             },
             elements: {
                line: {
                   tension: .600001
                }
             },        
             title: {
               display: false,
               text: 'Level Distribution'
             },
             legend: {
               display: false,
               labels: {
                 fontColor: 'rgb(255, 99, 132)'
               }
             },
             scales: {
               xAxes: [{
                 ticks: {
                   autoSkip: true,
                   maxTicksLimit: 20,
                   fontColor: 'white'
                 },
                 display: true,
                 scaleLabel: {
                   display: true,
                   labelString: 'Level',
                   fontColor: 'white'
                 }              
               }],
               yAxes: [{
                 ticks: {
                   fontColor: 'white'
                 },
                 display: true,
                 scaleLabel: {
                   display: true,
                   labelString: 'Count',
                   fontColor: 'white'
                 }
               }]
             }
           }
        });
      }
   }
</script>

<script type="text/javascript">
   //display the first tab after load BOTTOM tabs
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_BottomTabs UL LI', '#tab1', '#charbrowser DIV.PositionServer DIV.CB_ServerTabBoxes', '#tabbox1');
   });
   
   //display the first tab after load TOP tabs
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_TopTabs UL LI', '#toptab1', '#charbrowser DIV.PositionServer TABLE.CB_TopTable', '#toptabbox1');
   });
</script>
