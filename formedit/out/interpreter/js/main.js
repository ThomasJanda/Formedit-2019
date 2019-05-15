var main={};

main.waitShow = function() {
    $('#wait').addClass('show');
};
main.waitHide = function() {
    $('#wait').removeClass('show');
};



main.getSessionId = function() {
    return $('body').attr('data-sessionid');
};
main.getSessionName = function() {
    return $('body').attr('data-sessionname');
};
main.getFormId = function() {
    return $('body').attr('data-formid');
};
main.getDesktopId = function() {
    return $('body').attr('data-desktopid');
};
main.getProjectId = function() {
    return $('body').attr('data-projectid');
};
main.getProjectPath = function() {
    return $('body').attr('data-projectpath');
};
