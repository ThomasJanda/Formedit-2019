var selectbox = {};
selectbox.forms = {};
selectbox.forms.oElement=$('#forms');
selectbox.forms.init=function() {
    selectbox.forms.oElement.change(function() {
        selector.state.clear();

        var id = $(this).val();
        var oDesktopForm = $('#' + id);
        main.formSelect(oDesktopForm);
    });
};
selectbox.forms.remove=function(oDesktopForm) {
    selectbox.forms.removeById(oDesktopForm.attr('id'));
};
selectbox.forms.removeById=function(sFormId) {
    selectbox.forms.oElement.children('[value=' + sFormId + ']').remove();
};
selectbox.forms.refreshById=function(sFormId, sName) {
    selectbox.forms.oElement.children('[value=' + sFormId + ']').html(sName);
};
selectbox.forms.addById=function(sFormId, sName) {
    var option = new Option(sName, sFormId);
    selectbox.forms.oElement.append($(option));
    selectbox.forms.selectById(sFormId);
};
/*
selectbox.forms.select=function(oDesktop) {

    selectbox.forms.oElement.children('option').removeAttr('selected');
    selectbox.forms.oElement.children('option[value='+oDesktop.attr('id')+']').attr('selected','selected');
    selectbox.forms.oElement.change();

    property.loadForm(oDesktop);
};
*/
selectbox.forms.selectById=function(sId) {

    selectbox.forms.oElement.children('option').removeAttr('selected');
    selectbox.forms.oElement.children('option[value='+sId+']').attr('selected','selected');
    selectbox.forms.oElement.change();

    property.loadFormById(sId);
};

selectbox.forms.getFirst=function() {
    var id = selectbox.forms.oElement.children('option:first').val();
    if(id!=undefined)
    {
        return $('#' + id);
    }
    return false;
};








selectbox.elements = {};
selectbox.elements.oElement=$('#elements');
selectbox.elements.init=function() {
    selectbox.elements.oElement.change(function() {
        selector.state.clear();

        var id = $(this).val();
        var oElement = $('#' + id);

        //oElement.addClass('selected');
        main.selectElement(oElement)
        property.loadControl(oElement);
    });
};
selectbox.elements.refreshDesktop = function() {
  selectbox.elements.initDesktop(main.getDesktop());
};
selectbox.elements.initDesktop = function(oDesktop) {

    var sIdSelected = selectbox.elements.oElement.val();

    // remove all options
    selectbox.elements.oElement.empty();

    function addElements(oContainer, sOffset, sIdSelected)
    {
        if(sOffset==undefined)
            sOffset = "";

        //add all controls
        oContainer.children('.control').each(function(i) {

            let oElement = $(this);

            let sTitle = oElement.attr('data-controlname');
            if(oElement.children('title').length==1)
                sTitle = oElement.children('title').html();
            if(oElement.children('div').children('title').length==1)
                sTitle = oElement.children('div').children('title').html();

            let option = new Option(sOffset + sTitle, oElement.attr('id'));
            selectbox.elements.oElement.append($(option));
            if(oElement.attr('id')==sIdSelected)
            {
                //console.log('ja');
                $(option).attr('selected', true);
            }


            oElement.find('[data-sub_container_from_id=' + oElement.attr('id') + '][data-dropable]').each(function(i) {
                let oSubElement = $(this);

                let sTitle = oSubElement.children('title').html();
                if(sTitle=="")
                    sTitle = "Container";

                let option = new Option(sOffset + "-" + sTitle, oSubElement.attr('id'));
                selectbox.elements.oElement.append($(option));
                $(option).attr('disabled', true);

                addElements(oSubElement, sOffset + "--", sIdSelected);
            });
        });
    }

    addElements(oDesktop, "", sIdSelected);


    //init editor
    main.getDesktopForm().children('.desktop_panel').children().children('pre').each(function() {
        if($(this).attr('data-init')==undefined)
        {
            var mode = $(this).attr('data-mode');
            var e = ace.edit($(this)[0]);
            e.setTheme("ace/theme/twilight");
            e.session.setMode({path:"ace/mode/" + mode.toLowerCase(), inline: true});

            $(this).attr('data-init','1');

            e.on("blur", function(event) {
                let value = e.getValue();
                let oDesktopForm = main.getDesktopForm();
                request.ajax('saveEditorForm', { formid: oDesktopForm.attr('id'), mode: mode, value:value });
            });
        }
    });
};
selectbox.elements.remove=function(oElement) {
    selectbox.elements.removeById(oElement.attr('id'));
};
selectbox.elements.removeById = function(sId) {
    selectbox.elements.oElement.children('[value=' + sId + ']').remove();
};
selectbox.elements.add=function(oElement) {
};


selectbox.elements.select=function(oElement) {

    selectbox.elements.oElement.children('option').removeAttr('selected');
    selectbox.elements.oElement.children('option[value='+oElement.attr('id')+']').attr('selected','selected');
    selectbox.elements.oElement.change();
};


selectbox.forms.init();
selectbox.elements.init();
