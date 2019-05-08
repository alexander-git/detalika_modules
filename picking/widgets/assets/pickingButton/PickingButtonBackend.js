var PickingButtonBackend = (function() {
    
    var Constr = function(config) {
        this._startPickingUrl = config.startPickingUrl;
        this._stopPickingUrl = config.stopPickingUrl;
        this._backend = new Backend();
    };
    
    Constr.prototype.startPicking = function(success, error) {
        var url = this._startPickingUrl;
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.stopPicking = function(success, error) {
        var url = this._stopPickingUrl;
        this._backend.performPostRequest(url, success, error);
    };
         
    return Constr;

})();