var toolbar={};
toolbar.init = function() {
    $('#controls .toolbox .toolbox_headline').click(function () {
        var sData = $(this).parent().attr('data-toolbox_container');
        if(sData=="closed")
            sData="open";
        else
            sData="closed";
        $(this).parent().attr('data-toolbox_container',sData);
    });
};
$('input#sidebar_tools_search').keyup(function() {
    var sVal = $(this).val().toLowerCase();
    if(sVal=="")
    {
        //display all, close all headline
        $('#controls .toolbox').attr('data-toolbox_container','closed');
        $("#controls .toolbox .toolbox_container > div.control" ).css('display','block');
    }
    else
    {
        //open all hear
        $('#controls .toolbox').attr('data-toolbox_container','open');
        $("#controls .toolbox .toolbox_container > div.control[data-searchtext*='" + sVal + "']" ).css('display','block');
        $("#controls .toolbox .toolbox_container > div.control").not("[data-searchtext*='" + sVal + "']" ).css('display','none');
    }

});
toolbar.init();