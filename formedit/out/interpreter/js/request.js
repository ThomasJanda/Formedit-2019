var request = {};

request.ajax = function(sFnc, aParam) {

    main.waitShow();

    var sRet = "";

    if(aParam==null) aParam={};
    aParam.fnc = sFnc;
    var sParam = request.createHtmlParameter(aParam);

    var url = "interpreterrequest.php";

    $.ajax({
        url: url,
        async: false,
        data: sParam
    })
        .done(function(sData) {
            //console.log(sData);
            sRet = sData;
        })
        .fail(function() {
            /* alert( "error" ); */
        })
        .always(function() {
            /* alert( "complete" ); */
        });

    main.waitHide();

    return sRet;
};


request.createHtmlParameter = function(aParam, bSession) {

    if(bSession==null) bSession=true;
    if(aParam==null) aParam={};

    var bHasForm=false;
    var bHasProject=false;

    jQuery.each(aParam, function(sKey, sValue) {
        if(sKey=="formid")
            bHasForm=true;
        if(sKey=="projectid")
            bHasProject=true;
    });

    if(!bHasForm) aParam.formid=main.getFormId();
    if(!bHasProject) aParam.projectid=main.getProjectId();

    let sParam = "";
    if(bSession)
        sParam = sParam + main.getSessionName() + "=" + main.getSessionId() + "&";
    sParam = sParam + jQuery.param( aParam );
    return sParam;
};