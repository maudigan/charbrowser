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
 *   January 7, 2018 - Maudigan
 *      Database performance code wasn't running because the template 
 *      class name was wrong. 
 *   January 7, 2018 - Athrogate/Maudigan
 *      Athrogate sent some code for MySQLi conversion, I added to that
 *      to make it more object oriented. $cbsql is now an object that
 *      wraps around a MqSQLi object.
 *   April 14, 2020 - Maudigan
 *      Added a subdirectory to the template folder
 ***************************************************************************/


if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}


/*********************************************
                 INCLUDES
*********************************************/ 
include_once(__DIR__ . "/language.php");
include_once(__DIR__ . "/functions.php");
include_once(__DIR__ . "/config.php");
include_once(__DIR__ . "/template.php");


/*********************************************
           DATABASE WRAPPER CLASS
   all MySQL calls route through this class
*********************************************/ 
class Charbrowser_SQL
{
   //local database handle
   var $_mysql_handle;
   
   //holds performance data about queries
   var $_dbp_performance = array();   
   

   
   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct($host, $user, $pass, $database)
   {
      $this->_mysql_handle = New mysqli($host, $user, $pass, $database); 
   }

   
   
   //-------------------------------------
   //            DESTRUCTOR
   //-------------------------------------
   function __destruct()
   {
      $this->_mysql_handle->close();
   }
   
   
   
   //-------------------------------------
   //          CONNECT ERROR
   //-------------------------------------  
   function connect_error()
   {
      return $this->_mysql_handle->connect_error;
   }   

   
   
   //------------------------------------
   //          FETCH QUERIES
   // fetches performance data about the 
   // queries that have been run
   //------------------------------------
   function dbp_fetch_queries()
   {
      return $this->_dbp_performance;
   }



   //------------------------------------
   //          FETCH PARSED
   // fetches query data parsed into the
   // database_performance_body template
   //------------------------------------
   function dbp_fetch_parsed()
   {  
      //create our own template class instance so we don't interfere with the global one
      $dbp_template = new CB_Template(__DIR__ . "/../templates/" . $cb_override_template_dir, __DIR__ . "/../templates/default");
      
      //load the template
      $dbp_template->set_filenames(array('database_performance' => 'database_performance_body.tpl'));
      
      //skip it if its empty
      if (is_array($this->_dbp_performance))
      {
         //loop through each query
         foreach ($this->_dbp_performance as $dbp_query)
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
   function escape_string($string)
   {
      return $this->_mysql_handle->real_escape_string($string);
   } 



   //------------------------------------
   //       MYSQL QUERY WRAPPER
   // has performance tracking option
   // added. it's turned off and on
   // in the config.php
   //------------------------------------
   function query($query)
   {      
      //is this a select?
      $select = !strncmp("SELECT", strtoupper(trim($query)), 6);
      
      //if we aren't monitoring database performance just 
      //return the query results, don't explain non-selects either
      if (!defined('DB_PERFORMANCE') || !$select) return $this->_mysql_handle->query($query);
      
      
      
      //gather query performance stats
      //do the query with a timer
      timer_start('query');
      $return = $this->_mysql_handle->query($query);
      $time = timer_stop('query');
      
      //report errors
      if (!$return)
      {
         cb_message_die($language['MESSAGE_ERROR'], $this->_mysql_handle->error);
      }
      
      //get an explanation of the query
      $result = $this->_mysql_handle->query("EXPLAIN ".$query);
      
      //load the results into an array
      $explanations = array();
      while($row = $result->fetch_array())
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
      $this->_dbp_performance[] = array( 
         'QUERY' => $query,
         'TIME' => $time,
         'EXPLANATIONS' => $explanations
      );
      
      return $return;
   }



   //------------------------------------
   //       MYSQL ERROR WRAPPER
   //------------------------------------
   function error()
   {
      return $this->_mysql_handle->error();
   }



   //------------------------------------
   //       MYSQL NUM ROWS WRAPPER
   //------------------------------------
   function rows($result)
   {
      return $result->num_rows;
   } 



   //------------------------------------
   //      MYSQL FETCH ROW WRAPPER
   //------------------------------------
   function nextrow($result)
   {
      return $result->fetch_array();
   }
}


/*********************************************
      CREATE OUR DATABASE CLASS INSTANCE
*********************************************/ 
$cbsql = new Charbrowser_SQL($cb_host, $cb_user, $cb_pass, $cb_db);

if ($cbsql->connect_error()) cb_message_die($language['MESSAGE_ERROR'], $language['MESSAGE_DB_NOCONNECT']);
?>