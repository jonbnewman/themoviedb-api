define([
  "jquery", "knockout", "postal" ],
  function( $, ko, postal ) {

    var Movie = function( movieData ) {
      var movie = this;

      this.id = ko.observable( movieData.id );
      this.title = ko.observable( movieData.title );
      this.character = ko.observable( movieData.character );
      this.poster = ko.observable( movieData.poster_path );
      this.adult = ko.observable( movieData.adult );
      this.releaseDate = ko.observable( movieData.release_date || 'N/A' );

      this.config = movieData.config;
      this.posterImageURL = ko.computed(function() {
        return this.config.images.base_url + this.config.images.poster_sizes[0] + '/' + this.poster();
      }, this);
    };

    return Movie;
  }
);