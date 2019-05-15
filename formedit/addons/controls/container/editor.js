var container = {};
container.aTimer = [];
container.add = function(oElement) {
    if(oElement.hasClass('relative'))
    {
        let sHeight = oElement[0].style.height;
        sHeight = sHeight.toLowerCase().trim();
        if(sHeight == "auto")
        {
            let aContainer = oElement.children('div[data-sub_container_from_id]');
            if(aContainer.length > 0)
            {
                aContainer.each(function(i) {
                    let oElementContainer = $(this);

                    var aParam={};
                    aParam.elementid=oElement.attr('id');
                    aParam.elementcontainerid=oElementContainer.attr('id');
                    aParam.handletimer = setInterval(function() { container.ajustHeight(aParam) }, 500);

                    container.aTimer.push(aParam);
                });
            }
        }
    }
};
container.remove = function(oElement) {
    var sId = oElement.attr('id');

    let iLength = container.aTimer.length;
    for(let i=iLength - 1; i=0; i++)
    {
        let aTimer = container.aTimer[i];
        if(aTimer.elementid == sId)
        {
            clearInterval(aTimer.handletimer);
            aTimer.splice(i,1);
        }
    }
};
container.ajustHeight = function(aParam) {

    //aParam.elementid
    //aParam.elementcontainerid
    //aParam.handletimer
    let oElement = $('#' + aParam.elementid);
    let iMinHeight = oElement.attr('data-minheight');
    var oElementContainer = $('#' + aParam.elementcontainerid);

    //find most height
    var iHeightContent = iMinHeight;
    oElementContainer.children('.control').each(function(i) {
        let oTmp = $(this);
        //console.log(oTmp.attr('id'));
        let position = oTmp.position();
        let iHeight = position.top + oTmp.outerHeight(true);

        if(iHeight > iHeightContent)
            iHeightContent = iHeight;
    });

    oElement.innerHeight(iHeightContent);
    oElementContainer.innerHeight(iHeightContent);
};


/* add to the event system */
controls.addHook('add', function(oElement) {
    if(oElement.hasClass('control_container'))
    {
        //container.add(oElement);
    }
});
/* remove to the event system */
controls.addHook('remove', function(oElement) {
    if(oElement.hasClass('control_container'))
    {
        //container.remove(oElement);
    }
});
