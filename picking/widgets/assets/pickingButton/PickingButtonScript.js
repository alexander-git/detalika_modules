var PickingButtonScript = (function() {
    
    function run(buttonConfig) {
        var pickingButton = new PickingButton(buttonConfig);
        pickingButton.init();
    }
    
    return {
        'run' : run
    };

})();