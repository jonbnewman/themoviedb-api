<?php
class index extends AppController {
  function _index() {
    App::set('page', 'index');
    App::loadView( '/main.view' );
  }
}
?>