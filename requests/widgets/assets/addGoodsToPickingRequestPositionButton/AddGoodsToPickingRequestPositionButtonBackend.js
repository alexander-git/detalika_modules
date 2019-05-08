var AddGoodsToPickingRequestPositionButtonBackend = (function() {
    
    var Constr = function(config) {
        this._addGoodsUrl = config.addGoodsUrl;
        this._removeGoodsUrl = config.removeGoodsUrl;
        this._backend = new Backend();
    };
    
    Constr.prototype.addGoods = function(success, error, goodIds) {
        var url = this._addGoodsUrl;
        var data = {
            'goodIds' : goodIds
        };
        
        this._backend.performPostRequest(url, success, error, data);
    };
    
    Constr.prototype.removeGoods = function(success, error, goodIds) {
        var url = this._removeGoodsUrl;
        var data = {
            'goodIds' : goodIds
        };
        
        this._backend.performPostRequest(url, success, error, data);
    };
         
    return Constr;

})();