var contextmenu={};

contextmenu.eventDesktopContextMenu = function(oElement, event) {
    event.stopPropagation();
    if (event.preventDefault) event.preventDefault(); // allows us to drop

    var sElementId = "";
    var sContainerId = "";
    var sRole = '';
    var iLeft = event.originalEvent.pageX;
    var iTop = event.originalEvent.pageY;

    var oContextmenu = $('#contextmenu');

    //remove all underlaying elements
    oContextmenu.find('[data-function="select"]').remove();

    var oElement = $(event.target);
    sContainerId = oElement.closest('[data-sub_container_from_id]').attr('id');
    if(oElement.hasClass('desktop'))
    {
        sRole = 'desktop';
        sElementId = oElement.attr('id');
    }
    else
    {
        sRole = 'control';
        sElementId = oElement.closest('.control').attr('id');
    }

    oContextmenu.attr('data-elementid', sElementId);
    oContextmenu.attr('data-containerid', sContainerId);
    oContextmenu.attr('data-posx', event.originalEvent.layerX);
    oContextmenu.attr('data-posy', event.originalEvent.layerY);
    oContextmenu.children('[data-role=' + sRole + ']').css('display', 'block');
    oContextmenu.children(':not([data-role=' + sRole + '])').css('display', 'none');

    oContextmenu.children('.readonly').removeClass('readonly');
    if(!clipboard.containData())
        oContextmenu.children('[data-function=past]').addClass('readonly');
    if(!main.hasSelectedElements())
    {
        oContextmenu.children('[data-function=copy]').addClass('readonly');
        oContextmenu.children('[data-function=cut]').addClass('readonly');
        oContextmenu.children('[data-function=delete]').addClass('readonly');
    }


    if(sRole=="control")
    {
        let sHtml = '<hr data-function="select" />';
        let oItem = $(sHtml);
        oContextmenu.append(oItem);

        //detect underlaying elements
        oElement.parents('.control').each(function(i) {
            let name=$(this).attr('data-controlname');
            let id=$(this).attr('id');
            let sHtml = '<div data-role="control" data-function="select" data-select_id="' + id + '">Select ' + name + '</div>';
            let oItem = $(sHtml);

            //console.log(oItem);
            oContextmenu.append(oItem);
        });
    }


    oContextmenu.css({
        left: iLeft + 'px',
        top: iTop + 'px',
        display: 'block'
    });

    //close always
    $('body *').one('click', function(event) {
        contextmenu.hide();
    })
};
contextmenu.isVisible = function() {
    if($('#contextmenu').css('display')=="none")
        return false;
    return true;
};
contextmenu.hide = function() {
    $('#contextmenu').css('display','none');
};
contextmenu.click = function(oElement, oContainer, oElementCommand, iPosX, iPosY) {

    contextmenu.hide();

    var sCommand = oElementCommand.attr('data-function');
    //console.log(sCommand);

    switch(sCommand)
    {
        case 'cut':
            clipboard.cut();
            break;
        case 'copy':
            clipboard.copy();
            break;
        case 'past':
            clipboard.past(oElement, oContainer, iPosX, iPosY);
            break;
        case 'delete':
            var aElementsSelected = main.selectedElementsAll();
            if(aElementsSelected.length > 0)
            {
                if(confirm('Really delete ' + aElementsSelected.length + ' element(s)?'))
                {
                    $.each(aElementsSelected, function(i, oElementSelected) {
                        oElementSelected = $(oElementSelected);
                        main.deleteElement(oElementSelected);
                    });
                    selectbox.elements.refreshDesktop();
                }
            }
            break;
        case 'select':
            //console.log('ja');
            let sId = oElementCommand.attr('data-select_id');
            main.unselectElements();
            main.selectElement($('#' + sId));
            break;
    }
};




$(document).click(function() {
    contextmenu.hide();
}).keyup(function(e) {
    if ( e.keyCode === 27 ) {
        contextmenu.hide();
    }
});

$('.griditemsizer').mousedown(function() {
    contextmenu.hide();
});

$('#contextmenu').on('click', 'div', function() {

    if($(this).hasClass('readonly'))
        return;

    var sElementId = $(this).parent().attr('data-elementid');
    var sContainerId = $(this).parent().attr('data-containerid');
    var iPosX = $(this).parent().attr('data-posx');
    var iPosY = $(this).parent().attr('data-posy');
    contextmenu.click($('#' + sElementId),$('#' + sContainerId), $(this), iPosX, iPosY);
});


