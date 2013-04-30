<?php
class error_404 {
	function _index( $controller ) {
    App::set('bodyView', '/error404.view');
    App::loadView( '/main.view' );
	}
}
?>