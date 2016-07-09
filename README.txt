Legal
-----------------------------------------------------------------
Copyright (C) 2009  Maudigan

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation; either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of  
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public 
License along with this program; if not, write to the 

Free Software Foundation, Inc.
51 Franklin Street, Fifth Floor
Boston, MA  02110-1301, USA.




Installation
-----------------------------------------------------------------
Copy all of the project files to the directory of your choosing. 
Edit the “/includes/config.php”  file with your database 
information as the minimum settings. Additional settings can be 
altered in the config file to hide portions of the software from 
your users. 










New Features
-----------------------------------------------------------------
Server Operators:

Configuration changes
Several changes have been made to the formatting of the 
configuration file.  

Support has been added for server that do not have FreeType 
installed. This support will impact the way you set up the 
“titlefont” variables in the config file. If you have FreeType 
installed the titlefont you choose to use can be found by 
referencing the “/fonts” directory. If you do NOT have FreeType 
you will need to choose a font from “/fontsold”. In addition you 
will NOT  be able to choose a font size.

Permissions
In previous versions particular pages could be blocked entirely 
and/or types of characters could be blocked entirely. These two 
concepts have been combined into a permissions matrix. 

Characters are lumped into 4 different user categories, ALL, 
ROLEPLAY, ANON, GM. Each following user category takes precedence
over the previous. An ANON, GM, will be of the user category GM. 
In the config file under each category of user there is a set of 
pages to allow or block for that user type. Placing a “1” will 
block the particular page for that user group. A “0” will allow 
that page.

Character Mover
This build includes a character mover with increased security and
function. To enable it set “$blockcharmove” to “0” in the config 
file. Available zones should be set under $charmovezones using 
the zones shortname as a key pointing to an array with a key for 
x,y, and z each pointing to a coordinate of where you want the 
characters to be place. For more help with setting character move
zones, or an explanation that makes sense visit the tech support 
forums.

Signatures
This has been included as a method to auto generate a signature 
for use with phpbb1-3. Phpbb currently will only generate an 
<img> tag for a supplied link if it ends with a png, jpg, gif, 
etc. This partially prevents the passing of GET variables, or 
using php generated images as they end with .php. 

To make this function an .htaccess file is included with this 
build. This file will receive an URL where the signatures 
parameters are passed as hyphen delimited subdirectories and the 
extention of the file is a .png. Htaccess will then rewrite this 
to the true location of .php ending signature file, and recode 
the parameters into the normal GET formatting.

Measures have been taken to make this .htaccess dynamic so it 
will function from the various random locations it will be 
installed in. To ensure that it is indeed working correctly 
navigate to the signature builder by clicking its link in the 
top right. If it is functioning correctly the image at the 
bottom of the page will say “Error you must specify a 
character.”, if it is NOT functioning you will see a red “X” or 
a broken image icon. If this is the case visit the tech support 
forums for help.

Some sections of this don’t include much artwork for creating the
signatures, but all the portions of the tool are generated based 
on what images are in the signature builder folders. So if you 
add a background it will automatically show up as available, just
make sure you take care and make them the same size, and that 
they are PNGs. Again, more help with this can be received in the 
tech support forums.








Users:

Signatures
You can now generate a signature using the Character Browser. 
This signature will be a dynamic image that will automatically 
scale with your character. There is an array of artwork that can 
be selected and combined to form the final image using the 
signature builder. Simply visit the signature builder by clicking
the link in the top right. From there type your characters name 
as a minimum.  You can select the fonts, font colors, drop 
shadows, sub fonts, stats, epics, backgrounds, alpha layers, 
borders, and background colors to create a unique feel. After 
selecting all your options hit the “create” button and a sample 
image will be generated. 

If this is to be used in a forum you can then copy the code from 
the “BBCode” box and paste it into your board account. If it is 
to be put into a webpage copy from the “HTML” box.


Character mover
You can put your login name, and character name in the first two 
boxes. Depending on your server you may also be able to select 
from multiple zones. If you wish to move more than 1 character 
click the “[add row]” link, and repeat for the next character. 
Once you are finished click “move”.

Once you have successfully moved your characters you can click 
the “Click here to add a bookmark for this move!”. This will 
generate a bookmark that will allow you to reactivate that move 
with one click of the bookmark in the future.



Profile Navigation
Some minor changes have been made to navigation. All buttons 
have been removed from the bottom of the inventory window and 
imbedded in a tool bar on the far left of the screen. This 
toolbar will remain up on all of the characters pages. 
Previously it was only viewable from the inventory. The toolbar 
has added 2 buttons. The “Move” button which will open the 
character mover for this character, and the “link” button which 
will create a bookmark to that profile. 

In relation to the toolbar changes the “bank” button has been 
completely removed, and the bank window will remain open now.











Credits
-----------------------------------------------------------------
This project has taken from the original Magelo Clone, EQEditor, 
Allakhazam Clone, phpBB, and other publicly licensed sources. 
Thanks to TheConquistador, Trevius, and Cavedude for helping with
the project.
