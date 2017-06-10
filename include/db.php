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
 *
 ***************************************************************************/
 
 
 
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}

include_once("language.php");
include_once("functions.php");
include_once("config.php");
include_once("template.php");


//------------------------------------
//          CONNECT TO DB
//------------------------------------
mysql_connect($host, $user, $pass) or message_die($language['MESSAGE_ERROR'], $language['MESSAGE_DB_NOCONNECT']);
mysql_select_db("$db") or message_die($language['MESSAGE_ERROR'], $language['MESSAGE_DB_NODB']);  



//holds data about queries
$dbp_performance = array();



//------------------------------------
//          FETCH QUERIES
// fetches data about the quries that
// have been run
//------------------------------------
function dbp_fetch_queries()
{
   global $dbp_performance;
   return $dbp_performance;
}



//------------------------------------
//          FETCH PARSED
// fetches query data parsed into the
// database_performance_body template
//------------------------------------
function dbp_fetch_parsed()
{
   global $dbp_performance;
   
   //create our own template class instance so we don't interfere with the global one
   $dbp_template = new Template("./templates");
   
   //load the template
   $dbp_template->set_filenames(array('database_performance' => 'database_performance_body.tpl'));
   
   //skip it if its empty
   if (is_array($dbp_performance))
   {
      //loop through each query
      foreach ($dbp_performance as $dbp_query)
      {
         //load the top level items into the array
         $dbp_template->assign_block_vars("query", array( 
            'QUERY' => $dbp_query['QUERY'],
            'TIME' => $dbp_query['TIME'])
         );
         
         //loop through the explanation and load its data into the array
         foreach ($dbp_query['EXPLANATIONS'] as $dbp_explanation)
         {
            $dbp_template->assign_block_vars("query.explanation", $dbp_explanation);
         }
      }
   
   }
   
   //grab the output
   $dbp_output = $dbp_template->pparse_str('database_performance');
      
   //cleanup
   $dbp_template->destroy;
   
   return $dbp_output;
}
   


//------------------------------------
//    MYSQL ESCAPE STRING WRAPPER
//------------------------------------
function cbsql_escape_string($string)
{
   return mysql_real_escape_string($string);
} 



//------------------------------------
//       MYSQL CLOSE WRAPPER
//------------------------------------
function cbsql_close($connection)
{
   return mysql_close($connection);
}



//------------------------------------
//       MYSQL QUERY WRAPPER
// has performance tracking option
// added. it's turned off and on
// in the config.php
//------------------------------------
function cbsql_query($query)
{
   global $dbp_performance;
   
   //is this a select?
   $select = !strncmp("SELECT", strtoupper(trim($query)), 6);
   
   //if we aren't monitoring database performance just 
   //return the query results, don't explain non-selects either
   if (!defined('DB_PERFORMANCE') || !$select) return mysql_query($query);
   
   
   
   //gather query performance stats
   //do the query with a timer
   timer_start('query');
   $return = mysql_query($query);
   $time = timer_stop('query');
   
   //report errors
   if (!$return)
   {
      message_die($language['MESSAGE_ERROR'], mysql_error());
   }
   
   //get an explanation of the query
   $result = mysql_query("EXPLAIN ".$query);
   
   //load the results into an array
   $explanations = array();
   while($row = mysql_fetch_array($result))
   { 
      $explanations[] = array( 
         'SELECT_TYPE' => $row['select_type'],
         'TABLE' => $row['table'],
         'TYPE' => $row['type'],
         'POSSIBLE_KEYS' => $row['possible_keys'],
         'KEY' => $row['key'],
         'KEY_LEN' => $row['key_len'],
         'REF' => $row['ref'],
         'ROWS' => $row['rows'],
         'EXTRA' => $row['Extra']
      );

   }
      
   //cache the performance
   $dbp_performance[] = array( 
      'QUERY' => $query,
      'TIME' => $time,
      'EXPLANATIONS' => $explanations
   );
   
   return $return;
}



//------------------------------------
//       MYSQL ERROR WRAPPER
//------------------------------------
function cbsql_error()
{
   return mysql_error();
}



//------------------------------------
//       MYSQL NUM ROWS WRAPPER
//------------------------------------
function cbsql_rows($result)
{
   return mysql_num_rows($result);
}



//------------------------------------
//      MYSQL FETCH ROW WRAPPER
//------------------------------------
function cbsql_nextrow($result)
{
   return mysql_fetch_array($result);
}

?>