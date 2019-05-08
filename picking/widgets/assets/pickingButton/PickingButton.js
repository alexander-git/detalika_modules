var PickingButton = (function() {
    
    var Constr = function(config) {
        var selector = this._getButtonSelector(config.buttonId);
        this._buttonId = config.buttonId;  
        this._type = config.type;
        this._isPickingOn = config.isPickingOn;
        this._button = $(selector);
        this._backend = new PickingButtonBackend(config);
    };
    
    Constr.prototype.BUTTON_TEXT_ON = 'Подбор идёт';
    Constr.prototype.BUTTON_TEXT_OFF = 'Начать подбор';
    Constr.prototype.CSS_CLASSESS_ON = ['btn', 'btn-success'];
    Constr.prototype.CSS_CLASSESS_OFF = ['btn', 'btn-default'];
        
    Constr.prototype.init = function() {
        var that = this;
        this._button.parent().show();
        this._button.on('click', function() {
            that._buttonClick();
        });
        
        Events.on(this._getStartPickingEventName(), function(event, data) {
            that._onStartPickingEvent(event, data);
        });
    };
    
    Constr.prototype._buttonClick = function() {
        var that = this;
        
        if (this._isPickingOn) {
            // Остановим подбор.
            this._backend.stopPicking(
                function(data) {
                    that._stopPickingSuccess(data);
                },
                function(data) {
                    that._ajaxRequestError(data);
                }
            );
        } else {
            // Начнём подбор.
            this._backend.startPicking(
                function(data) {
                    that._startPickingSuccess(data);
                },
                function(data) {
                    that._ajaxRequestError(data);
                }
            );
        }
    };
    
    Constr.prototype._getButtonSelector = function(buttonId) {
        return '#'+buttonId+'';
    };
    
    
    Constr.prototype._startPickingSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            return;
        }
        
        this._toPickingOnState();
        
        Events.trigger(this._getStartPickingEventName(), {
            'buttonId' : this._buttonId
        });
    };
    
    Constr.prototype._stopPickingSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            return;
        }
        
        this._toPickingOffState();
    };
    
    Constr.prototype._onStartPickingEvent = function(event, data) {
        if (data.buttonId === this._buttonId) {
            // Если сама кнопка посылала событие, ничего не делаем.
            return;
        }
        
        if (this._isPickingOn) {
            // Подбор начался для другого объекта. 
            // Для этого объекта подбор прекращаем.
            this._toPickingOffState();
        }
    };
    
    Constr.prototype._toPickingOnState = function() {
        var button = $(this._button);
        this._removeCssClassesFromButton(this.CSS_CLASSESS_OFF);
        this._addCssClassesToButton(this.CSS_CLASSESS_ON);
        button.text(this.BUTTON_TEXT_ON);
        this._isPickingOn = true;
    };
    
    Constr.prototype._toPickingOffState = function() {
        var button = $(this._button);
        this._removeCssClassesFromButton(this.CSS_CLASSESS_ON);
        this._addCssClassesToButton(this.CSS_CLASSESS_OFF);
        button.text(this.BUTTON_TEXT_OFF);
        this._isPickingOn = false;
    };
    
    Constr.prototype._addCssClassesToButton = function(classes) {
        var button = $(this._button);
        for (var i = 0; i < classes.length; i++) {
            button.addClass(classes[i]);
        }
    };
    
    Constr.prototype._removeCssClassesFromButton = function(classes) {
        var button = $(this._button);
        for (var i = 0; i < classes.length; i++) {
            button.removeClass(classes[i]);
        }
    };
        
    Constr.prototype._ajaxRequestError = function(data) {
        if (typeof data.errorMessage !== 'undefined') {
            console.log(data.errorMessage);
        } else {
            console.log('error');
        }
    };
    
    Constr.prototype._refreshPage = function() {
        location.reload();
    };
    
    Constr.prototype._getStartPickingEventName = function() {
        return 'startPicking-'+this._type;
    };
    
    return Constr;
})();