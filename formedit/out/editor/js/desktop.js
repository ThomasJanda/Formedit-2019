var desktop={};

desktop.resizer={};
desktop.resizer.state={};
desktop.resizer.state.bActive=false;
desktop.resizer.state.oElement=null;
desktop.resizer.clear = function() {
    desktop.resizer.state.bActive=false;
    desktop.resizer.state.oElement=null;
    desktop.resizer.ajust();
};
desktop.resizer.ajust = function() {

    var iMinWidth = 100;
    $('.griditemsizer').each(function(i) {
        var oElement = $(this);

        var sFixed = oElement.attr('data-fixed');

        iMinWidth += oElement.outerWidth();

        var oElementLeft = $(oElement.attr('data-left'));
        var oElementRight = $(oElement.attr('data-right'));

        if(sFixed=="left")
        {
            iMinWidth += oElementLeft.outerWidth();
        }
        else
        {
            iMinWidth += oElementRight.outerWidth();
        }
    });
    $('.griditemsizer').parent().css({minWidth: iMinWidth + 'px'});
};

desktop.resizer.eventBodyMouseMove = function(oElement, event) {
    if(desktop.resizer.state.bActive)
    {
        event.stopPropagation();

        var bFinish = false;

        var oElement = desktop.resizer.state.oElement;
        var sFixed = oElement.attr('data-fixed');

        var iOffset = oElement.outerWidth() / 2;
        var iMaxWidth = oElement.parent().outerWidth();

        var iLeft = event.originalEvent.clientX;
        var iRight = iMaxWidth - iLeft;

        //if it get to thin, stop at min width
        if(iLeft < 100 || iRight < 100)
        {
            iLeft = 100;
            iRight = 100;
            bFinish = true;
        }

        var oElementLeft = $(oElement.attr('data-left'));
        var oElementRight = $(oElement.attr('data-right'));

        if(sFixed=="left")
        {
            var oPosition = oElementRight.offset();
            var iTmp = (oPosition.left + oElementRight.outerWidth() - iLeft - iOffset);
            if(iTmp < 100)
            {
                iLeft -= (100 - iTmp);
                bFinish = true;
            }

            oElement.css({left: (iLeft - iOffset) +'px', right: 'auto' });

            oElementLeft.css({ left: '0px' ,width: (iLeft - iOffset) + 'px' });
            oElementRight.css({ left: (iLeft + iOffset) + 'px' });
        }
        else
        {
            var oPosition = oElementLeft.offset();
            var iTmp = iLeft - oPosition.left;
            if(iTmp > 0 && iTmp < 100)
            {
                iRight -= (100 - iTmp);
                bFinish = true;
            }

            oElement.css({right: (iRight - iOffset) +'px', left: 'auto' });

            //left element change "right", right element change "width"
            oElementLeft.css({ width: 'auto' ,right: (iRight + iOffset) + 'px' });
            oElementRight.css({ right: '0px', width: (iRight - iOffset) + 'px' });
        }

        if(bFinish==true)
        {
            desktop.resizer.clear();
        }
    }
};
desktop.resizer.init = function() {

    $('.griditemsizer').parent().css({minWidth: '300px'});
    $('.griditemsizer').each(function(i) {
        var oElement = $(this);
        var iOffset = oElement.outerWidth() / 2;

        var oElementLeft = $(oElement.attr('data-left'));
        oElementLeft.css({minWidth: (100 - iOffset) + 'px'});
        var oElementRight = $(oElement.attr('data-right'));
        oElementRight.css({minWidth: (100 - iOffset) + 'px'});
    });
    desktop.resizer.ajust();

};



$('.griditemsizer').mousedown(function(event) {
    if(event.buttons==1)
    {
        event.stopPropagation();
        desktop.resizer.state.oElement=$(this);
        desktop.resizer.state.bActive=true;

        $('body').one('mouseup', function(event) {
            //console.log('desktop.resize body.mouseup');
            desktop.resizer.clear();
        });
        $(window).one('resize', function(event) {
            //console.log('desktop.resize windows.resize');
            desktop.resizer.clear();
        });
    }
});
$('body').mousemove(function(event) {
    desktop.resizer.eventBodyMouseMove($(this),event);
});


desktop.resizer.init();
desktop.resizer.clear();