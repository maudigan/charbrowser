<?php 
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
 *   March 14, 2011 - Updated for Bank slots 17-24 
 *   February 5, 2014 - Updated for Powersource (Maudigan c/o Natedog)
 *   February 9, 2014 - Fixed charm item type getting set wrong (Maudigan)
 *   November 10,2018 - Added uleats inventory changes (Maudigan): 
 *                         Ammo moved 21 to 22
 *                         Power source moved 9999 to 21 
 *                         Cursor moves 30 to 33
 *                         Bags moved 22-29 to 23-32 (+1 and two new slots)
 *                         Cursor bag contents moved 331-340 to 351-360
 *                         New bags contents are in 331-340 and 341-350
 *   March 8, 2020 - implement shared bank (Maudigan)
 *   March 21, 2020 - track item skill (Maudigan)
 *   April 2, 2020 - Maudigan
 *     show stack size code
 *     cleaned up commented out code
 *   April 4, 2020 - Maudigan
 *     cap bag slots to 10
 *   April 6, 2020 - Maudigan
 *     made bag cap a constant
 *   July 28, 2020 - Maudigan
 *     cached the items db row in constructor
 *     added a function to get a column value from the db row
 *   January 16, 2023 - Maudigan
 *      added _ prefix to private properties
 *
 ***************************************************************************/ 
  
  
  
  
if ( !defined('INCHARBROWSER') ) 
{ 
        die("Hacking attempt"); 
} 

Include_once(__DIR__ . "/item.php"); 


// holds all the items 
class Charbrowser_Item 
{ 
   //1: Equipment 
   //2: the 8 inventory slots 
   //3: the 16 bank slots 
   //4: the 2 shared bank slots 
   //other: bag contents, i.e. 22 would be an item in bag 22
   private $_mytype;  

   //item icon for this item 
   private $_myicon; 

   //actual slot in the inventory table 
   private $_myslot; 

   //how many items in the stack
   private $_mystack; 

   //id of the item for linking 
   private $_myid; 

   //html text for displaying the item 
   private $_myhtml; 

   //name to use for display later 
   private $_myname; 

   //the weapon skill for this item
   private $_myskill; 

   //0 for items, 1-10 for bags 
   private $_myslotcount; 

   //if it goes in a bag, this is 1-10 for which bag slot 
   private $_myvslot; 

   //cache the items db row
   private $_myrow;

   //augment arrays 
   private $_myaugshtml = array(); 
   private $_myaugsname = array(); 
   private $_myaugsid = array(); 
   private $_myaugsicon = array(); 
   private $_myaugcount = 0; 


   public function addaug($row){ 
      $this->_myaugshtml[] = GetItem($row); 
      $this->_myaugsname[] = $row['Name']; 
      $this->_myaugsid[] = $row['id']; 
      $this->_myaugsicon[] = $row['icon']; 
      $this->_myaugcount++;          
   } 

   public function __construct($row) { 
      $this->_myrow = $row;
      $this->_myhtml = GetItem($row); 
      $this->_myslotcount = $row['bagslots']; 
      $this->_myicon=$row['icon']; 
      $this->_myname=$row['Name']; 
      $this->_myid=$row['id']; 
      $this->_myskill=$row["itemtype"];

      //equiped in a slot?
      if (array_key_exists('myslot', $row))
      {
         $this->_myslot = $row['myslot']; 
      }
      else
      {
         $this->_myslot = false;
      }

      //stackable?
      if ($row['stackable'] && array_key_exists('charges', $row)) {
         $this->_mystack = $row['charges'];
      }
      else {
         $this->_mystack = "";
      }


      //determine the type for this item, types define what part of the inventory
      //it will be placed, 
      //1 is equiped items,
      //2 is the top level inventory items, like your bags (but not their contents)
      //3 is top level items in the bank, like bags (but not their contents)
      //4 is top level item in the shared bank, like bags (but not their contents)
      //5 is the item on the cursor
      //other items that are inside of bags will have their type set to the slot
      //of their containing bag. For example, the bag in slot 23 is in the general
      //inventory so it has a type of 2 which is top level inventory items. Inside of
      //that bag are slots 251 to 260. If an item was in 251 it would have a type
      //of 23, which is its parents slot. 
      //in short, all items types are 1-4 if they aren't in a bag, and if they are
      //in a bag, their type is their parents slot #.
      switch (true)
      { 
         case ($this->_myslot >= 0 && $this->_myslot <= 22):
            $this->_mytype = 1; 
            $this->_myvslot = $this->_myslot; 
            break; 
         case ($this->_myslot >= 23 && $this->_myslot <= 32): 
            $this->_mytype = 2; 
            $this->_myvslot = $this->_myslot; 
            break; 
         case ($this->_myslot >= 2000 && $this->_myslot <= 2023): 
            $this->_mytype = 3; 
            $this->_myvslot = $this->_myslot; 
            break; 
         case ($this->_myslot >= 2500 && $this->_myslot <= 2501): 
            $this->_mytype = 4; 
            $this->_myvslot = $this->_myslot; 
            break; 
         case ($this->_myslot == 33): 
            $this->_mytype = 5; 
            $this->_myvslot = $this->_myslot; 
            break; 
            
         //SMALL BAGS inventory bag slots
         case ($this->_myslot >= 251 && $this->_myslot <= 260): 
            $this->_mytype = 23; 
            $this->_myvslot = $this->_myslot - 250; 
            break; 
         case ($this->_myslot >= 261 && $this->_myslot <= 270): 
            $this->_mytype = 24; 
            $this->_myvslot = $this->_myslot - 260; 
            break; 
         case ($this->_myslot >= 271 && $this->_myslot <= 280): 
            $this->_mytype = 25; 
            $this->_myvslot = $this->_myslot - 270; 
            break; 
         case ($this->_myslot >= 281 && $this->_myslot <= 290): 
            $this->_mytype = 26; 
            $this->_myvslot = $this->_myslot - 280; 
            break; 
         case ($this->_myslot >= 291 && $this->_myslot <= 300): 
            $this->_mytype = 27; 
            $this->_myvslot = $this->_myslot - 290; 
            break; 
         case ($this->_myslot >= 301 && $this->_myslot <= 310): 
            $this->_mytype = 28; 
            $this->_myvslot = $this->_myslot - 300; 
            break; 
         case ($this->_myslot >= 311 && $this->_myslot <= 320): 
            $this->_mytype = 29; 
            $this->_myvslot = $this->_myslot - 310; 
            break; 
         case ($this->_myslot >= 321 && $this->_myslot <= 330): 
            $this->_mytype = 30; 
            $this->_myvslot = $this->_myslot - 320; 
            break; 
         case ($this->_myslot >= 331 && $this->_myslot <= 340): 
            $this->_mytype = 31; 
            $this->_myvslot = $this->_myslot - 330; 
            break; 
         case ($this->_myslot >= 341 && $this->_myslot <= 350): 
            $this->_mytype = 32; 
            $this->_myvslot = $this->_myslot - 340; 
            break; 
            
         //SMALL BAGS cursor bag slots
         case ($this->_myslot >= 351 && $this->_myslot <= 360): 
            $this->_mytype = 33; 
            $this->_myvslot = $this->_myslot - 350; 
            break; 
            
         //SMALL BAGS bank bag slots
         case ($this->_myslot >= 2031 && $this->_myslot <= 2040): 
            $this->_mytype = 2000; 
            $this->_myvslot = $this->_myslot - 2030; 
            break; 
         case ($this->_myslot >= 2041 && $this->_myslot <= 2050): 
            $this->_mytype = 2001; 
            $this->_myvslot = $this->_myslot - 2040; 
            break; 
         case ($this->_myslot >= 2051 && $this->_myslot <= 2060): 
            $this->_mytype = 2002; 
            $this->_myvslot = $this->_myslot - 2050; 
            break; 
         case ($this->_myslot >= 2061 && $this->_myslot <= 2070): 
            $this->_mytype = 2003; 
            $this->_myvslot = $this->_myslot - 2060; 
            break; 
         case ($this->_myslot >= 2071 && $this->_myslot <= 2080): 
            $this->_mytype = 2004; 
            $this->_myvslot = $this->_myslot - 2070; 
            break; 
         case ($this->_myslot >= 2081 && $this->_myslot <= 2090): 
            $this->_mytype = 2005; 
            $this->_myvslot = $this->_myslot - 2080; 
            break; 
         case ($this->_myslot >= 2091 && $this->_myslot <= 2100): 
            $this->_mytype = 2006; 
            $this->_myvslot = $this->_myslot - 2090; 
            break; 
         case ($this->_myslot >= 2101 && $this->_myslot <= 2110): 
            $this->_mytype = 2007; 
            $this->_myvslot = $this->_myslot - 2100; 
            break; 
         case ($this->_myslot >= 2111 && $this->_myslot <= 2120): 
            $this->_mytype = 2008; 
            $this->_myvslot = $this->_myslot - 2110; 
            break; 
         case ($this->_myslot >= 2121 && $this->_myslot <= 2130): 
            $this->_mytype = 2009; 
            $this->_myvslot = $this->_myslot - 2120; 
            break; 
         case ($this->_myslot >= 2131 && $this->_myslot <= 2140): 
            $this->_mytype = 2010; 
            $this->_myvslot = $this->_myslot - 2130; 
            break; 
         case ($this->_myslot >= 2141 && $this->_myslot <= 2150): 
            $this->_mytype = 2011; 
            $this->_myvslot = $this->_myslot - 2140; 
            break; 
         case ($this->_myslot >= 2151 && $this->_myslot <= 2160): 
            $this->_mytype = 2012; 
            $this->_myvslot = $this->_myslot - 2150; 
            break; 
         case ($this->_myslot >= 2161 && $this->_myslot <= 2170): 
            $this->_mytype = 2013; 
            $this->_myvslot = $this->_myslot - 2160; 
            break; 
         case ($this->_myslot >= 2171 && $this->_myslot <= 2180): 
            $this->_mytype = 2014; 
            $this->_myvslot = $this->_myslot - 2170; 
            break; 
         case ($this->_myslot >= 2181 && $this->_myslot <= 2190): 
            $this->_mytype = 2015; 
            $this->_myvslot = $this->_myslot - 2180; 
            break; 
         case ($this->_myslot >= 2191 && $this->_myslot <= 2200): 
            $this->_mytype = 2016; 
            $this->_myvslot = $this->_myslot - 2190; 
            break; 
         case ($this->_myslot >= 2201 && $this->_myslot <= 2210): 
            $this->_mytype = 2017; 
            $this->_myvslot = $this->_myslot - 2200; 
            break; 
         case ($this->_myslot >= 2211 && $this->_myslot <= 2220): 
            $this->_mytype = 2018; 
            $this->_myvslot = $this->_myslot - 2210; 
            break; 
         case ($this->_myslot >= 2221 && $this->_myslot <= 2230): 
            $this->_mytype = 2019; 
            $this->_myvslot = $this->_myslot - 2220; 
            break; 
         case ($this->_myslot >= 2231 && $this->_myslot <= 2240): 
            $this->_mytype = 2020; 
            $this->_myvslot = $this->_myslot - 2230; 
            break; 
         case ($this->_myslot >= 2241 && $this->_myslot <= 2250): 
            $this->_mytype = 2021; 
            $this->_myvslot = $this->_myslot - 2240; 
            break; 
         case ($this->_myslot >= 2251 && $this->_myslot <= 2260): 
            $this->_mytype = 2022; 
            $this->_myvslot = $this->_myslot - 2250; 
            break; 
         case ($this->_myslot >= 2261 && $this->_myslot <= 2270): 
            $this->_mytype = 2023; 
            $this->_myvslot = $this->_myslot - 2260; 
            break; 
            
         //SMALL BAGS shared bank bag slots
         case ($this->_myslot >= 2531 && $this->_myslot <= 2540): 
            $this->_mytype = 2500; 
            $this->_myvslot = $this->_myslot - 2530; 
            break; 
         case ($this->_myslot >= 2541 && $this->_myslot <= 2550): 
            $this->_mytype = 2501; 
            $this->_myvslot = $this->_myslot - 2540; 
            break;
            
         //BIG BAGS inventory bag slots
         case ($this->_myslot >= 4010 && $this->_myslot <= 4209): 
             $this->_mytype = 23; 
             $this->_myvslot = $this->_myslot - 4009; 
             break;
         case ($this->_myslot >= 4210 && $this->_myslot <= 4409): 
             $this->_mytype = 24; 
             $this->_myvslot = $this->_myslot - 4209; 
             break;
         case ($this->_myslot >= 4410 && $this->_myslot <= 4609): 
             $this->_mytype = 25; 
             $this->_myvslot = $this->_myslot - 4409; 
             break;
         case ($this->_myslot >= 4610 && $this->_myslot <= 4809): 
             $this->_mytype = 26; 
             $this->_myvslot = $this->_myslot - 4609; 
             break;
         case ($this->_myslot >= 4810 && $this->_myslot <= 5009): 
             $this->_mytype = 27; 
             $this->_myvslot = $this->_myslot - 4809; 
             break;
         case ($this->_myslot >= 5010 && $this->_myslot <= 5209): 
             $this->_mytype = 28; 
             $this->_myvslot = $this->_myslot - 5009; 
             break;
         case ($this->_myslot >= 5210 && $this->_myslot <= 5409): 
             $this->_mytype = 29; 
             $this->_myvslot = $this->_myslot - 5209; 
             break;
         case ($this->_myslot >= 5410 && $this->_myslot <= 5609): 
             $this->_mytype = 30; 
             $this->_myvslot = $this->_myslot - 5409; 
             break;
         case ($this->_myslot >= 5610 && $this->_myslot <= 5809): 
             $this->_mytype = 31; 
             $this->_myvslot = $this->_myslot - 5609; 
             break;
         case ($this->_myslot >= 5810 && $this->_myslot <= 6009): 
             $this->_mytype = 32; 
             $this->_myvslot = $this->_myslot - 5809; 
             break;
             
         //BIG BAGS cursor bag slots
         case ($this->_myslot >= 6010 && $this->_myslot <= 6209): 
             $this->_mytype = 33; 
             $this->_myvslot = $this->_myslot - 6009; 
             break;
             
         //BIG BAGS shared bank bag slots
         case ($this->_myslot >= 6210 && $this->_myslot <= 6409): 
             $this->_mytype = 2000; 
             $this->_myvslot = $this->_myslot - 6209; 
             break; 
         case ($this->_myslot >= 6410 && $this->_myslot <= 6609): 
             $this->_mytype = 2001; 
             $this->_myvslot = $this->_myslot - 6409; 
             break; 
         case ($this->_myslot >= 6610 && $this->_myslot <= 6809): 
             $this->_mytype = 2002; 
             $this->_myvslot = $this->_myslot - 6609; 
             break; 
         case ($this->_myslot >= 6810 && $this->_myslot <= 7009): 
             $this->_mytype = 2003; 
             $this->_myvslot = $this->_myslot - 6809; 
             break; 
         case ($this->_myslot >= 7010 && $this->_myslot <= 7209): 
             $this->_mytype = 2004; 
             $this->_myvslot = $this->_myslot - 7009; 
             break; 
         case ($this->_myslot >= 7210 && $this->_myslot <= 7409): 
             $this->_mytype = 2005; 
             $this->_myvslot = $this->_myslot - 7209; 
             break; 
         case ($this->_myslot >= 7410 && $this->_myslot <= 7609): 
             $this->_mytype = 2006; 
             $this->_myvslot = $this->_myslot - 7409; 
             break; 
         case ($this->_myslot >= 7610 && $this->_myslot <= 7809): 
             $this->_mytype = 2007; 
             $this->_myvslot = $this->_myslot - 7609; 
             break; 
         case ($this->_myslot >= 7810 && $this->_myslot <= 8009): 
             $this->_mytype = 2008; 
             $this->_myvslot = $this->_myslot - 7809; 
             break; 
         case ($this->_myslot >= 8010 && $this->_myslot <= 8209): 
             $this->_mytype = 2009; 
             $this->_myvslot = $this->_myslot - 8009; 
             break;
         case ($this->_myslot >= 8210 && $this->_myslot <= 8409): 
             $this->_mytype = 2010; 
             $this->_myvslot = $this->_myslot - 8209; 
             break;
         case ($this->_myslot >= 8410 && $this->_myslot <= 8609): 
             $this->_mytype = 2011; 
             $this->_myvslot = $this->_myslot - 8409; 
             break;
         case ($this->_myslot >= 8610 && $this->_myslot <= 8809): 
             $this->_mytype = 2012; 
             $this->_myvslot = $this->_myslot - 8609; 
             break;
         case ($this->_myslot >= 8810 && $this->_myslot <= 9009): 
             $this->_mytype = 2013; 
             $this->_myvslot = $this->_myslot - 8809; 
             break;
         case ($this->_myslot >= 9010 && $this->_myslot <= 9209): 
             $this->_mytype = 2014; 
             $this->_myvslot = $this->_myslot - 9009; 
             break;
         case ($this->_myslot >= 9210 && $this->_myslot <= 9409): 
             $this->_mytype = 2015; 
             $this->_myvslot = $this->_myslot - 9209; 
             break;
         case ($this->_myslot >= 9410 && $this->_myslot <= 9609): 
             $this->_mytype = 2016; 
             $this->_myvslot = $this->_myslot - 9409; 
             break;
         case ($this->_myslot >= 9610 && $this->_myslot <= 9809): 
             $this->_mytype = 2017; 
             $this->_myvslot = $this->_myslot - 9609; 
             break;
         case ($this->_myslot >= 9810 && $this->_myslot <= 10009): 
             $this->_mytype = 2018; 
             $this->_myvslot = $this->_myslot - 9809; 
             break;
         case ($this->_myslot >= 10010 && $this->_myslot <= 10209): 
             $this->_mytype = 2019; 
             $this->_myvslot = $this->_myslot - 10009; 
             break;
         case ($this->_myslot >= 10210 && $this->_myslot <= 10409): 
             $this->_mytype = 2020; 
             $this->_myvslot = $this->_myslot - 10209; 
             break;
         case ($this->_myslot >= 10410 && $this->_myslot <= 10609): 
             $this->_mytype = 2021; 
             $this->_myvslot = $this->_myslot - 10409; 
             break;
         case ($this->_myslot >= 10610 && $this->_myslot <= 10809): 
             $this->_mytype = 2022; 
             $this->_myvslot = $this->_myslot - 10609; 
             break;
         case ($this->_myslot >= 10810 && $this->_myslot <= 11009): 
             $this->_mytype = 2023; 
             $this->_myvslot = $this->_myslot - 10809; 
             break;
             
         //BIG BAGS shared bank bag slots
         case ($this->_myslot >= 11010 && $this->_myslot <= 11209): 
             $this->_mytype = 2500; 
             $this->_myvslot = $this->_myslot - 11009; 
             break;
         case ($this->_myslot >= 11210 && $this->_myslot <= 11409): 
             $this->_mytype = 2501; 
             $this->_myvslot = $this->_myslot - 11209; 
             break;
    
         default: 
            $this->_mytype = 0; 
            $this->_myvslot = 0; 
            break; 
      } 
   } 


   function fetchColumn($col) { 
      return $this->_myrow[$col]; 
   } 

   function aughtml($key) { 
      return $this->_myaugshtml[$key]; 
   } 

   function augname($key) { 
      return $this->_myaugsname[$key]; 
   } 

   function augid($key) { 
      return $this->_myaugsid[$key]; 
   } 

   function augicon($key) { 
      return $this->_myaugsicon[$key]; 
   } 

   function augcount() { 
      return $this->_myaugcount; 
   } 

   function icon() { 
      return $this->_myicon; 
   } 

   function id() { 
      return $this->_myid; 
   } 

   function slot() { 
      return $this->_myslot; 
   } 

   function stack() { 
      return $this->_mystack; 
   } 

   function html() { 
      return $this->_myhtml; 
   } 

   function name() { 
      return $this->_myname; 
   } 

   function skill() { 
      return $this->_myskill; 
   } 

   function slotcount() { 
   //cap the bag at whatever the max slot count is
      return min(MAX_BAG_SLOTS, $this->_myslotcount); 
   } 

   function type() { 
      return $this->_mytype; 
   } 

   function vslot() { 
      return $this->_myvslot; 
   } 

   function setvslot($setval) { 
      $this->_myvslot = $setval; 
   } 

   function seticon($setval) { 
      $this->_myicon = $setval; 
   } 

   function setslot($setval) { 
      $this->_myslot = $setval; 
   } 

   //pass item id, pulls html from getitem function 
   function sethtml($setval) { 
      $this->_myhtml = GetItem($setval); 
   } 

   function setname($setval) { 
      $this->_myname = $setval; 
   } 

   function setslotcount($setval) { 
      $this->_myslotcount = $setval; 
   } 

   function settype($setval) { 
      $this->_mytype = $setval; 
   } 

   function setid($setval) { 
      $this->_myid = $setval; 
   } 
  
}


?>