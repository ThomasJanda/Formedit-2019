<?php
define('_FORMEDIT_EDITOR',1);

require __DIR__."/vendor/autoload.php";
$oConfig = \formedit\inc\config::getInstance();
$oSession = \formedit\inc\session::getInstance();

/**
 * @var \core\project $oProject
 */
$oProject = null;
$sPath = "";

//load project
$fnc = $oConfig->getRequestParameter('fnc');
if ($fnc == "loadProject") {
    $sPath = $oConfig->getRequestParameter('path');
    $oProject = \formedit\core\project::loadProject($sPath);
}


?>
<!doctype html>
<html lang="en">
<head>
    <title>FormEdit Editor</title>
    <meta charset="utf-8">
    <link rel="icon" href="formedit/out/editor/img/favicon.ico" type="image/x-icon">
    <style type="text/css">
        @import url('https://code.jquery.com/ui/1.12.1/themes/ui-darkness/jquery-ui.css');
        @import url('formedit/out/editor/css/variables.css');
        @import url('formedit/out/editor/css/main.css');
        @import url('formedit/out/editor/css/controls.css');
        @import url('formedit/out/editor/css/dragdrop.css');
        @import url('formedit/out/editor/css/selector.css');
        @import url('formedit/out/editor/css/resizer.css');
        @import url('formedit/out/editor/css/contextmenu.css');
        @import url('formedit/out/editor/css/tooltip.css');
        @import url('formedit/out/editor/css/menu.css');

<?php
$aAllControls = \formedit\core\controls::getControls();
foreach ($aAllControls as $sGroup => $aControls) {
    /**
    * @var \formedit\core\interfaces\control $oControl
    */
    foreach ($aControls as $oControl) {
        echo $oControl->getCssEditor();
    }
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
<body class="grid"
      data-sessionid="<?php echo $oSession->getSessionId(); ?>"
      data-sessionname="<?php echo $oSession->getSessionName(); ?>"
      data-projectid="<?php echo($oProject ? $oProject->getId() : "") ?>"
      data-projectpath="<?php echo $sPath; ?>"
      data-editorurl="<?php echo dirname($_SERVER['SCRIPT_URI']); ?>"
>
<div class="griditem" id="menu">
    <div id="menu_main" class="left">
        <div class="menu_item" class="left">
            Project
            <div class="menu_sub" id="menu_project" >
                <button data-command="new">New project</button>
                <button data-command="load">Load project</button>
                <hr>
                <button data-command="edit">Edit</button>
                <hr>
                <button data-command="saveas">Save as</button>
                <button data-command="save">Save</button>
                <hr>
                <button data-command="open">Start in interpreter</button>
                <button data-command="phpinfo">Show php info</button>
            </div>
        </div>
        <div class="menu_item" class="left">
            Form
            <div class="menu_sub" id="menu_form">
                <button data-command="new">New</button>
                <button data-command="edit">Edit</button>
                <button data-command="delete">Delete</button>
                <hr>
                <button data-command="up">Move up</button>
                <button data-command="down">Move down</button>
                <hr>
                <button data-command="settaborder">Tab order</button>
            </div>
        </div>
    </div>
    <div id="menu_project_name" class="left"><?php echo($oProject ? $oProject->getText() : ""); ?></div>
    <div id="menu_project_path" class="right"><?php echo $sPath; ?></div>
</div>
<div class="griditem" id="controls">
    <div>
        <div>
            <div class="headline">
                Form
            </div>
            <select id="forms">
                <?php
                if ($oProject) {
                    /**
                     * @var \core\form $oForm
                     */
                    foreach ($oProject->getChildren() as $oForm)
                        echo '<option value="' . $oForm->getFormId() . '">' . $oForm->getTitle() . '</option>';

                }
                ?>
            </select>
        </div>
        <div>
            <div class="headline">Elements</div>
            <select id="elements">
                <?php
                if ($oProject) {
                    /**
                     * @var \formedit\core\form $oForm
                     */
                    if ($oForm = reset($oProject->getChildren())) {
                        foreach ($oForm->getChildren() as $oControl)
                            echo '<option value="' . $oControl->getId() . '">' . $oControl->getName() . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div>
        <div class="headline">
            Tools
            <input type="text" autocomplete="off" id="sidebar_tools_search" value="">
        </div>
        <?php
        $aAllControls = \formedit\core\controls::getControls();
        foreach ($aAllControls as $sGroup => $aControls) {
            echo '<div class="toolbox" data-toolbox_container="closed">
                        <div class="toolbox_headline">' . $sGroup . '</div>
                        <div class="toolbox_container">';
            /**
             * @var \formedit\core\interfaces\control $oControl
             */
            foreach ($aControls as $oControl) {
                echo $oControl->getHtmlEditorSidebar();
            }
            echo '</div></div>';
        }
        ?>
    </div>

</div>
<div class="griditemsizer" data-left=".griditem#controls" data-right=".griditem#desktopborder" data-fixed="left"></div>
<div class="griditem" id="desktopborder">
    <?php
    if ($oProject) {
        foreach ($oProject->getChildren() as $oForm) {
            echo $oForm->getHtmlEditorFull();
        }
    }
    ?>
</div>
<div class="griditemsizer" data-left=".griditem#desktopborder" data-right=".griditem#property" data-fixed="right"></div>
<div class="griditem" id="property">
    <div class="propertybox">
        <div class="headline">
            Properties
            <input type="text" autocomplete="off" id="sidebar_property_search" value="">
        </div>
        <div id="propertybox_container" class="propertybox_container"></div>
    </div>
</div>

<!-- always when a web service call, page will blocked -->
<div id="wait" class="show"></div>

<!-- right click menu on desktop -->
<div id="contextmenu" data-elementid="" data-posx="" data-posy="">
    <div data-role="control" data-function="cut">Cut</div>
    <div data-role="control" data-function="copy">Copy</div>
    <div data-role="control" data-function="past">Past</div>
    <div data-role="control" data-function="delete">Delete</div>

    <div data-role="desktop" data-function="cut">Cut</div>
    <div data-role="desktop" data-function="copy">Copy</div>
    <div data-role="desktop" data-function="past">Past</div>
    <div data-role="desktop" data-function="delete">Delete</div>
</div>

<!-- for popup -->
<input type="hidden" id="popup_returnid" value="">

<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
<script src="formedit/out/editor/js/jquery-rect.min.js"></script>
<script src="formedit/out/editor/js/jquery.mousewheel.min.js"></script>
<!-- CKEditor 5 -->
<!--<script src="https://cdn.ckeditor.com/ckeditor5/11.1.1/classic/ckeditor.js"></script>-->
<!-- CKEditor 4 -->
<script src="https://cdn.ckeditor.com/4.11.1/full/ckeditor.js"></script>
<script src="formedit/out/editor/js/helper.js"></script>
<script src="formedit/out/editor/js/main.js"></script>
<script src="formedit/out/editor/js/selector.js"></script>
<script src="formedit/out/editor/js/resizer.js"></script>
<script src="formedit/out/editor/js/dragdrop.js"></script>
<script src="formedit/out/editor/js/request.js"></script>
<script src="formedit/out/editor/js/toolbar.js"></script>
<script src="formedit/out/editor/js/desktop.js"></script>
<script src="formedit/out/editor/js/property.js"></script>
<script src="formedit/out/editor/js/elements.js"></script>
<script src="formedit/out/editor/js/contextmenu.js"></script>
<script src="formedit/out/editor/js/clipboard.js"></script>
<script src="formedit/out/editor/js/observer.js"></script>
<script src="formedit/out/editor/js/events/other.js"></script>
<script src="formedit/out/editor/js/events/desktop.js"></script>
<script src="formedit/out/editor/js/events/controls.js"></script>
<script src="formedit/out/editor/js/ace/ace.js" charset="utf-8"></script>

<?php
$aAllControls = \formedit\core\controls::getControls();
foreach ($aAllControls as $sGroup => $aControls) {
    /**
     * @var \formedit\core\interfaces\control $oControl
     */
    foreach ($aControls as $oControl) {
        echo $oControl->getJsEditor();
    }
}
?>

<script>
    if (main.getProjectId() == "") {
        //start new project
        main.projectNew();
        main.formNew();
    }
    else
    {
        //init current loaded project
        main.formSelect($('#desktopborder > .desktop_form').first());
    }
    main.waitHide();

</script>
</body>
</html>