var AddGoodsToPickingRequestPositionButton = (function() {
    
    var Constr = function(config) {
        var selector = this._getButtonSelector(config.buttonId);
        this._buttonId = config.buttonId;
        this._button = $(selector);
        this._state = config.state;
        this._goodIds = config.goodIds;
        this._buttonTextAdd = config.buttonTextAdd;
        this._buttonTextRemove = config.buttonTextRemove;
        this._backend = new AddGoodsToPickingRequestPositionButtonBackend(config);
    };
    
    Constr.prototype.STATE_ADD = 'add';
    Constr.prototype.STATE_REMOVE = 'remove';
    Constr.prototype.CSS_CLASSESS_ADD = ['btn', 'btn-success'];
    Constr.prototype.CSS_CLASSESS_REMOVE = ['btn', 'btn-danger'];
        
    Constr.prototype.EVENT_ADD_REMOVE_GOODS_SUCCESS = 'addRemoveGoodsSuccess';
        
    Constr.prototype.init = function() {
        var that = this;
        this._button.on('click', function() {
            that._buttonClick();
        });
        
        Events.on(this.EVENT_ADD_REMOVE_GOODS_SUCCESS, function(event, data) {
            that._onAddRemoveGoodsEvent(event, data);
        });
    };
    
    Constr.prototype._buttonClick = function() {
        var that = this;
        
        if (this._isInAddState()) {
            // Добавим торвары.
            this._backend.addGoods(
                function(data) {
                    that._addGoodsSuccess(data);
                },
                function(data) {
                    that._ajaxRequestError(data);
                },
                this._goodIds
            );
        } else {
            // Удалим товары.
            this._backend.removeGoods(
                function(data) {
                    that._removeGoodsSuccess(data);
                },
                function(data) {
                    that._ajaxRequestError(data);
                },
                this._goodIds
            );
        }
    };
    
    Constr.prototype._getButtonSelector = function(buttonId) {
        return '#'+buttonId;
    };
    
    Constr.prototype._isInAddState = function() {
        return this._state === this.STATE_ADD;
    };
    
    Constr.prototype._addGoodsSuccess = function(data) {
        if (typeof data.success === 'undefined') {
            return;
        }
        
        if (!data.success) {
            if (typeof data.errorMessage !== 'undefined') {
                alert(data.errorMessage);
            }
            return;
        }
        
        Events.trigger(this.EVENT_ADD_REMOVE_GOODS_SUCCESS, {
            'buttonId' : this._buttonId,
            'childGoodIds' : data.childGoodIds
        });
        
        this._toRemoveState();
    };
    
    Constr.prototype._removeGoodsSuccess = function(data) {
        if (typeof data.success === 'undefined') {
            return;
        }
        
        if (!data.success) {
            if (typeof data.errorMessage !== 'undefined') {
                alert(data.errorMessage);
            }
            return;
        }
        
        this._toAddState();
        
        Events.trigger(this.EVENT_ADD_REMOVE_GOODS_SUCCESS, {
            'buttonId' : this._buttonId,
            'childGoodIds' : data.childGoodIds
        });
    };
    
    Constr.prototype._onAddRemoveGoodsEvent = function(event, data) {
        
        if (data.buttonId === this._buttonId) {
            // Если сама кнопка посылала событие, ничего не делаем.
            return;
        }
       
        // Проверим есть ли у активной позиции хотя бы одна дочерняя позиция 
        // с товаром который должна добавлять кнопка.
        var haveAtLeastOne = false;
        var goodId;
        var childGoodIds = data.childGoodIds;
        for (var i = 0; i < this._goodIds.length; i++) {
            goodId = this._goodIds[i];
            if (childGoodIds.indexOf(goodId) !== -1) {
                haveAtLeastOne = true;
                break;
            }
        }

        if (haveAtLeastOne && this._isInAddState()) {
            this._toRemoveState();
            return;
        }
        
        if (!haveAtLeastOne && !this._isInAddState()) {
            this._toAddState();
            return;
        }
    };
    
    Constr.prototype._toAddState = function() {
        this._removeCssClassesFromButton(this.CSS_CLASSESS_REMOVE);
        this._addCssClassesToButton(this.CSS_CLASSESS_ADD);
        this._button.text(this._buttonTextAdd);
        this._state = this.STATE_ADD;
    };
    
    Constr.prototype._toRemoveState = function() {
        this._removeCssClassesFromButton(this.CSS_CLASSESS_ADD);
        this._addCssClassesToButton(this.CSS_CLASSESS_REMOVE);
        this._button.text(this._buttonTextRemove);
        this._state = this.STATE_REMOVE;
    };
    
    Constr.prototype._addCssClassesToButton = function(classes) {
        for (var i = 0; i < classes.length; i++) {
            this._button.addClass(classes[i]);
        }
    };
    
    Constr.prototype._removeCssClassesFromButton = function(classes) {
        for (var i = 0; i < classes.length; i++) {
            this._button.removeClass(classes[i]);
        }
    };
        
    Constr.prototype._ajaxRequestError = function(data) {
        if (typeof data.errorMessage !== 'undefined') {
            console.log(data.errorMessage);
        } else {
            console.log('error');
        }
    };
        
    return Constr;
})();