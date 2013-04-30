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

      this.checkValidation = function( params ) {
        var valid = true;

        _.each( this.validation.check, function( regex, fieldName ) {
          if( params[ fieldName ] !== undefined ) {
            var errorState = !regex.test( params[ fieldName ] );
            main.validation.state[ fieldName ]( errorState );

            if( errorState === true ) {
              valid = false;
            }
          }
        });

        return valid;
      };

      this.form.queryString.subscribe( function() {
        this.checkValidation( ko.toJS( this.form ) );
      }, this );
    };

    Main.prototype.runQuery = function() {
      var main = this;
      var params = ko.toJS( this.form );
      if( this.checkValidation( params ) !== false ) {
        // do query
      }
    };

    return Main;
  }
);