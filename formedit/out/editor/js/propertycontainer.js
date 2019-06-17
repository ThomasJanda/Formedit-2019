$('#controlproperties .controlproperties_header button').click(function() {
    $('#controlproperties .controlproperties_container > div').css('display','none');

    let display=$(this).attr('data-display');
    $(display).css('display','block');
});