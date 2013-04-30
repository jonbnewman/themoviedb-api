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

      this.validation = {
        check: {
          queryString: /^[a-zA-Z]+$/
        },
        state: {
          queryString: ko.observable( false )
        }
      };
    };

    Main.prototype.runQuery = function() {
      var main = this;
      var params = ko.toJS( this.form );

      // run through our listed validation checks
      _.each( this.validation.check, function( regex, fieldName ) {
        if( params[ fieldName ] !== undefined ) {
          main.validation.state[ fieldName ]( !regex.test( params[ fieldName ] ) );
        }
      });
    };

    return Main;
  }
);