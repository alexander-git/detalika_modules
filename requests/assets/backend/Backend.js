var Backend = (function() {
    
    var Constr = function() {
        
    };
    
    Constr.prototype.performGetRequest = function(url, success, error, data) {
        if (data === undefined) {
            data = null;
        }
        
        $.ajax({
            'url' : url,
            'success' : success,
            'error' : error,
            'method' : 'GET',
            'data' : data,
            'dataType' : 'json',
            'cache' : false
        });  
    };

    Constr.prototype.performPostRequest = function(url, success, error, data) {
        $.ajax({
            'url' : url,
            'success' : success,
            'error' : error,
            'method' : 'POST',
            'data' : data,
            'dataType' : 'json'
        });
    };
    
    Constr.prototype.getUrlFromTemplate = function(urlTemplate, params) {
        var url = urlTemplate;
        for (var paramName in params) {
            if (params.hasOwnProperty(paramName)) {
                var replacement = '__'+paramName+'__';
                var value = params[paramName];
                url = url.replace(replacement, value);
            }
        }
    
        return url;
    };

    return Constr;

})();