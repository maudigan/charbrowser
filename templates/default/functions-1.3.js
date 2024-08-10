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
 *   April 3, 2020 - Add ZOrder handling to anything with the
 *                   CB_Should_ZOrder class (Maudigan)
 *   April 6, 2020 - make bags and item windows resizable (Maudigan)
 *   October 19, 2022 - make profile menu resizeable (Maudigan)
 *
 ***************************************************************************/


//display a new tab in a window
function CB_displayTab(alltabs, tabid, alltabboxes, tabboxid) {
  //close all display tables
  $(alltabboxes).hide();

  //open requested one
  $(tabboxid).show();

  //push down all tabs
  $(alltabs).removeClass('CB_Tab_Open');

  //pop the clicked tab
  $(tabid).addClass('CB_Tab_Open');
}

//bookmark the current page
function cb_BookmarkThisPage() {
  if (window.sidebar && window.sidebar.addPanel) { // Mozilla Firefox Bookmark
    window.sidebar.addPanel(document.title, window.location.href, '');
  } else if (window.external && ('AddFavorite' in window.external)) { // IE Favorite
    window.external.AddFavorite(location.href, document.title);
  } else if (window.opera && window.print) { // Opera Hotlist
    this.title = document.title;
    return true;
  } else { // webkit - safari/chrome
    alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL') + ' + D to bookmark this page.');
  }
}

//go back one page in history
function cb_GoBackOnePage() {
  history.go(-1);
  return true;
}


//popup global vars
var cbPopup_moveLeft          = 5;
var cbPopup_moveDown          = -1;
var cbPopup_displayTrigger    = '#charbrowser .CB_HoverParent';
var cbPopup_displayContainers = '#charbrowser DIV.CB_Item_Open';
var cbPopup_curZIndex         = 100000;
var cbPopup_gapBetweenTiled   = 13;

//LOCK ITEM ON SCREEN AND/OR CHECK ITS STATUS
function cbPopup_KeepItemUp(curItemID, keepItemUp = 'check') {
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
      curDisplayDiv.addClass('CB_Item_Open');
      return true;
    } else {
      //turn it off
      curDisplayDiv.attr('keepItemUp', 0);
      curDisplayDiv.removeClass('CB_Item_Open');
      return false;
    }
  }
  return false;
}


//CLICK CONTENT
//move it to the top of the stack when interacted with
function cbPopup_ZOrder(that) {
  $(that).css('z-index', cbPopup_curZIndex++);
}

$('#charbrowser .CB_Should_ZOrder').mousedown(function () {
  cbPopup_ZOrder(this);
});


//TILE CONTENT
//loop through every popup and tile them on the screen
var cbPopup_curLeft      = 0;
var cbPopup_maxRowHeight = 0;
var cbPopup_curTop       = 0;

function cbPopup_tileItems() {
  cbPopup_curLeft = cbPopup_gapBetweenTiled;
  cbPopup_curTop  = $(document).scrollTop() + cbPopup_gapBetweenTiled;

  $(cbPopup_displayContainers).each(function (index) {
    attr = $(this).attr('id');

    //skip if it has no id
    if (typeof attr === typeof undefined || attr === false) {
      return;
    }

    var curItemID = '#' + attr;

    var curDisplayDiv = $(this);

    //where will the right edge land
    var curRight = cbPopup_curLeft + curDisplayDiv.outerWidth();

    //if the right edge will fall off the screen, jump to the next row
    if (curRight >= $(window).width()) {
      cbPopup_curLeft      = cbPopup_gapBetweenTiled;
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
  $(cbPopup_displayContainers).each(function (index) {
    attr = $(this).attr('id');

    //skip if it has no id
    if (typeof attr === typeof undefined || attr === false) {
      return;
    }

    var curItemID = '#' + attr;

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
  var curDisplayDiv        = 0;
  var curDisplayDivContent = 0;
  var curDisplayDivHeader  = 0;

  //item already exists, grab our pointer to it
  curDisplayDiv = $(curItemID);

  //display the div
  curDisplayDiv.css('z-index', cbPopup_curZIndex++);
  curDisplayDiv.show();
  curDisplayDiv.draggable();

  //CALCULATE POPUP POSITION
  //place it slightly to the left of the mouse
  leftD       = X + cbPopup_moveLeft;
  maxRight    = leftD + curDisplayDiv.outerWidth();
  windowLeft  = $(window).width() - 40;
  windowRight = 0;
  maxLeft     = X - (cbPopup_moveLeft + curDisplayDiv.outerWidth() + 20);

  if (maxRight > windowLeft && maxLeft > windowRight) {
    leftD = maxLeft;
  }

  topD         = Y - cbPopup_moveDown;
  maxBottom    = Y + cbPopup_moveDown + curDisplayDiv.outerHeight() + 50;
  windowBottom = $(document).scrollTop() + $(window).height();
  maxTop       = topD;
  windowTop    = $(document).scrollTop();
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
  var curItemID = $(this).attr('hoverChild');

  if (cbPopup_KeepItemUp(curItemID)) return;
  cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
}, function () {
  var curItemID = $(this).attr('hoverChild');

  if (cbPopup_KeepItemUp(curItemID)) return;
  //hover is over, if its displayed turn it off
  cbPopup_hideItem(curItemID);
});

//DO POPUP POSITION MAINTENANCE
//move the popup around with the mouse
$(cbPopup_displayTrigger).mousemove(function (e) {
  var curItemID = $(this).attr('hoverChild');

  if (cbPopup_KeepItemUp(curItemID)) return;
  cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
});

//KEEP POPUP UP ON CLICK
//if the user clicks the item trigger
//make the div stay up until they click
//again
$(cbPopup_displayTrigger).click(function (e) {
  var curItemID = $(this).attr('hoverChild');

  if (cbPopup_KeepItemUp(curItemID)) {
    cbPopup_KeepItemUp(curItemID, false);
  } else {
    cbPopup_KeepItemUp(curItemID, true);
  }
  cbPopup_maintainItem(curItemID, e.pageX, e.pageY);
});


//make all our windows drag

/**
 * Delay window drag initialization to prevent from
 * blocking initial page render
 */
setTimeout(function () {
  console.log('Rendering draggables / resizables');
  var startTime, endTime;
  startTime = performance.now();

  $('#charbrowser .CB_Can_Drag').draggable();

  console.log("Draggables rendered in [%s] seconds", (performance.now() - startTime) / 1000);

  //loop through every resize element
  //and make it resize and have a minimum
  //slots take up 42x42 (including padding)
  var buttonBottomPad     = 0;
  var buttonSlotDimension = 42;
  $('#charbrowser .PositionMenu').resizable({
    minHeight: buttonSlotDimension + buttonBottomPad,
    minWidth: buttonSlotDimension * 2,
    resize: function (e, ui) {
      var buttonCount = $(this).attr('buttoncount');
      
      if ($(this).data('ui-resizable').axis == 's') {
        var rows      = Math.floor((ui.size.height - buttonBottomPad) / buttonSlotDimension);
        var cols      = Math.ceil(buttonCount / rows);
        var maxHeight = buttonCount * buttonSlotDimension + buttonBottomPad;
        var minWidth  = cols * buttonSlotDimension;
        $('#area').html(maxHeight);
        $('#test').html(ui.size.height);
        if (ui.size.height > maxHeight) {
          ui.size.height = maxHeight;
        }
        ui.size.width = minWidth;
      } else {
        var cols      = Math.floor(ui.size.width / buttonSlotDimension);
        var rows      = Math.ceil(buttonCount / cols);
        var minHeight = rows * buttonSlotDimension + buttonBottomPad;
        var maxWidth  = buttonCount * buttonSlotDimension;
        if (ui.size.width > maxWidth) {
          ui.size.width = maxWidth;
        }
        ui.size.height = minHeight;
      }
    }
  });

  console.log("PositionMenu Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);

  //loop through every item stat window
  //and make it resize with a minimum of its
  //starting size
  /*
  $( "#charbrowser .PositionItem" ).each(function( index ) {
    var tempMinWidth = $( this ).width();
    var tempMinHeight = $( this ).height();
     $( this ).resizable({
        minHeight: tempMinHeight,
        minWidth: tempMinWidth
     });
  });*/
  $('#charbrowser .PositionItem').resizable({
    minHeight: 100,
    minWidth: 350,
    resize: function (e, ui) {
      //resize the contents div inside the window so the scroll bar appears
      $(this).children('#charbrowser .Stats').height(ui.size.height)
    }
  });
  
  console.log("PositionItem Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);


  //loop through every resize element
  //and make it resize and have a minimum
  //slots take up 42x42 (including padding)
  var buffBottomPad     = 0;
  var buffSlotDimension = 42;
  $('#charbrowser .PositionBuffs').resizable({
    minHeight: buffSlotDimension + buffBottomPad,
    minWidth: buffSlotDimension * 2,
    resize: function (e, ui) {
      var buffCount = $(this).attr('buffcount');
      
      if ($(this).data('ui-resizable').axis == 's') {
        var rows      = Math.floor((ui.size.height - buffBottomPad) / buffSlotDimension);
        var cols      = Math.ceil(buffCount / rows);
        var maxHeight = buffCount * buffSlotDimension + buffBottomPad;
        var minWidth  = cols * buffSlotDimension;
        $('#area').html(maxHeight);
        $('#test').html(ui.size.height);
        if (ui.size.height > maxHeight) {
          ui.size.height = maxHeight;
        }
        ui.size.width = minWidth;
      } else {
        var cols      = Math.floor(ui.size.width / buffSlotDimension);
        var rows      = Math.ceil(buffCount / cols);
        var minHeight = rows * buffSlotDimension + buffBottomPad;
        var maxWidth  = buffCount * buffSlotDimension;
        if (ui.size.width > maxWidth) {
          ui.size.width = maxWidth;
        }
        ui.size.height = minHeight;
      }
    }
  });
  console.log("PositionBuff Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);
  
  $('#charbrowser .PositionAdventure').resizable({
    minHeight: 498,
    maxWidth: 550,
    minWidth: 500,
    resize: function (e, ui) {
      //only resize vertically
      //ui.size.width = maxWidth
    }
  });

  console.log("PositionAdventure Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);
  
  $('#charbrowser .PositionBarter').resizable({
    minHeight: 500,
    minWidth: 850,
    resize: function (e, ui) {
      //only resize vertically
      //ui.size.width = maxWidth
    }
  });

  console.log("PositionBarter Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);
  
  $('#charbrowser .PositionBazaar').resizable({
    minHeight: 560,
    minWidth: 825,
    resize: function (e, ui) {
      //only resize vertically
      //ui.size.width = maxWidth
    }
  });

  console.log("PositionBazaar Resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);
  
  //loop through every resize element
  //and make it resize and have a minimum
  //slots take up 42x42 (including padding)
  var bagBottomPad     = 40;
  var bagSlotDimension = 42;
  $('#charbrowser .PositionBag').resizable({
    //handles: 'e, s',
    minHeight: bagSlotDimension + bagBottomPad,//tempMinHeight,
    minWidth: bagSlotDimension * 2,//tempMinWidth
    resize: function (e, ui) {
      var slotCount = $(this).attr('slotcount');
      if ($(this).data('ui-resizable').axis == 's') {
        var rows      = Math.floor((ui.size.height - bagBottomPad) / bagSlotDimension);
        var cols      = Math.ceil(slotCount / rows);
        var maxHeight = slotCount * bagSlotDimension + bagBottomPad;
        var minWidth  = cols * bagSlotDimension;
        $('#area').html(maxHeight);
        $('#test').html(ui.size.height);
        if (ui.size.height > maxHeight) {
          ui.size.height = maxHeight;
        }
        ui.size.width = minWidth;
      } else {
        var cols      = Math.floor(ui.size.width / bagSlotDimension);
        var rows      = Math.ceil(slotCount / cols);
        var minHeight = rows * bagSlotDimension + bagBottomPad;
        var maxWidth  = slotCount * bagSlotDimension;
        if (ui.size.width > maxWidth) {
          ui.size.width = maxWidth;
        }
        ui.size.height = minHeight;
      }
    }
  });

  console.log("PositionBag Resizables in [%s] seconds", (performance.now() - startTime) / 1000);

  console.log("Total Draggables / resizables rendered in [%s] seconds", (performance.now() - startTime) / 1000);

}, 100)
