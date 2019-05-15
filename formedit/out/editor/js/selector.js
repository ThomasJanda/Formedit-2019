
var selector = {};

selector.state = {};
selector.state.bMoved=false;
selector.state.bActive=false;
selector.state.oParent=null;
selector.state.position={};
selector.state.position.P1left = 0;
selector.state.position.P1top = 0;
selector.state.position.P2left = 0;
selector.state.position.P2top = 0;
selector.state.position.oElement = null;

selector.state.clear = function()
{
    selector.state.position.P1left = 0;
    selector.state.position.P1top = 0;
    selector.state.position.P2left = 0;
    selector.state.position.P2top = 0;
    selector.state.bMoved=false;
    selector.state.oParent=null;
    main.unselectElements();
    //selector.getControls().removeClass('selected');

};
selector.getControls = function() {
    return main.getDesktop().find('.control');
    //return $('.desktop_form.selected .desktop .control');
};
selector.state.aElements=function() {
    return main.getDesktop().find('.control.selected');
    //return $('.desktop_form.selected .desktop .control.selected');
};
selector.init = function() {
    selector.state.position.oElement = $('<div id="selectorrect"></div>');
    selector.state.position.oElement.appendTo('body');
};
selector.eventContainerMouseDown = function(oElement, event) {
    if(event.originalEvent != undefined && event.originalEvent.buttons == 1)
    {
        //event.stopPropagation();

        $('body').one('mouseup', function(event) {
            //console.log('eventContainerMouseDown body.mouseup');
            selector.eventBodyMouseUp($(this),event);
        });

        selector.state.clear();

        selector.state.oParent = oElement;

        var oPosition = main.getDesktop().offset();
        var iOffsetLeft = oPosition.left;
        var iOffsetTop = oPosition.top;
        selector.state.position.P1left = event.originalEvent.pageX - iOffsetLeft;
        selector.state.position.P1top = event.originalEvent.pageY - iOffsetTop;
        selector.state.position.P2left = selector.state.position.P1left
        selector.state.position.P2top = selector.state.position.P1top

        selector.visible();
    }
};
selector.eventContainerMouseMove = function(oElement, event) {
    if(selector.state.bActive == true)
    {
        event.stopPropagation();

        selector.state.bMoved = true;
        var oPosition = main.getDesktop().offset();
        selector.state.position.P2left = event.originalEvent.pageX - oPosition.left;
        selector.state.position.P2top = event.originalEvent.pageY - oPosition.top;
        selector.paint();
    }
};


selector.eventBodyMouseUp = function(oElement, event) {
    if(selector.state.bActive == true) {
        var oPosition = oElement.offset();
        selector.state.position.P2left = event.originalEvent.pageX - oPosition.left;
        selector.state.position.P2top = event.originalEvent.pageY - oPosition.top;
        selector.hide();
        selector.state.bMoved = false;
        property.clear();
    }
};

selector.visible = function()
{
    //copy to desktop
    var o = selector.state.position.oElement.detach();
    o.appendTo('.desktop_form.selected .desktop');
    o = null;

    selector.state.bActive = true;
    selector.paint();
    selector.state.position.oElement.css({
        display:'block'
    });
};
selector.hide = function()
{
    selector.state.bActive = false;
    selector.state.position.oElement.css({
        display:'none'
    });

    //remove from desktop
    var o = selector.state.position.oElement.detach();
    o.appendTo('body');
    o = null;
};
selector.paint = function() {

    if(selector.state.bActive==false)
        return;

    var iLeft = 0;
    var iWidth = 0;
    if(selector.state.position.P1left < selector.state.position.P2left)
    {
        iLeft = selector.state.position.P1left;
        iWidth = selector.state.position.P2left - selector.state.position.P1left;
    }
    else
    {
        iLeft = selector.state.position.P2left;
        iWidth = selector.state.position.P1left - selector.state.position.P2left;
    }

    var iTop = 0;
    var iHeight = 0;
    if(selector.state.position.P1top < selector.state.position.P2top)
    {
        iTop = selector.state.position.P1top;
        iHeight = selector.state.position.P2top - selector.state.position.P1top;
    }
    else
    {
        iTop = selector.state.position.P2top;
        iHeight = selector.state.position.P1top - selector.state.position.P2top;
    }
    selector.state.position.oElement.css({
        left: iLeft + 'px',
        top: iTop + 'px',
        width: iWidth + 'px',
        height: iHeight + 'px',
    });

    var oPosition = selector.state.position.oElement.offset();
    var oRect = {
      left: oPosition.left,
      right: oPosition.left + selector.state.position.oElement.outerWidth(),
      top:  oPosition.top,
      bottom:  oPosition.top + selector.state.position.oElement.outerHeight()
    };

    var aControls = selector.getControls();
    aControls.each(function(i,oElement){

        var bSelect = false;
        if($(oElement).parent().attr('id')==selector.state.oParent.attr('id'))
        {
            oPosition = $(oElement).offset();
            var oRectElement = {
                left: oPosition.left,
                right: oPosition.left + $(oElement).outerWidth(),
                top:  oPosition.top,
                bottom:  oPosition.top + $(oElement).outerHeight()
            };

            var x_overlap = Math.max(0, Math.min(oRect.right, oRectElement.right) - Math.max(oRect.left, oRectElement.left));
            var y_overlap = Math.max(0, Math.min(oRect.bottom, oRectElement.bottom) - Math.max(oRect.top, oRectElement.top));
            var overlapArea = x_overlap * y_overlap;
            if(overlapArea > 0)
                bSelect=true;
        }

        if(bSelect) {
            main.selectElement($(oElement));
            //$(oElement).addClass('selected');
        }
        else {
            main.unselectElement($(oElement));
            //$(oElement).removeClass('selected');
        }
    });

};

selector.init();