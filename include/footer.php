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
 *   September 16, 2017 - added an optional simple footer.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy for db performance output
 *     output which tables were queried, and which connection it used for
 *       testing/developing
 *
 ***************************************************************************/
 
//dont make a header if there is an API request 
if (isset($_GET['api'])) return;
 
if ( !defined('INCHARBROWSER') )
{
	die("Hacking attempt");
}


//do we have a seperate content DB?
//if db performance is turned on fetch the output
if (defined('DB_PERFORMANCE')) 
{
   //TABLES USED
   //if we have a content connection dump 2 segments
   if ($cb_use_content_db) 
   {     
      if ($cbsql)
      {
         $dbp_table_output = $cbsql->dbp_table_fetch_parsed("Player Tables Queried");
      }      
      if ($cbsql_content)
      {
         $dbp_table_output_content = $cbsql_content->dbp_table_fetch_parsed("Content Tables Queried");
      }
   }
   
   //else cbsql houses all the queries
   else
   {
      if ($cbsql)
      {
         $dbp_table_output = $cbsql->dbp_table_fetch_parsed("Tables Queried");
      }
   }
   
   //QUERIES RUN
   //if we have a content connection dump 2 segments
   if ($cb_use_content_db) 
   {     
      if ($cbsql)
      {
         $dbp_output = $cbsql->dbp_fetch_parsed("Player Queries");
      }      
      if ($cbsql_content)
      {
         $dbp_output_content = $cbsql_content->dbp_fetch_parsed("Content Queries");
      }
   }
   
   //else cbsql houses all the queries
   else
   {
      if ($cbsql)
      {
         $dbp_output = $cbsql->dbp_fetch_parsed("Database Queries");
      }
   }
}

if ($charbrowser_simple_header)
{
   $cb_template->set_filenames(array(
     'footer' => 'footer_simple_body.tpl')
   );
}
else
{
   $cb_template->set_filenames(array(
     'footer' => 'footer_body.tpl')
   );
}

$cb_template->assign_vars(array(  
  'TITLE' => $mytitle,
  'VERSION' => $version,
  'DATABASE_TABLE_PERFORMANCE' => $dbp_table_output,
  'DATABASE_TABLE_PERFORMANCE_CONTENT' => $dbp_table_output_content,
  'DATABASE_PERFORMANCE' => $dbp_output,
  'DATABASE_PERFORMANCE_CONTENT' => $dbp_output_content,
  'ADVERTISEMENT' => $adscript)
);

$cb_template->pparse('footer');

$cb_template->destroy;
?>