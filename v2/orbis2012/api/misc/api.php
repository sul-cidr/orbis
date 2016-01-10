<?php
    ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	$osite = $_GET['s'];
	/* 
		This is an example class script proceeding secured API
		To use this class you should keep same as query string and function name
		Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
		     function delete_user(){
				 You code goes here
			 }
		Class will execute the function dynamically;
		
		usage :
		
		    $object->response(output_data, status_code);
			$object->_request	- to get santinized input 	
			
			output_data : JSON (I am using)
			status_code : Send status message for headers
			
		Add This extension for localhost checking :
			Chrome Extension : Advanced REST client Application
			URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo
 	*/
	
	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		public $_REQUEST = "";
		private $db = NULL;
		

	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			//include("../conn/conn_webapp.php");
		    $connectString_orbis = "host=orbis-dev.stanford.edu dbname=orbis user=webapp password=sl1ppy";
			$dbconn = pg_connect($connectString_orbis) or die;
			if (!$dbconn) {
				handleError('Not connecting to the database');
			}
		}
		/*
		 * Public method for access api.
		 * This method dynamically calls the method based on the query string
		 *
		 */
		public function processApi(){
			// echo 'php sucks bad';
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			echo '<pre>'; var_dump($_REQUEST).' <- var_dump(_REQUEST)'; echo '</pre>';
			echo "mod_rewrite has converted your request to: <strong>".$func."</strong><br/>";
			if((int)method_exists($this,$func) > 0) {
				echo 'yep, '.$func.' is a function in this class <br/><br/>';
				$this->$func();
				}
			else {
				echo 'throwing a 404 because you asked for the non-existent function, <strong>'.$func.'</strong><br/>';
				//echo print_r($_COOKIE['__utma']);
				//$this->response('',404);	// If the method not exist within this class, response would be "Page not found".
				}
		}

		private function sites(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			// echo 'fer cryin out loud...really';
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			////MANUAL
			$connectString_orbis = "host=orbis-dev.stanford.edu dbname=orbis user=webapp password=sl1ppy";
			$dbconn = pg_connect($connectString_orbis) or die;
			if (!$dbconn) {
				handleError('Not connecting to the database');
			}
			//$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, \"PLPATH\", //\"Contributo\", isport, rank as relative_size
			//	FROM o_sites WHERE displayed = 1");
			$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, isport, 
			rank as relative_size, \"Contributo\" FROM o_sites WHERE displayed = 1 ORDER BY name");	
			
			//$rs = pg_query($dbconn, 'SELECT * FROM v_osites');
			if(pg_num_rows($rs) > 0){
				while ($row = pg_fetch_row($rs)) {
					echo "$row[0] $row[1] $row[2] $row[3] $row[4] $row[5] $row[6]<br/>";
				}
				//$result = array();
				//while($rlt = pg_fetch_array($rs,PGSQL_ASSOC)){
				//	$result[] = $rlt;
				//}
				// If success everything is good send header as "OK" and return list of users in JSON format
				//$this->response($this->json($result), 200);
			}
			$this->response('',204);	// If no records "No Content" status
		}
		private function site(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			// echo 'fer cryin out loud...really';
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			////MANUAL
			$connectString_orbis = "host=orbis-dev.stanford.edu dbname=orbis user=webapp password=sl1ppy";
			$dbconn = pg_connect($connectString_orbis) or die;
			if (!$dbconn) {
				handleError('Not connecting to the database');
			}
			//$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, \"PLPATH\", //\"Contributo\", isport, rank as relative_size
			//	FROM o_sites WHERE displayed = 1");
			$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, isport, 
			rank as relative_size, \"Contributo\" FROM o_sites WHERE displayed = 1 AND objectid = .$osite = ". $osite." ORDER BY name");	
			
			//$rs = pg_query($dbconn, 'SELECT * FROM v_osites');
			if(pg_num_rows($rs) > 0){
				while ($row = pg_fetch_row($rs)) {
					echo "$row[0] $row[1] $row[2] $row[3] $row[4] $row[5] $row[6]<br/>";
				}
				//$result = array();
				//while($rlt = pg_fetch_array($rs,PGSQL_ASSOC)){
				//	$result[] = $rlt;
				//}
				// If success everything is good send header as "OK" and return list of users in JSON format
				//$this->response($this->json($result), 200);
			}
			$this->response('',204);	// If no records "No Content" status
		}

		/*private function dam(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			// echo 'fer cryin out loud...really';
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			////MANUAL
			$connectString_orbis = "host=orbis-dev.stanford.edu dbname=orbis user=webapp password=sl1ppy";
			$dbconn = pg_connect($connectString_orbis) or die;
			if (!$dbconn) {
				handleError('Not connecting to the database');
			}
			//$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, \"PLPATH\", //\"Contributo\", isport, rank as relative_size
			//	FROM o_sites WHERE displayed = 1");
			$rs = pg_query($dbconn, "SELECT objectid, label AS name, st_x(the_geom) as xcoord, st_y(the_geom) as ycoord, isport, 
			rank as relative_size FROM o_sites WHERE displayed = 1 ORDER BY name");	
			
			//$rs = pg_query($dbconn, 'SELECT objectid, label AS name FROM v_osites');
			if(pg_num_rows($rs) > 0){
				//while ($row = pg_fetch_row($rs)) {
					//echo $row;
				//	echo "$row[0] $row[1] $row[2] $row[3] <br/>";
				//}
				$result = array();
				while($rlt = pg_fetch_array($rs,PGSQL_ASSOC)){
					$result[] = $rlt;
				}
				// If success everything is good send header as "OK" and return list of users in JSON format
				$this->response($this->json($result), 200);
			}
			$this->response('',204);	// If no records "No Content" status
		}	*/
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>