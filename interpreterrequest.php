<?php
/**
 * handle all requests from interpreter
 */
require __DIR__ . "/vendor/autoload.php";
$oSession = \formedit\inc\session::getInstance();
$oConfig = \formedit\inc\config::getInstance();

$sSuccessString = "SUCCESS";

$fnc = $oConfig->getRequestParameter('fnc');
$sProjectId = $oConfig->getRequestParameter('projectid');
/**
 * @var \formedit\core\project $oProject
 */
$oProject = ($sProjectId != "" ? $oSession->getProject($sProjectId) : null);
$oConfig->setCurrentProjectId($sProjectId);

/**
 * @var \formedit\core\form $oForm
 */
$oForm = null;
$sFormId = $oConfig->getRequestParameter('formid');
if ($sFormId != "" && $oProject != null) {
    $sFormId = $oProject->getIdByFormId($sFormId);
    $oForm = ($sFormId != "" ? $oProject->getChild($sFormId) : null);
    $oConfig->setCurrentFormId($sFormId);
}

$sControlId = $oConfig->getRequestParameter('controlid');
/**
 * @var \formedit\core\interfaces\control $oControl
 */
$oControl = ($sControlId!="" && $oForm!=null?$oForm->getChild($sControlId):null);

switch ($fnc) {

    /**
     * control functions
     */
    case "getControlAjax":
        $sCtrlFnc = $oConfig->getRequestParameter('ctrlfnc');
        echo $oControl->{$sCtrlFnc}();
        die("");
        break;
}