/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   Portions of this program are derived from publicly licensed software
 *   projects including, but not limited to phpBB, Magelo Clone, 
 *   EQEmulator, EQEditor, and Allakhazam Clone.
 *
 *                                  Author:
 *                           Maudigan(Airwalking) 
 *
 *   March 7, 2020 - initial revision (Maudigan) 
 *      
 ***************************************************************************/

var cbPopup_moveLeft = 5;
var cbPopup_moveDown = -1;
var cbPopup_displayTrigger = '#charbrowser .HoverSlot';
var cbPopup_displayContainers = '#charbrowser DIV.ItemOuterOpen';
var cbPopup_curZIndex = 100000;
var cbPopup_gapBetweenTiled = 13;

//LOCK ITEM ON SCREEN AND/OR CHECK ITS STATUS
function cbPopup_KeepItemUp(curItemID, keepItemUp = "check") {   
   var curDisplayDiv = $(curItemID);

   //if they passed a boolean they're trying to set the value
   if (keepItemUp === 'check') {
      //check if it's on or off
      var checkValKIP = (curDisplayDiv.attr('keepItemUp'));
      if (checkValKIP == '1') {
         return true;
      } else {
         return false;
      }
   } else {
      if (keepItemUp) {
         //turn it on
         curDisplayDiv.attr('keepItemUp', 1);
         curDisplayDiv.addClass('ItemOuterOpen');
         return true;
      } else {
         //turn it off
         curDisplayDiv.attr('keepItemUp', 0);
         curDisplayDiv.removeClass('ItemOuterOpen'); 
         return false;
      }
   }
   return false;
}

//CLICK CONTENT
//move it to the top of the stack when interacted with
function cbPopup_ZOrder(curItemID) {
  $(curItemID).css('z-index', cbPopup_curZIndex++); 
}


//TILE CONTENT
//loop through every popup and tile them on the screen
var cbPopup_curLeft = 0;
var cbPopup_maxRowHeight = 0;
var cbPopup_curTop = 0;
function cbPopup_tileItems() {
   cbPopup_curLeft = cbPopup_gapBetweenTiled;
   cbPopup_curTop = $(document).scrollTop() + cbPopup_gapBetweenTiled;
   
   $(cbPopup_displayContainers).each(function( index ) {
      attr = $(this).attr('id');
      
     //skip if it has no id
      if (typeof attr === typeof undefined || attr === false) {
        return;
      }
         
      var curItemID = "#" + attr;

      var curDisplayDiv = $(this);

      //where will the right edge land 
      var curRight = cbPopup_curLeft + curDisplayDiv.outerWidth();

      //if the right edge will fall off the screen, jump to the next row
      if (curRight >= $(window).width()) {
         cbPopup_curLeft = cbPopup_gapBetweenTiled;
         cbPopup_curTop += cbPopup_maxRowHeight + cbPopup_gapBetweenTiled;
         cbPopup_maxRowHeight = 0;
      }

      //SET THE POSITION
      curDisplayDiv.css('top', parseInt(cbPopup_curTop)).css('left', parseInt(cbPopup_curLeft));

      //track the max height of this row
      if (cbPopup_maxRowHeight < curDisplayDiv.outerHeight()) {
         cbPopup_maxRowHeight = curDisplayDiv.outerHeight();
      }

      cbPopup_curLeft += curDisplayDiv.outerWidth() + cbPopup_gapBetweenTiled;
   });    
}


//CLOSE ALL BUTTON
function cbPopup_closeAllItems() {
   $(cbPopup_displayContainers).each(function( index ) {
      attr = $(this).attr('id');
      
     //skip if it has no id
      if (typeof attr === typeof undefined || attr === false) {
        return;
      }
         
      var curItemID = "#" + attr;
      
      cbPopup_closeItem(curItemID);
   }); 
}


//CLOSE BUTTON
function cbPopup_closeItem(curItemID) {
   cbPopup_KeepItemUp(curItemID, false);
   cbPopup_hideItem(curItemID);
}


//HOVER OVER
//when youre done hovering hide the item
function cbPopup_hideItem(curItemID) {
   $(curItemID).hide();
}

//MAINTAIN ITEM POPUP POSITION
//load/display/animate the item popup
function cbPopup_maintainItem(curItemID, X, Y) {
   var curDisplayDiv = 0;
   var curDisplayDivContent = 0;
   var curDisplayDivHeader = 0;
   
   //item already exists, grab our pointer to it
   curDisplayDiv = $(curItemID);

   //display the div
   curDisplayDiv.css('z-index', cbPopup_curZIndex++); 
   curDisplayDiv.show();
   curDisplayDiv.draggable();
   
   //CALCULATE POPUP POSITION
   //place it slightly to the left of the mouse
   leftD = X + cbPopup_moveLeft;
   maxRight = leftD + curDisplayDiv.outerWidth();
   windowLeft = $(window).width() - 40;
   windowRight = 0;
   maxLeft = X - (cbPopup_moveLeft + curDisplayDiv.outerWidth() + 20);

   if (maxRight > windowLeft && maxLeft > windowRight) {
      leftD = maxLeft;
   }

   topD = Y - cbPopup_moveDown;
   maxBottom = Y + cbPopup_moveDown + curDisplayDiv.outerHeight() + 50;
   windowBottom = $(document).scrollTop() + $(window).height();
   maxTop = topD;
   windowTop = $(document).scrollTop();
   if (maxBottom > windowBottom) {
      topD = windowBottom - curDisplayDiv.outerHeight() - 20;
   } else if (maxTop < windowTop) {
      topD = windowTop + 20;
   }
   
   if (cbPopup_KeepItemUp(curItemID)) {
      topD += 5;
      leftD += 5;
   }
   
   //SET THE POSITION
   curDisplayDiv.css('top', parseInt(topD)).css('left', parseInt(leftD));
}

//LOAD ITEM HTML
//When you hover over an item trigger
//show its preview div, and populate its
//html from the item_popup script
$(cbPopup_displayTrigger).hover(function (e) {
   var curItemID = $(this).attr('itemid');

   if (cbPopup_KeepItemUp(curItemID)) return;
   cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
}, function () {
   var curItemID = $(this).attr('itemid');

   if (cbPopup_KeepItemUp(curItemID)) return;
   //hover is over, if its displayed turn it off
   cbPopup_hideItem(curItemID);
});

//DO POPUP POSITION MAINTENANCE
//move the popup around with the mouse
$(cbPopup_displayTrigger).mousemove(function (e) {
   var curItemID = $(this).attr('itemid');
   
   if (cbPopup_KeepItemUp(curItemID)) return;          
   cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
});

//KEEP POPUP UP ON CLICK
//if the user clicks the item trigger
//make the div stay up until they click
//again
$(cbPopup_displayTrigger).click(function (e) {
   var curItemID = $(this).attr('itemid');
   
   if (cbPopup_KeepItemUp(curItemID)) {
      cbPopup_KeepItemUp(curItemID, false);
   } else {
      cbPopup_KeepItemUp(curItemID, true);
   }  
   cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
});