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

main.oElementsSelected = [];
main.unselectElements=function() {
    $('.desktop_form.selected .control.selected').each(function() {
        console.log('test');
        main.unselectElement($(this));
    });
    //$('.desktiop .control.selected').removeClass('selected');
};
main.unselectElement=function(oElement) {
    oElement.removeClass('selected');
    oElement.css('background-color', oElement.attr('data-backgroundcolor'));
};
main.selectElement=function(oElement) {
    oElement.addClass('selected');
    oElement.css('background-color', oElement.attr('data-backgroundcolorselected'));
    //data-backgroundcolordrop
};
main.selectedElements = function() {
    return $('.desktop_form.selected .control.selected');
};
main.hasSelectedElements = function() {
    if(main.selectedElements().length==0)
        return false;
    return true;
};

main.dragoverElement = function(oElement) {
    main.undragoverElements();

    if(!oElement.hasClass('desktop'))
    {
        //search for container
        var oParent=false;
        if(oElement.is('[data-dropable]'))
            oParent = oElement;
        else
            oParent = oElement.closest('[data-dropable]');

        if(oParent.hasClass('desktop')==false)
        {
            oElement = oParent;
            oElement.addClass('dragover');

            oParent = oElement.closest('.control');
            oElement.css('background-color', oParent.attr('data-backgroundcolordrop'));
        }
    }
};
main.undragoverElements=function() {
    $('.dragover').each(function() {
        main.undragoverElement($(this));
    });
};
main.undragoverElement = function(oElement) {
    oElement.removeClass('dragover');
    if(oElement.parents('.control').length > 0)
    {
        oElement.css('background-color', '');
    }
};
main.selectedElementsAll = function() {

    main.oElementsSelected=[];

    function getSelectedElements(aElements)
    {
        aElements.each(function(i, oElement) {
            oElement = $(oElement);

            oElement.find('[data-sub_container_from_id=' + oElement.attr('id') + ']').each(function() {
                var oSubElement = $(this);
                getSelectedElements(oSubElement.children('.control'));
            });

            main.oElementsSelected.push(oElement);
        });
    }

    getSelectedElements($('.desktop_form.selected .control.selected'));

    //console.log(main.oElementsSelected);

    return main.oElementsSelected;
};

main.refreshElement = function(sId) {

    var sIdOld = sId + '_refresh';
    var sHtml = request.ajax('getControlHtml', {controlid: sId});

    if (sHtml != "") {

        var oElementOld = $('#' + sId + '.control');
        selectbox.elements.removeById(sId);
        oElementOld.attr('id', sIdOld).css({ visibility: 'visible'});
        //container
        oElementOld.find('[data-sub_container_from_id=' + sId + ']').each(function() {
            var oSubElement = $(this);
            var sIdSub = oSubElement.attr('id');
            var sIdNew =  sIdSub.replace(sId,sIdOld);
            //console.log(sIdNew);
            oSubElement.attr('id', sIdNew)
                .attr('data-sub_container_from_id',sIdOld)
                .attr('data-sub_container_from_id_new',sIdSub);
        });

        //NEW ELEMENT LOADED
        var oElement = $(sHtml);

        oElement.css('visibility', 'hidden');
        oElement.appendTo("#" + oElementOld.parent().attr('id'));
        oElement.css({visibility: 'visible'});

        //add new element
        selectbox.elements.add(oElement);

        //test because sub container
        oElementOld.find('[data-sub_container_from_id=' + sIdOld + ']').each(function() {
            var oSubElement = $(this);

            var bMoveDesktop = false;
            var sNewId = oSubElement.attr('data-sub_container_from_id_new');
            if($('#' + sNewId).length==0)
            {
                //never exists, move to desktop
                sNewId = main.getDesktop().attr('id');
                bMoveDesktop = true;
            }

            oSubElement.children('.control').each(function() {

                if(bMoveDesktop)
                {
                    /* send new information to server */
                    request.ajax('setControlPosition',{
                        controlid: $(this).attr('id'),
                        left: $(this).css('left'),
                        top: $(this).css('top'),
                        parent:sNewId
                    });
                }
                $(this).appendTo('#' + sNewId);
            });
        });

        //delete old element
        oElementOld.replaceWith(oElement);
        oElementOld.remove();

        //refresh tree
        selectbox.elements.refreshDesktop();
        selectbox.elements.select(oElement);
    }
};
main.initElement = function(oElement) {
};
main.loadElement = function(sControlClass, iLeft, iTop, sParentId, sSiblingRelation, sSiblingId ) {

    //ajax to create desktop
    var sJson = request.ajax('newControl',{
        controlclass: sControlClass,
        left: iLeft + 'px',
        top: iTop + 'px',
        siblingrelation: sSiblingRelation,
        siblingid: sSiblingId,
        parent: sParentId
    });
    var oJson = helper.parseJson(sJson);

    var sId=helper.getJsonItem(oJson,'System/Standard/Id');

    if(sId!="") {
        //ajax to get the html of the desktop
        let sHtml = request.ajax('getControlHtml', {controlid: sId});

        if (sHtml != "") {
            //NEW ELEMENT LOADED
            var oElement = $(sHtml);

            oElement.css('visibility', 'hidden');


            if(oElement.hasClass('absolute'))
            {
                oElement.appendTo("#" + sParentId);

                //lost because appendTo a container
                oElement.css({
                    left: iLeft + 'px',
                    top: iTop + 'px',
                    visibility: 'visible'
                });

                //important information which can different in the real control
                request.ajax('setControlDimension',{
                    controlid: sId,
                    width: oElement.css('width'),
                    height: oElement.css('height')
                });
            }
            else
            {
                if(sSiblingRelation=="after")
                    $('#' + sSiblingId).after(oElement);
                else if(sSiblingRelation=="before")
                    $('#' + sSiblingId).before(oElement);
                else
                    oElement.appendTo("#" + sParentId);

                oElement.css({visibility: 'visible'});
            }

            //add new element
            selectbox.elements.add(oElement);
            selectbox.elements.refreshDesktop();
            selectbox.elements.select(oElement);
        }
    }
};
main.deleteElement = function(oElement) {
    property.clear();
    request.ajax('deleteControl', { controlid: oElement.attr('id') });
    oElement.remove();
};


main.aForms = [];
main.formNew = function() {

    //ajax to create desktop
    var sJson = request.ajax('newForm',null);
    var oJson = helper.parseJson(sJson);

    var sId=helper.getJsonItem(oJson,'System/Standard/Id');
    var sFormId=helper.getJsonItem(oJson,'System/Standard/FormId');
    var sName=helper.getJsonItem(oJson,'System/Standard/Name');
    var sTitle=helper.getJsonItem(oJson,'Standard/Text');
    if(sTitle!="")
        sTitle = sTitle + ' (' + sName + ')';
    else
        sTitle = sName;

    if(sId!="")
    {
        //ajax to get the html of the desktop
        let sHtml = request.ajax('getFormHtml',{ formid: sFormId});

        if(sHtml!="")
        {
            //remove selected flag from current desktop
            $('#desktopborder > .desktop_form.selected').removeClass('selected');

            //add new desktop
            let oDesktopForm = $(sHtml);
            oDesktopForm.appendTo('#desktopborder');

            selectbox.forms.addById(sFormId, sTitle);

            //display new desktop
            main.formSelect(oDesktopForm);
        }
    }
};
main.formEdit = function() {
    property.loadForm(main.getDesktopForm());
};
main.formSelect = function(oDesktopForm) {
    $('#desktopborder > .desktop_form.selected').removeClass('selected');
    oDesktopForm.addClass('selected');
    selectbox.elements.initDesktop(main.getDesktop());

    //console.log('form select');

    property.clear();
    //property.loadForm(oDesktop);
};
main.formDelete = function(oDesktopForm) {

    if(request.ajax('deleteForm', { formid: oDesktopForm.attr('id') } )=="SUCCESS")
    {
        selectbox.forms.remove(oDesktopForm);
        oDesktopForm.remove();
    }
};
main.formsRefresh = function() {
    var sIdSelect= main.getFormId();

    //clear all
    selectbox.forms.oElement.find('option').remove();

    var sJson = request.ajax('getFormsProperties');
    var oJson = helper.parseJson(sJson);

    jQuery.each(oJson, function(sKey, oJsonForm) {
        var sId=helper.getJsonItem(oJsonForm,'System/Standard/Id');
        var sFormId=helper.getJsonItem(oJsonForm,'System/Standard/FormId');
        var sName=helper.getJsonItem(oJsonForm,'System/Standard/Name');
        var sTitle=helper.getJsonItem(oJsonForm,'Standard/Text');
        if(sTitle!="")
            sTitle = sTitle + ' (' + sName + ')';
        else
            sTitle = sName;

        var option = new Option(sTitle, sFormId);
        selectbox.forms.oElement.append($(option));
        if(sIdSelect==sFormId)
            selectbox.forms.oElement.children('option[value='+sId+']').attr('selected','selected');
    });
};

main.getFormId = function() {
    var oDesktopForm = main.getDesktopForm();
    var oRet = null;
    if(oDesktopForm)
    {
        oRet = oDesktopForm.attr('id');
    }
    return oRet;
};
main.getDesktop = function() {
    return $('#desktopborder > .desktop_form.selected .desktop');
};
main.getDesktopId = function() {
    let oDesktop = main.getDesktop();
    let sId = "";
    if(oDesktop)
        sId = oDesktop.attr('id');
    return sId;
};
main.getDesktopForm = function() {
    return $('#desktopborder > .desktop_form.selected');
};


main.getProjectId = function() {
    return $('body').attr('data-projectid');
};
main.setProjectId = function(sId) {
    $('body').attr('data-projectid',sId);
};


main.getProjectPath = function() {
    return $('body').attr('data-projectpath');
};
main.setProjectPath = function(sPath) {
    $('body').attr('data-projectpath',sPath);
    $('#menu_project_path').html(sPath);
};



main.setProjectName = function(sName) {
    $('#menu_project_name').html(sName);
};
main.projectNew = function() {
    if($('#desktopborder > .desktop_form').length!=0) {
        $('#desktopborder > .desktop_form').each(function(i) {
            var oDesktopForm = $(this);
            main.formDelete(oDesktopForm);
        });
    }
    request.ajax('closeProject');

    main.setProjectId('');
    main.setProjectPath('');

    var sJson = request.ajax('newProject');
    var oJson = jQuery.parseJSON(sJson);

    jQuery.each(oJson, function(sKey, oJsonItem) {
        if(oJsonItem.name=="System/Standard/Id")
            main.setProjectId(oJsonItem.value);
        if(oJsonItem.name=="Standard/Text")
            main.setProjectName(oJsonItem.value);
    });
};
main.projectClose = function() {
    request.ajax('closeProject');
};












$('#menu_project button[data-command=new]').click(function() {

    if(main.getProjectId()!="")
    {
        if (!confirm('Really create new project?'))
            return;
    }
    main.projectNew();
    main.formNew();
});
$('#menu_project button[data-command=edit]').click(function() {
    property.loadProject();
});
$('#menu_project button[data-command=load]').click(function() {

    if(main.getProjectId()!="")
    {
        if (!confirm('Really load a project?'))
            return;
    }

    main.waitShow();

    var sId="popup_returnid";
    $('#' + sId).val("");

    var sUrl = "browser.php?" + main.getSessionName() + "=" + main.getSessionId() + "&returnid=" + sId + "&type=open&filebrowserconfig=editor";
    var popup = window.open(sUrl, "Load project", "toolbar=no,menubar=no,width=800,height=600,status=yes,scrollbars=yes,resizable=yes");
    popup.focus();

    var pollTimer = window.setInterval(function() {
        if (popup.closed !== false) { // !== is required for compatibility with Opera
            window.clearInterval(pollTimer);

            var sPath = $('#' + sId).val();
            if(sPath!="")
                main.loadProject(sPath);
            main.waitHide();
        }
    }, 200);

});
$('#menu_project button[data-command=saveas]').click(function() {

    if(main.getProjectId()=="")
    {
        if (!confirm('No project present'))
            return;
    }

    main.waitShow();

    var sId="popup_returnid";
    $('#' + sId).val("");

    var sUrl = "browser.php?" + main.getSessionName() + "=" + main.getSessionId() + "&returnid=" + sId + "&type=save&filebrowserconfig=editor";
    var popup = window.open(sUrl, "Save project", "toolbar=no,menubar=no,width=800,height=600,status=yes,scrollbars=yes,resizable=yes");
    popup.focus();

    var pollTimer = window.setInterval(function() {
        if (popup.closed !== false) { // !== is required for compatibility with Opera
            window.clearInterval(pollTimer);

            var sPath = $('#' + sId).val();
            if(sPath!="")
                main.saveAsProject(sPath);
            main.waitHide();
        }
    }, 200);

});
$('#menu_project button[data-command=save]').click(function() {
    main.saveProject();
});
$('#menu_project button[data-command=open]').click(function() {
    if(main.getProjectPath()=="")
        alert('Please save project first');
    else
        request.redirectInterpreter();
});
$('#menu_project button[data-command=phpinfo]').click(function() {
    request.redirectPhpInfo();
});
main.loadProject = function(sPath) {
    request.redirectEditor('loadProject', {path: sPath});
};
main.saveAsProject = function(sPath) {
    main.setProjectPath(sPath);
    request.ajax('saveAsProject', {path: sPath});
};
main.saveProject = function() {
    var sPath = main.getProjectPath();
    if(sPath!="")
        request.ajax('saveProject', {path: sPath});
    else
        $('#menu_project button[data-command=saveas]').click();
};


$('#menu_form button[data-command=new]').click(function() {
    main.formNew();
});
$('#menu_form button[data-command=up]').click(function() {
    var sId = main.getFormId();
    if(sId!="")
    {
        request.ajax('upForm', { formid: sId });
        main.formsRefresh();
    }
});
$('#menu_form button[data-command=down]').click(function() {
    var sId = main.getFormId();
    if(sId!="")
    {
        request.ajax('downForm', { formid: sId });
        main.formsRefresh();
    }
});
$('#menu_form button[data-command=settaborder]').click(function() {
    var sId = main.getFormId();
    if(sId!="")
    {
        property.clear();
        request.ajax('rebuildTabsForm', { formid: sId });
    }
});
$('#menu_form button[data-command=edit]').click(function() {
    main.formEdit();
});
$('#menu_form button[data-command=delete]').click(function() {

    if($('#desktopborder > .desktop_form.selected').length!=0) {
        var oDesktopForm = $('#desktopborder > .desktop_form.selected');
        if (confirm('Really delete formular?')) {
            main.formDelete(oDesktopForm);

            selectbox.elements.oElement.empty();
            if(oDesktopForm = selectbox.forms.getFirst())
                main.formSelect(oDesktopForm);
        }
    }
});
