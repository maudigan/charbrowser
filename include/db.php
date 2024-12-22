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
 *   April 25, 2020 - Maudigan
 *      Added a port option to the constructor
 *      Added multi tenancy support
 *      Added a method to quickly query a single field val
 *      Added a datbase performance method to check which tables
 *        were queried with an instance
 *   May 3, 2020 - Maudigan
 *      fixed fetch_all to not return an extra blank row
 *      split up rows searched and rows returned for the db performance code
 *   January 16, 2023 - Maudigan
 *       added _ prefix to private properties
 *       changed constructor to fetch local referenecs to global objects
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
   private $_mysql_handle;

   //holds performance data about queries
   private $_dbp_performance = array();

   //holds data about which tables have been accessed
   private $_dbp_tables = array();
   

   
   //local references to external classes
   //imported using "global" in the constructor
   private $_error;
   private $_language;


   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct($host, $user, $pass, $database, $port)
   {
      
      global $cb_error;
      global $language;
      
      //make sure the error class exists, store pointer
      if (!isset($cb_error)) 
      {
         die("The Charbrowser_SQL class can't be initialized prior to the error class (error.php) being created.");
      }
      else
      {
         $this->_error = $cb_error;
      }
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_SQL class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //create the connection
      $this->_mysql_handle = New mysqli($host, $user, $pass, $database, $port);
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
   function dbp_fetch_parsed($type)
   {
      global $cb_override_template_dir;

      //create our own template class instance so we don't interfere with the global one
      $dbp_template = new Charbrowser_Template(__DIR__ . "/../templates/" . $cb_override_template_dir, __DIR__ . "/../templates/default");

      //load the template
      $dbp_template->set_filenames(array('database_performance' => 'database_performance_body.tpl'));

      //skip it if its empty
      if (cb_count($this->_dbp_performance))
      {
         //load the top level items into the array
         $dbp_template->assign_vars(array(
            'TYPE' => $type)
         );

         //loop through each query
         $tables = array();
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
      $dbp_template->destroy();

      return $dbp_output;
   }



   //------------------------------------
   //        FETCH PARSED TABLES
   // fetches query data parsed into the
   // database_performance_body template
   //------------------------------------
   function dbp_table_fetch_parsed($type)
   {
      global $cb_override_template_dir;
      
      if(!cb_count($this->_dbp_tables)) return;
      
      //create our own template class instance so we don't interfere with the global one
      $dbp_template = new Charbrowser_Template(__DIR__ . "/../templates/" . $cb_override_template_dir, __DIR__ . "/../templates/default");

      //load the template
      $dbp_template->set_filenames(array('database_table_performance' => 'database_table_performance_body.tpl'));


      //load the top level items into the array
      $dbp_template->assign_vars(array(
         'TYPE' => $type)
      );

      //loop through each query
      $tables = array();
      foreach ($this->_dbp_tables as $dbp_table)
      {
         //load the top level items into the array
         $dbp_template->assign_block_vars("tables", $dbp_table);
      }


      //grab the output
      $dbp_output = $dbp_template->pparse_str('database_table_performance');

      //cleanup
      $dbp_template->destroy();

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
      if (!defined('DEVELOPER_MODE') || !$select) return $this->_mysql_handle->query($query);



      //gather query performance stats
      //do the query with a timer
      timer_start('query');
      $return = $this->_mysql_handle->query($query);
      $time = timer_stop('query');
      $rowcount = $return->num_rows;

      //report errors
      if (!$return)
      {
         $this->_error->message_die($this->_language['MESSAGE_ERROR'], $this->_mysql_handle->error);
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

         //track how many queries were done on this table
         if (array_key_exists($row['table'], $this->_dbp_tables))
         {
            $this->_dbp_tables[$row['table']]['COUNT'] = $this->_dbp_tables[$row['table']]['COUNT'] + 1;
            $this->_dbp_tables[$row['table']]['ROWSSEARCHED'] = $this->_dbp_tables[$row['table']]['ROWSSEARCHED'] + $row['rows'];
            $this->_dbp_tables[$row['table']]['ROWSRETURNED'] = $this->_dbp_tables[$row['table']]['ROWSRETURNED'] + $rowcount;
         }
         else
         {
            $this->_dbp_tables[$row['table']] = array(
               'TABLE' => $row['table'],
               'COUNT' => 1,
               'ROWSSEARCHED' => $row['rows'],
               'ROWSRETURNED' => $rowcount
            );
         }

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
   //    MYSQL FIELD QUERY WRAPPER
   //------------------------------------
   function field_query($field, $query)
   {
      $result = $this->query($query);

      if ($row = $result->fetch_array())
      {
         return $row[$field];
      }

      return false;
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

   //------------------------------------
   //      MYSQL FETCH ALL WRAPPER
   //------------------------------------
   function fetch_all($result)
   {
      $rows = array();
      while ($row = $result->fetch_array()) {
         $rows[] = $row;
      }
      return $rows;
   }
}


/*********************************************
      CREATE OUR DATABASE CLASS INSTANCE(S)
*********************************************/
$cbsql = new Charbrowser_SQL($cb_host, $cb_user, $cb_pass, $cb_db, $cb_port);

if ($cbsql->connect_error()) $cb_error->message_die($language['MESSAGE_ERROR'], $language['MESSAGE_DB_NOCONNECT']);

//do we have a seperate content DB?
if ($cb_use_content_db)
{
   $cbsql_content = new Charbrowser_SQL($cb_content_host, $cb_content_user, $cb_content_pass, $cb_content_db, $cb_content_port);
   if ($cbsql_content->connect_error()) $cb_error->message_die($language['MESSAGE_ERROR'], $language['MESSAGE_DB_NOCONNECT']);
}
else
{
   $cbsql_content = $cbsql;
}
?>