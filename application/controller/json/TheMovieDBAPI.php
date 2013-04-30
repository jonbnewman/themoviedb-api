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
    $idList = array();

    if( is_numeric( $_GET['id'] ) ) {
      $idList[] = $_GET['id'];
    } else if( is_array( $_GET['id'] ) ) {
      foreach( $_GET['id'] as $id ) {
        if( is_numeric( $id ) ) {
          $idList[] = $id;
        }
      }
    }

    if( count( $idList ) ) {
      App::set( 'json', json_encode( App::$Model->TheMovieDB->getMovieList( $idList ) ) );
    } else {
      App::set( 'json', json_encode( array( 'error' => 'Invalid query string' ) ) );
    }

    App::mode( 'json' );
    App::loadView( '/json.view' );
  }
}
?>