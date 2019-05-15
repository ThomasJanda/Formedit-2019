<?php
namespace formedit\core;

class clipboard
{
    /**
     * @param \core\form $oForm
     * @param string[] $aIds
     */
    public static function cut($oForm, $aIds)
    {
        $aClipboard=[];
        $aControls=$oForm->getChildren();
        foreach($aIds as $sId)
        {
            $aClipboard[$sId]=$aControls[$sId];
            unset($aControls[$sId]);
        }
        $oForm->setChildren($aControls);

        //search for elements which parent not exists (top element) and set the parent id = ""

        //create id tranlation list
        $aIdTranslation=[];
        foreach($aClipboard as $sId => $oControl)
        {
            $aIdTranslation[]=$sId;
        }

        /**
         * @var \formedit\core\interfaces\control $oControl
         */
        foreach($aClipboard as $sId => $oControl)
        {
            $sParent = $oControl->getParentId();
            //separate because container
            $f = explode("_", $sParent);
            if(!isset($aIdTranslation[$f[0]]))
            {
                //element not exists, means it is the top element
                $sParent="";
            }
            $oControl->setParentId($sParent);
        }

        //remember in session
        \formedit\inc\session::getInstance()->setValue('clipboard',$aClipboard);
    }

    /**
     * @param \formedit\core\form $oForm
     * @param string[] $aIds
     */
    public static function copy($oForm, $aIds)
    {
        $aClipboard=[];
        $aControls=$oForm->getChildren();
        foreach($aIds as $sId)
        {
            //$aClipboard[$sId]=clone($aControls[$sId]);
            $aData = $aControls[$sId]->getDataForSave();
            $sClassName = $aControls[$sId]->getClassName();
            /**
             * @var \formedit\core\interfaces\base $oTmp
             */
            $oTmp = new $sClassName();
            $oTmp->setDataFromSave($aData);
            $aClipboard[$sId] = $oTmp;
        }

        //create id tranlation list
        $aIdTranslation=[];
        foreach($aClipboard as $sId => $oControl)
        {
            $aIdTranslation[$sId]=uniqid("");
        }

        //set control new ids
        $aTmp = [];
        /**
         * @var \formedit\core\interfaces\control $oControl
         */
        foreach($aClipboard as $sId => $oControl)
        {
            $oControl->setId($aIdTranslation[$sId]);

            $sParent = $oControl->getParentId();
            //separate because container
            $f = explode("_", $sParent);
            if(isset($aIdTranslation[$f[0]]))
            {
                //control exists
                $sParent=$aIdTranslation[$f[0]];
                if(isset($f[1]))
                    $sParent.="_".$f[1];
            }
            else
            {
                //element not exists, means it is the top element
                $sParent="";
            }
            $oControl->setParentId($sParent);

            $aTmp[$aIdTranslation[$sId]]=$oControl;
        }
        $aClipboard=$aTmp;

        //remember in session
        \formedit\inc\session::getInstance()->setValue('clipboard',$aClipboard);
    }

    /**
     * @param \formedit\core\form $oForm
     * @param $sParentElementId
     * @param $sParentContainerId
     * @param $iLeft
     * @param $iTop
     * @return string
     */
    public static function past($oForm, $sParentElementId, $sParentContainerId, $iLeft, $iTop):string
    {
        if(!self::containData())
            return "";

        $aClipboard = \formedit\inc\session::getInstance()->getValue('clipboard');
        $iLeftMin = null;
        $iTopMin = null;
        /**
         * @var \formedit\core\interfaces\control $oControl
         */
        foreach($aClipboard as $sId => $oControl) {

            if ($oControl->getParentId() == "") {
                $iLeftControl = $oControl->getLeftInteger();
                if ($iLeftMin == null || $iLeftControl < $iLeftMin)
                    $iLeftMin = $iLeftControl;

                $iTopControl = $oControl->getTopInteger();
                if ($iTopMin == null || $iTopControl < $iTopMin)
                    $iTopMin = $iTopControl;
            }
        }

        /**
         * @var \formedit\core\interfaces\control $oControl
         */
        foreach($aClipboard as $sId => $oControl)
        {
            if ($oControl->getParentId() == "") {
                $iLeftControl = $oControl->getLeftInteger();
                $iLeftControl = $iLeftControl - $iLeftMin + $iLeft;

                $iTopControl = $oControl->getTopInteger();
                $iTopControl = $iTopControl - $iTopMin + $iTop;

                $oControl->setPosition($iLeftControl.'px', $iTopControl.'px', $sParentContainerId);
            }
        }

        $aControls = $oForm->getChildren();
        foreach($aClipboard as $sId => $oControl)
        {
            $aControls[$sId] = $oControl;
        }
        $oForm->setChildren($aControls);

        //collect html
        $aHtml = [];
        /**
         * @var \formedit\core\interfaces\control $oControl
         */
        foreach($aClipboard as $sId => $oControl)
        {
            if($oControl->getParentId() == $sParentContainerId) {
                $aHtml[$oControl->getId()]=$oForm->getHtmlEditorContainer($oControl->getId());
            }
        }

        //remove all from clipboard
        \formedit\inc\session::getInstance()->deleteValue('clipboard');

        return json_encode($aHtml, JSON_PRETTY_PRINT);
    }

    /**
     * @return bool
     */
    public static function containData()
    {
        if($aClipboard = \formedit\inc\session::getInstance()->getValue('clipboard'))
        {
            return true;
        }
        return false;
    }
}