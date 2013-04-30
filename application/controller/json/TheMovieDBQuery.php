<?php
class TheMovieDBQuery {
	function _index() {
    // App::set( 'json', json_encode( array( 'data' => App::$Model->TheMovieDB->getMovieList() ) ) );
    App::mode( 'json' );
    
		App::loadView( '/json.view' );
	}
}
?>