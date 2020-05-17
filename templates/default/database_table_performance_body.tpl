<!--------------------------------------------------->
<!--        DATABASE TABLES USED STATISTICS        -->
<!--------------------------------------------------->
<div class='db_dump'>
   <h1>{TYPE}</h1>
   <div class='db_query'>
      <table class='db_explain'>
         <caption>Tables Accessed:</caption>
         <thead>
            <tr>
               <th>Table</th>
               <th>Num Queries</th>
               <th>Rows Searched</th>
               <th>Rows Returned</th>
            </tr>
         </thead>
         <tbody>
         <!-- BEGIN tables -->
                  <tr>
                     <td>{tables.TABLE}</td>
                     <td>{tables.COUNT}</td>
                     <td>{tables.ROWSSEARCHED}</td>
                     <td>{tables.ROWSRETURNED}</td>
                  </tr>
         <!-- END tables -->
         </tbody>
      </table>
   </div>
</div>