<?php
/**
 *	Bare bones MVC style 'framework'
 *		@author Jonathan Newman
 *		@version 0.0.1 6-18-2012
 *		
 *		@todo
 *			- Finish documentation
 */
class App {
	/**
	 *	Available (handled) exception codes
	 */
	const ERROR_FATAL = 0,
	      ERROR_404   = 404;

	/**#@+
	 *	@access protected
	 */
	/**
	 *	Path types used to determine what default paths are configurable.
	 */
	protected static $pathTypes = array('models', 'views', 'controllers');

	/**
	 *	Default paths for models/views/controllers.
	 */
	protected static $paths = array('models'      => 'application/model',
	                                'views'       => 'application/view',
	                                'controllers' => 'application/controller',
	                                'libraries'   => 'application/library');

	/**
	 *	Defined routes (overrides default routes within the controllers path)
	 */
	protected static $routes = array(404 => 'errors/error_404');

	/**
	 *	Base URI for application
	 */
	protected static $baseURI = '/';
	
	/**
	 *	Base path for application
	 */
	protected static $basePath = '';
	/**#@-*/

	/**#@+
	 *	@access private
	 */
	/**
	 *	Flag indicating internal re-routing
	 */
	private static $internalRoute = false;

	/**
	 *	Internal register used to pass data between controllers/views/models
	 */
	private static $data = array();

	/**
	 *	Hidden internal register used to pass data between controllers/models
	 */
	private static $hiddenData = array();
	/**#@-*/

	private static $count = 0;

	/**
	 *	Internal model registery, used to store loaded module objects
	 *
	 *	@access public
	 */
	public static $Model;

	/**
	 *	Class constructor/initializer
	 *	@access public
	 */
	public static function init() {
		// initialize exception handler
		set_exception_handler( function($exception) { App::exceptionHandler($exception); } );
		self::$Model = new \stdClass; // initialize blank object
	}

	/**
	 *	Uncaught exception handler, used to backtrace to initial source of error to display actual file and line #
	 *
	 *	@param Exception Exception to handle
	 */
	public static function exceptionHandler( $exception ) {
		if( $exception->getCode() !== 0 ) {
			try {
				self::$internalRoute = true;
				self::route( self::resolveURI( $exception->getCode(), false ) );
			}
			catch(Exception $e) {
				self::dumpError( $e->getMessage(), $e->getTrace() );
			}
		}
		else {
			self::dumpError( $exception->getMessage(), $exception->getTrace() );
		}
	}

	/**
	 *	Very ugly error dump for traces/exceptions
	 *	
	 *	@param string $msg   Message to dump
	 *	@param array  $trace Array containing the current trace for the supplied $msg
	 */
	protected static function dumpError($msg, $trace) {
		if ( !self::$internalRoute ) // if it isn't an internal route, all we care about is the initial starting point in the trace (where the error started)
			$trace = end($trace);
		else // internal route, we want the latest position in the trace
			$trace = $trace[0];

		echo "<br /><b>Error</b>: {$msg} source: <b>{$trace['file']}</b> on line <b>{$trace['line']}</b><br />";
	}

	/**
	 *	Assign a value to the internal data store, used to pass data between controllers
	 *	If hidden is set to true, the value will not be passed on to any views, and is only stored internally and referenced with the same ::get() call
	 *
	 *	@param string $key    String which will be the 'key' for the key => value pair to be saved
	 *	@param string $value  String specifying the value of $key
	 *	@param bool   $hidden Boolean value indicating whether or not this value is to be supplied to any resulting view's called, or whether this is an internal value only
	 */
	public static function set( $key, $value = '', $hidden = false ) {
		if( is_array($key) ) {
			foreach($key as $subKey => $y)
				self::set( $subKey, $y , $hidden );
		}
		else {
			if( $hidden )
				self::$hiddenData[$key] = $value;
			else
				self::$data[$key] = $value;
		}
	}

	/**
	 *	Get a value from the internal data store, used to pass data between controllers
	 *
	 *  @param  string $key Key to be returned from internal data store
	 *	@return mixed Data for $key value
	 */
	public static function get( $key = '' ) {
		return strlen($key) ? (isset(self::$data[$key]) ? self::$data[$key] : self::$hiddenData[$key]) : self::$data;
	}

	/**
	 *	Define relative base directories for models, views, and controllers.
	 *
	 *	@param string $pathType accepts one of three values: models, views, controllers
	 *	@param string $path path (relative or absolute) for $pathType
	 */
	public static function setPath( $pathType, $path ) {
		$path = trim( $path, '/' );
		if(in_array( $pathType, self::$pathTypes ))
			self::$paths[$pathType] = $path;
		else
			throw new \Exception("Invalid pathType '{$pathType}' supplied to ".__CLASS__."::setPath()", self::ERROR_FATAL);
	}

	/**
	 *	Set the base path for the application
	 *
	 *	@param string $basePath Full path to the base of the application
	 */
	public static function setBasePath( $basePath ) {
		if( is_dir($basePath) )
			self::$basePath = $basePath;
		else
			throw new \Exception("Invalid basePath '{$basePath}' supplied to ".__CLASS__."::setBasePath()", self::ERROR_FATAL);
	}

	/**
	 *	Get relative base directory for models, views, and controllers.
	 *
	 *	@param string $pathType pathType (typically models/views/controllers) path to retrieve
	 */
  public static function getPath( $pathType, $base = false ) {
    if (isset(self::$paths[$pathType])) {
      return ($base ? (self::$basePath.'/') : '').self::$paths[$pathType];
    } else {
			throw new \Exception("Undefined pathType '{$pathType}' supplied to ".__CLASS__."::getPath()", self::ERROR_FATAL);
    }
	}

	/**
	 *	Define specific route
	 *	@example App::route('/notWhatYouThink/', 'misc/foobar') makes http://foo.com/notWhatYouThink/ route through controller misc/foobar
	 *
	 *	@param string $uri        URI of resource to route
	 *	@param string $controller Controller to route $uri to
	 */
	public static function setRoute( $uri, $controller ) {
		$controller = self::fileNameSanitize( $controller ); // make sure the controller has the proper extension
		$uri = trim($uri, "/");

		if( file_exists( self::$basePath.'/'.self::$paths['controllers'].'/'.$controller ) ) {
			self::$routes[$uri] = $controller;
		}
		else {
			throw new \Exception("Specified route {$controller} does not exist", self::ERROR_FATAL);
		}
	}

	/**
	 *	Define base URI for the application.
	 *	It is assumed the base URI is '/', this function is used to change that.
	 *
	 *	@param string $base Relative (to /) offset for the application
	 */
	public static function setBaseURI( $base ) {
		self::$baseURI = '/'.trim($base, '/');
	}

	/**
	 *	Resolve URI to relative controller path
	 *
	 *	@param string $uri          URI to resolve into designated controller path
	 *	@param bool   $checkBaseURI Flag indicating whether or not we should check to make sure the baseURI is included in $uri, and supplement it if desired
	 *	@return       array         Array containing the controller and arguments (or an exception is thrown on error)
	 */
	public static function resolveURI( $uri, $checkBaseURI = true ) {
		// get rid of the query string if it exists
		$uri = preg_replace( '/\?.*$/', '', $uri );

		// make sure the supplied URI begins with our baseURI
		if( $checkBaseURI ) {
			if( !strncmp( $uri, self::$baseURI, strlen( self::$baseURI ) ) )
				$uri = substr( $uri, strlen(self::$baseURI) );
			else
				throw new \Exception("Base URI is invalid (".self::$baseURI." compared to {$uri}), set the correct baseURI with ".__CLASS__."::setBaseURI()", self::ERROR_FATAL);
		}
		$uri = trim($uri, '/');

		// Check if we have a user defined route for this URI
		if ($route = self::crawlRoute($uri)) {
			return array('controller' => $route['controller'], 'arguments' => $route['arguments']);
		}

		// crawl the default controller directory looking for the appropriate controller
		if ($controller = self::crawl($uri)) { // controller exists in the default controller directory
			return array('controller' => $controller['controller'], 'arguments' => $controller['arguments']);
		} else { // 404, we cannot locate the controller for this URI
			self::set('uri', $uri);
			throw new \Exception("Resource not found", self::ERROR_404);
		}

		return false;
	}

	public static function castController( $controller ) {
		if( !is_array($controller) ) {
			$controller = array('controller' => $controller, 'arguments' => array());
		}

		// if the controller is an integer we need to route internally through handler for specified http code (ex: 404)
		if( is_int($controller['controller']) ) {
			$controller = array('controller' => App::getPath('controllers', true).'/'.self::$routes[$controller['controller']], 'arguments' => $controller['arguments']);
		}

		return $controller;
	}

	public static function validController( $controller ) {
		$controller = self::castController( $controller );
		$controller['controller'] = ( '/'.self::fileNameSanitize($controller['controller']) ); // make sure the controller has the proper extension

		return ( is_file($controller['controller']) ? $controller : false );
	}

	/**
	 *	Route execution through specified controller
	 *
	 *	@example controllerFile -> calls controllerFile->_index()
	 *	@example controllerFile/method/argument1/argument2 -> calls controllerFile->method(array('argument1', 'argument2'))
	 *
	 *	@param  mixed Either an array (if we need to pass arguments to the controller) or a string indicating which controller to route execution through
	 *	@return bool  Returns false if an error code was supplied, or throws an exception on error
	 */
	public static function route( $controller ) {
		if ( ( $controller = self::validController( $controller ) ) !== false ) {
			/**
			 *	At this point we have a valid controller to load. The controller is encapsulated within a closure and the same-name class within
			 *		it is called either with the _index() method (if no trailing arguments are supplied), or with the first supplied argument as the method
			 */
			$_controller = $controller;
			$_controller['class'] = __NAMESPACE__.'\\'.self::fileNameUnsanitize($_controller['controller']); // add namespace path while stripping off the extension
			
			$closure = function() use ( $_controller ) {
				require_once($_controller['controller']); // jump execution to the controller
				$controllerClass = new $_controller['class'];

				if( count( $_controller['arguments'] ) ) {
					$method = array_shift($_controller['arguments']); // first argument contains the method name, we don't need to send that to the method
					$_controller = array_merge($_controller, array('_REQUEST' => $_REQUEST) ); // supply the _REQUEST variable to the controller in a generic form
          
          if( is_callable( array( $controllerClass, $method) ) ) {
            $controllerClass->$method($_controller['arguments']);
          } else {
            if( is_callable( array( $controllerClass, '_404' ) ) ) {
              $controllerClass->_404( $_controller['arguments'] );
            } else {
              $controllerClass->_index( $_controller['arguments'] );
            }
          }

				} else { // call the 'index' method by default
					$controllerClass->_index( $_controller );
				}
			};

			$closure(); // route through/call the encapsulated controller
		} else {
			throw new \Exception("Invalid controller path '{$controller['controller']}'", self::ERROR_FATAL);
		}
	}

	/**
	 *	Add extension to file name if necessary.
	 *
	 *	@param  string $file Make sure the file includes .php at the end
	*	@return string File name after sanitation
	 */
	protected static function fileNameSanitize( $file ) {
		$file = trim( $file, '/' );
		if( !preg_match("/^.*\.php$/", $file) ) { // ensure this controller contains .php at the end
			$file .= '.php';
		}

		return $file;
	}

	/**
	 *	Remove extension from file name.
	 *
	 *	@param  string $file Remove the .php extension from a supplied $file name
	 *	@return string File name after un-sanitation
	 */
	protected static function fileNameUnsanitize( $file ) {
		return str_replace( '.php', '', end( explode( '/', $file ) ) );
	}

	/**
	 *	Load a specified model.
	 *	The model must contain a class of the same name as the model file (before any periods).
	 *
	 *	@example App::loadModel('test.model.php'); will create an object called $test (unless specified using $objectName) using the class 'test' which is inside that model file.
	 *	@param string $model Path to model we want to load
	 *	@param string $objectName Specify the name to give the resulting Model object
	 */
	public static function loadModel($model, $objectName = '') {
		if(is_array($model)) { // load a list of models
			foreach( $model as $key => $m ) {
				self::loadModel( $m, is_numeric($key) ? '' : $key );
      }

			return true;
		}

		$model = self::fileNameSanitize($model); // make sure the proper extension is on the file

		$modelName = array_shift(explode('.', end(explode('/', $model))));
		$modelPath = self::$basePath.'/'.self::$paths['models'].'/'.$model;
		if( is_file($modelPath) ) {
			require_once($modelPath); // load the model

			if( $objectName !== false ) {
				if( !strlen($objectName) ) // if the object name isn't supplied we just use the class name
					$objectName = $modelName; // no object name supplied, lets fill in the blank

				global $$objectName; // make this model object exist in the global namespace
				$modelName = __NAMESPACE__.'\\'.$modelName; // provide full namespace path to the model
				self::$Model->$objectName = new $modelName(); // instantiate the model in the global object
			}
		}
		else {
			throw new \Exception("Invalid model path '{$model}'", self::ERROR_FATAL);
		}
	}

	/**
	 *	Load a library (essentially simple require_once).
	 *
	 *	@example App::loadLibrary('test.library'); // (require_once's library file from %APPDIR%/library/test.library.php)
	 *	@param string $library Path to library we want to load
	 */
	public static function loadLibrary($library) {
		if(is_array($library)) { // load a list of models
			foreach( $library as $lib ) {
				self::loadLibrary( $lib );
      }

			return true;
		}

		$library = self::fileNameSanitize($library); // make sure the proper extension is on the file
		$libraryPath = self::$basePath.'/'.self::$paths['libraries'].'/'.$library;

		if( is_file($libraryPath) ) {
			require_once($libraryPath); // load the model
		}
		else {
			throw new \Exception("Invalid library path '{$library}'", self::ERROR_FATAL);
		}
	}

	public static function validView( $view ) {
		$view = self::$basePath.'/'.self::$paths['views'].'/'.self::fileNameSanitize( $view ); // make sure the proper extension is on the file

		return ( is_file($view) ? $view : false);
	}

	/**
	 *	Load a specific view.
	 *
	 *	@param string $view Path to view we wish to load
	 */
	public static function loadView($view) {
		if( ($_viewFile = self::validView($view)) !== false ) {
			$closure = function() use ($_viewFile) {
				extract(App::get());
				require_once($_viewFile); // jump execution to the view
			};
			$closure();
		}
		else
			throw new \Exception("Invalid view path '{$view}'", self::ERROR_FATAL);
	}

	/**
	 *	Crawl the default controller directory and find the appropriate controller for a given URI
	 *	
	 *	@param string $uri URI we wish to attempt to 'crawl'
	 *	@param int    $pieces Specify the number of 'pieces' we want from the supplied $uri
	 *	@return       Array containing the controller and arguments if successfull, false on failure
	 */
	private static function crawl($uri, $pieces = 1) {
		if( !is_array($uri) ) {
			$uri = explode( '/', $uri );
    }

		$uriSlice = implode( '/', array_slice( $uri, 0 , $pieces ) );
		if( $pieces <= count( $uri ) ) {
			if( is_dir( self::$basePath.'/'.self::$paths['controllers'].'/'.$uriSlice ) ) {
				return self::crawl( $uri, $pieces + 1 );
			} else if( is_file( $controllerPath = self::$basePath.'/'.self::$paths['controllers'].'/'.self::fileNameSanitize( $uriSlice ) ) ) {
				return array( 'controller' => $controllerPath, 'arguments' => array_slice( $uri, $pieces ) );
			}
		} else {
			// first check for default 'index' controller in the full path
			if( is_file( $controllerPath = self::$basePath.'/'.self::$paths['controllers'].'/'.implode('/', $uri).'/'.self::fileNameSanitize( 'index' ) ) ) {
				$uri[] = self::fileNameSanitize( 'index' );
				return array( 'controller' => $controllerPath, 'arguments' => array() );
			}
			return false;
		}
	}

	/**
	 *	Crawls the user-defined routes (App::setRoute()) looking for the appropriate controller for the given $uri
	 *
	 *	@param string $uri URI we wish to attempt to 'crawl'
	 *	@param int    $pieces Specify the number of 'pieces' we want from the supplied $uri
	 *	@return       false on failure
	 */
	private static function crawlRoute( $uri, $pieces = 1 ) {
		if( !is_array($uri) ) {
			$uri = explode('/', $uri);
    }

		$uriSlice = implode('/', array_slice($uri, 0 , $pieces));
		if( array_key_exists($uriSlice, self::$routes) ) {
			// found route
			$arguments = array_slice($uri, $pieces);
			return array('controller' => self::$basePath.'/'.self::$paths['controllers'].'/'.self::$routes[$uriSlice], 'arguments' => array_slice($uri, $pieces));
		}
		else if( $pieces < count($uri) ) {
			return self::crawlRoute($uri, $pieces+1);
    }

		return false;
	}

	/**
	 *	Process request
	 */
	public static function run() {
		// resolve the current URI to its controller and route to/through it
		if( $controller = self::resolveURI($_SERVER['REQUEST_URI']) )
			self::route($controller);
	}
  
  /**
   * Redirect the user to another location
   */
  public static function redirect( $url ) {
    if( $url[0] == '/' || !strncmp( $url, 'http://', strlen('http://') ) || !strncmp( $url, 'https://', strlen('https://') )  ) {
      header( "Location: {$url}" );
    } else {
      header( "Location: ".(BASE_URI.$url) );
    }
    exit;
  }
	
	/**
	 *	Set the headers to reflect the desired 'mode'
	 *
	 *	@param string $mode 'Mode' or type of data we are sending
	 */
	public static function mode( $mode = 'normal' ) {
		switch( $mode ) {
			case 'json':
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Content-type: application/json');
				break;

			case 'normal':
			default:
				break;
		}
	}
} App::init();

class AppController {
	public function _index() {
	}
  
  public function _404() {
    App::route( 404 );
  }
}
?>