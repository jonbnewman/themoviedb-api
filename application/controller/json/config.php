<?php
class config {
  function _index() {
    App::set( 'json', json_encode( App::$Model->TheMovieDB->getConfig() ) );
    App::mode( 'json' );
    App::loadView( '/json.view' );
  }
}
?>