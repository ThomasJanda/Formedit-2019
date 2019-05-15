var property={};
property.lastFocus = "";
property.hasChangesOldValue = "";
property.hasChanges = false;
property.noReload=false;

property.refresh = function() {

    $('#property .TYPE_selectboxSwitch select').each(function(i) {
        property.executeSelectSwitch($(this));
    });

    $('#property span[data-tippy]').each(function (i) {
        tippy(this, {content: $(this).attr('data-tippy')});
    });

    /* CKEditor 5 */
    /*
    ClassicEditor.create(document.querySelector( '.group_container table tr.TYPE_htmlarea textarea' ), {
        toolbar: [
            "heading","blockQuote","|",
            "bold","italic","|",
            "numberedList","bulletedList","insertTable","|",
            "undo","redo"
        ]
    }).then(
        editor => {
            // console.log(Array.from( editor.ui.componentFactory.names() ));
            editor.ui.focusTracker.on( 'change:isFocused', ( evt, name, isFocused ) => {
            if ( !isFocused ) {
                    // Do whatever you want with current editor data:
                    //console.log( editor.getData() );
                    editor.updateSourceElement();
                    //save data
                    $('#property .group_footer button').click();
                }
            } );
        }
    ).catch(
        error => { console.error( error ); }
    );
    */

    /* CKEditor 4 */
    $('#property .group_container table tr.TYPE_htmlarea textarea').each(function(i) {
        let sId = "CKEDITOR4_" + $(this).attr('name');
        $(this).attr('id',sId);
        let editor = CKEDITOR.replace( sId, {
            language: 'en',
            customConfig: '',
            filebrowserBrowseUrl: $('body').attr('data-editorurl') + '/browser.php?type=open&filebrowserconfig=assetlink',
            filebrowserImageBrowseUrl: $('body').attr('data-editorurl') + '/browser.php?type=open&filebrowserconfig=assetimage',
            /*filebrowserUploadUrl: $('body').attr('data-editorurl') + '/browser.php?type=open',*/
            /*filebrowserImageUploadUrl: '/browser.php?type=open',*/
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                { name: 'forms', groups: [ 'forms' ] },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                '/',
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                '/',
                { name: 'links', groups: [ 'Link','Unlink','Anchor','image' ] },
                { name: 'insert', groups: [ 'insert' ] },
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] },
                { name: 'about', groups: [ 'about' ] }
            ],
            removeButtons: 'Save,NewPage,Preview,Print,Templates,Find,Replace,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Flash,Smiley,PageBreak,Iframe,Format,Font,FontSize,About,Styles'
        });
        editor.on( 'blur', function( evt ) {
            let sName = editor.name;
            $('#' + sName).val(evt.editor.getData());
            $('#property .group_footer button').click();
        });
    });

    property.openStandard();


    property.hasChangesOldValue="";
    property.hasChanges=false;
    property.noReload=false;

    property.setFocus();
};
property.setFocus = function() {
    if(property.lastFocus!="")
        $('#property .group [name="' + property.lastFocus + '"]').focus();
};
property.loadControl = function(oElement) {

    if(property.noReload==true)
    {
        property.noReload=false;
        return;
    }

    var sControlId = oElement.attr('id');
    var sPropertyHtml = request.ajax('getControlProperties', { controlid: sControlId } );
    $('#propertybox_container').html(sPropertyHtml);
    property.refresh();
};
property.loadForm = function(oDesktop) {

    if(property.noReload==true)
    {
        property.noReload=false;
        return;
    }

    var sFormId = oDesktop.attr('id');
    var sPropertyHtml = request.ajax('getFormProperties', { formid: sFormId} );
    $('#propertybox_container').html(sPropertyHtml);
    property.refresh();
};
property.loadFormById = function(sId) {

    if(property.noReload==true)
    {
        property.noReload=false;
        return;
    }

    var sPropertyHtml = request.ajax('getFormProperties', { formid: sId} );
    $('#propertybox_container').html(sPropertyHtml);
    property.refresh();
};

property.loadProject = function() {

    if(property.noReload==true)
    {
        property.noReload=false;
        return;
    }

    var sPropertyHtml = request.ajax('getProjectProperties');
    $('#propertybox_container').html(sPropertyHtml);
    property.refresh();
};

property.clear = function() {
    $('#propertybox_container').html("");
    property.refresh();
};

property.executeSearch = function() {
    var sVal = $('input#sidebar_property_search').val().toLowerCase();
    if(sVal=="")
    {
        //display all, close all headline
        $('#property .group').attr('data-group_container','closed');
        $("#property .group .group_container table tr" ).css('display','table-row');
        property.openStandard();
    }
    else
    {
        //open all headline, display line which match
        $('#property .group').attr('data-group_container','open');
        $("#property .group .group_container table tr[data-searchtext*='" + sVal + "']" ).css('display','table-row');
        $("#property .group .group_container table tr[data-searchtext]").not("[data-searchtext*='" + sVal + "']" ).css('display','none');
    }
};
property.openStandard = function() {
    //if only one group, open it, but do not save it
    if($('#property .group').length==1) {
        $('#property .group').attr('data-group_container','open');
    };

    //if more then one and all closed, open standard
    if($('#property .group').length>=1 &&
        $('#property .group[data-group_container=closed]').length == $('#property .group').length) {
        $('#property .group[data-group_name=Standard]').attr('data-group_container',1);
    };
};

property.executeSelectSwitch = function(oElement) {
    var sKey = oElement.val();
    var sGroupKey = oElement.parent().parent().attr('data-groupkey');

    $('#property tr[data-groupkey="' + sGroupKey + '"]').each(function () {
        if ($(this).attr('data-key') != "") {
            if ($(this).attr('data-key') == sKey)
                $(this).css('display', 'table-row');
            else
                $(this).css('display', 'none');
        }
    });
};





$('#property').on('click','button.property_popup_button', function(event) {
    main.waitShow();
    let sId = $(this).attr('data-popup');
    let oJson = {};
    let sJson = $(this).next().val();
    if(sJson!="")
        oJson = JSON.parse(sJson);

    $.each( oJson, function( key, value ) {
        var oInput = $('#' + sId).find('[data-name="' + key + '"]');
        if(oInput.prop("tagName").toLowerCase()=="input" && oInput.attr('type')=="checkbox")
        {
            if(value=="1")
                oInput.prop("checked", true);
            else
                oInput.prop("checked", false);
        }
        else if(oInput.prop("tagName").toLowerCase()=="select")
        {
            oInput.val(value);
        }
        else
        {
            oInput.val(value);
        }
    });

    $('#' + sId).attr('data-open','1');
    $(document).trigger( "property_popup_open" );
});
$('#property').on('click','div.property_popup > div.property_popup_close', function(event) {
    $(this).parent().attr('data-open','0');
    main.waitHide();
    $(document).trigger( "property_popup_close" );
});
$('#property').on('click','div.property_popup > div.property_popup_buttons > button.property_popup_buttons_cancel', function(event) {
    $('#property div.property_popup > div.property_popup_close').click();
});
$('#property').on('click','div.property_popup > div.property_popup_buttons > button.property_popup_buttons_save', function(event) {
    $('#property div.property_popup > div.property_popup_close').click();

    var oJson={};
    $(this).parent().parent().find('[data-name]').each(function() {

        let value="";
        if($(this).prop("tagName").toLowerCase()=="input" && $(this).attr('type')=="checkbox")
        {
            if($(this).prop("checked"))
                value="1";
            else
                value="0";
        }
        else
        {
            value=$(this).val();
        }
        oJson[$(this).attr('data-name')] = value;
    });

    $(this).parent().parent().next().next().val(JSON.stringify(oJson));
    $('#property .group_footer button').click();
    refreshAttachedProperties($(this).parent().parent().next().next());
});




$('#property').on('keyup', 'input#sidebar_property_search',  function(event) {
    property.executeSearch();
});


$('#property').on('change', '.TYPE_selectboxSwitch select',  function(event) {
    property.executeSelectSwitch($(this));
});


$('#property').on('keypress', '.TYPE_textboxInteger input',  function(event) {
    //console.log(e.which);
    if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
        return false;
    }
    return true;
});
$('#property').on('blur', '.TYPE_textboxInteger input',  function(event) {
    var sVal = $(this).val();
    if (parseFloat(sVal) != NaN) {
        $(this).val(parseInt(sVal));
    }
    else {
        $(this).val("");
    }
});


$('#property').on('keypress', '.TYPE_textboxIntegerAndNegative input',  function(event) {
    //console.log(e.which);
    if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57) && event.which != 45) {
        return false;
    }
    return true;
});
$('#property').on('blur', '.TYPE_textboxIntegerAndNegative input',  function(event) {
    var sVal = $(this).val();
    if (parseFloat(sVal) != NaN) {
        $(this).val(parseInt(sVal));
    }
    else {
        $(this).val("");
    }
});

$('#property').on('blur', '.TYPE_textboxPopulate input',  function(event) {
    var sVal = $(this).val();
    var sJson = $(this).attr('data-populate_to');
    var oJson = helper.parseJson(sJson);
    for(let i=0;i<oJson.length;i++)
    {
        let name = oJson[i];
        $('#property [data-name_property="' + name + '"]').val(sVal);
    }
    $(this).val("");
    $('#property .group_footer button').click();
});



$('#property').on('click', '.group .group_headline',  function(event) {
    var sName = $(this).parent().attr('data-group_name');
    var sData = $(this).parent().attr('data-group_container');
    if (sData == "closed")
        sData = "open";
    else
        sData = "closed";
    $(this).parent().attr('data-group_container', sData);

    request.ajax('setPropertyGroupState', {propertygroupname: sName, state: sData});
});

$('#property').on('click', '.group_footer button',  function(event) {
    var oFormular = $(this).closest('form');

    property.noReload = true;

    var sFnc = "";
    var sType = oFormular.find('input[type=hidden][name=type]').val();
    var sId = oFormular.find('input[type=hidden][name=id]').val();
    if (sType == "project")
        sFnc = "setProjectProperties";
    else if (sType == "form")
        sFnc = "setFormProperties";
    else if (sType == "control")
        sFnc = "setControlProperties";

    var aParams = oFormular.serializeArray();
    var sParam = JSON.stringify(aParams);

    if (sFnc != "") {
        request.ajax(sFnc, {param: sParam});

        if (sType == "control") {
            main.refreshElement(sId);
        }
        else if (sType == "form") {
            var sJson = request.ajax('refreshForm', null);
            var oJson = helper.parseJson(sJson);

            var sId = helper.getJsonItem(oJson, 'System/Standard/Id');
            var sFormId = helper.getJsonItem(oJson, 'System/Standard/FormId');
            var sName = helper.getJsonItem(oJson, 'System/Standard/Name');
            var sTitle = helper.getJsonItem(oJson, 'Standard/Text');
            var iMinWidth = helper.getJsonItem(oJson, 'Standard/Min. width');
            var iMaxWidth = helper.getJsonItem(oJson, 'Standard/Max. width');
            var sDefaultDimensionPosition=helper.getJsonItem(oJson, 'Default/Dimension/Position');
            if (sTitle != "")
                sTitle = sTitle + ' (' + sName + ')';
            else
                sTitle = sName;
            main.getDesktop().css({'min-width': iMinWidth + 'px', 'max-width': iMaxWidth + 'px'});
            main.getDesktop().attr('data-defaultdimensionposition',sDefaultDimensionPosition);
            selectbox.forms.refreshById(sFormId, sTitle);
        }
        else if (sType == "project") {
            var sJson = request.ajax('refreshProject');
            var oJson = jQuery.parseJSON(sJson);

            jQuery.each(oJson, function (sKey, oJsonItem) {
                if (oJsonItem.name == "Standard/Text")
                    main.setProjectName(oJsonItem.value);
            });
        }
    }
});
$('#property').on('submit', 'form',  function(event) {
    $('#property .group_footer button').click();
    return false;
});






$('#property').on('focus', '.group input[name][type=text], ' +
    '.group input[name][type=password],  ' +
    '.group textarea[name], ' +
    '.group select[name]',  function(event) {
    property.hasChangesOldValue = $(this).val();
    property.lastFocus = $(this).attr('name');
});
$('#property').on('focus', '.group input[name][type=checkbox]',  function(event) {
    property.hasChangesOldValue = $(this).attr('checked');
    property.lastFocus = $(this).attr('name');
});
$('#property').on('blur', '.group input[name][type=text], ' +
    '.group input[name][type=password], ' +
    '.group textarea[name]',  function(event) {
    if (property.hasChangesOldValue = $(this).val() != property.hasChangesOldValue) {
        $('#property .group_footer button').click();
    }
});
function refreshAttachedProperties(oElement)
{
    let oTableRow = oElement.parent().parent();
    if(oTableRow.hasClass('TYPE_selectboxObjects') || oTableRow.hasClass('TYPE_dataset'))
    {
        //console.log('test');
        let oFormular = oElement.closest('form');
        let sType = oFormular.find('input[type=hidden][name=type]').val();
        let sId = oFormular.find('input[type=hidden][name=id]').val();

        //special element, load some properties from the server because has to reset
        let aTableRows = oTableRow.parent().children('tr[data-groupkey="' + oTableRow.attr('data-groupkey') + '"]');
        aTableRows.each(function() {
            if($(this).attr('data-key')!=oTableRow.attr('data-key'))
            {
                //console.log('yes');
                let aParam={};
                let sFnc = "";

                //reset property
                aParam={};
                sFnc="";
                if (sType == "project")
                {
                    sFnc = "setResetProjectProperty";
                    aParam.projectid = sId;
                }
                else if (sType == "form")
                {
                    sFnc = "setResetFormProperty";
                    aParam.projectid = main.getProjectId();
                    aParam.formid = main.getFormId();
                }
                else if (sType == "control")
                {
                    sFnc = "setResetControlProperty";
                    aParam.projectid = main.getProjectId();
                    aParam.formid = main.getFormId();
                    aParam.controlid = sId;
                }
                aParam.propertyid = $(this).attr('data-id');
                request.ajax(sFnc, aParam);

                //load html
                aParam={};
                sFnc="";
                if (sType == "project")
                {
                    sFnc = "getProjectPropertyHtml";
                    aParam.projectid = sId;
                }
                else if (sType == "form")
                {
                    sFnc = "getFormPropertyHtml";
                    aParam.projectid = main.getProjectId();
                    aParam.formid = main.getFormId();
                }
                else if (sType == "control")
                {
                    sFnc = "getControlPropertyHtml";
                    aParam.projectid = main.getProjectId();
                    aParam.formid = main.getFormId();
                    aParam.controlid = sId;
                }
                aParam.propertyid = $(this).attr('data-id');
                let html = request.ajax(sFnc, aParam);
                $(this).replaceWith(html);
            }
        });
    }
}

$('#property').on('change', '.group select[name]',  function(event) {
    if (property.hasChangesOldValue = $(this).val() != property.hasChangesOldValue) {
        $('#property .group_footer button').click();
        refreshAttachedProperties($(this));
    }
});
$('#property').on('change', '.group input[name][type=checkbox]',  function(event) {
    if (property.hasChangesOldValue = $(this).attr('checked') != property.hasChangesOldValue) {
        $('#property .group_footer button').click();
    }
});
$('#property').on('keyup', '.group input[name][type=text], ' +
    '.group input[name][type=password]',  function(event) {
    if (event.which == 13) {
        $(this).blur();
        property.setFocus();
    }
});