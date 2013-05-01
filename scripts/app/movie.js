define([
  "jquery", "knockout", "postal" ],
  function( $, ko, postal ) {

    var Movie = function( movieData ) {
      var movie = this;

      this.id = ko.observable( movieData.id );
      this.title = ko.observable( movieData.title );
      this.character = ko.observable( movieData.character );
      this.poster = ko.observable( movieData.poster_path );
      this.releaseDate = ko.observable( movieData.release_date || 'N/A' );
    };

    return Movie;
  }
);