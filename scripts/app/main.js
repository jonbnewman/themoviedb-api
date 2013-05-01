define([
  "jquery", "postal", "knockout", "app/person", "app/movie" ],
  function( $, postal, ko, Person, Movie ) {

    var Main = function( opt ) {
      var main = this;
      var options = opt || {};

      this.numPeopleResults = ko.observable();
      this.numPeoplePages = ko.observable();
      this.person = ko.observableArray();
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
          main.stopInTransitRequest();
          main.searchingMovies( true );
          main.movies.removeAll();

          main.reqInTransit = $.ajax({
            method: 'get',
            url: '/json/TheMovieDBAPI/movieSearch/' + id,
            dataType: 'json'
          });

          main.reqInTransit.then(function( movies ) {
            main.movies.removeAll();

            for( var year in movies ) {
              main.movies.push( new Movie( $.extend( true, {}, movies[ year ], { config: main.config } ) ) );
            }
          }).always(function() {
            main.searchingMovies( false );
            // main.reqInTransit = undefined;
          });
        }
      });

      postal.subscribe({
        channel: 'people',
        topic: 'activated:person',
        callback: function( person ) {
          main.person.removeAll();
          main.person.push( person );
        }
      });

      // load the config from tmdb.org so we can generate poster image URLs correctly
      $.ajax({
        method: 'get',
        url: '/json/config',
        dataType: 'json'
      }).then(function( config ) {
        main.config = config;
      });
    };

    Main.prototype.stopInTransitRequest = function() {
      if( this.reqInTransit !== undefined && typeof this.reqInTransit.abort === 'function' ) {
        this.reqInTransit.abort();
      }

      this.searchingActors( false );
      this.searchingMovies( false );
    };

    Main.prototype.searchActors = function() {
      var main = this;
      var params = ko.toJS( this.form );

      if( this.checkValidation( params ) !== false ) {
        this.stopInTransitRequest();
        this.searchingActors( true );
        this.people.removeAll();
        this.movies.removeAll();
        this.person.removeAll();
        main.numPeopleResults( undefined );
        main.numPeoplePages( undefined );

        main.reqInTransit = $.ajax({
          method: 'get',
          url: '/json/TheMovieDBAPI/personSearch',
          dataType: 'json',
          data: params
        });

        main.reqInTransit.then(function( data ) {
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
          // main.reqInTransit = undefined;
        });
      }
    };

    return Main;
  }
);