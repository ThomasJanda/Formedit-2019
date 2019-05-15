
var resizer = {};

resizer.state = {};
resizer.state.bActive = false;
resizer.state.oElement = null;

resizer.state.position={};
resizer.state.position.P1left = 0;
resizer.state.position.P1top = 0;
resizer.state.position.P2left = 0;
resizer.state.position.P2top = 0;
resizer.state.position.oElement = null;

resizer.state.clear = function() {
    resizer.hide();
    resizer.state.bActive = false;
    resizer.state.oElement = null;
};
resizer.visible = function()
{
    //copy to desktop
    var o = resizer.state.position.oElement.detach();
    o.appendTo('.desktop_form.selected .desktop');
    o = null;

    resizer.state.position.oElement.css({
        display:'block'
    });
};
resizer.hide = function()
{
    resizer.state.position.oElement.css({
        display:'none'
    });

    //remove from desktop
    var o = resizer.state.position.oElement.detach();
    o.appendTo('body');
    o = null;
};
resizer.init = function() {

    resizer.state.position.oElement = $('<div id="resizerrect"></div>');
    resizer.state.position.oElement.appendTo('body');
};



resizer.eventDesktopMouseMove = function(oDesktop, event) {
    if(resizer.state.bActive==true)
    {
        event.stopPropagation();

        var oPosition = oDesktop.offset();
        resizer.state.position.P2left = event.originalEvent.pageX - oPosition.left;
        resizer.state.position.P2top = event.originalEvent.pageY - oPosition.top;

        resizer.paint();
    }
};
resizer.eventBodyMouseUp = function(oBody, event) {
    if(resizer.state.bActive==true) {

        //console.log('reiszer.eventBodyMouseUp');

        var iWidth = resizer.state.position.oElement.outerWidth();
        var iHeight = resizer.state.position.oElement.outerHeight();

        var iGrid = 10;
        iWidth = iGrid * (Math.ceil(Math.round(iWidth / 10 * 100) / 100));
        iHeight = iGrid * (Math.ceil(Math.round(iHeight / 10 * 100) / 100));
        if(iWidth < 20) iWidth = 10;
        if(iHeight < 10) iHeight = 10;

        var iMinWdith = resizer.state.oElement.attr('data-minwidth');
        if(iWidth < iMinWdith)
            iWidth = iMinWdith;
        var iMinHeight = resizer.state.oElement.attr('data-minheight');
        if(iHeight < iMinHeight)
            iHeight = iMinHeight;

        if(resizer.state.oElement.hasClass('absolute')) {
            resizer.state.oElement.css({
                width: iWidth + 'px',
                height: iHeight + 'px',
            });

            //save position
            request.ajax('setControlDimensionAbsolute', {
                controlid: resizer.state.oElement.attr('id'),
                width: iWidth + 'px',
                height: iHeight + 'px'
            });
        }
        if(resizer.state.oElement.hasClass('relative')) {
            resizer.state.oElement.css({
                width: iWidth + 'px',
                height: iHeight + 'px',
            });
            //save position
            request.ajax('setControlDimensionRelative', {
                controlid: resizer.state.oElement.attr('id'),
                width: iWidth + 'px',
                height: iHeight + 'px'
            });
        }
        property.loadControl(resizer.state.oElement);

        resizer.state.clear();

    }
};
resizer.eventControlMouseDown = function(oElement) {
    resizer.state.clear();
    resizer.state.oElement = oElement.parent();
    resizer.state.bActive=true;

    main.unselectElements();
    main.selectElement(resizer.state.oElement);

    var oPosition = $('.desktop_form.selected .desktop').offset();
    var iOffsetLeft = oPosition.left;
    var iOffsetTop = oPosition.top;
    oPosition = resizer.state.oElement.offset();
    resizer.state.position.P1left = oPosition.left - iOffsetLeft;
    resizer.state.position.P1top = oPosition.top - iOffsetTop;

    resizer.state.position.P2left = resizer.state.position.P1left + resizer.state.oElement.outerWidth();
    resizer.state.position.P2top = resizer.state.position.P1top + resizer.state.oElement.outerHeight();
    resizer.visible();
    resizer.paint();
};


resizer.paint = function() {

    if(resizer.state.bActive==false)
        return;

    var iLeft = resizer.state.position.P1left;
    var iWidth = 0;
    if(resizer.state.position.P1left < resizer.state.position.P2left)
    {
        iWidth = resizer.state.position.P2left - resizer.state.position.P1left;
    }
    else
    {
        iWidth = 0;
    }

    var iTop = resizer.state.position.P1top;
    var iHeight = 0;
    if(resizer.state.position.P1top < resizer.state.position.P2top)
    {
        iHeight = resizer.state.position.P2top - resizer.state.position.P1top;
    }
    else
    {
        iHeight = 0;
    }
    resizer.state.position.oElement.css({
        left: iLeft + 'px',
        top: iTop + 'px',
        width: iWidth + 'px',
        height: iHeight + 'px',
    });
};


resizer.init();