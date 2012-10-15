<?php

       /*
	* Example Controller Using _bind
	* Author: Weston Watson - Oct 14, 2012
	* 
	* This example will use ?sql by default.
	* Please Dive Into APP_ROOT/vendor/php-activerecord
	*
	*/

$routes = array(
	'/' => 'index_page',
	'/help.html' => 'help_page',
	'/log/[a-zA-Z0-9].html' => 'logs_page'
	);	
	

       /*
	* With in each class, Several Methods are called.
	* Depending on the Type of request; GET, POST, etc
	*/ 

class index_page { //naming convention completely up to you :)
	function GET(){
		_bind::view('header','index','footer'); 
	}
}

class help_page { //this is called when help.html is requested
	function GET(){
		_bind::view('header','help','footer'); 
	}	
}

class logs_page { //interacting with URI request example
	function GET(){
		_bind::view('header');
		echo "<pre>".print_r(_bind::map_request_keys_and_values(),true)."</pre>";
		_bind::view('footer');
	}
}


