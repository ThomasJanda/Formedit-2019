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
main.getProjectId = function() {
    return "";
};
main.getFormId = function() {
    return "";
};






browser = [];
browser.init = function() {
    $('#main .tree .folder .title span').click(function() {

        var sId = $(this).parent().parent().attr('id');
        var iState = $(this).parent().parent().attr('data-folder_open');
        var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');
        if(iState==1)
            iState=0;
        else
            iState=1;
        $(this).parent().parent().attr('data-folder_open',iState);

        request.ajax('changeBrowserTreeState', { browsertreeid: sId, state: iState, filebrowserconfig: sFileBrowserConfig });

        $(this).parent().children('label').click();
    });

    $('#main .tree .folder .title label').click(function() {
        $('#main .tree .folder.selected').removeClass('selected');
        $(this).parent().parent().addClass('selected');
        var sId = $(this).parent().parent().attr('id');
        var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');

        $('input[name="path"]').val($(this).parent().parent().attr('data-folderpath'));

        request.ajax('setBrowserTreeSelected', { browsertreeid: sId, filebrowserconfig: sFileBrowserConfig});
        var sHtml = request.ajax('getBrowserFileListHtml', { browsertreeid: sId, filebrowserconfig: sFileBrowserConfig });
        $('#main .files').html(sHtml);
        browser.initFiles();
    });

    //load files list
    $('#main .tree .folder.selected > .title label').click();
};
browser.refreshTree = function() {
    var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');

    $('#main .tree').html("");
    var sHtml = request.ajax('browserRefreshTree', { filebrowserconfig: sFileBrowserConfig });
    $('#main .tree').html(sHtml);
    browser.init();
};
browser.initFiles = function() {
    $('#main .files .file.active').click(function() {
        var sPath = $(this).attr('data-file_name');
        $('input[name="file"]').val(sPath);
    });
    $('#main .files .file.active div').dblclick(function() {
        var sPath = $(this).parent().attr('data-file_name');
        $('input[name="file"]').val(sPath);
        $('button.ok').click();
    });
    //console.log('initFiles done');

    $('#main .files .file .action button').click(function() {
        var sPath = $(this).parent().parent().attr('data-file_path');
        var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');

        if(sPath!="" && sPath!=null)
        {
            if(confirm('Do you really want to delete the file?'))
            {
                request.ajax('browserDeleteFile', { filepath: sPath, filebrowserconfig: sFileBrowserConfig });
                $('#main .tree .folder.selected > .title label').click();
            }
        }
    });
};
$('input[name="search"]').keyup(function() {
    var sSearch = $(this).val();
    $('#main .tree .folder').removeClass('hide');
    if(sSearch!="")
    {
        $('#main .tree .folder').addClass('hide');

        $('#main .tree .folder[data-folderpath*="' + sSearch + '"]').each(function( index ) {
            $( this ).removeClass('hide');
            $(this).parents().map(function() {
                $(this).removeClass('hide');
            });
        });
    }
});
$('button.cancel').click(function() {
    window.close();
});
$('button.ok').click(function() {

    var sFilename = $('input[name="file"]').val();
    var sPath = $('input[name="path"]').val();
    if(sFilename=="")
    {
        alert('No file name');
        return;
    }

    if($('select[name="ext"]').length >0)
    {
        var sExt = $('select[name="ext"]').val().toLowerCase();
        var tmp = sFilename.toLowerCase();
        if(!tmp.endsWith(sExt))
            sFilename = sFilename + sExt;
    }

    var sResult = sPath + "/" + sFilename;

    var sReturnId = $('body').attr('data-returnid');
    if(sReturnId!="")
    {
        window.opener.document.getElementById(sReturnId).value=sResult;
    }
    else
    {
        sResult = $('body').attr('data-url') + sResult;

        //var sCKEditor = $('body').attr('data-CKEditor');
        var sCKEditorFuncNum = $('body').attr('data-CKEditorFuncNum');
        window.opener.CKEDITOR.tools.callFunction( sCKEditorFuncNum, sResult );
    }
    window.close();
});

$('button[data-command="newfolder"]').click(function() {
    var sRootFolder = $('input[name="path"]').val();
    var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');
    var sRootFolderName = sRootFolder;
    if(sRootFolderName=="")
        sRootFolderName="Root folder";
    var sName = prompt("Folder name (" + sRootFolderName + ")", "NewFolder");
    if(sName!="" && sName!=null)
    {
        request.ajax('browserNewFolder', { rootfolder: sRootFolder, foldername: sName, filebrowserconfig: sFileBrowserConfig });
        browser.refreshTree();
    }
});
$('button[data-command="deletefolder"]').click(function() {
    var sRootFolder = $('input[name="path"]').val();
    if(sRootFolder=="")
        alert('Root folder can not delete');
    else
    {
        if(confirm('Do you really want to delete the folder and the entire contents?'))
        {
            var sId = $('#main .tree .folder.selected').parents('.folder').attr('id');
            var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');

            request.ajax('browserDeleteFolder', { rootfolder: sRootFolder, filebrowserconfig: sFileBrowserConfig });
            browser.refreshTree();

            $('#main .tree .folder#' + sId + ' > .title label').click();
        }
    }
});
/*
$('button[data-command="upload"]').click(function() {
    var sRootFolder = $('input[name="path"]').val();
    if(confirm('Do you really want to delete the folder and the entire contents?'))
    {
        var sId = $('#main .tree .folder.selected').parents('.folder').attr('id');
        var sFileBrowserConfig = $('body').attr('data-filebrowserconfig');

        request.ajax('browserDeleteFolder', { rootfolder: sRootFolder, filebrowserconfig: sFileBrowserConfig });
        browser.refreshTree();

        $('#main .tree .folder#' + sId + ' > .title label').click();
    }
});
*/
$('form#uploadform').submit(function() {

    var sUrl = $(this).attr('action');
    var sRootFolder = $('input[name="path"]').val();
    $(this).children('input[name="uploadpath"]').val(sRootFolder);

    $.ajax({
        url: sUrl,
        async: false,
        type: "POST",
        data: new FormData(this),
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData:false,        // To send DOMDocument or non processed data file it is set to false
    })
    .done(function(sData) {
        if(sData!="")
            alert(sData);
    })
    .fail(function() {
        /* alert( "error" ); */
    })
    .always(function() {
        let sId = $('#main .tree .folder.selected').attr('id');
        let sFileBrowserConfig = $('body').attr('data-filebrowserconfig');
        request.ajax('setBrowserTreeSelected', { browsertreeid: sId, filebrowserconfig: sFileBrowserConfig });
        var sHtml = request.ajax('getBrowserFileListHtml', { browsertreeid: sId, filebrowserconfig: sFileBrowserConfig });
        $('#main .files').html(sHtml);
        browser.initFiles();
    });
    return false;
});


browser.init();
