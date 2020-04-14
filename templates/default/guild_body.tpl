<div class='WindowComplexFancy PositionGuild CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_GUILD} - {GUILD_NAME}</div>
   <table class='CB_Table'>
      <thead>
         <tr>
            <th>{L_GUILD}</th>     
            <th>{L_LEADER}</th>  
            <th>{L_AVG_LEVEL}</th>
            <th>{L_MEMBERS}</th>
         </tr>
      </thead>
      <tbody>   
         <tr>
            <td>{GUILD_NAME}</td>    
            <td><a href="{INDEX_URL}?page=character&char={GUILD_LEADER}">{GUILD_LEADER}</a></td>
            <td>{GUILD_AVG_LEVEL}</td>
            <td>{GUILD_COUNT}</td>
         </tr>
      </tbody>
   </table>      
   <nav class='CB_Tab_Box'>
      <ul>
         <li id='tab1' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionGuild DIV.CB_GuildTabBoxes', '#tabbox1');">{L_CLASSES}</li> 
         <li id='tab2' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab2', '#charbrowser DIV.PositionGuild DIV.CB_GuildTabBoxes', '#tabbox2');">{L_MEMBERS}</li> 
         <li id='tab3' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab3', '#charbrowser DIV.PositionGuild DIV.CB_GuildTabBoxes', '#tabbox3');populateChart();">{L_LEVELS}</li> 
      </ul>
   </nav>   
   <div id='tabbox1' class='CB_GuildTabBoxes'>
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
         <!-- BEGIN guildclasses -->
            <tr>
               <td>{guildclasses.CLASS}</td>
               <td class='CB_Chart_Bar'><div style='width:{guildclasses.RELATIVE_CLEAN_PERCENT}%;'><span>{guildclasses.ROUNDED_PERCENT}%</span></div></td>   
               <td>{guildclasses.COUNT}</td>    
               <td>{guildclasses.LEVEL}</td>
            </tr>
         <!-- END guildclasses -->
         </tbody>
      </table>
   </div>
   <div id='tabbox2' class='CB_GuildTabBoxes'>
      <table class='CB_Table CB_Highlight_Rows'>
         <thead>
            <tr>
               <th>{L_NAME}</th>     
               <th>{L_RANK}</th>  
               <th>{L_RACE}</th>
               <th>{L_CLASS}</th>
               <th>{L_LEVEL}</th>
            </tr>
         </thead>
         <tbody>
         <!-- BEGIN guildmembers -->
            <tr>
               <td>{guildmembers.NAME}</td>
               <td>{guildmembers.RANK}</td>    
               <td>{guildmembers.RACE}</td>    
               <td>{guildmembers.CLASS}</td>
               <td>{guildmembers.LEVEL}</td>
            </tr>
         <!-- END guildmembers -->
         </tbody>
      </table>
   </div>
   <div id='tabbox3' class='CB_GuildTabBoxes'>
      <canvas id="myChart" width="400" height="200">Your browser does not support the HTML5 canvas tag.</canvas>
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
               <!-- BEGIN guildlevels -->
               {guildlevels.LEVEL},
               <!-- END guildlevels -->
             ],
             datasets: [{ 
                 data: [
                   <!-- BEGIN guildlevels -->
                   {guildlevels.COUNT},
                   <!-- END guildlevels -->
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
                   return data.datasets[0].data[tooltipItem['index']] + ' of {GUILD_COUNT} members are level ' + data.labels[tooltipItem['index']];
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
   //display the first tab after load
   $( document ).ready(function() {
      CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionGuild DIV.CB_GuildTabBoxes', '#tabbox1');
   });
</script>
