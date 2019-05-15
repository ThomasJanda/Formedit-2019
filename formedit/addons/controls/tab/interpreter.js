var tab = {};
tab.init = function(sId) {
    $('#'+ sId + ' div.tabheader > button').click(function(e) {
        $(this).parent().parent().children('.tabcontent').children('.tabcontainer').removeClass('active');
        $(this).parent().children('button').removeClass('active');
        var sId = $(this).attr('data-tabcontainer_id');
        $(this).addClass('active');
        $(this).parent().parent().children('.tabcontent').children('#' + sId + '.tabcontainer').addClass('active');
    });
};

$('.control_tab').each(function(i) {
   var sId = $(this).attr('id');
   tab.init(sId);
});
