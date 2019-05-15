<?php
namespace formedit\core;

class project extends \formedit\core\interfaces\basecontainer
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new property('System/Standard/Type','project', property::TYPE_label ));
        $this->addProperty(new property('Standard/Text','PROJECT', property::TYPE_textbox ));
        $this->addProperty(new property('Standard/Description','', property::TYPE_textarea ));
        $this->addProperty(new property('System/FormIndex',0, property::TYPE_hidden ));
        $this->addProperty(new property('System/PropertyGroupState','', property::TYPE_hidden ));

        $aConnections = \formedit\inc\config::getInstance()->getProjectConnections($this);
        $aConnectionData=[];
        $sValue="";
        foreach($aConnections as $oConnection)
        {
            if($sValue=="") $sValue=$oConnection->getClassName();
            $aConnectionData[$oConnection->getClassName()] = $oConnection->getName();
        }
        $this->addProperty(new property('Connection/Type',$sValue, property::TYPE_selectbox, 'Where should the project connect to', $aConnectionData));

        parent::loadProperties();
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->getProperty('Standard/Text')->getValue();
    }


    /**
     * @return \formedit\core\form
     */
    public function newChild()
    {
        $iFormIndex = $this->getFormIndex();
        $iFormIndex++;
        $this->setFormIndex($iFormIndex);
        $sName = "Form ".$iFormIndex;
        $oForm = new \formedit\core\form($sName);
        $this->addChild($oForm->getId(), $oForm);
        return $oForm;
    }


    /**
     * @return int
     */
    public function getFormIndex()
    {
        return $this->getProperty('System/FormIndex')->getValue();
    }

    /**
     * @param $iIndex
     */
    public function setFormIndex($iIndex)
    {
        $this->getProperty('System/FormIndex')->setValue($iIndex);
    }

    /**
     * @param string $sGroup
     * @return string
     */
    public function getPropertyGroupState($sGroup)
    {
        $sRet = 'closed';
        $aValues = $this->getProperty('System/PropertyGroupState')->getValue();
        if(isset($aValues[$sGroup]))
            $sRet = $aValues[$sGroup];
        return $sRet;
    }

    /**
     * @param $sGroup
     * @param $sState
     */
    public function setPropertyGroupState($sGroup, $sState)
    {
        $aValues = $this->getProperty('System/PropertyGroupState')->getValue();
        if(!is_array($aValues))
            $aValues=[];
        $aValues[$sGroup]=$sState;
        $this->getProperty('System/PropertyGroupState')->setValue($aValues);
    }

    /**
     * @return string
     */
    public function getHtmlEditorFull()
    {

    }

    /**
     * @param $sPath
     * @return null
     */
    public static function loadProject($sPath)
    {
        $oProject = null;
        $path_parts = pathinfo($sPath);
        $sExt = strtolower($path_parts['extension']);
        if ($sExt != "") {
            $sPathFull = \formedit\inc\config::getInstance()->getBrowserPathReal() . $sPath;
            $sClass = "\\formedit\\addons\\diskoperations\\" . $sExt;
            /**
             * @var \formedit\core\interfaces\diskoperation $oDiskoperation
             */
            $oDiskoperation = new $sClass();
            $oProject = $oDiskoperation->load($sPathFull);
            \formedit\inc\session::getInstance()->setProject($oProject);
            \formedit\inc\config::getInstance()->setCurrentProjectId($oProject->getId());
        }
        return $oProject;
    }


    /**
     * @var \formedit\core\interfaces\connection $_getConnection
     */
    protected $_getConnection = null;
    public function getConnection():\formedit\core\interfaces\connection
    {
        $sClass = $this->getProperty("Connection/Type")->getValue();

        if($this->_getConnection===false)
            $this->_getConnection=null;

        if($this->_getConnection!==null)
        {
            if($this->_getConnection->getClassName()!=$sClass)
            {
                unset($this->_getConnection);
                $this->_getConnection=null;
            }

        }
        if($this->_getConnection===null)
        {
            $this->_getConnection = false;
            /**
             * @var \formedit\core\interfaces\connection $oConnection
             */
            $aConnections = \formedit\inc\config::getInstance()->getProjectConnections($this);
            foreach($aConnections as $oConnection)
            {
                if($oConnection->getClassName()==$sClass)
                    $this->_getConnection = $oConnection;
            }
        }

        return $this->_getConnection;
    }


    public function getChildByFormId($sFormId):?\formedit\core\form
    {
        /**
         * @var \formedit\core\form $oForm
         */
        foreach($this->getChildren() as $sId=>$oForm)
        {
            if($oForm->getFormId()==$sFormId)
                return $oForm;
        }
        return null;
    }

    /**
     * @param string $sFormId
     * @return string
     */
    public function getIdByFormId($sFormId)
    {
        $oForm = $this->getChildByFormId($sFormId);
        if($oForm!==null)
            return $oForm->getId();
        return "";
    }


    /**
     * @param string $sControlId
     * @return form|null
     */
    public function getFormByControlId($sControlId)
    {
        /**
         * @var \core\form $oForm
         */
        foreach($this->getChildren() as $sId=>$oForm)
        {
            if($oForm->containControl($sControlId))
                return $oForm;
        }
        return null;
    }


    /**
     * @param $sId
     * @return form|null
     */
    public function getFormById($sId)
    {
        if(isset($this->_aChildren[$sId]))
            return $this->_aChildren[$sId];
        return null;
    }


    public function getControlById($sControlId)
    {
        /**
         * @var \core\form $oForm
         */
        foreach($this->getChildren() as $sId=>$oForm)
        {
            if($oControl = $oForm->getChild($sControlId))
                return $oControl;
        }
        return null;
    }

}