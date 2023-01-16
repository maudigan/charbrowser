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
 *   September 30, 2014
 *      added an an API
 *   October 25, 2014
 *      added a new parse type that returns the parsing as a string
 *   April 14, 2020
 *      added a way to have an override template directory
 *   Devember 1, 2022
 *      in assign_api_block_vars, make sure an array key exists before
 *      trying to count the elements inside of it
 *   January 16, 2023 - Maudigan
 *      added _ prefix to private properties
 *      modified contructor to fetch global vars on its own
 *      renamed class with Charbrowser_ prefix
 *      swap "var" for "private"
 *      renamed constructor
 *
 ***************************************************************************/
 
 
 
 
 if ( !defined('INCHARBROWSER') )
{
   die("Hacking attempt");
}

class Charbrowser_Template 
{

   // variable that holds all the data we'll be substituting into
   // the compiled templates.
   // ...
   // This will end up being a multi-dimensional array like this:
   // $this->_tpldata[block.][iteration#][child.][iteration#][child2.][iteration#][variablename] == value
   // if it's a root-level variable, it'll be like this:
   // $this->_tpldata[.][0][varname] == value
   private $_tpldata = array();
   
   //holds the data that goes into the api 9/30/2014
   private $_apidata = array();
   
   //holds the last index used for an api array
   private $_lastindex = array();

   // Hash of filenames for each template handle.
   private $_filenames = array();

   // Root template directories.
   private $_primary_root = "";
   private $_secondary_root = "";

   // this will hash handle names to the compiled code for that handle.
   private $_compiled_code = array();

   // This will hold the uncompiled code for that handle.
   private $_uncompiled_code = array();

   /**
    * Constructor. Simply sets the root dir.
    *
    */
   function __construct($primary_dir = ".", $secondary_dir = ".")
   {
      $this->set_rootdir($primary_dir, $secondary_dir);
   }

   /**
    * Destroys this template object. Should be called when you're done with it, in order
    * to clear out the template data so you can load/parse a new template set.
    */
   function destroy()
   {
      $this->_tpldata = array();
   }

   /**
    * Sets the template root directory for this Template object.
    */
   function set_rootdir($primary_dir, $secondary_dir)
   {
      if (!is_dir($primary_dir) || !is_dir($secondary_dir))
      {
         return false;
      }

      $this->_primary_root = $primary_dir;
      $this->_secondary_root = $secondary_dir;
      return true;
   }

   /**
    * Sets the template filenames for handles. $filename_array
    * should be a hash of handle => filename pairs.
    */
   function set_filenames($filename_array)
   {
      if (!is_array($filename_array))
      {
         return false;
      }

      foreach($filename_array as $handle => $filename)
      {
         $this->_filenames[$handle] = $this->make_filename($filename);
      }

      return true;
   }


   /**
    * Load the file for the handle, compile the file,
    * and run the compiled code. This will print out
    * the results of executing the template.
    */
   function pparse($handle)
   {      
   
      //for debugging the array
      //print "ARRAY:\r\n";    
      //$this->printarray($this->_apidata); 
      //print "\r\n\r\n";  

      //output the data as json if the api flag is on
      if (checkParm('api'))
      {
         echo json_encode($this->_apidata);
         return true;
      }
      
      if (!$this->loadfile($handle))
      {
         die("Charbrowser_Template->pparse(): Couldn't load template file for handle $handle");
      }

      // actually compile the template now.
      if (!isset($this->_compiled_code[$handle]) || empty($this->_compiled_code[$handle]))
      {
         // Actually compile the code now.
         $this->_compiled_code[$handle] = $this->compile($this->_uncompiled_code[$handle]);
      }

      // Run the compiled code.
      eval($this->_compiled_code[$handle]);
      return true;
   }
   
   /**
    * Load the file for the handle, compile the file,
    * and run the compiled code. This will return
    * the results of executing the template as a string. -- added 10/25/2014
    */
   function pparse_str($handle)
   {    
      //output the data as json if the api flag is set
      if (checkParm('api'))
      {
         return json_encode($this->_apidata);
      }
      
      if (!$this->loadfile($handle))
      {
         die("Charbrowser_Template->pparse_str(): Couldn't load template file for handle $handle");
      }

      // Compile it, with the "no echo statements" option on.
      $_str = "";
      $code = $this->compile($this->_uncompiled_code[$handle], true, '_str');

      // evaluate the variable assignment.
      eval($code);

      return $_str;
   }   
   
   /**
    * dumps the api out 
    * added - 9/30/2014
    */
   function pparse_api()
   {
      echo json_encode($this->_apidata);
      return true;
   }

   /**
    * Inserts the uncompiled code for $handle as the
    * value of $varname in the root-level. This can be used
    * to effectively include a template in the middle of another
    * template.
    * Note that all desired assignments to the variables in $handle should be done
    * BEFORE calling this function.
    */
   function assign_var_from_handle($varname, $handle)
   {
      if (!$this->loadfile($handle))
      {
         die("Charbrowser_Template->assign_var_from_handle(): Couldn't load template file for handle $handle");
      }

      // Compile it, with the "no echo statements" option on.
      $_str = "";
      $code = $this->compile($this->_uncompiled_code[$handle], true, '_str');

      // evaluate the variable assignment.
      eval($code);
      // assign the value of the generated variable to the given varname.
      $this->assign_var($varname, $_str);

      return true;
   }
   

   /**
    * Block-level variable assignment. Adds a new block iteration with the given
    * variable assignments. Note that this should only be called once per block
    * iteration.
    */
   function assign_block_vars($blockname, $vararray)
   {
      if (strstr($blockname, '.'))
      {
         // Nested block.
         $blocks = explode('.', $blockname);
         $blockcount = sizeof($blocks) - 1;
         $str = '$this->_tpldata';
         for ($i = 0; $i < $blockcount; $i++)
         {
            $str .= '[\'' . $blocks[$i] . '.\']';
            eval('$lastiteration = sizeof(' . $str . ') - 1;');
            $str .= '[' . $lastiteration . ']';
         }
         // Now we add the block that we're actually assigning to.
         // We're adding a new iteration to this block with the given
         // variable assignments.
         $str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';

         // Now we evaluate this assignment we've built up.
         eval($str);
      }
      else
      {
         // Top-level block.
         // Add a new iteration to this block with the variable assignments
         // we were given.
         $this->_tpldata[$blockname . '.'][] = $vararray;
      }

      return true;
   }

   /**
    * Root-level variable assignment. Adds to current assignments, overriding
    * any existing variable assignment with the same name.
    */
   function assign_vars($vararray)
   {
      foreach($vararray as $key => $val)
      {
         $this->_tpldata['.'][0][$key] = $val;
      }

      return true;
   }

   /**
    * Root-level variable assignment. Adds to current assignments, overriding
    * any existing variable assignment with the same name.
    */
   function assign_var($varname, $varval)
   {
      $this->_tpldata['.'][0][$varname] = $varval;

      return true;
   }


   /**
    * A cleaner print for debuggin the arrays
    * added - 5/24/2016    
    */     
   function printarray($array, $spaces = "")
   {      
      foreach ($array as $key => $value) {
         if (is_array($value)) {
            print $spaces.$key."=>\r\n";
            $this->printarray($value, $spaces."   ");
         }
         else
            print $spaces.$key."=>".$value."\r\n";
      }  
   }   
   
   
   /**
    * Block-level api assignment
    * added - 9/30/2014    
    */ 
   function assign_api_block_vars($blockname, $vararray)
   {
      $blocks = explode('.', $blockname);
      $blockcount = cb_count($blocks);
      $curblock = "";
      $divider = "";
      $str = '$this->_apidata';
      for ($i = 0; $i < $blockcount; $i++)
      {
         $curblock .= $divider.$blocks[$i];
         $divider = ".";
         
         $str .= '[\''.$curblock.'\']';
         
         //for each template variable name, check how many elements with that name already exist
         //save the value as $index.
         //this used to not have array_key_exists, which means, for the first element you add
         //it would try to get the count of elements within an element that didn't yet exist
         //which posts a notice. Added the array_key_exists to stop that spam
         //eval('$index = count('.$str.', 0);');
         eval('$index = (array_key_exists(\''.$curblock.'\', $this->_apidata)) ? cb_count('.$str.', 0) : 0;');
         
         //only incriment the index on the last element
         if ($curblock != $blockname)
            $index--;
         
         //apply that index which is 1 higher than already exists
         $str .= '['.$index.']';
      }
      
      //add the new value
      $str .= ' = $vararray;';
      eval($str);       

      return true;
   }

   /**
    * adds multiple propertys to the api feed
    * added - 9/30/2014
    */
   function assign_api_vars($vararray)
   {
      foreach($vararray as $key => $val)
      {
         $this->_apidata[$key] = $val;
      }

      return true;
   }

   /**
    * adds a single property to the api feed
    * added - 9/30/2014
    */
   function assign_api_var($varname, $varval)
   {
      $this->_apidata[$varname] = $varval;

      return true;
   }
   
   /**
    * Block-level api & template assignment
    * added - 9/30/2014    
    */
   function assign_both_block_vars($blockname, $vararray)
   {
      $this->assign_api_block_vars($blockname, $vararray);
      $this->assign_block_vars($blockname, $vararray);
      return true;
   }

   /**
    * adds multiple propertys to the api feed & template
    * added - 9/30/2014
    */
   function assign_both_vars($vararray)
   {
      $this->assign_api_vars($vararray);
      $this->assign_vars($vararray);
      return true;
   }

   /**
    * adds a single property to the api feed & template 
    * added - 9/30/2014
    */
   function assign_both_var($varname, $varval)
   {
      $this->assign_api_var($varname, $varval);
      $this->assign_var($varname, $varval);
      return true;
   }   

   /**
    * Generates a full path+filename for the given filename, which can either
    * be an absolute name, or a name relative to the rootdir for this Template
    * object.
    */
   function make_filename($filename)
   {
      // Check if it's an absolute or relative path      
      if (substr($filename, 0, 1) == '/') 
      {
            //check if the primary file exists
            if (!file_exists($filename))
            {
               die("Charbrowser_Template->make_filename(): Error - file $filename does not exist");
            }

            return $filename;
      }
      else
      {
            //default to primary file
            $temp_filename = ($rp_filename = $this->_primary_root . '/' . $filename) ? $rp_filename : $filename;
            
            if (file_exists($temp_filename)) 
            {
                 return $temp_filename;
            }
            
            //if it doesnt exist revert to secondary/default directory
            $temp_filename = ($rp_filename = $this->_secondary_root . '/' . $filename) ? $rp_filename : $filename;
            
            if (file_exists($temp_filename)) 
            {
                 return $temp_filename;
            }
            
            //else we error
            die("Charbrowser_Template->make_filename(): Error - file $filename does not exist in the primary or secondary directory");
      }

      return $filename;
   }


   /**
    * If not already done, load the file for the given handle and populate
    * the _uncompiled_code[] hash with its code. Do not compile.
    */
   function loadfile($handle)
   {
      // If the file for this handle is already loaded and compiled, do nothing.
      if (isset($this->_uncompiled_code[$handle]) && !empty($this->_uncompiled_code[$handle]))
      {
         return true;
      }

      // If we don't have a file assigned to this handle, die.
      if (!isset($this->_filenames[$handle]))
      {
         die("Charbrowser_Template->loadfile(): No file specified for handle $handle");
      }

      $filename = $this->_filenames[$handle];

      $str = implode("", @file($filename));
      if (empty($str))
      {
         die("Charbrowser_Template->loadfile(): File $filename for handle $handle is empty");
      }

      $this->_uncompiled_code[$handle] = $str;

      return true;
   }



   /**
    * Compiles the given string of code, and returns
    * the result in a string.
    * If "do_not_echo" is true, the returned code will not be directly
    * executable, but can be used as part of a variable assignment
    * for use in assign_code_from_handle().
    */
   function compile($code, $do_not_echo = false, $retvar = '')
   {
      // replace \ with \\ and then ' with \'.
      $code = str_replace('\\', '\\\\', $code);
      $code = str_replace('\'', '\\\'', $code);

      // change template varrefs into PHP varrefs

      // This one will handle varrefs WITH namespaces
      $varrefs = array();
      preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);
      $varcount = sizeof($varrefs[1]);
      for ($i = 0; $i < $varcount; $i++)
      {
         $namespace = $varrefs[1][$i];
         $varname = $varrefs[3][$i];
         $new = $this->generate_block_varref($namespace, $varname);

         $code = str_replace($varrefs[0][$i], $new, $code);
      }

      // This will handle the remaining root-level varrefs
      $code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->_tpldata[\'.\'][0][\'\1\']) ) ? $this->_tpldata[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);

      // Break it up into lines.
      $code_lines = explode("\n", $code);

      $block_nesting_level = 0;
      $block_names = array();
      $block_names[0] = ".";

      // Second: prepend echo ', append ' . "\n"; to each line.
      $line_count = sizeof($code_lines);
      for ($i = 0; $i < $line_count; $i++)
      {
         $code_lines[$i] = chop($code_lines[$i]);
         if (preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m))
         {
            $n[0] = $m[0];
            $n[1] = $m[1];

            // Added: dougk_ff7-Keeps templates from bombing if begin is on the same line as end.. I think. :)
            if ( preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n) )
            {
               $block_nesting_level++;
               $block_names[$block_nesting_level] = $m[1];
               if ($block_nesting_level < 2)
               {
                  // Block is not nested.
                  $code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->_tpldata[\'' . $n[1] . '.\']) ) ?  sizeof($this->_tpldata[\'' . $n[1] . '.\']) : 0;';
                  $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
                  $code_lines[$i] .= "\n" . '{';
               }
               else
               {
                  // This block is nested.

                  // Generate a namespace string for this block.
                  $namespace = implode('.', $block_names);
                  // strip leading period from root level..
                  $namespace = substr($namespace, 2);
                  // Get a reference to the data array for this block that depends on the
                  // current indices of all parent blocks.
                  $varref = $this->generate_block_data_ref($namespace, false);
                  // Create the for loop code to iterate over this block.
                  $code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
                  $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
                  $code_lines[$i] .= "\n" . '{';
               }

               // We have the end of a block.
               unset($block_names[$block_nesting_level]);
               $block_nesting_level--;
               $code_lines[$i] .= '} // END ' . $n[1];
               $m[0] = $n[0];
               $m[1] = $n[1];
            }
            else
            {
               // We have the start of a block.
               $block_nesting_level++;
               $block_names[$block_nesting_level] = $m[1];
               if ($block_nesting_level < 2)
               {
                  // Block is not nested.
                  $code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->_tpldata[\'' . $m[1] . '.\']) ) ? sizeof($this->_tpldata[\'' . $m[1] . '.\']) : 0;';
                  $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
                  $code_lines[$i] .= "\n" . '{';
               }
               else
               {
                  // This block is nested.

                  // Generate a namespace string for this block.
                  $namespace = implode('.', $block_names);
                  // strip leading period from root level..
                  $namespace = substr($namespace, 2);
                  // Get a reference to the data array for this block that depends on the
                  // current indices of all parent blocks.
                  $varref = $this->generate_block_data_ref($namespace, false);
                  // Create the for loop code to iterate over this block.
                  $code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
                  $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
                  $code_lines[$i] .= "\n" . '{';
               }
            }
         }
         else if (preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m))
         {
            // We have the end of a block.
            unset($block_names[$block_nesting_level]);
            $block_nesting_level--;
            $code_lines[$i] = '} // END ' . $m[1];
         }
         else
         {
            // We have an ordinary line of code.
            if (!$do_not_echo)
            {
               $code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
            }
            else
            {
               $code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";'; 
            }
         }
      }

      // Bring it back into a single string of lines of code.
      $code = implode("\n", $code_lines);
      return $code   ;

   }


   /**
    * Generates a reference to the given variable inside the given (possibly nested)
    * block namespace. This is a string of the form:
    * ' . $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
    * It's ready to be inserted into an "echo" line in one of the templates.
    * NOTE: expects a trailing "." on the namespace.
    */
   function generate_block_varref($namespace, $varname)
   {
      // Strip the trailing period.
      $namespace = substr($namespace, 0, strlen($namespace) - 1);

      // Get a reference to the data block for this namespace.
      $varref = $this->generate_block_data_ref($namespace, true);
      // Prepend the necessary code to stick this in an echo line.

      // Append the variable reference.
      $varref .= '[\'' . $varname . '\']';

      $varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';

      return $varref;

   }


   /**
    * Generates a reference to the array of data values for the given
    * (possibly nested) block namespace. This is a string of the form:
    * $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
    *
    * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
    * NOTE: does not expect a trailing "." on the blockname.
    */
   function generate_block_data_ref($blockname, $include_last_iterator)
   {
      // Get an array of the blocks involved.
      $blocks = explode(".", $blockname);
      $blockcount = sizeof($blocks) - 1;
      $varref = '$this->_tpldata';
      // Build up the string with everything but the last child.
      for ($i = 0; $i < $blockcount; $i++)
      {
         $varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
      }
      // Add the block reference for the last child.
      $varref .= '[\'' . $blocks[$blockcount] . '.\']';
      // Add the iterator for the last child if requried.
      if ($include_last_iterator)
      {
         $varref .= '[$_' . $blocks[$blockcount] . '_i]';
      }

      return $varref;
   }

}

?>