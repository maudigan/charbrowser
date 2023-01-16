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
 *   Decemeber 12, 2020 - Initial Revision. Centralize error handling.
 *                       (Maudigan)
 *
 ***************************************************************************/

if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

//uncomment to output errors imediately instead of spooling
//define('INSTANT_ERROR', true);


/****************************************************************
             ERROR HANDLING AND MESSAGE OUTPUT
****************************************************************/
// This class has features to output messages, and to terminate 
// execution if the message is fatal. Those same functions are 
// utilized by php generated errors and notices/warnings. 
// Warnings and notices are rerouted to these message functions 
// using the set_error_handler, and fatal errors are captured 
// and redirected here using register_shutdown_function. 
// Regular PHP error reporting is then shutoff so that we handle 
// all the output here. 
// The messages are formated as an HTML popup window via the 
// template class. 
// For terminal messages it will output the header (if needed) 
// before it displays the error, and then it drops the footer. 
// For non terminal messages it will instead store the messsage 
// until the header is naturally displayed and then it will 
// display those messages later (either when the next message 
// is requested, or when the footer executes). 
// If a script is defined as an image using the 
// 'IS_IMAGE_SCRIPT' constant the output will ALWAYS be treated
//  as fatal and it will always display the error as a 
// formatted image. 
// If 'INSTANT_ERROR' is declared the errors will always 
// display as very basic HTML text and always immediately be 
// displayed instead of possibly spooling. Turn this on to 
// improve the ability to debug. 
// If 'DEVELOPER_MODE' is set in the config, all errors and 
// warnings will display, and if it's not set no warnings or 
// notices will display and fatal errors will be generically 
// worded.
class Charbrowser_Error
{
   //local database handle
   private $_pending_messages = array();
   
   //store the original error reporting level
   private $_initial_error_reporting = 0;
   
   //local references to external classes
   //imported using "global" in the constructor
   private $_language = 0;
   private $_template = 0;


   //-------------------------------------
   //            CONSTRUCTOR
   //-------------------------------------
   function __construct()
   {
      global $language;
      global $cb_template;
      
      //make sure the language class exists, store pointer
      if (!isset($language)) 
      {
         $this->_error->message_die("Error", "The Charbrowser_Error class can't be initialized prior to the language array (language.php) language.php.");
      }
      else
      {
         $this->_language = $language;
      }
      
      //make sure the template class exists, store pointer
      if (!isset($cb_template)) 
      {
         $this->message_die($this->_language['MESSAGE_ERROR'], sprintf($this->_language['MESSAGE_LOAD_ORDER'], 'Charbrowser_Error', 'template.php'));
      }
      else
      {
         $this->_template = $cb_template;
      }
      
      //store initial error reporting to restore later
      $this->_initial_error_reporting = error_reporting();
   }


   //-------------------------------------
   //            DESTRUCTOR
   //-------------------------------------
   function __destruct()
   {
      //resture error reporting
      error_reporting($this->_initial_error_reporting);
   }


   //-------------------------------------
   //       SET ERROR REPORTING
   //turn standard PHP error reporting
   //off or on. 
   //-------------------------------------
   function reporting($level)
   {
      error_reporting($level);
   }


   //-------------------------------------
   //        INSTANT ERROR
   //outputs a message as a simple
   //line of text
   //-------------------------------------
   function instant_message($dietitle, $text) 
   {
      print "<span style='font-weight:700;'>".$dietitle.": </span><span style='font-weight:100;'>".$text."</span><br>";
   }
   
   
   //-------------------------------------
   //           IMAGE, DIE
   //outputs a message as an image then
   //terminates execution
   //-------------------------------------
   function image_die($dietitle, $text)
   {
      $image_width = 500;
      $image_height = 100;
      $top_margin = 15;
      $left_margin = 10;
      $title_font = 3;
      $body_font = 2;
      $line_height = 12;
      $first_body_line = 30;
      
      //calculate how many letters fit on a row of the image
      $letters_per_row = floor(($image_width - $left_margin) / imagefontwidth($body_font));
      $image_die = imagecreatetruecolor($image_width, $image_height);
      $color = imagecolorallocate($image_die, 255, 255, 255);
      imagestring($image_die, $title_font, $left_margin, $top_margin, $dietitle, $color);
      
      //output the text with wordwrap. We break the text up on spaces
      //then loop through the words, building a line of text until
      //it exceedes the max number of chars on a row. Then we print
      //and move down a line.
      $words = explode(" ", $text);
      $wordcount = (count($words));
      $cur_line_text = '';
      $cur_line_top = $first_body_line;
      for ($i = 0; $i < $wordcount; $i++)
      {
         if (strlen($cur_line_text) + strlen($words[$i]) > $letters_per_row)
         {
            //print the old line, move down to the next line and reset text buffer
            imagestring($image_die, $body_font, $left_margin, $cur_line_top, $cur_line_text, $color);
            $cur_line_top += $line_height;
            $cur_line_text = '';
         }
         
         $cur_line_text .= $words[$i] . ' ';
      }
      //print the last line
      imagestring($image_die, $body_font, $left_margin, $cur_line_top, $cur_line_text, $color);
      
      if (ob_get_contents()) ob_clean(); //make sure we haven't sent a text header
      header("Content-Type: image/png");
      imagepng($image_die);
      ImageDestroy($image_die);
      exit();
   }
   
   
   //-------------------------------------
   //           MESSAGE, DIE
   //outputs a message then terminates
   //execution
   //-------------------------------------
   function message_die($dietitle, $text) 
   {      
      //annoying, but these have to be included to pass 
      //through to header.php and footer.php
      global $cb_template;
      global $cb_error;
      global $language;
      global $charbrowser_root_url;
      global $charbrowser_wrapped;
      global $charbrowser_simple_header;
      global $mytitle;
      global $version;
      global $adscript;
      global $cb_index_url;
      global $cb_use_content_db;
      global $cbsql;
      global $cbsql_content;
      global $cb_start_time;
      global $header_name_search;
      global $header_guild_search;
      
      //immediately output the error if INSTANT_ERROR flag is set
      //this is used for locating/debugging errors
      if (defined('INSTANT_ERROR')) 
      {
         $this->instant_message($dietitle, $text);
         exit();
      }
      
      //output error as an image if GD librari is available
      if (defined('IS_IMAGE_SCRIPT') && SERVER_HAS_GD) 
      {
         $this->image_die($dietitle, $text);
      }  
      //drop page
      $d_title = " - ".$dietitle;
      include(__DIR__ . "/header.php");
      
      $this->_template->set_filenames(array(
        'message' => 'message_body.tpl')
      );
      
      $this->_template->assign_both_vars(array(  
         'DIETITLE' => $dietitle,
         'TEXT' => $text)
      );
      $this->_template->assign_vars(array(
         'BUTTON_CODE' => "cb_GoBackOnePage();",
         'L_BUTTON' => $this->_language['BUTTON_BACK'])
      );
      
      $this->_template->pparse('message');
      
      //dump footer
      include(__DIR__ . "/footer.php");
      exit();
   }


   //-------------------------------------
   //           MESSAGE
   //outputs a message, but only after
   //the header is printed, else it
   //spools it for later output
   //-------------------------------------
   function message($dietitle, $text) 
   {      
      //immediately output the error if INSTANT_ERROR flag is set
      //this is used for locating/debugging errors
      if (defined('INSTANT_ERROR')) 
      {
         $this->instant_message($dietitle, $text);
         return;
      }

      //output error as an image if GD librari is available
      if (defined('IS_IMAGE_SCRIPT') && SERVER_HAS_GD) 
      {
         $this->image_die($dietitle, $text);
      }
      
      $this->_pending_messages[] = array(  
         'DIETITLE' => $dietitle,
         'TEXT' => $text
      );
      
      //if the header hasn't been dropped
      //we need to spool the messages for later
      //output
      if ( !defined('CB_HEADER_RUN') )
      {
         return;
      }
      
      //output any messages
      $this->output_messages();
   }


   //-------------------------------------
   //           OUTPUT MESSAGE
   //dumps any pending messages out
   //-------------------------------------
   function output_messages()
   {
      foreach($this->_pending_messages as $message)
      {
      
         $this->_template->set_filenames(array(
            'message' => 'message_body.tpl')
         );

         $this->_template->assign_both_vars($message);
         
         $this->_template->assign_vars(array( 
            'BUTTON_CODE' => "this.parentElement.remove();",
            'L_BUTTON' => $this->_language['BUTTON_CLEAR'])
         );

         $this->_template->pparse('message');
      }

      //reset the output array
      $this->_pending_messages = array();
   }
   

   //-------------------------------------
   //          ERROR HANDLER
   // process any notifications, warnings
   // errors, etc. that are raised by
   // php
   //-------------------------------------
   function error_handler($errno, $errstr, $errfile, $errline)
   {
      //check if this was triggered by a shutdown event
      $shutdown = false;
      switch ($errno)
      {
         case E_ERROR:
         case E_CORE_ERROR:
         case E_COMPILE_ERROR:
         case E_USER_ERROR:
         case E_RECOVERABLE_ERROR:
         case E_CORE_WARNING:
         case E_COMPILE_WARNING:
         case E_PARSE:
            $shutdown = true;
      }
      
      //if we are NOT in developer mode don't show
      //errors that aren't shutdown events
      if (!defined('DEVELOPER_MODE'))
      {
         //if we aren't in dev mode, and a shutdown event
         //occurs, we censor the error message
         if ($shutdown) 
         {
            $this->message_die($this->_language['MESSAGE_ERROR'], $this->_language['MESSAGE_GENERIC']);
         }
         return false;
      }
      
      //if we're going to report this, determin the error class
      $title = "Unknown";
      switch ($errno) {
         case E_ERROR:
         case E_CORE_ERROR:
         case E_COMPILE_ERROR:
            $title = $this->_language['MESSAGE_FATAL_ERROR'];
            break;
         case E_PARSE:
            $title = $this->_language['MESSAGE_PARSER_ERROR'];
            break;
         case E_USER_ERROR:
         case E_RECOVERABLE_ERROR:
            $title = $this->_language['MESSAGE_ERROR'];
            break;
         case E_WARNING:
         case E_CORE_WARNING:
         case E_COMPILE_WARNING:
         case E_USER_WARNING:
            $title = $this->_language['MESSAGE_WARNING'];
            break;
         case E_NOTICE:
         case E_USER_NOTICE:
            $title = $this->_language['MESSAGE_NOTICE'];
            break;
         case E_STRICT:
            $title = $this->_language['MESSAGE_DEBUG'];
            break;
      }
      
      //output the error
      $text = sprintf($this->_language['MESSAGE_ERROR_TEMPLATE'], $errstr, $errfile, $errline);
      if ($shutdown)
      {
         $this->message_die($title, $text);
      }
      else
      {
         $this->message($title, $text);
      }
   }

   //-------------------------------------
   //        SHUTDOWN FUNCTION
   // fatal errors and parse errors don't 
   // trigger the error handler, so check 
   // the shutdown process to see if there 
   // are any abnormal endings (errors)
   // then pass those on to the error
   // handler
   //-------------------------------------
   function shutdown_function()
   {
      //last chance to output non fatal messages
      $this->output_messages();
      
      //see if the last error was fatal, if so output it
      $lasterror = error_get_last();
      if (is_array($lasterror))
      {
         switch ($lasterror['type'])
         {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:
               $this->error_handler($lasterror['type'], $lasterror['message'], $lasterror['file'], $lasterror['line']);
         }
      }
   }
}


//create instance of the error handler
$cb_error = new Charbrowser_Error();

//redirect warnings and notices to the custom error 
//handler in the error class
set_error_handler(function($errno, $errstr, $errfile, $errline)
{
   global $cb_error;
   $cb_error->error_handler($errno, $errstr, $errfile, $errline);
}, E_ALL);

//pass the shutdown process on to the error
//class for handling so we can capture fatal
//errors that aren't triggered by set_error_handler()
register_shutdown_function(function()
{
   global $cb_error;
   $cb_error->shutdown_function();
});

//now that the error class is setup, shutdown the normal
//php error reporting so the error class can takeover
$cb_error->reporting(0);

?>