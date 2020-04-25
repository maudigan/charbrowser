<!--------------------------------------------------->
<!--        DATABASE PERFORMANCE STATISTICS        -->
<!--------------------------------------------------->
<div class='db_dump'>
   <h1>{TYPE}</h1>
   <!-- BEGIN query -->
   <div class='db_query'>
      <div class='db_sql'>
      <h1>Query:</h1>
      <p>{query.QUERY}</p>
      </div>
      <div class='db_time'>
      <h1>Time:</h1>
      <p>{query.TIME}<p>
      </div>
      <table class='db_explain'>
         <caption>Explanation:</caption>
         <thead>
            <tr>
               <th>select_type</th>
               <th>table</th>
               <th>type</th>
               <th>possible_keys</th>
               <th>key</th>
               <th>key_len</th>
               <th>ref</th>
               <th>rows</th>
               <th>Extra</th>
            </tr>
         </thead>
         <tbody>
         <!-- BEGIN explanation -->
                  <tr>
                     <td>{query.explanation.SELECT_TYPE}</td>
                     <td>{query.explanation.TABLE}</td>
                     <td>{query.explanation.TYPE}</td>
                     <td>{query.explanation.POSSIBLE_KEYS}</td>
                     <td>{query.explanation.KEY}</td>
                     <td>{query.explanation.KEY_LEN}</td>
                     <td>{query.explanation.REF}</td>
                     <td>{query.explanation.ROWS}</td>
                     <td>{query.explanation.EXTRA}</td>
                  </tr>
         <!-- END explanation -->
         </tbody>
      </table>
   </div>
   <!-- END query -->
</div>