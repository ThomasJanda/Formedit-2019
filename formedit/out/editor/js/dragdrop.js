var dragdrop = {};

/**
ALL FOR SAFE DRAG DROP STATE
 which elements should move / copy
 move only elements on the desktipo
 copy only elements from the sidebar
*/

dragdrop.state = {};
dragdrop.state.bActive=false;
dragdrop.state.oElement=null;
dragdrop.state.oControlElement=null;
dragdrop.state.oStartElementParent=null;
dragdrop.state.iStartX=0;
dragdrop.state.iStartY=0;
dragdrop.state.oEndElementParent=null;
dragdrop.state.iEndX=0;
dragdrop.state.iEndY=0;
dragdrop.state.sDropEffect="";

dragdrop.state.clear = function()
{
    dragdrop.state.bActive=false;
    dragdrop.state.oElement=null;
    dragdrop.state.oControlElement=null;
    dragdrop.state.oStartElementParent=null;
    dragdrop.state.iStartX=0;
    dragdrop.state.iStartY=0;
    dragdrop.state.oEndElementParent=null;
    dragdrop.state.iEndX=0;
    dragdrop.state.iEndY=0;
    dragdrop.state.sDropEffect="";
};


dragdrop.setActiveState = function(bActive)
{
    dragdrop.state.bActive=bActive;
}

/**
 * helper function
 * @param oElement
 * @param sEvent
 * @param oFunction
 */
dragdrop.addEvent = function(oElement, sEvent, oFunction)
{
    oElement.addEventListener(sEvent,oFunction, false);
};
dragdrop.removeEvent = function(oElement, sEvent, oFunction) {
    oElement.addEventListener(sEvent,oFunction, false);
};




dragdrop.eventControlDragStart = function(event) {
    if(dragdrop.state.bActive==false)
    {
        dragdrop.state.clear();
        dragdrop.setActiveState(true);

        var oElement = $(event.currentTarget);
        var oMover = oElement;
        if(oElement.prop("tagName").toLowerCase()=='mover')
        {
            var oTmp = oElement.parent();
            oMover = oElement;
            oElement = oTmp;

            var oPosition = oElement.offset();
            dragdrop.state.iStartX = oPosition.left;
            dragdrop.state.iStartY = oPosition.top;
        }
        else
        {
            var relX = event.pageX - oElement.offset().left;
            var relY = event.pageY - oElement.offset().top;
            dragdrop.state.iStartX = relX;
            dragdrop.state.iStartY = relY;
        }

        dragdrop.state.oElement=oMover;
        dragdrop.state.oControlElement=oElement;
        dragdrop.state.oStartElementParent=oElement.parent();

        var sTmp = oElement.data('dropeffect');
        if(sTmp==undefined)
            sTmp = oElement.parent().data('dropeffect');
        dragdrop.state.sDropEffect = sTmp;
        event.dataTransfer.effectAllowed = sTmp;

        event.dataTransfer.setData('text/html', 'none'); // required otherwise doesn't work
        oElement.addClass('dragstart');
    }
};
dragdrop.eventControlDragEnd = function(event) {
    if(dragdrop.state.bActive==true) {
        dragdrop.setActiveState(false);
        $('.dragstart').removeClass('dragstart');
        main.undragoverElements();
    }
};



dragdrop.eventContainerDragOver = function(event) {
    var oElement = $(event.target);
    event.preventDefault(); // allows us to drop
    event.stopPropagation(); // not click trought

    event.dataTransfer.dropEffect = 'copyMove';
    main.dragoverElement(oElement);

    $('.dragover_mouseover_relative').removeClass('dragover_mouseover_relative');

    if(dragdrop.state.oControlElement!=null)
    {
        if(dragdrop.state.oControlElement.hasClass('relative'))
        {
            var oParent = oElement.closest('.control');
            if(oParent.length!=0)
            {
                if(oParent.hasClass('relative') && oParent.attr('id')!=dragdrop.state.oControlElement.attr('id'))
                {
                    if(!oParent.hasClass('dragover_mouseover_relative'))
                        oParent.addClass('dragover_mouseover_relative');
                }
            }
        }
    }

    return false;
};
dragdrop.eventContainerDragLeave = function(event) {
    var oElement = $(event.target);
    var oParent = oElement.closest('.control');
    if(oParent.length!=0)
    {
        if(oParent.hasClass('dragover_mouseover_relative'))
            oParent.removeClass('dragover_mouseover_relative');
    }
};
dragdrop.eventContainerDrop = function(event) {
    event.stopPropagation(); // stops the browser from redirecting...why???
    event.preventDefault();

    var iGrid = 10;

    $('.dragover_mouseover_relative').removeClass('dragover_mouseover_relative');



    var oElementDrop = $(event.originalTarget);
    var sInsert="";
    var sInsertId = "";
    var oParentNew = null;
    if(oElementDrop.prop("tagName").toLowerCase() == 'insert')
    {
        if(oElementDrop.hasClass('after'))
            sInsert="after";
        else if(oElementDrop.hasClass('before'))
            sInsert="before";

        //element itself, but need the parent container
        oElementDrop = oElementDrop.parent();
        sInsertId = oElementDrop.attr('id');

        //search for parent container
        oParentNew = oElementDrop.closest('[data-dropable]');
    }
    else
    {
        oParentNew = oElementDrop.closest('[data-dropable]');
    }


    if(oParentNew!=null && oParentNew.length==1)
    {

        if(dragdrop.state.sDropEffect=="copy")
        {
            // element from sidebar
            var sControlClass = dragdrop.state.oElement.data('controlclass');

            var iLeft = event.offsetX - dragdrop.state.iStartX;
            var iTop = event.offsetY - dragdrop.state.iStartY;

            iLeft = iGrid * (Math.floor(Math.round(iLeft / iGrid * 100) / 100));
            iTop = iGrid * (Math.floor(Math.round(iTop / iGrid * 100) / 100));


            //unselect all other elements
            main.unselectElements();

            main.loadElement(sControlClass, iLeft, iTop, oParentNew.attr('id'), sInsert, sInsertId);
        }
        else if(dragdrop.state.sDropEffect=="move")
        {
            //element from desktop
            var oElement = dragdrop.state.oElement;
            var oMover = oElement;
            if(oElement.prop("tagName").toLowerCase() == 'mover')
            {
                var oTmp = oElement.parent();
                oMover = oElement;
                oElement = oTmp;
            }


            var oPosition;

            //calculate new absolute end position
            dragdrop.state.iEndX = event.pageX;
            dragdrop.state.iEndY = event.pageY;

            var iDeltaX = dragdrop.state.iEndX - dragdrop.state.iStartX;
            var iDeltaY = dragdrop.state.iEndY - dragdrop.state.iStartY;

            //if new parent is dragdrop element or a child of it use old parent
            if(oParentNew.attr('id')==oElement.attr('id') ||
                oParentNew.parents('#' + oElement.attr('id')).length != 0
            ){
                oParentNew = oElement.parent();
            }
            dragdrop.state.oEndElementParent = oParentNew;

            oPosition = dragdrop.state.oEndElementParent.offset();
            var iOffsetX = oPosition.left;
            var iOffsetY = oPosition.top;

            //move the elements
            var aElementsSelected = selector.state.aElements();
            aElementsSelected.each( function(i, oElementSelected) {
                oElementSelected = $(oElementSelected);

                //only if the start dragdrop element have the same parent
                if(oElementSelected.parent().attr('id')==dragdrop.state.oStartElementParent.attr('id'))
                {
                    oPosition = oElementSelected.offset();
                    var iLeft = oPosition.left + iDeltaX - iOffsetX;
                    var iTop = oPosition.top + iDeltaY - iOffsetY;

                    if(iLeft < 0)
                        iLeft = 0;
                    if(iTop < 0)
                        iTop = 0;

                    oElementSelected.css('visibility','hidden');
                    if(sInsert=="")
                        oElementSelected.appendTo("#" + dragdrop.state.oEndElementParent.attr('id'));
                    else if(sInsert=="after")
                        oElementDrop.after(oElementSelected);
                    else if(sInsert=="before")
                        oElementDrop.before(oElementSelected);

                    //console.log("iLeft before: " + iLeft);
                    iLeft = iGrid * (Math.floor(Math.round(iLeft / iGrid * 100) / 100));
                    iTop = iGrid * (Math.floor(Math.round(iTop / iGrid * 100) / 100));

                    if(oElementSelected.hasClass('absolute'))
                    {
                        request.ajax('setControlPositionAbsolute',{
                            controlid: oElementSelected.attr('id'),
                            left: iLeft + 'px',
                            top: iTop + 'px'
                        });
                        oElementSelected.css({
                            left: iLeft + 'px',
                            top: iTop + 'px'
                        });
                    }
                    if(oElementSelected.hasClass('relative'))
                    {
                        //set siblings
                        request.ajax('setControlSiblings',{
                            controlid: oElementSelected.attr('id'),
                            siblingid: sInsertId,
                            siblingrelation: sInsert
                        });
                    }
                    //set the parent
                    request.ajax('setControlParent',{
                        controlid: oElementSelected.attr('id'),
                        parent: oElementSelected.parent().attr('id')
                    });

                    oElementSelected.css({
                        visibility: 'visible'
                    });
                }
            });

            selectbox.elements.refreshDesktop();
            //only if one element move...
            if(aElementsSelected.length==1)
            {
                var oElement = $(aElementsSelected[0]);
                selectbox.elements.select(oElement);
            }
        }
    }

    return false;
};


