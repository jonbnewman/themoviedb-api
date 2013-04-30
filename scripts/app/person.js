define([
  "jquery", "knockout", "postal" ],
  function( $, ko, postal ) {

    var Person = function( personData ) {
      var person = this;

      this.name = ko.observable( personData.name );
      this.id = ko.observable( personData.id );
      this.active = ko.observable( personData.active );

      postal.subscribe({
        channel: 'people',
        topic: 'deactivate:all',
        callback: function() {
          person.active( false );
        }
      });
    };

    Person.prototype.loadMovies = function() {
      postal.publish({
        channel: 'people',
        topic: 'deactivate:all'
      });
      this.active( true );
    };

    return Person;
  }
);