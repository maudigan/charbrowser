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
	***************************************************************************/	
	
	if (!defined('INCHARBROWSER'))
		die("Hacking attempt");
	
	include_once(__DIR__ . "/item.php");
	
	// Item Class
	class item { 
		var $mytype; // (1 = Equipment Slots, 2 = Main Inventory Slots, 3 = Bank Slots)
		var $myicon; // Item Icon		
		var $myslot; // Item Slot
		var $myid; // Item ID		
		var $myhtml; // Item HTML
		var $myname; // Item Name		
		var $myslotcount; // Item Slots (0 = Item, 1-10 = Bags)		
		var $myvslot; // Bag Slot (1-10)		
		var $myaugshtml = array();
		var $myaugsname = array();
		var $myaugsid = array();
		var $myaugsicon = array();
		var $myaugcount = 0;
		
		// Augment Arrays		
		public function addaug($row) {
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
			$this->myicon = $row['icon'];
			$this->myname = $row['Name'];
			$this->myid = $row['id'];
			switch (true) {
				case ($this->myslot >= 0 && $this->myslot <= 22):
					$this->mytype  = 1;
					$this->myvslot = $this->myslot;
					break;
				case ($this->myslot >= 23 && $this->myslot <= 32):
					$this->mytype  = 2;
					$this->myvslot = $this->myslot;
					break;
				case ($this->myslot >= 262 && $this->myslot <= 271):
					$this->mytype  = 23;
					$this->myvslot = ($this->myslot - 261);
					break;
				case ($this->myslot >= 272 && $this->myslot <= 281):
					$this->mytype  = 24;
					$this->myvslot = ($this->myslot - 271);
					break;
				case ($this->myslot >= 282 && $this->myslot <= 291):
					$this->mytype  = 25;
					$this->myvslot = ($this->myslot - 281);
					break;
				case ($this->myslot >= 292 && $this->myslot <= 301):
					$this->mytype  = 26;
					$this->myvslot = ($this->myslot - 291);
					break;
				case ($this->myslot >= 302 && $this->myslot <= 311):
					$this->mytype  = 27;
					$this->myvslot = ($this->myslot - 301);
					break;
				case ($this->myslot >= 312 && $this->myslot <= 321):
					$this->mytype  = 28;
					$this->myvslot = ($this->myslot - 311);
					break;
				case ($this->myslot >= 322 && $this->myslot <= 331):
					$this->mytype  = 29;
					$this->myvslot = ($this->myslot - 321);
					break;
				case ($this->myslot >= 332 && $this->myslot <= 341):	
					$this->mytype  = 30;
					$this->myvslot = ($this->myslot - 331);
					break;
				case ($this->myslot >= 342 && $this->myslot <= 351):	
					$this->mytype  = 31;
					$this->myvslot = ($this->myslot - 341);
					break;
				case ($this->myslot >= 352 && $this->myslot <= 361):	
					$this->mytype  = 32;
					$this->myvslot = ($this->myslot - 351);
					break;
				case ($this->myslot >= 2000 && $this->myslot <= 2023):
					$this->mytype  = 3;
					$this->myvslot = $this->myslot;
					break;
				case ($this->myslot >= 2032 && $this->myslot <= 2041):
					$this->mytype  = 2000;
					$this->myvslot = ($this->myslot - 2031);
					break;
				case ($this->myslot >= 2042 && $this->myslot <= 2051):
					$this->mytype  = 2001;
					$this->myvslot = ($this->myslot - 2041);
					break;
				case ($this->myslot >= 2052 && $this->myslot <= 2061):
					$this->mytype  = 2002;
					$this->myvslot = ($this->myslot - 2051);
					break;
				case ($this->myslot >= 2062 && $this->myslot <= 2071):
					$this->mytype  = 2003;
					$this->myvslot = ($this->myslot - 2061);
					break;
				case ($this->myslot >= 2072 && $this->myslot <= 2081):
					$this->mytype  = 2004;
					$this->myvslot = ($this->myslot - 2071);
					break;
				case ($this->myslot >= 2082 && $this->myslot <= 2091):
					$this->mytype  = 2005;
					$this->myvslot = ($this->myslot - 2081);
					break;
				case ($this->myslot >= 2092 && $this->myslot <= 2101):
					$this->mytype  = 2006;
					$this->myvslot = ($this->myslot - 2091);
					break;
				case ($this->myslot >= 2102 && $this->myslot <= 2111):
					$this->mytype  = 2007;
					$this->myvslot = ($this->myslot - 2101);
					break;
				case ($this->myslot >= 2112 && $this->myslot <= 2121):
					$this->mytype  = 2008;
					$this->myvslot = ($this->myslot - 2111);
					break;
				case ($this->myslot >= 2122 && $this->myslot <= 2131):
					$this->mytype  = 2009;
					$this->myvslot = ($this->myslot - 2121);
					break;
				case ($this->myslot >= 2132 && $this->myslot <= 2141):
					$this->mytype  = 2010;
					$this->myvslot = ($this->myslot - 2131);
					break;
				case ($this->myslot >= 2142 && $this->myslot <= 2151):
					$this->mytype  = 2011;
					$this->myvslot = ($this->myslot - 2141);
					break;
				case ($this->myslot >= 2152 && $this->myslot <= 2161):
					$this->mytype  = 2012;
					$this->myvslot = ($this->myslot - 2151);
					break;
				case ($this->myslot >= 2162 && $this->myslot <= 2171):
					$this->mytype  = 2013;
					$this->myvslot = ($this->myslot - 2161);
					break;
				case ($this->myslot >= 2172 && $this->myslot <= 2181):
					$this->mytype  = 2014;
					$this->myvslot = ($this->myslot - 2171);
					break;
				case ($this->myslot >= 2182 && $this->myslot <= 2191):
					$this->mytype  = 2015;
					$this->myvslot = ($this->myslot - 2181);
					break;
				case ($this->myslot >= 2192 && $this->myslot <= 2201):
					$this->mytype  = 2016;
					$this->myvslot = ($this->myslot - 2191);
					break;
				case ($this->myslot >= 2202 && $this->myslot <= 2211):
					$this->mytype  = 2017;
					$this->myvslot = ($this->myslot - 2201);
					break;
				case ($this->myslot >= 2212 && $this->myslot <= 2221):
					$this->mytype  = 2018;
					$this->myvslot = ($this->myslot - 2211);
					break;
				case ($this->myslot >= 2222 && $this->myslot <= 2231):
					$this->mytype  = 2019;
					$this->myvslot = ($this->myslot - 2221);
					break;
				case ($this->myslot >= 2232 && $this->myslot <= 2241):
					$this->mytype  = 2020;
					$this->myvslot = ($this->myslot - 2231);
					break;
				case ($this->myslot >= 2242 && $this->myslot <= 2251):
					$this->mytype  = 2021;
					$this->myvslot = ($this->myslot - 2241);
					break;
				case ($this->myslot >= 2252 && $this->myslot <= 2261):
					$this->mytype  = 2022;
					$this->myvslot = ($this->myslot - 2251);
					break;
				case ($this->myslot >= 2262 && $this->myslot <= 2271):
					$this->mytype  = 2023;
					$this->myvslot = ($this->myslot - 2261);
					break;
				default:
					$this->mytype  = 0;
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
		
		function html() {
			return $this->myhtml;
		}
		
		function name() {
			return $this->myname;
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
?>