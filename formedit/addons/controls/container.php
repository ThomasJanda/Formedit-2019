<?php
namespace formedit\addons\controls;

class container extends \formedit\core\interfaces\controls\controlcontainer
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Container'));
        $this->addProperty(new \formedit\core\property('Standard/Text',"", \formedit\core\property::TYPE_textbox, 'Text which should display in the label'));
        parent::loadProperties();
    }

    /**
     * @return null|string
     */
    public function getHtmlEditorDesktopText()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        if($sText!="")
            $sText.=" (".$this->getName().")";
        else
            $sText = $this->getName();
        return $sText;
    }




    /**
     * if control contain sub container, return the ids of them
     *
     * @return null|array
     */
    public function getContainerIds()
    {
        $aRet = null;
        if($this->hasContainer())
            $aRet = [$this->getId()."_0"];
        return $aRet;
    }




    /**
     * @return string
     */
    protected function _getHtmlInterpreterDesktopBody()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        if($sText!="")
            $sText='<label>'.$sText.'</label>';

        $sId=$this->getId();
        $sControlId=$this->getProperty('System/Standard/Control id')->getValue();
        if($this->hasContainer())
        {
            $sId = $sId."_0";
            $sControlId = $sControlId."_0";
        }


        return '<div
        '.$this-> _getHtmlInterpreterDeskopContainerAttribute($sId, $sControlId).'
        >'.$sText.'
        '.$this->_getHtmlInterpreterDesktopName($sId).'
        </div>';
    }

}