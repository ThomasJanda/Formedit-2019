
$('#desktopborder').on('click', 'div.control.control_tab > div.tabheader > button', function(event) {
    $(this).parent().parent().children('.tabcontent').children('.tabcontainer').removeClass('active');
    $(this).parent().children('button').removeClass('active');
    var sId = $(this).attr('data-tabcontainer_id');
    $(this).addClass('active');
    $(this).parent().parent().children('.tabcontent').children('#' + sId + '.tabcontainer').addClass('active');
});

