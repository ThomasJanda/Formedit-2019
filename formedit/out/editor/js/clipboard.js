var clipboard = {};
clipboard.copy = function() {
    var aIds=[];
    var aElementsSelected = main.selectedElementsAll();
    if(aElementsSelected.length > 0)
    {
        $.each(aElementsSelected, function(i, oElementSelected) {
            aIds.push($(oElementSelected).attr('id'));
        });
    }
    request.ajax('clipboardCopy', {controlids: aIds});
};
clipboard.cut = function() {
    var aIds=[];
    var aElementsSelected = main.selectedElementsAll();
    if(aElementsSelected.length > 0)
    {
        $.each(aElementsSelected, function(i, oElementSelected) {
            aIds.push($(oElementSelected).attr('id'));
        });
    }
    request.ajax('clipboardCut', {controlids: aIds});

    //remove the elements
    selector.getControls().remove();

    //clear property
    property.clear();
};
clipboard.past = function(oElement, oContainer, iLeft, iTop) {
    var sJson = request.ajax('clipboardPast',
        {
            controlidparent: oElement.attr('id'),
            containeridparent: oContainer.attr('id'),
            left: iLeft,
            top: iTop
        }
    );
    if(sJson!="")
    {
        var oJson = helper.parseJson(sJson);
        jQuery.each(oJson, function(sId, sHtml) {

            var oElement = $(sHtml);

            oElement.css('visibility', 'hidden');
            oElement.appendTo("#" + oContainer.attr('id'));
            oElement.css('visibility', 'visible');

            //add to selectbox
            //selectbox.elements.addById(oElement.attr('id'), oElement.attr('data-controlname'));

            main.initElement(oElement);
            oElement.find('.control').each(function (ii) {
                var oElementSub = $(this);
                main.initElement(oElementSub);

                //add to selectbox
                //selectbox.elements.addById(oElementSub.attr('id'), oElementSub.attr('data-controlname'));
            });
        });

        //refresh all
        selectbox.elements.refreshDesktop();
    }

    main.unselectElements();

    //clear property
    property.clear();
};
clipboard.containData = function() {
    var sRet = request.ajax('clipboardContainData');
    if(sRet=="1")
        return true;
    return false;
};



