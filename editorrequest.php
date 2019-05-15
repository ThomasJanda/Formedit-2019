<?php
/**
 * handle all requests from editor
 */
define('_FORMEDIT_EDITOR',1);
require __DIR__."/vendor/autoload.php";
$oSession = \formedit\inc\session::getInstance();
$oConfig = \formedit\inc\config::getInstance();

$sSuccessString="SUCCESS";

$fnc=$oConfig->getRequestParameter('fnc');
$sProjectId = $oConfig->getRequestParameter('projectid');
/**
 * @var \formedit\core\project $oProject
 */
$oProject = ($sProjectId!=""?$oSession->getProject($sProjectId):null);
$oConfig->setCurrentProjectId($sProjectId);

/**
 * @var \formedit\core\form $oForm
 */
$oForm=null;
$sFormId = $oConfig->getRequestParameter('formid');
if($sFormId!="" && $oProject!=null)
{
    $sFormId = $oProject->getIdByFormId($sFormId);
    $oForm = ($sFormId!=""?$oProject->getChild($sFormId):null);
    $oConfig->setCurrentFormId($sFormId);
}



$sControlId = $oConfig->getRequestParameter('controlid');
/**
 * @var \formedit\core\interfaces\control $oControl
 */
$oControl = ($sControlId!="" && $oForm!=null?$oForm->getChild($sControlId):null);

switch ($fnc)
{
    /**
     * project functions
     */
    case "newProject":
        $oSession->destroy();
        $oProject = new \formedit\core\project();
        $oSession->setProject($oProject);
        echo $oProject->getPropertiesJson();
        
        break;

    case "saveProject":
    case "saveAsProject":
        $sPath = $oConfig->getRequestParameter('path');
        echo $sPath;

        $path_parts = pathinfo($sPath);
        $sExt = strtolower($path_parts['extension']);
        if($sExt!="")
        {
            $sPath = $oConfig->getBrowserPathReal().$sPath;
            $sClass = "\\formedit\\addons\\diskoperations\\".$sExt;
            /**
             * @var \formedit\core\interfaces\diskoperation $oDiskoperation
             */
            $oDiskoperation = new $sClass();
            $oDiskoperation->save($sPath, $oProject);
        }

        break;
    case "closeProject":
        $oSession->setProject(null);
        break;

    case "getProjectProperties":
        echo $oProject->getPropertiesHtml();

        break;
    case "setProjectProperties":
        $sJson = $oConfig->getRequestParameter('param');
        if($sJson!="")
        {
            $sId = "";
            $aData = json_decode($sJson,true);
            $oProject->setProperties($aData);
        }

        break;
    case "refreshProject":
        echo $oProject->getPropertiesJson();

        break;

    case "setResetProjectProperty":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oProject)
            {
                foreach($oProject->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        $oProperty->resetValue();
                        break;
                    }
                }
            }
        }

        break;
    case "getProjectPropertyHtml":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oProject)
            {
                foreach($oProject->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        echo $oProperty->getHtml();
                        break;
                    }
                }
            }
        }

        break;

    /**
     * form functions
     */
    case "getFormsProperties":
        echo $oProject->getChildrenProperties();

        break;
    case "newForm":
        $oForm = $oProject->newChild();

        echo $oForm->getPropertiesJson();

        break;
    case "getFormHtml":
        echo $oForm->getHtmlEditor();

        break;
    case "upForm":
        $oProject->moveUp($sFormId);

        break;
    case "downForm":
        $oProject->moveDown($sFormId);

        break;
    case "deleteForm":
        $oProject->deleteChild($sFormId);
        echo $sSuccessString;

        break;
    case "refreshForm":
        echo $oForm->getPropertiesJson();

        break;
    case "getFormProperties":
        echo $oForm->getPropertiesHtml();

        break;
    case "rebuildTabsForm":
        $oForm->rebuildTabOrder();
        break;

    case "saveEditorForm":
        $sMode = $oConfig->getRequestParameter('mode');
        $sValue = $oConfig->getRequestParameter('value');
        $oForm->setEditorSource($sMode, $sValue);
        //echo $sMode;
        //echo $sValue;
    case "setFormProperties":
        $sJson = $oConfig->getRequestParameter('param');
        if($sJson!="")
        {
            $sId = "";
            $aData = json_decode($sJson,true);
            foreach($aData as $aValue)
            {
                if($aValue['name']=="id")
                {
                    $sId=$aValue['value'];
                    break;
                }
            }

            if($sId!="")
            {
                if($oForm = $oProject->getChild($sId))
                {
                    $oForm->setProperties($aData);
                }
            }
        }

        break;
    case "setResetFormProperty":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oForm)
            {
                foreach($oForm->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        $oProperty->resetValue();
                        break;
                    }
                }
            }
        }

        break;
    case "getFormPropertyHtml":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oForm)
            {
                echo "2";
                foreach($oForm->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        echo $oProperty->getHtml();
                        break;
                    }
                }
            }
        }

        break;


    /**
     * control functions
     */
    case "newControl":
        $sClassPath = $oConfig->getRequestParameter('controlclass');
        $sLeft = $oConfig->getRequestParameter('left');
        $sTop = $oConfig->getRequestParameter('top');
        $sParent = $oConfig->getRequestParameter('parent');
        $sSiblingId = $oConfig->getRequestParameter('siblingid');
        $sSiblingRelation = $oConfig->getRequestParameter('siblingrelation');
        if($oControl = \formedit\core\controls::getControl($sClassPath))
        {
            $oForm->addChild($oControl->getId(), $oControl);
            $oControl->setDefault($oForm);
            $oControl->setPosition($sLeft,$sTop,$sParent);
            $oForm->moveChild($oControl->getId(), $sSiblingId, $sSiblingRelation);
            echo $oControl->getPropertiesJson();
            
        }

        break;
    case "getControlHtml":
        echo $oControl->getHtmlEditorDesktop();
        

        break;
    case "refreshControl":
        echo $oControl->getHtmlEditorDesktop();
        

        break;
    case "deleteControl":
        $oForm->deleteChild($sControlId);
        

        break;
    case "setControlParent":
        $sParent = $oConfig->getRequestParameter('parent');
        $oControl->setParentId($sParent);

        break;
    case "setControlSiblings":
        $sSiblingId = $oConfig->getRequestParameter('siblingid');
        $sSiblingRelation = $oConfig->getRequestParameter('siblingrelation');

        $oForm->moveChild($oControl->getId(), $sSiblingId, $sSiblingRelation);
        /*
        $aChildren = $oForm->getChildren();

        if($sSiblingId!="" && $sSiblingRelation!="")
        {
            //find control an remove it from the array
            unset($aChildren[$oControl->getId()]);

            $aChildrenNew = [];
            //add it to the childern
            foreach($aChildren as $sId=>$oChild)
            {
                if($oChild->getId()==$sSiblingId)
                {
                    if($sSiblingRelation=="before")
                    {
                        $aChildrenNew[$oControl->getId()]=$oControl;
                        $aChildrenNew[$sId] = $oChild;
                    }
                    else
                    {
                        $aChildrenNew[$sId] = $oChild;
                        $aChildrenNew[$oControl->getId()]=$oControl;
                    }
                }
                else
                {
                    $aChildrenNew[$sId] = $oChild;
                }
            }
            $oForm->setChildren($aChildrenNew);
        }
        */
        break;
    case "setControlPosition":
    case "setControlPositionAbsolute":
        $sLeft = $oConfig->getRequestParameter('left');
        $sTop = $oConfig->getRequestParameter('top');
        $sParent = $oConfig->getRequestParameter('parent');
        $oControl->setPosition($sLeft,$sTop,$sParent);

        break;
    case "setControlDimension":
    case "setControlDimensionAbsolute":
        $sWidth = $oConfig->getRequestParameter('width');
        $sHeight = $oConfig->getRequestParameter('height');
        $oControl->setDimension($sWidth,$sHeight);

        break;

    case "setControlDimensionRelative":
        $sWidth = $oConfig->getRequestParameter('width');
        $sHeight = $oConfig->getRequestParameter('height');
        $oControl->setDimensionRelative($sWidth,$sHeight);
        break;

    case "getControlProperties":
        echo $oControl->getPropertiesHtml();
        

        break;
    case "setControlProperties":
        $sJson = $oConfig->getRequestParameter('param');
        if($sJson!="")
        {
            $sId = "";
            $aData = json_decode($sJson,true);
            foreach($aData as $aValue)
            {
                if($aValue['name']=="id")
                {
                    $sId=$aValue['value'];
                    break;
                }
            }

            if($sId!="")
            {
                if($oControl = $oForm->getChild($sId))
                {
                    $oControl->setProperties($aData);
                }
            }
        }
        
        break;
    case "setResetControlProperty":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oControl)
            {
                foreach($oControl->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        $oProperty->resetValue();
                        break;
                    }
                }
            }
        }

        break;
    case "getControlPropertyHtml":
        $sPropertyId = $oConfig->getRequestParameter('propertyid');
        if($sPropertyId!="")
        {
            if($oControl)
            {
                foreach($oControl->getProperties() as $oProperty)
                {
                    if($oProperty->getId() == $sPropertyId)
                    {
                        echo $oProperty->getHtml();
                        break;
                    }
                }
            }
        }

        break;

    /**
     * Editor settings
     */
    case "setPropertyGroupState":
        $sGroup = $oConfig->getRequestParameter('propertygroupname');
        $sState = $oConfig->getRequestParameter('state');
        $oProject->setPropertyGroupState($sGroup,$sState);

        break;


    /**
     * Browser settings
     */
    case "changeBrowserTreeState":

        $sId = $oConfig->getRequestParameter('browsertreeid');
        $iState = $oConfig->getRequestParameter('state');
        if($sId!="")
        {
            $aData = $oSession->getValue('browser_tree_open');
            if(!is_array($aData))
                $aData=[];
            $aData[$sId]=$iState;
            $oSession->setValue('browser_tree_open', $aData);
            //print_r($aData);
        }
        

    case "setBrowserTreeSelected":
        $sId = $oConfig->getRequestParameter('browsertreeid');
        $oSession->setValue('browser_tree_selected_'.$oConfig->getCurrentFileBrowserConfig(), $sId);
        
        break;

    case "getBrowserFileListHtml":

        $sId = $oConfig->getRequestParameter('browsertreeid');
        $oFolder = \formedit\core\browser\folder::getSelectedFolder();
        $aFiles = $oFolder->getFiles();
        foreach($aFiles as $oFile)
        {
            echo $oFile->getHtml();
        }
        
        break;

    case "browserRefreshTree":
        echo \formedit\core\browser::getHtmlTree();

        break;
    case "browserNewFolder":
        $sRootFolder = $oConfig->getRequestParameter('rootfolder');
        $sFolderName = $oConfig->getRequestParameter('foldername');

        $sDir = $oConfig->getBrowserPathReal().$sRootFolder."/".$sFolderName;
        \formedit\core\browser::createFolder($sDir);
        break;

    case "browserDeleteFolder":
        $sRootFolder = $oConfig->getRequestParameter('rootfolder');
        $sDir = $oConfig->getBrowserPathReal().$sRootFolder;

        \formedit\core\browser::deleteFolder($sDir);
        break;

    case "browserDeleteFile":
        $sPath = $oConfig->getRequestParameter('filepath');
        $sPath = $oConfig->getBrowserPathReal().$sPath;
        \formedit\core\browser::deleteFile($sPath);
        break;


    /**
     * clipboard
     */
    case "clipboardCut":

        $aIds = $oConfig->getRequestParameter('controlids',false);
        if(is_array($aIds))
        {
            \formedit\core\clipboard::cut($oForm,$aIds);
        }


        break;

    case "clipboardCopy":

        $aIds = $oConfig->getRequestParameter('controlids', false);
        if(is_array($aIds))
        {
            \formedit\core\clipboard::copy($oForm,$aIds);
        }

        break;

    case "clipboardPast":
        if(\core\clipboard::containData())
        {
            $sParentElement = $oConfig->getRequestParameter('controlidparent');
            $sParentContainer = $oConfig->getRequestParameter('containeridparent');
            $iLeft = $oConfig->getRequestParameter('left');
            $iTop = $oConfig->getRequestParameter('top');

            $sHtml = \formedit\core\clipboard::past($oForm, $sParentElement, $sParentContainer, $iLeft, $iTop);
            echo $sHtml;
        }
        break;

    case "clipboardContainData":
        if(\formedit\core\clipboard::containData())
            echo "1";
        else
            echo "0";

    default:
        http_response_code(400);
}