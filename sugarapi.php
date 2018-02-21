<?php

/* Sugar Rest API Class
*/

class SugarRestAPI
{

     // Our private state variables these should be obvious
     private $url = NULL;
     private $username = NULL;
     private $password = NULL;
     private $application = NULL;
     private $session = NULL;
     private $logged_in = FALSE;
     private $error = FALSE;
     private $debug = 0;

     // our private functions
     // make a curl rest request (taken from the sugarcrm api docs)
     private function request($method, $parameters)
     {
         ob_start();
         $curl_request = curl_init();

         curl_setopt($curl_request, CURLOPT_URL, $this->url);
         curl_setopt($curl_request, CURLOPT_POST, 1);
         curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
         curl_setopt($curl_request, CURLOPT_HEADER, 1);
         curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

         $jsonEncodedData = json_encode($parameters);

         $post = array(
              'method' => $method,
              'input_type' => "JSON",
              'response_type' => "JSON",
              'rest_data' => $jsonEncodedData
         );

         curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
         $result = curl_exec($curl_request);
         curl_close($curl_request);

         $result = explode("\r\n\r\n", $result, 2);
         $response = json_decode($result[1]);
         ob_end_flush();

         return $response;
     }

     // log output to a file (hard coded at this time)
     function LogToFile($Message) {
         error_log(date("Y-m-d H:i:s")." - ",3,"./sugarcrm.txt");
         error_log($Message,3,"./sugarcrm.txt");
     }

     // our public functions
     // print out our private state variables.  use for debugging
     public function PrintPrivateState()
     {
          echo "<pre>\n";
          echo "Our State is as follows:\n";
          echo "   \$url = ".$this->url."\n";
          echo "   \$username = ".$this->username."\n";
          echo "   \$password = ".$this->password."\n";
          echo "   \$application = ".$this->application."\n";
          echo "   \$logged_in = ";
          if($this->logged_in) { 
               echo "True\n";
               echo "   \$session = ".$this->session."\n";
          } else echo "False\n";
          echo "   \$error = ";
          if(!$this->error) echo "none\n";
          else $this->PrintError($this->error,FALSE);
          echo "   \$debug = ".$this->debug."\n";
	  echo "</pre>\n";
     }

     public function PrintPrivateState2()
     {
          error_log("Our State is as follows:\n",3,"/tmp/sugarcrm.log");
          error_log("   \$url = ".$this->url."\n",3,"/tmp/sugarcrm.log");
          error_log("   \$username = ".$this->username."\n",3,"/tmp/sugarcrm.log");
          error_log("   \$password = ".$this->password."\n",3,"/tmp/sugarcrm.log");
          error_log("   \$application = ".$this->application."\n",3,"/tmp/sugarcrm.log");
          error_log("   \$logged_in = ",3,"/tmp/sugarcrm.log");
          if($this->logged_in) { 
               error_log("True\n",3,"/tmp/sugarcrm.log");
               error_log("   \$session = ".$this->session."\n",3,"/tmp/sugarcrm.log");
          } else error_log("False\n",3,"/tmp/sugarcrm.log");
          error_log("   \$error = ",3,"/tmp/sugarcrm.log");
          if(!$this->error) error_log("none\n",3,"/tmp/sugarcrm.log");
          else $this->PrintError($this->error,FALSE);
          error_log("   \$debug = ".$this->debug."\n",3,"/tmp/sugarcrm.log");
     }

     // set the url we are going to connect to
     public function SetURL($url)
     {
          $this->url = $url;
     }

     // set the username we going to use
     public function SetUsername($username)
     {
          $this->username = $username;
     }

     // set the password of the user we going to be using
     public function SetPassword($password)
     {
          $this->password = $password;
     }

     // set the application name
     public function SetApplicationName($appname)
     {
          $this->application = $appname;
     }

     // set the debug level 
     public function SetDebugLevel($debug)
     {
          $this->debug = $debug;
     }

     // set login infomation
     public function SetLoginInfo($url, $username, $password, $appname=NULL)
     {
          $this->SetURL($url);
          $this->SetUsername($username);
          $this->SetPassword($password);
          if(isset($appname)) $this->SetApplicationName($appname);
     }

     // logins in using the login info
     // if success sets session state
     // else sets and returns error
     public function Login()
     {
          $parameters = array (
              'user_auth' => array (
                  'user_name' => $this->username,
                  'password' => md5($this->password),
              ),
              'application_name' => $this->application,
              'name_value_list' => array(),
          );

          $result = $this->request("login", $parameters);

          // set the session and login states
          if (isset($result->id)) {
              $this->session = $result->id;
              $this->logged_in = TRUE;
              return TRUE;
          }
   
          // there was an error
          $this->error = $result; 
          return FALSE;
     }

     // returns the current eror and clears the error state
     public function GetError()
     {
          $error = TRUE;

          if(isset($this->error->name)) {
              $error=$this->error;
          } else if(is_bool($this->error)) {
              $error = $this->error;
          }

          // clear the current error
          $this->error=FALSE;
          return $error;
     }

     // prints the error and optionially dies
     public function PrintError($error,$flag=FALSE)
     {
          echo "<pre>\n";
          switch(gettype($error)) {
              case "string":
                  echo $error;
                  break;
              case "array":
                  print_r($error);
                  break;
              case "boolean":
                  echo "error : ";
                  if($error) echo "True\n";
                  else echo "False\n";
                  break;
              case "NULL":
                  echo "error : NULL\n";
                  break;
              case "object":
                  if(isset($error->number)) echo "error number ".$error->number." ";
                  if(isset($error->name)) echo "[".$error->name."]\n";
                  if(isset($error->description)) echo $error->description."\n";
                  break;
              default:
                  echo "unknown error type: ".gettype($error)."\n";
          }
          echo "</pre>\n\n";

          // optionially die
          if ($flag) die;
      }

      // return a list of all teh available modules
      public function get_available_modules($filter="all")
      {
          $args = array(
              'session' => $this->session,
              'filter' => $filter
          );

          $result = $this->request('get_available_modules',$args);
          return $result;
      }

      // Retrieves a list of beans based on query specifications
      // options['query'] The SQL WHERE clause without the word "where"
      // options['order_by'] The SQL ORDER BY clause without the phrase "order by"
      // options['offset'] The record offset from which to start
      // options['max_results'] The maximum number of results to return
      // options['deleted'] If deleted records should be included in the results
      // options['favorites'] If only records marked as favorites should be returned
      public function get_entry_list($module,$fields,$related,$options=array())
      {
          // set some defaults and merge in any option arguments
          $options = array_merge(
              array(
                  'query' => "",
                  'order_by' => "",
                  'offset' => 0,
                  'max_results' => 0,
                  'deleted' => FALSE,
                  'favorites' => FALSE,
              ),
              $options
          );

          // set the request arguments 
          $args = array(
              'session' => $this->session,
              'module' => $module,
              'query' => $options['query'],
              'order_by' => $options['order_by'],
              'offset' => $options['offset'],
              'select_fields' => $fields,
              'link_name_to_fields_array' => $related,
              'max_results' => $options['max_results'],
              'deleted' => $options['deleted'],
              'favorites' => $options['favorites'],
          );

          // do the request
          if($this->debug) print_r($args);
          $result = $this->request('get_entry_list',$args);

          // return the results
          if($this->debug) print_r($result);
          return $result;
      }

      // Retrieves a single bean based on record ID
      public function get_entry($module,$id,$fields=array(),$link=array(),$track=FALSE) {
          $args = array(
              'session' => $this->session,
              'module_name' => $module,
              'id' => $id,
              'select_fields' => $fields,
              'link_name_to_fields_array' => $link,
              'track_view' => $track
          );

          // do the request
          if($this->debug) print_r($args);
          $result = $this->request('get_entry',$args);

          // return the results
          if($this->debug) print_r($result);
          return $result;
      }

      // Retrieves a list of beans based on search specifications
      // options['offset'] The record offset from which to start
      // options['max_results'] The maximum number of results to return
      // options['id'] Filters records by the assigned user ID
      // options['unified_search_only'] If the search is only search modules participating in the unified search
      // options['favorites'] If only records marked as favorites should be returned
      public function search_by_module($searchstring,$modules,$fields=array(),$options=array()) {
          
          // set some defaults and merge in any option arguments
          $options = array_merge(
              array(
                  'offset' => 0,
                  'max_results' => 100,
                  'id' => '',
                  'unified_search_only' => FALSE,
                  'favorites' => FALSE,
              ),
              $options
          );


          // set the request arguments
          $args = array(
              'session' => $this->session,
              'search_string' => $searchstring,
              'modules' => $modules,
              'offset' => $options['offset'],
              'max_results' => $options['max_results'],
              'id' => $options['id'],
              'select_fields' => $fields,
              'unified_search_only' => $options['unified_search_only'],
              'favorites' => $options['favorites'],
          );

          // do the request
          if($this->debug) print_r($args);
          $result = $this->request('search_by_module',$args);

          // return the results
          if($this->debug) print_r($result);
          return $result;
      }
}
