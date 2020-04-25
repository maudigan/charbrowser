<div class='WindowComplex PositionBots CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_BOTS} - {NAME}</div>
   <!-- BEGIN bots -->
   <a class="CB_Bot_Avatar_Window" href='{INDEX_URL}?page=bot&bot={bots.NAME}'>
      <div class='CB_Avatar_Layer1'></div>
      <div class='CB_Avatar_Layer2' style='background-image: url({ROOT_URL}images/faces/{bots.AVATAR_IMG});'></div>
      <div class='CB_Avatar_Layer3'></div>
      <div class='CB_Bot_Caption'>
         <p>{bots.NAME}</p>
         <p>{bots.LEVEL} {bots.RACE} {bots.CLASS}</p>
      </div>
   </a>
   <!-- END bots -->
   <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>
