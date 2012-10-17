<?php

    /**
     * _bind
     *
     * _bind was branched from the gluephp project
     * You can find gluephp at http://gluephp.com/
     *
     * > When the URLs are processed:
     *      * delimiter (/) are automatically escaped: (\/)
     *      * The beginning and end are anchored (^ $)
     *      * An optional end slash is added (/?)
     *	    * The i option is added for case-insensitive searches
     *
     * > Example:
     *
     * $urls = array(
     *     '/' => 'index',
     *     '/page/(\d+)' => 'page'
     * );
     *
     * class page {
     *      function GET($matches) {
     *          echo "Your requested page " . $matches[1];
     *      }
     * }
     *
     * _bind::route($urls); //defines routes for actions
     * _bind::view('index',$data); //renders view APP_ROOT/views/index.phtml
     * _bind::view('views/forms/email.phtml',$data); //renders view APP_ROOT/views/forms/email.phtml 
     */

    class _bind {

       /*
	* Author: Weston Watson - Oct 14, 2012
	* renders view, yay!
	* $view may be either 
	* 	a.) A Direct path to that file in which you're rendering or 
	*	b.) simple the name with no path, no file extension (ie- 'index')
	*	c.) An Array of mixed (a and b) - now that's cool!
	*	
	*	Views Location: APP_ROOT/views/*.phtml
	*/

	function __construct(){
		//parse URI/Request and set up Arrays for values
		define("REQUEST_MAP", self::map_request_keys_and_values());
		define("REQUEST", explode('/',$_SERVER['REQUEST_URI']));
	}

	/*
	 * view
	 * 
	 * Author: Weston Watson - Oct 15, 2012
	 * Takes either direct path to view or general view name and loads
	 * it from app/views/VIEW_NAME - also takes an array of either.
	 * 
	 * _bind:view(array('header','app/views/forms/_form.phtml','footer'),$d);
	 */
	
	static function view($view,$data){
	    if (is_array($view)) {
		//multiple views passed
		foreach($view as $views){
			$current_view = strpos($views,'.')!==false ? $views : ('app/views/' . $views . '.phtml');
			echo self::renderPhpToString( $current_view, $data );
		}
	    }elseif(is_string($view)){
		//render just one view passed
		$the_view = strpos($view,'.')!==false ? $view : ('app/views/' . $view . '.phtml');
		echo self::renderPhpToString( $the_view, $data );
	    }else{
		throw new Exception('Invalid View Type Passed');
	    }
	}
																													

       /*
	* Author: Markus Wulftange
	* Link: http://stackoverflow.com/questions/761922/can-you-render-a-php-file-into-a-variable
	*/

	public function renderPhpToString($file, $vars=null){
	    if (!file_exists($file)) return false; //Modified by Weston Watson - Oct 14, 2012
	    
	    if (is_array($vars) && !empty($vars)) {
		extract($vars);
	    }
	    ob_start();
	    include $file;
	    return ob_get_clean();
	}

        /**
         * route
         *
         * was the main static function of the gluephp class.
	 * THIS DOES THE ROUTING!
         *
         * @param   array    	$urls  	    The regex-based url to class mapping
         * @throws  Exception               Thrown if corresponding class is not found
         * @throws  Exception               Thrown if no match is found
         * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
         *
         */

        static function route ($urls,$remove=null) {

            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            $path = $_SERVER['REQUEST_URI'];
	    
	    /*
	     * $remove is used for Testing purposes. Before you set up .htaccess
	     * to remove the /index.php from the URI, you can set $remove to
	     * ignore a particular part of the Request, in my case it would be 
	     * Example _bind::route($routes,'/test/index.php');
	     */
	    
	    if ($remove) $path = str_replace ($remove, '', $path);
	    
	    $found = false;

            krsort($urls); //Why sort? Shouldn't we leave it in the Coder's Order?

            foreach ($urls as $regex => $class) {
                $regex = str_replace('/', '\/', $regex);
                $regex = '^' . $regex . '\/?$';
                if (preg_match("/$regex/i", $path, $matches)) {
                    $found = true;
                    if (class_exists($class)) {
                        $obj = new $class;
                        if (method_exists($obj, $method)) {
                            $obj->$method($matches);
                        } else {
                            throw new BadMethodCallException("Method, $method, not supported.");
                        }
                    } else {
                        throw new Exception("Class, $class, not found.");
                    }
                    break;
                }
            }
            if (!$found) {
                throw new Exception("URL, $path, not found.");
            }
        }

       /* map_request_keys_and_values
	* Author: Weston Watson - Dec 8, 2011
	*
	* this method takes the URI Request and 
	* pairs up keys and values from the URL segments
	* Example DOMAIN/key:value/key:value/key:value/etc...
	*/

	static function map_request_keys_and_values(){ 
		//declare an array of request and add add basic page info 
		$requestArray = array(); 
		$requests = explode('/',$_SERVER['REQUEST_URI']);
		foreach ($requests as $request)
		{ 
			$pos = strrpos($request, ':');
			if($pos >0)
			{
				list($key,$value)=explode(':', $request);
				if(!empty($value) || $value='') $requestArray[urldecode($key)]=urldecode($value);
			}
		}
		return $requestArray ; 
	} 

    }
    
    
?>