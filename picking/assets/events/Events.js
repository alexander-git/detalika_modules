var Events = (function(){
  
    var eventNode = $({});
  
    function on() {
        eventNode.on.apply(eventNode, arguments);
    }
  
    function trigger() {
        eventNode.trigger.apply(eventNode, arguments);
    }
  
    return {
        on: on,
        trigger: trigger
    };
    
})();