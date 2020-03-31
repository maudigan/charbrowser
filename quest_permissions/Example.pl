sub EVENT_SAY 
{ 

   if($text=~/Hail/) 
   { 
      quest::say("Welcome traveler! Is there anything I can do for you?"); 
      $client->Message(4,"If you would like to set the permissions on your character profile simply say [public] or [private]. If you would like the server to handle your privacy settings say [default]. To set your guild to private say [guildprivate], [guildpublic], or [guilddefault]."); 
   } 

   if($text=~/public/) 
   { 
      quest::setglobal("charbrowser_profile", 1, 0, "F"); 
      $client->Message(4,"Your profile is now set to public!"); 
   } 

   if($text=~/private/) 
   { 
      quest::setglobal("charbrowser_profile", 2, 0, "F"); 
      $client->Message(4,"Your profile is now set to private!"); 
   } 

   if($text=~/default/) 
   { 
      quest::setglobal("charbrowser_profile", 0, 0, "F"); 
      $client->Message(4,"Your profile is now set to the server default!"); 
   } 

   if($text=~/guildpublic/) 
   { 
      quest::setglobal("charbrowser_guild", 1, 0, "F"); 
      $client->Message(4,"Your GUILDS profile is now set to public!"); 
   } 

   if($text=~/guildprivate/) 
   { 
      quest::setglobal("charbrowser_guild", 2, 0, "F"); 
      $client->Message(4,"Your GUILDS profile is now set to private!"); 
   } 

   if($text=~/guilddefault/) 
   { 
      quest::setglobal("charbrowser_guild", 0, 0, "F"); 
      $client->Message(4,"Your GUILDS profile is now set to the server default!"); 
   } 

}