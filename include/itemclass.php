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
 *
 ***************************************************************************/ 
  
  
  
  
 if ( !defined('INCHARBROWSER') ) 
{ 
        die("Hacking attempt"); 
} 
Include_once(__DIR__ . "/item.php"); 
// holds all the items 
class item { 
  
        // Variables 
        var $mytype; 
        //1: Equipment 
        //2: the 8 inventory slots 
        //3: the 16 bank slots 
        //4: the 2 shared bank slots 
        //other: bag contents, i.e. 22 would be an item in bag 22 
  
        var $myicon; 
        //item icon for this item 
  
        var $myslot; 
        //actual slot in the inventory table 
  
        var $mystack; 
        //how many items in the stack
        
        var $myid; 
        //id of the item for linking 
  
        var $myhtml; 
        //html text for displaying the item 
  
        var $myname; 
        //name to use for display later 
  
        var $myskill; 
        //the weapon skill for this item
  
        var $myslotcount; 
        //0 for items, 1-10 for bags 
  
        var $myvslot; 
        //if it goes in a bag, this is 1-10 for which bag slot 
        
        var $myaugshtml = array(); 
        var $myaugsname = array(); 
        var $myaugsid = array(); 
        var $myaugsicon = array(); 
        var $myaugcount = 0; 
        //augment arrays 


         public function addaug($row){ 
           $this->myaugshtml[] = GetItem($row); 
           $this->myaugsname[] = $row['Name']; 
           $this->myaugsid[] = $row['id']; 
           $this->myaugsicon[] = $row['icon']; 
           $this->myaugcount++;          
         } 

         public function item($row) { 
           $this->myslot = $row['myslot']; 
           $this->myhtml = GetItem($row); 
           $this->myslotcount = $row['bagslots']; 
           $this->myicon=$row['icon']; 
           $this->myname=$row['Name']; 
           $this->myid=$row['id']; 
           $this->myskill=$row["itemtype"];
           //stackable?
           if ($row['stackable']) {
               $this->mystack = $row['charges'];
           }
           else {
               $this->mystack = "";
           }
           
           
           switch (true){ 
                case ($this->myslot >= 0 && $this->myslot <= 22):
                     $this->mytype = 1; 
                     $this->myvslot = $this->myslot; 
                     break; 
                case ($this->myslot >= 23 && $this->myslot <= 32): 
                     $this->mytype = 2; 
                     $this->myvslot = $this->myslot; 
                     break; 
                case ($this->myslot >= 251 && $this->myslot <= 260): 
                     $this->mytype = 23; 
                     $this->myvslot = $this->myslot - 250; 
                     break; 
                case ($this->myslot >= 261 && $this->myslot <= 270): 
                     $this->mytype = 24; 
                     $this->myvslot = $this->myslot - 260; 
                     break; 
                case ($this->myslot >= 271 && $this->myslot <= 280): 
                     $this->mytype = 25; 
                     $this->myvslot = $this->myslot - 270; 
                     break; 
                case ($this->myslot >= 281 && $this->myslot <= 290): 
                     $this->mytype = 26; 
                     $this->myvslot = $this->myslot - 280; 
                     break; 
                case ($this->myslot >= 291 && $this->myslot <= 300): 
                     $this->mytype = 27; 
                     $this->myvslot = $this->myslot - 290; 
                     break; 
                case ($this->myslot >= 301 && $this->myslot <= 310): 
                     $this->mytype = 28; 
                     $this->myvslot = $this->myslot - 300; 
                     break; 
                case ($this->myslot >= 311 && $this->myslot <= 320): 
                     $this->mytype = 29; 
                     $this->myvslot = $this->myslot - 310; 
                     break; 
                case ($this->myslot >= 321 && $this->myslot <= 330): 
                     $this->mytype = 30; 
                     $this->myvslot = $this->myslot - 320; 
                     break; 
                case ($this->myslot >= 331 && $this->myslot <= 340): 
                     $this->mytype = 31; 
                     $this->myvslot = $this->myslot - 330; 
                     break; 
                case ($this->myslot >= 341 && $this->myslot <= 350): 
                     $this->mytype = 32; 
                     $this->myvslot = $this->myslot - 340; 
                     break; 
                case ($this->myslot >= 2000 && $this->myslot <= 2023): 
                     $this->mytype = 3; 
                     $this->myvslot = $this->myslot; 
                     break; 
                case ($this->myslot >= 2031 && $this->myslot <= 2040): 
                     $this->mytype = 2000; 
                     $this->myvslot = $this->myslot - 2030; 
                     break; 
                case ($this->myslot >= 2041 && $this->myslot <= 2050): 
                     $this->mytype = 2001; 
                     $this->myvslot = $this->myslot - 2040; 
                     break; 
                case ($this->myslot >= 2051 && $this->myslot <= 2060): 
                     $this->mytype = 2002; 
                     $this->myvslot = $this->myslot - 2050; 
                     break; 
                case ($this->myslot >= 2061 && $this->myslot <= 2070): 
                     $this->mytype = 2003; 
                     $this->myvslot = $this->myslot - 2060; 
                     break; 
                case ($this->myslot >= 2071 && $this->myslot <= 2080): 
                     $this->mytype = 2004; 
                     $this->myvslot = $this->myslot - 2070; 
                     break; 
                case ($this->myslot >= 2081 && $this->myslot <= 2090): 
                     $this->mytype = 2005; 
                     $this->myvslot = $this->myslot - 2080; 
                     break; 
                case ($this->myslot >= 2091 && $this->myslot <= 2100): 
                     $this->mytype = 2006; 
                     $this->myvslot = $this->myslot - 2090; 
                     break; 
                case ($this->myslot >= 2101 && $this->myslot <= 2110): 
                     $this->mytype = 2007; 
                     $this->myvslot = $this->myslot - 2100; 
                     break; 
                case ($this->myslot >= 2111 && $this->myslot <= 2120): 
                     $this->mytype = 2008; 
                     $this->myvslot = $this->myslot - 2110; 
                     break; 
                case ($this->myslot >= 2121 && $this->myslot <= 2130): 
                     $this->mytype = 2009; 
                     $this->myvslot = $this->myslot - 2120; 
                     break; 
                case ($this->myslot >= 2131 && $this->myslot <= 2140): 
                     $this->mytype = 2010; 
                     $this->myvslot = $this->myslot - 2130; 
                     break; 
                case ($this->myslot >= 2141 && $this->myslot <= 2150): 
                     $this->mytype = 2011; 
                     $this->myvslot = $this->myslot - 2140; 
                     break; 
                case ($this->myslot >= 2151 && $this->myslot <= 2160): 
                     $this->mytype = 2012; 
                     $this->myvslot = $this->myslot - 2150; 
                     break; 
                case ($this->myslot >= 2161 && $this->myslot <= 2170): 
                     $this->mytype = 2013; 
                     $this->myvslot = $this->myslot - 2160; 
                     break; 
                case ($this->myslot >= 2171 && $this->myslot <= 2180): 
                     $this->mytype = 2014; 
                     $this->myvslot = $this->myslot - 2170; 
                     break; 
                case ($this->myslot >= 2181 && $this->myslot <= 2190): 
                     $this->mytype = 2015; 
                     $this->myvslot = $this->myslot - 2180; 
                     break; 
                case ($this->myslot >= 2191 && $this->myslot <= 2200): 
                     $this->mytype = 2016; 
                     $this->myvslot = $this->myslot - 2190; 
                     break; 
                case ($this->myslot >= 2201 && $this->myslot <= 2210): 
                     $this->mytype = 2017; 
                     $this->myvslot = $this->myslot - 2200; 
                     break; 
                case ($this->myslot >= 2211 && $this->myslot <= 2220): 
                     $this->mytype = 2018; 
                     $this->myvslot = $this->myslot - 2210; 
                     break; 
                case ($this->myslot >= 2221 && $this->myslot <= 2230): 
                     $this->mytype = 2019; 
                     $this->myvslot = $this->myslot - 2220; 
                     break; 
                case ($this->myslot >= 2231 && $this->myslot <= 2240): 
                     $this->mytype = 2020; 
                     $this->myvslot = $this->myslot - 2230; 
                     break; 
                case ($this->myslot >= 2241 && $this->myslot <= 2250): 
                     $this->mytype = 2021; 
                     $this->myvslot = $this->myslot - 2240; 
                     break; 
                case ($this->myslot >= 2251 && $this->myslot <= 2260): 
                     $this->mytype = 2022; 
                     $this->myvslot = $this->myslot - 2250; 
                     break; 
                case ($this->myslot >= 2261 && $this->myslot <= 2270): 
                     $this->mytype = 2023; 
                     $this->myvslot = $this->myslot - 2260; 
                     break; 
                case ($this->myslot >= 2500 && $this->myslot <= 2501): 
                     $this->mytype = 4; 
                     $this->myvslot = $this->myslot; 
                     break; 
                case ($this->myslot >= 2531 && $this->myslot <= 2540): 
                     $this->mytype = 2500; 
                     $this->myvslot = $this->myslot - 2530; 
                     break; 
                case ($this->myslot >= 2541 && $this->myslot <= 2550): 
                     $this->mytype = 2501; 
                     $this->myvslot = $this->myslot - 2540; 
                     break; 
                default: 
                     $this->mytype = 0; 
                     $this->myvslot = 0; 
                     break; 
           } 
         } 

        
        function aughtml($key) { 
          return $this->myaugshtml[$key]; 
        } 
        
        function augname($key) { 
          return $this->myaugsname[$key]; 
        } 
        
        function augid($key) { 
          return $this->myaugsid[$key]; 
        } 
        
        function augicon($key) { 
          return $this->myaugsicon[$key]; 
        } 
        
        function augcount() { 
          return $this->myaugcount; 
        } 
        
        function icon() { 
         return $this->myicon; 
        } 
        
        function id() { 
         return $this->myid; 
        } 
  
        function slot() { 
         return $this->myslot; 
        } 
  
        function stack() { 
         return $this->mystack; 
        } 
         
        function html() { 
         return $this->myhtml; 
        } 
  
        function name() { 
         return $this->myname; 
        } 
  
        function skill() { 
         return $this->myskill; 
        } 
  
        function slotcount() { 
         return $this->myslotcount; 
        } 
  
        function type() { 
         return $this->mytype; 
        } 
  
        function vslot() { 
         return $this->myvslot; 
        } 
  
        function setvslot($setval) { 
         $this->myvslot = $setval; 
        } 
  
        function seticon($setval) { 
         $this->myicon = $setval; 
        } 
  
        function setslot($setval) { 
         $this->myslot = $setval; 
        } 
  
        //pass item id, pulls html from getitem function 
        function sethtml($setval) { 
         $this->myhtml = GetItem($setval); 
        } 
  
        function setname($setval) { 
         $this->myname = $setval; 
        } 
  
        function setslotcount($setval) { 
         $this->myslotcount = $setval; 
        } 

        function settype($setval) { 
         $this->mytype = $setval; 
        } 
  
        function setid($setval) { 
         $this->myid = $setval; 
        } 
  
} 

