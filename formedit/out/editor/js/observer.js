
// Create an observer instance
var observer = new MutationObserver(function( mutations ) {

    //console.log(mutations);

    mutations.forEach(function( mutation ) {
        var newNodes = mutation.addedNodes; // DOM NodeList
        if( newNodes !== null ) { // If there are new nodes added
            var nodes = $( newNodes ); // jQuery set
            nodes.each(function() {
                var node = $( this );
                if(node.hasClass('control'))
                {
                    controls.add(node);
                }
            });
        }
        var removedNodes = mutation.removedNodes; // DOM NodeList
        if( removedNodes !== null ) { // If there are new nodes added
            var nodes = $( removedNodes ); // jQuery set
            nodes.each(function() {
                var node = $( this );
                if(node.hasClass('control'))
                {
                    controls.remove(node);
                }
            });
        }
    });
});

// Pass in the target node, as well as the observer options
observer.observe($( "#desktopborder" )[0], {
    attributes: false,
    childList: true,
    characterData: false,
    subtree: true
});

// Later, you can stop observing
//observer.disconnect();