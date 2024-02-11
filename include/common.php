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
 *   March 22, 2020 - Initial Revision. Moved common features here instead
 *                    of using global.php (Maudigan)
 *   March 28, 2020 - add the new self registration code (Maudigan)
 *   April 2, 2020 - make our index url a global var (Maudigan)
 *   April 14, 2020 - relocated the API security check to here instead of
 *                    global.php
 *                    Added a subdirectory to the template folder (Maudigan)
 *   October 24, 2022 - add page execution time (Maudigan)
 *   January 16, 2023 - make sure variables are declared before use
 *                      implemented the new error class
 *
 ***************************************************************************/

if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

//this script only needs to run once
if ( !defined('CB_COMMON_RUN') )
{
   ///marktime for when we start running
   $cb_start_time = microtime(true);
   
   //make sure variables set in the wrapped page are set,
   //even when charbrowser is not wrapped
   $charbrowser_wrapped =       isset($charbrowser_wrapped) ? $charbrowser_wrapped : false;
   $charbrowser_is_admin_page = isset($charbrowser_is_admin_page) ? $charbrowser_is_admin_page : false;
   $charbrowser_simple_header = isset($charbrowser_simple_header) ? $charbrowser_simple_header : false;
   $charbrowser_root_url =      isset($charbrowser_root_url) ? $charbrowser_root_url : false;

   
   /*********************************************
                 LOAD CONFIGURATION
   *********************************************/
   include_once(__DIR__ . "/config.php");
   //docker config overwrites
   include_once(__DIR__ . "/docker_config.php");
   
   
   //if developer mode is turned on, enable strict error reporting
   //otherwise hide notices and warnings, this changes once
   //the error handler in error.php is loaded
   if (defined('DEVELOPER_MODE'))
   {
      error_reporting (E_ALL);
   }
   else
   {
      error_reporting (0);
   }
      
   /*********************************************
                    INCLUDES
   *********************************************/
   include_once(__DIR__ . "/version.php");
   include_once(__DIR__ . "/language.php");
   include_once(__DIR__ . "/functions.php");
   include_once(__DIR__ . "/global.php");
   include_once(__DIR__ . "/template.php" );

   //figure our current url
   $cb_index_url = ($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php";

   //CREATE TEMPLATE CLASS
   if (!isset($cb_override_template_dir)) $cb_override_template_dir = "custom";
   $cb_template = new Charbrowser_Template(__DIR__ . "/../templates/" . $cb_override_template_dir, __DIR__ . "/../templates/default");
   
   /*********************************************
                  INCLUDES CONT'D
   *********************************************/
   include_once(__DIR__ . "/error.php");

   //self reg timestamp file
   $cb_reg_file = '.register';


   //BLOCK API
   //the template class will allow data to be output as json
   //if this is an API request and API is not enabled kill the api
   //request and then show an error saying api is unavailable
   if (checkParm('api') && !$api_enabled)
   {
      checkParm('api', false);
      $cb_error->message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NOAPI']);
   }


   //VERIFY CONFIG FILE
   //if the version stamp in your config file
   //doesn't match the stamp in your code then
   //you need to regenerate your config file from
   //config.template
   if ($cb_config_version != $cb_expected_config_version) {
      $cb_error->message_die($language['MESSAGE_ERROR'],$language['MESSAGE_BAD_CONFIG']);
   }


   //SELF REGISTRATION HANDSHAKE
   //if the installer allowed for self registration, after a registration
   //packet is sent the registration server will call back to this
   //routine to verify the data set was genuine.
   //a genuine request will have a hash of the unique string and
   //request timestamp that matches.
   //this wont automatically verify the registration, it's only to
   //filter out obviously bogus registrations. All "genuine" registrations
   //are manually confirmed.
   
   $confirmserver = preg_Get_Post('confirmserver', '/^[0-9a-fA-F]{64}$/', false);
   if ($cb_allow_self_register && $confirmserver) {
      //make sure we have a good timestamp
      clearstatcache(true, $cb_reg_file);

      $cb_reg_time = filemtime($cb_reg_file);

      $cb_confirmationhash = hash('sha256', $cb_unique_string.$cb_reg_time);
      //a good request is one with a matching key, a matching timestamp of when
      //we generated our registration, and it had to have happened within the last
      //minute
      if ($confirmserver == $cb_confirmationhash && time() < $cb_reg_time + 60) {
         die(json_encode(array('response' => 1))); //GOOD
      }
      else {
         die(json_encode(array('response' => 0))); //BAD
      }
   }

   //SELF REGISTRATION SEND
   //if the installer allowed the self registration
   //to notify the registration server of this installation
   //build and send the registration packet
   //(skip if it's an image request, dont want to risk breaking
   //the image)
   if ($cb_allow_self_register && !defined('IS_IMAGE_SCRIPT')) {

      //how many seconds to wait between self registrations
      //default is once an hour max
      $cb_ttw_registration = 60 * 60;

      //use a lock file to limit registration rate
      $cb_need_register = true;

      //if its been $cb_ttw_registration number of seconds since the last registration, re-register
      if (file_exists($cb_reg_file)) {
         if (time() - filemtime($cb_reg_file) < $cb_ttw_registration) {
            $cb_need_register = false;
         }
      }

      //mark the registration time
      if ($cb_need_register) {
         //mark the last time we self registered
         touch($cb_reg_file);

         //make sure we have a good timestamp
         clearstatcache(true, $cb_reg_file);

         $cb_host = $_SERVER['HTTP_HOST'];
         $cb_request = strtok($_SERVER['REQUEST_URI'], '?');

         //build registration query string
         $cb_vars  = '?email='.urlencode($cb_admin_email);
         $cb_vars .= '&contact='.urlencode($cb_admin_contact);
         $cb_vars .= '&url='.urlencode($cb_installation_url);
         $cb_vars .= '&rhost='.urlencode($cb_host);
         $cb_vars .= '&rrequest='.urlencode($cb_request);
         $cb_vars .= '&custom='.urlencode($cb_is_custom);
         $cb_vars .= '&title='.urlencode($cb_server_title);
         $cb_vars .= '&list='.urlencode($cb_list_server);
         $cb_vars .= '&key='.urlencode($cb_unique_string);
         $cb_vars .= '&version='.urlencode($version);
         $cb_vars .= '&time='.urlencode(filemtime($cb_reg_file));

         //send it
         $cbhost = 'charbrowser.net';
         $cbpath = '/register.php'.$cb_vars;
         $cbhttp = "GET $cbpath HTTP/1.0\r\nHost: $cbhost\r\n\r\n";
         $cbstream = @stream_socket_client("$cbhost:80", $cberrno, $cberrstr, 120,STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT);
         if ($cbstream) {
            fwrite($cbstream, $cbhttp);
         }
      }
   }


   //LIMIT ME
   //this script only needs to run once
   define('CB_COMMON_RUN', true);
}
?>
