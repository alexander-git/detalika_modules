var AddGoodsToPickingRequestPositionButtonScript = (function() {
    
    function run(buttonConfig) {
        var button = new AddGoodsToPickingRequestPositionButton(buttonConfig);
        button.init();
    }
    
    return {
        'run' : run
    };

})();