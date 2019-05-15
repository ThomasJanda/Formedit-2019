<?php
require __DIR__."/vendor/autoload.php";
$oSession = \formedit\inc\session::getInstance();
$oConfig = \formedit\inc\config::getInstance();

//load project
/**
 * @var \formedit\core\project $oProject
 */
$oProject = null;
$sPath = "";
$fnc = $oConfig->getRequestParameter('fnc');
if ($fnc == "loadProject") {
    $sPath = $oConfig->getRequestParameter('path');
    $oProject = \formedit\core\project::loadProject($sPath);
}
if($oProject == null)
    die("No project");

//load form
/**
 * @var \formedit\core\form $oForm
 */
$oForm = null;
if($oConfig->getRequestParameter('formid')!="")
{
    $sFormId = $oConfig->getRequestParameter('formid');
    $oForm = $oProject->getChildByFormId($sFormId);
}
else
{
    $oForm = reset($oProject->getChildren());
}
if($oForm == null)
    die("No form");

?>
<!doctype html>
<html lang="en">
<head>
    <title>FormEdit Editor</title>
    <meta charset="utf-8">
    <link rel="icon" href="formedit/out/interpreter/img/favicon.ico" type="image/x-icon">
    <style type="text/css">
        @import url('https://code.jquery.com/ui/1.12.1/themes/ui-darkness/jquery-ui.css');
        @import url('formedit/out/interpreter/css/main.css');

        <?php
        /**
        * @var \formedit\core\interfaces\control $oControl
        */
        foreach ($oForm->getChildren() as $oControl) {
            echo $oControl->getCssInterpreter();
        }
        ?>
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
            crossorigin="anonymous"></script>
</head>
<body
      data-sessionid="<?php echo $oSession->getSessionId(); ?>"
      data-sessionname="<?php echo $oSession->getSessionName(); ?>"
      data-projectid="<?php echo($oProject ? $oProject->getId() : "") ?>"
      data-projectpath="<?php echo $sPath; ?>"
      data-formid="<?php echo $oForm->getFormId(); ?>"
      data-desktopid="<?php echo $oForm->getId(); ?>"
>
<?php

echo $oForm->getHtmlInterpreterFull();

?>
<script src="formedit/out/interpreter/js/main.js"></script>
<script src="formedit/out/interpreter/js/request.js"></script>
<?php
/**
 * @var \formedit\core\interfaces\control $oControl
 */
foreach ($oForm->getChildren() as $oControl) {
    echo $oControl->getJsInterpreter();
}

?>
</body>
</html>