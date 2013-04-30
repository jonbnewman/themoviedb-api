define([
  "jquery", "postal", "knockout", "app/person" ],
  function( $, postal, ko, Person ) {

    var Main = function( opt ) {
      var main = this;
      var options = opt || {};

      this.numPeopleResults = ko.observable();
      this.numPeoplePages = ko.observable();
      this.people = ko.observableArray();
      this.form = {
        queryString: ko.observable('')
      };

      this.validation = {
        check: {
          queryString: /^[a-zA-Z ]+$/
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

    Main.prototype.searchActors = function() {
      var main = this;
      var params = ko.toJS( this.form );
      if( this.checkValidation( params ) !== false ) {
        // do query
        return $.ajax({
          method: 'get',
          url: '/json/TheMovieDBAPI/personSearch',
          dataType: 'json',
          data: params
        }).then(function( data ) {
          console.log( data );
          main.people.removeAll();
          main.numPeopleResults( data.total_results );
          main.numPeoplePages( data.total_pages );

          if( data.total_results > 0 ) {
            _.each( data.results, function( resultData ) {
              main.people.push( new Person( resultData ) );
            });
          }
        }).always(function() {

        });
      }
    };

    return Main;
  }
);