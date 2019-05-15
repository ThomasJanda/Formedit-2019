var controls = {};
controls.aHooks=[];
controls.add = function(oElement) {
    for (let i = 0; i < controls.aHooks.length; i++) {
        let aParam = controls.aHooks[i];
        if(aParam.function=="add")
            aParam.callback(oElement);
    }
};
controls.remove = function(oElement) {
    for (let i = 0; i < controls.aHooks.length; i++) {
        let aParam = controls.aHooks[i];
        if(aParam.function=="remove")
            aParam.callback(oElement);
    }
};



/**
 * @param sFunction : add|remove
 * @param oCallback : name of the function which should call back
 */
controls.addHook = function(sFunction, oCallback) {
    if(typeof oCallback == "function")
    {
        aParam={};
        aParam.function = sFunction;
        aParam.callback = oCallback;
        controls.aHooks.push(aParam);
    }
};


//control
$('#desktopborder').on('mousedown', 'div.control',  function(event) {

    event.stopPropagation();

    var oElement = $(this);
    $('body').one('mouseup', function(event) {
        //MOUSEUP

        //selector
        if(selector.state.bMoved==false && contextmenu.isVisible()==false)
        {
            /*
            console.log('mousedown');
            selector.state.aElements().each(function() {
                main.unselectElement($(this));
            });
            */
            main.unselectElements();
            selectbox.elements.select(oElement);
        }
    });

    //MOUSEDOWN

    //selector
    if(event.originalEvent.buttons == 1)
    {
        main.selectElement(oElement);
    }

    //contexmenu
    if(event.originalEvent.buttons == 1)
    {
        contextmenu.hide();
    }
});


//resizer
$('#desktopborder').on('mousedown', 'div.control[data-resizable] > sizer',  function(event) {
    if (event.originalEvent.buttons == 1) {
        event.stopPropagation(); //not click through
        resizer.eventControlMouseDown($(this));

        $('body').one('mouseup', function(event) {
            resizer.eventBodyMouseUp($(this),event);
        });
    }
});

//selector
$('#desktopborder').on('mousedown', 'div.control[data-selectable], div[data-sub_container_from_id][data-selectable]',  function(event) {
    selector.eventContainerMouseDown($(this),event);
});
$('#desktopborder').on('mousemove', 'div.control[data-selectable], div[data-sub_container_from_id][data-selectable]',  function(event) {
    selector.eventContainerMouseMove($(this),event);
});



//dragdrop desktop
$('#desktopborder').on('mousedown', 'div.control > mover',  function(event) {
    let oMover = $(this);
    let oElement = $(this).parent();

    oElement[0].setAttribute('data-dropeffect', 'move');
    oMover[0].setAttribute('draggable', 'true');
    dragdrop.addEvent(oMover[0], 'dragstart', dragdrop.eventControlDragStart);
    dragdrop.addEvent(oMover[0], 'dragend', dragdrop.eventControlDragEnd);
});
//dragdrop sidebar
$('#controls').on('mousedown', '[data-draggable]',  function(event) {
    let oElement = $(this);

    //set relative, absolute or fill
    oElement[0].classList.add(main.getDesktop().attr('data-defaultdimensionposition'));

    oElement[0].setAttribute('data-dropeffect', 'copy');
    oElement[0].setAttribute('draggable', 'true');
    dragdrop.addEvent(oElement[0], 'dragstart', dragdrop.eventControlDragStart);
    dragdrop.addEvent(oElement[0], 'dragend', dragdrop.eventControlDragEnd);
});
//container
$('#desktopborder').on('dragover', '[data-dropable]',  function(event) {

    console.log('mouseenter');

    let oElement = $(this);
    dragdrop.addEvent(oElement[0], 'dragover', dragdrop.eventContainerDragOver);
    dragdrop.addEvent(oElement[0], 'dragleave', dragdrop.eventContainerDragLeave);
    dragdrop.addEvent(oElement[0], 'drop', dragdrop.eventContainerDrop);
});