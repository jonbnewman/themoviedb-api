"use strict";

require.config({
  baseUrl: "/scripts",
  shim: {
    'postal': {
      deps: ['underscore'],
      exports: 'postal'
    },
    'underscore': {
      exports: '_'
    }
  },
  paths: {
    "underscore": "lib/underscore-min",
    "knockout":   "lib/knockout",
    "postal":     "lib/postal",
    "util":       "lib/util",
    "bindings":   "app/bindingHandlers"
  },
  waitSeconds: 15
});

require([
  "jquery", "knockout", "app/main", "util", "bindings" ],
  function( $, ko, Main, util ) {
    window.main = new Main();
    ko.applyBindings( window.main );

    $('.focus').focus();
  }
);