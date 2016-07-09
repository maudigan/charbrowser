Changes have been made to the configuration setup for additional player privacy. Previously a player could manage their privacy using anon/roleplay. Now you can create an NPC that players can interact with to set their privacy level.

The config file has two new $permission types, PUBLIC and PRIVATE. These are activate d using the quest NPC. See example.pl on how to script the NPC.

When the player selects the private quest variable, the use the PRIVATE option. The public quest variable uses the PUBLIC option. The default quest variable makes the character browser fall back on the old system of managing privacy using anon/roleplay/gm.

For further information on how to setup quest NPCs visit the EQEmulator project home.
