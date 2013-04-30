<?php
// require_once('FirePHPCore/FirePHP.class.php');
// $firephp = FirePHP::getInstance(true);

/**
 * Root path to application
 */
define( 'ROOT', __DIR__ );

/**
 * Twitter username to stream
 */
define('TWITTER_USERNAME', 'overfocus');

/**
 * Github username to stream
 */
define('GITHUB_USERNAME', 'reflectiveSingleton');

/**
 * Reddit username to stream
 */
define('REDDIT_USERNAME', 'reflectiveSingleton');

/**
 *	Base URI for this application
 */
define( 'BASE_URI', '/' );

// Default controller for this application
define( 'DEFAULT_CONTROLLER', 'index' );

// If no $bodyView is supplied for the container main.view, use this one.
// main.view( header -> [ $bodyView / DEFAULT_BODY_VIEW ] -> footer )
define( 'DEFAULT_BODY_VIEW',  '/pages/default.view' );

define( 'DBNAME', 'overfocus' );
define( 'DBUSER', 'overfocus' );
define( 'DBPASS', '34ifmv9ccbre4mf4idvlwdc4d!' );

// true = generate debugging information
define( 'DEBUG',  false );

// define the redis database we use for this application
define( 'REDIS_HOST', '127.0.0.1' );
define( 'REDIS_DB', 10 );

/**
 * Connect to the databases (MySQL + Redis)
 */

// MySQL
try {
  $db = new PDO("mysql:host=localhost;dbname=".DBNAME, DBUSER, DBPASS);
} catch(PDOException $e) {
  if( DEBUG ) {
    echo $e->getMessage();
  }
  die('Could not connect to MySQL database.');
}

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