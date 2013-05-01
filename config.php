<?php
// require_once('FirePHPCore/FirePHP.class.php');
// $firephp = FirePHP::getInstance(true);

/**
 * Root path to application
 */
define( 'ROOT', __DIR__ );

/**
 * API Key for TMDB
 */
define('API_KEY', 'a0c930e0998ae0fcfe722333f6721c53');

/**
 *	Base URI for this application
 */
define( 'BASE_URI', '/' );

// Default controller for this application
define( 'DEFAULT_CONTROLLER', 'index' );

// If no $bodyView is supplied for the container main.view, use this one.
// main.view( header -> [ $bodyView / DEFAULT_BODY_VIEW ] -> footer )
define( 'DEFAULT_BODY_VIEW',  '/pages/default.view' );

// true = generate debugging information
define( 'DEBUG',  false );

// define the redis database we use for this application
define( 'REDIS_HOST', '127.0.0.1' );
define( 'REDIS_DB', 100 );

// Redis
try {
  $redis = new Redis();
  $redis->connect( REDIS_HOST );
  // ...select / choose the database specific to this application
  $redis->select( REDIS_DB );
} catch(RedisException $e) {
  if( DEBUG ) {
    echo $e->getMessage();
  }
  die('Could not connect to redis.');
}

session_start();
?>