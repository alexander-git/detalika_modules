(function($) {
  $.widget('detalika.clientTypeProcessingScript', {
 
    options : {
        clientTypeSelector : '[data-select="clientType"]',
        carServiceNameContainerSelector : '[data-select="carServiceNameContainer"]',
        carServiceClientTypeId : null,
        carParkClientTypeId : null
    },
 
    _create: function() {
        var Script = this._getScriptObject();
        (new Script(this.options)).run();
    },
    
    _getScriptObject : function() {
         var Constr = function(options) {
            this._sel = {};
            this._sel.clientType = options.clientTypeSelector;
            this._sel.carServiceNameContainer= options.carServiceNameContainerSelector;

            this._carServiceClientTypeId = parseInt(options.carServiceClientTypeId);
            this._carParkClientTypeId = parseInt(options.carParkClientTypeId);
        };
    
        Constr.prototype.run = function() {
            var that = this;
            $(this._sel.clientType).on('change', function() {
                that._clientTypeChange();
            });

            this._clientTypeChange();
        };
    
        Constr.prototype._clientTypeChange = function() {
            var value = parseInt($(this._sel.clientType).val());

            var carServiceNameContainer = $(this._sel.carServiceNameContainer);
            if (typeof value === 'number') {
                if (
                    value === this._carServiceClientTypeId || 
                    value === this._carParkClientTypeId
                ) {
                    carServiceNameContainer.show();
                } else {
                    carServiceNameContainer.hide();
                }
            } else {
                carServiceNameContainer.hide();
            }
        };
        
        return Constr;
    }
 
  });
}(jQuery));