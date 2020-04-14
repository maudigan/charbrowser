<div class='WindowComplex PositionCharMove CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_CHARACTER_MOVER}</div>
   <div class='WindowNestedBlue'>
      <table class='CB_Table CB_Highlight_Rows'>
         <thead> 
            <tr>                  
               <th>{L_LOGIN}</th>
               <th>{L_CHARNAME}</th>
               <th>{L_ZONE}</th>
               <th>{L_RESULT}</th>
            </tr>
         </thead>
         </tbody>
            <!-- BEGIN results -->
            <tr>
               <td>{results.LOGIN}</td>
               <td>{results.CHARACTER}</td>
               <td>{results.ZONE}</td>
               <td>{results.RESULT}</td>
            </tr>
            <!-- END results -->
         </tbody> 
      </table>
   </div>
   <a class='CB_CharmoveBookmark' href=# onClick='cb_BookmarkThisPage();'>{L_BOOKMARK}</a>
   <div class='CB_Button' onclick="cb_GoBackOnePage();">{L_BACK}</div>
</div>