<?php
define( 'TIMESTART', microtime(true) );
ob_start();

require_once( __DIR__.'/config.php' );
require_once( __DIR__.'/lib/app.class.php' );
include_once( __DIR__.'/application/autoload.php' );

// Define the base path as the directory this front controller is located in
App::setBasePath( __DIR__ );

// Define the base URI this application is located in
App::setBaseURI( BASE_URI );

// define the default controller
App::setRoute( '/', DEFAULT_CONTROLLER );

// Autoload Libaries
if( !empty($__AUTOLOAD['libraries']) ) {
  foreach( $__AUTOLOAD['libraries'] as $library ) {
    App::loadLibrary( $library );
  }
}

// Autoload Models
if( !empty($__AUTOLOAD['models']) ) {
  foreach( $__AUTOLOAD['models'] as $model ) {
    App::loadModel( $model );
  }
}

// and away we go...
App::run();

ob_end_flush(); // end and flush output buffer
?>