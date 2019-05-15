
$('#desktopborder').on('mousedown', 'div.desktop',  function(event) {
    var oDesktop = $(this);

    //selector
    selector.eventContainerMouseDown(oDesktop,event);

    //contexmenu
    if(event.originalEvent.buttons == 1)
    {
        contextmenu.hide();
    }
});

$('#desktopborder').on('mousedown', 'div.desktop_panel_bar button',  function(event) {
    $(this).parent().next().children().css('display','none');
    $(this).parent().children().removeClass('selected');
    $(this).addClass('selected');
    $(this).parent().next().children('.' + $(this).attr('data-display')).css('display','block');
});

$('#desktopborder').on('mousemove', 'div.desktop',  function(event) {
    var oDesktop = $(this);

    //resizer
    resizer.eventDesktopMouseMove(oDesktop, event);

    //selector
    selector.eventContainerMouseMove(oDesktop,event);
});

$('#desktopborder').on('contextmenu', 'div.desktop',  function(event) {
    var oDesktop = $(this);

    //contextmenu
    contextmenu.eventDesktopContextMenu(oDesktop,event);
});

$('#desktopborder').on('mousewheel', 'div.desktop',  function(event) {
    var oDesktop = $(this);

    //contextmenu
    contextmenu.hide();
});

