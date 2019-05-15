<?php
require __DIR__."/vendor/autoload.php";
$oConfig = \formedit\inc\config::getInstance();
$oSession = \formedit\inc\session::getInstance();
$sReturnId = $oConfig->getRequestParameter('returnid'); // if to which we send the path back
$sType = $oConfig->getRequestParameter('type'); // which type (open|save)
$sCKEditor=$oConfig->getRequestParameter('CKEditor');
$sCKEditorFuncNum=$oConfig->getRequestParameter('CKEditorFuncNum');
$sFileBrowserConfig=$oConfig->getRequestParameter('filebrowserconfig');

if($sReturnId=="" && $sCKEditor=="" && $sCKEditorFuncNum=="")
    die("No parameter returnid/CKEDITOR");
if($sType=="")
    die("No parameter type (open|save)");
if($sFileBrowserConfig=="")
    die("No parameter filebrowserconfig");

?>
<!doctype html>
<html lang="en">
<head>
    <title>FormEdit Editor</title>
    <meta charset="utf-8">
    <link rel="icon" href="src/editor/img/favicon.ico" type="image/x-icon">
    <style type="text/css">
        @import url('https://code.jquery.com/ui/1.12.1/themes/ui-darkness/jquery-ui.css');
        @import url('formedit/out/editor/css/variables.css');
        @import url('formedit/out/editor/css/browser.css');
    </style>
</head>
<body data-returnid="<?php echo $sReturnId; ?>"
      data-sessionid="<?php echo $oSession->getSessionId(); ?>"
      data-sessionname="<?php echo $oSession->getSessionName(); ?>"
      data-CKEditor="<?php echo $sCKEditor; ?>"
      data-CKEditorFuncNum="<?php echo $sCKEditorFuncNum; ?>"
      data-filebrowserconfig="<?php echo $sFileBrowserConfig; ?>"
      data-url="<?php echo $oConfig->getBrowserUrl(); ?>"
>
<div id="top">
    <div class="title"><?php echo ($sType=="open"?'Open file':'Save file'); ?></div>
    <div class="action">
        <div class="upload" data-tippy="Upload" onclick="$(this).next().css('display','block'); ">&#x2b06;</div>
        <div class="uploadbox" style="display:none; ">
            <form id="uploadform" action="<?php echo dirname($_SERVER['SCRIPT_URI']); ?>/browserupload.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="rootpath" value="<?php echo \formedit\core\browser\folder::getRootFolder()->getPathReal(); ?>">
            <input type="hidden" name="uploadpath" value="">
            <input type="file" name="upload" value="">
            <button type="submit" style="submit">Upload</button>
            </form>
        </div>
    </div>
</div>
<div id="main">
    <div class="search">
        <input type="text" name="search" value="" autocomplete="off" data-tippy="Search folder">
    </div>
    <div class="treemenu">
        <button type="button" data-command="newfolder" data-tippy="Create a new folder">+</button> <button type="button" data-command="deletefolder" data-tippy="Delete a folder">-</button>
    </div>
    <div class="tree">
        <?php
        echo \formedit\core\browser::getHtmlTree();
        ?>
    </div>
    <div class="path">
        Path: <input type="text" name="path" value="" readonly>
    </div>
    <div class="files"></div>
</div>
<div id="bottom">
    <input type="text" name="file" value="" <?php echo ($sType=="open"?'readonly':''); ?>>
    <?php
    if($sType=="save")
    {
        echo '<select name="ext">';
        $aExt = \formedit\core\browser::getExtensions();
        foreach($aExt as $sKey => $sValue)
        {
            echo "<option value='$sKey'>.$sValue</option>";
        }
        echo '</select>';
    }
    ?>
    <button class="ok" type="button"><?php echo ($sType=="open"?'Open':'Save'); ?></button>
    <button class="cancel" type="button">Cancel</button>
</div>

<!-- always when a web service call, page will blocked -->
<div id="wait"></div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
        crossorigin="anonymous"></script>
<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
<script src="formedit/out/editor/js/jquery-rect.min.js"></script>
<script src="formedit/out/editor/js/jquery.mousewheel.min.js"></script>
<script src="formedit/out/editor/js/request.js"></script>
<script src="formedit/out/editor/js/browser.js"></script>

</body>
</html>
