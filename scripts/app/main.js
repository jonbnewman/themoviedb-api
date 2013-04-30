define([
  "jquery", "postal", "knockout", "app/TheMovieDB" ],
  function( $, postal, ko, TheMovieDB ) {

    var Main = function( opt ) {
      var main = this;
      var options = opt || {};

      this.movieList = ko.observableArray();
      this.form = {
        queryString: ko.observable()
      };
    };

    Main.prototype.runQuery = function() {

      console.log( ko.toJS( this.form ) );
    };

    return Main;
  }
);