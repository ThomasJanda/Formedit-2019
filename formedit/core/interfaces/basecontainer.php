<?php
namespace formedit\core\interfaces;

abstract class basecontainer extends \formedit\core\interfaces\base
{

    /**
     * @var \formedit\core\interfaces\base[]
     */
    protected $_aChildren=[];

    /**
     * @return string[]
     */
    public function getDataForSave()
    {
        $aData=[];

        if($this->getProperties())
        {
            $aData['properties'] = parent::getDataForSave();
        }
        if($this->getChildren())
        {
            $i=0;
            foreach($this->getChildren() as $oChild)
            {
                $aItem['class']=$oChild->getClassName();
                $aItem['id']=$oChild->getId();
                $aItem['properties']=$oChild->getDataForSave();
                $aData['children'][$i]=$aItem;
                $i++;
            }
        }

        return $aData;
    }
    public function setDataFromSave($aData)
    {
        if($aData['properties'])
        {
            parent::setDataFromSave($aData['properties']);
            $this->loadPropertiesAfter();
        }
        if($aData['children'])
        {
            foreach($aData['children'] as $i => $aDataChild)
            {
                $sClass=$aDataChild['class'];
                $sId=$aDataChild['id'];
                $aProperties=$aDataChild['properties'];

                /**
                 * @var \formedit\core\interfaces\base $oChild
                 */
                $oChild = new $sClass();
                $oChild->setDataFromSave($aProperties);
                $this->addChild($sId,$oChild);
                $oChild->loadPropertiesAfter();
            }
        }

    }

    /**
     *
     */
    public function init()
    {
        parent::init();
        foreach($this->getChildren() as $oChild)
        {
            $oChild->init();
        }
    }

    /**
     * @param $sId
     * @param \formedit\core\interfaces\base $oChild
     */
    public function addChild($sId, $oChild)
    {
        $this->_aChildren[$sId]=$oChild;
    }

    /**
     * @param string $sId
     */
    public function deleteChild($sId)
    {
        unset($this->_aChildren[$sId]);
    }

    /**
     * @return \formedit\core\interfaces\base[]
     */
    public function getChildren()
    {
        return $this->_aChildren;
    }

    /**
     * @param \formedit\core\interfaces\base[] $aChildren
     */
    public function setChildren($aChildren)
    {
        $this->_aChildren = $aChildren;
    }

    /**
     * @param string $sControlId
     * @param string $sSiblingId
     * @param string $sSiblingRelation : before|after
     */
    public function moveChild($sControlId, $sSiblingId, $sSiblingRelation)
    {
        $aChildren = $this->getChildren();
        if($sSiblingId!="" && $sSiblingRelation!="" && isset($aChildren[$sControlId]))
        {
            $oControl = $aChildren[$sControlId];

            //find control an remove it from the array
            unset($aChildren[$sControlId]);

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
            $this->setChildren($aChildrenNew);
        }
    }

    /**
     * @param $sId
     * @return \formedit\core\interfaces\base
     */
    public function getChild($sId)
    {
        return $this->_aChildren[$sId];
    }

    /**
     * @return string
     */
    public function getChildrenProperties()
    {
        $aRet=[];
        foreach($this->_aChildren as $sId => $oChild)
        {
            $aRet[$sId]=$oChild->getProperties();
        }
        return json_encode($aRet,JSON_PRETTY_PRINT);
    }


    /**
     * @param $sId
     */
    public function moveUp($sId)
    {
        if(count($this->getChildren())<2)
            return;

        $sFormIdBefore="";
        $oFormMove=null;
        foreach($this->getChildren() as $sFormId => $oForm)
        {
            if($sFormId==$sId)
            {
                $oFormMove = $oForm;
                break;
            }
            $sFormIdBefore=$sFormId;
        }
        $this->deleteChild($sId);

        $aFormsNew=[];

        foreach($this->getChildren() as $sFormId => $oForm)
        {
            if($sFormId==$sFormIdBefore || $sFormIdBefore=="")
            {
                $aFormsNew[$sId] = $oFormMove;
            }
            $aFormsNew[$sFormId] = $oForm;
        }

        $this->setChildren($aFormsNew);
    }

    /**
     * @param $sId
     */
    public function moveDown($sId)
    {
        if(count($this->getChildren())<2)
            return;

        $sFormIdNext="";
        $bUseNext=false;
        $oFormMove=null;
        foreach($this->getChildren() as $sFormId => $oForm)
        {
            if($bUseNext)
            {
                $sFormIdNext=$sFormId;
                break;
            }
            if($sFormId==$sId)
            {
                $oFormMove = $oForm;
                $bUseNext=true;
            }
        }
        $this->deleteChild($sId);

        $aFormsNew=[];

        foreach($this->getChildren() as $sFormId => $oForm)
        {
            $aFormsNew[$sFormId] = $oForm;
            if($sFormId==$sFormIdNext)
            {
                $aFormsNew[$sId] = $oFormMove;
            }
        }
        if($sFormIdNext=="")
            $aFormsNew[$sId] = $oFormMove;

        $this->setChildren($aFormsNew);
    }


    public function containControl($sControlId)
    {
        if($this->getId()==$sControlId)
            return true;

        if($this->getProperty('System/Standard/Type')->getValue()=='form' || $this->getProperty('System/Standard/Type')->getValue()=='project')
        {
            foreach($this->getChildren() as $oChild)
            {
                if($oChild->getId()==$sControlId)
                    return true;
            }
        }

        return false;
    }
}