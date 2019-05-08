(function($) {
  $.widget('detalika.firstLetterUppercaseField', {
 
    _create: function() {
 
        processInput = function(field) {
            var value = field.val();
            if (value.length === 0) {
                return;
            }
            var result = value.substr(0, 1).toUpperCase() + value.substr(1);
            field.val(result);
        };
 
        var field = this.element;
        
        $(field).on('input', function() {
            processInput(field);
        });
 
        processInput(field);
    },
 
  });
}(jQuery));