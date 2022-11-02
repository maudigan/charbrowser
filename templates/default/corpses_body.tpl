<div class='WindowComplex PositionCorpses CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_CORPSES} - {NAME}</div>
   <!-- BEGIN corpses -->
   <a class='CB_Bot_Avatar_Window' href='{INDEX_URL}?page=corpse&corpse={corpses.CORPSE_ID}'>
      <div class='CB_Avatar_Layer1 {corpses.REZZED_STYLE}'></div>
      <div class='CB_Avatar_Layer2 {corpses.REZZED_STYLE}' style='background-image: url({ROOT_URL}images/faces/{corpses.AVATAR_IMG});'></div>
      <div class='CB_Avatar_Layer3 {corpses.REZZED_STYLE}' title='{corpses.AVATAR_TITLE}'></div>
      <div class='CB_Bot_Caption'>
         <p>{corpses.TOD_DATE}</p>
         <p>{corpses.ZONE_LONG_NAME}</p>
      </div>
      <div class='{corpses.HAS_ITEMS_CLASS}' title='{corpses.HAS_ITEMS_TITLE}'>
      </div>
   </a>
   <!-- END corpses -->
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
