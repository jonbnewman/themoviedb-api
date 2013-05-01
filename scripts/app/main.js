define([
  "jquery", "postal", "knockout", "app/person", "app/movie" ],
  function( $, postal, ko, Person, Movie ) {

    var Main = function( opt ) {
      var main = this;
      var options = opt || {};

      this.numPeopleResults = ko.observable();
      this.numPeoplePages = ko.observable();
      this.people = ko.observableArray();
      this.movies = ko.observableArray();
      this.searchingActors = ko.observable( false );
      this.searchingMovies = ko.observable( false );
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

      postal.subscribe({
        channel: 'main',
        topic: 'load:movies',
        callback: function( id ) {
          main.searchingMovies( true );
          main.movies.removeAll();

          return $.ajax({
            method: 'get',
            url: '/json/TheMovieDBAPI/movieSearch/' + id,
            dataType: 'json'
          }).then(function( movies ) {
            main.movies.removeAll();

            for( var year in movies ) {
              main.movies.push( new Movie( movies[ year ] ) );
            }
          }).always(function() {
            main.searchingMovies( false );
          });
        }
      });
    };

    Main.prototype.searchActors = function() {
      var main = this;
      var params = ko.toJS( this.form );
      if( this.checkValidation( params ) !== false ) {
        this.searchingActors( true );
        this.people.removeAll();
        this.movies.removeAll();
        main.numPeopleResults( undefined );
        main.numPeoplePages( undefined );

        return $.ajax({
          method: 'get',
          url: '/json/TheMovieDBAPI/personSearch',
          dataType: 'json',
          data: params
        }).then(function( data ) {
          main.numPeopleResults( data.total_results );
          main.numPeoplePages( data.total_pages );

          if( data.total_results > 0 ) {
            _.each( data.results, function( resultData ) {
              main.people.push( new Person( resultData ) );
            });
          }

          if( main.people().length === 1 ) {
            // pre-select the only result
            main.people()[0].activate();
          }
        }).always(function() {
          main.searchingActors( false );
        });
      }
    };

    return Main;
  }
);