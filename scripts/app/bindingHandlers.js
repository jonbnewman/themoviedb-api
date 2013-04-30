define([
  "jquery", "knockout" ],
  function( $, ko ) {

    /**
     * Custom binding handler to register the element which is bound against inside the model
     */
    ko.bindingHandlers.registerElement = {
      init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        viewModel.$el = viewModel.$el || {};
        viewModel.$el[ ko.utils.unwrapObservable( valueAccessor() ) ] = $(element);
      }
    };

    /**
     * class binding handler, pass in an observable to a binding and the string value of that will be applied as a class to the element
     * Source: https://github.com/SteveSanderson/knockout/wiki/Bindings---class
     */
    ko.bindingHandlers['class'] = {
      'update': function(element, valueAccessor) {
        if (element['__ko__previousClassValue__']) {
          $(element).removeClass(element['__ko__previousClassValue__']);
        }
        var value = ko.utils.unwrapObservable(valueAccessor());
        $(element).addClass(value);
        element['__ko__previousClassValue__'] = value;
      }
    };

  }
);