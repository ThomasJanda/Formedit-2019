$('body').on('click','div.control_table span.orderby_direction_asc, span.orderby_direction_desc',  function(event) {
    let oTable = $(this).closest('[data-control_id]');
    oTable.find('.orderby > span').removeClass('selected');
    $(this).addClass('selected');
    control_table_refresh(oTable);
});
$('body').on('change','div.control_table div.pages select',  function(event) {
    let oTable = $(this).closest('[data-control_id]');
    control_table_refresh(oTable);
});

$('body').on('click','div.control_table .navigation_new',  function(event) {
    let oTable = $(this).closest('[data-control_id]');
    alert('new');
});
$('body').on('click','div.control_table .navigation_edit',  function(event) {
    let oTable = $(this).closest('[data-control_id]');
    alert('edit');
});
$('body').on('click','div.control_table .navigation_delete',  function(event) {
    event.stopPropagation();
    let oTable = $(this).closest('[data-control_id]');
    alert('delete');
});


function control_table_refresh(oTable)
{
    let aParam={};
    let sId=oTable.attr('id');

    //collect all information from this element
    //page
    if(oTable.find('div.pages > select').length>0)
    {
        aParam[sId + "_LIMIT_START"] = oTable.find('div.pages select').val();
    }

    if(oTable.find('span.orderby > span.selected').length>0)
    {
        aParam[sId + "_ORDERBY_COLUMN"] = oTable.find('span.orderby > span.selected').attr('data-orderby_column');
        aParam[sId + "_ORDERBY_DIRECTION"] =oTable.find('span.orderby > span.selected').attr('data-orderby_direction');
    }

    aParam.controlid = sId
    aParam.ctrlfnc='getHtmlAjax';
    let sHtml = request.ajax('getControlAjax',aParam);
    oTable.find('div.table_data').html(sHtml);
}