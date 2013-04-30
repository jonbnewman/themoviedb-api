<?php
class TheMovieDBAPI {
	function personSearch() {
    $queryValidation = '/^[a-zA-Z ]+$/';
    if( preg_match( $queryValidation, $_GET['queryString'] ) ) {
      App::set( 'json', json_encode( App::$Model->TheMovieDB->getPersonList( $_GET['queryString'] ) ) );
    } else {
      App::set( 'json', json_encode( array( 'error' => 'Invalid query string' ) ) );
    }

    App::mode( 'json' );
		App::loadView( '/json.view' );
	}

  function movieSearch( $id ) {
    $id = $id[0];

    if( is_numeric( $id ) ) {
      App::set( 'json', json_encode( App::$Model->TheMovieDB->getMovieList( $id ) ) );
    } else {
      App::set( 'json', json_encode( array( 'error' => 'Invalid query string' ) ) );
    }

    App::mode( 'json' );
    App::loadView( '/json.view' );
  }
}
?>