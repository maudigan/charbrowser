Changes have been made to the configuration setup for additional player privacy. Previously a player could manage their privacy using anon/roleplay. Now you can create an NPC that players can interact with to set their privacy level. The guild leader can do the same thing for their guild page.

The config file has two new $permission types, PUBLIC and PRIVATE. These are activated using the quest NPC. See example.pl on how to script the NPC.

When the player selects the private quest variable, the use the PRIVATE option. The public quest variable uses the PUBLIC option. The default quest variable makes the character browser fall back on the old system of managing privacy using anon/roleplay/gm.

Character Quest Global: charbrowser_profile
when 0: default
when 1: public
when 2: private

Guild Quest Global (quest global tied to guild leader character): charbrowser_guild
when 0: default
when 1: public
when 2: private

For further information on how to setup quest NPCs visit the EQEmulator project home.
