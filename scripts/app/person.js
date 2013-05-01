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

      this.active.subscribe(function( state ) {
        if( state === true ) {
          postal.publish({
            channel: 'main',
            topic: 'load:movies',
            data: person.id()
          });
        }
      });
    };

    Person.prototype.activate = function() {
      postal.publish({
        channel: 'people',
        topic: 'deactivate:all'
      });

      postal.publish({
        channel: 'people',
        topic: 'activated:person',
        data: this
      });

      this.active( true );
    };

    return Person;
  }
);