<?php
namespace formedit\addons\controls;

class grid extends \formedit\core\interfaces\controls\controlcontainer
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Grid'));
        $this->addProperty(new \formedit\core\property('Standard/Column count',"3", \formedit\core\property::TYPE_textbox, 'How many columns should the grid have'));
        $this->addProperty(new \formedit\core\property('Standard/Row count',"3", \formedit\core\property::TYPE_textbox, 'How many rows should the grid have'));
        $this->addProperty(new \formedit\core\property('Standard/Gutter',"5", \formedit\core\property::TYPE_textbox, 'Margin (in px) to each container'));
        $this->addProperty(new \formedit\core\property('System/Editor/Min. height (Cell)',10, \formedit\core\property::TYPE_textboxInteger, 'Minimum width in px of a cell'), false);
        $this->addProperty(new \formedit\core\property('System/Editor/Min. width (Cell)',10, \formedit\core\property::TYPE_textboxInteger, 'Minimum width in px of a cell'), false);
        parent::loadProperties();

        $this->getProperty('System/Editor/Min. height')->setValue(90);
        $this->getProperty('System/Editor/Min. width')->setValue(100);
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
     * @return int
     */
    public function getColumnCount()
    {
        $sId=0;
        if($oProperty = $this->getProperty('Standard/Column count'))
        {
            $sId = (int) $oProperty->getValue();
        }
        return $sId;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
        $sId=0;
        if($oProperty = $this->getProperty('Standard/Row count'))
        {
            $sId = (int) $oProperty->getValue();
        }
        return $sId;
    }

    /**
     * @return int
     */
    public function getGutter()
    {
        $sId=0;
        if($oProperty = $this->getProperty('Standard/Gutter'))
        {
            $sId = (int) $oProperty->getValue();
        }
        return $sId;
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
        {
            for($iRow=0; $iRow<$this->getRowCount(); $iRow++)
            {
                for($iCol=0; $iCol<$this->getColumnCount(); $iCol++)
                {
                    if($aRet === null) $aRet=[];
                    $aRet[]=$this->getId()."_".$iRow."_".$iCol;
                }
            }
        }
        return $aRet;
    }

    public function setProperties($aData)
    {
        $iTmp = $this->getRowCount();

        parent::setProperties($aData);

        if($iTmp!=$this->getRowCount() && $this->getRowCount()>0)
        {
            //change height
            $iTmpHeight = $this->getHeightInteger() / $iTmp;
            $this->getProperty('System/Dimension/Absolute/Height')->setValue(((int) $iTmpHeight * $this->getRowCount()) . "px");
        }
    }

    /**
     * @return string
     */
    protected function _getHtmlEditorDesktopBody()
    {
        $sHtml="";
        for($iRow=0; $iRow<$this->getRowCount(); $iRow++)
        {
            $sHtml.='<div class="row">';
            for($iCol=0; $iCol<$this->getColumnCount(); $iCol++)
            {
                $sId = $this->getId()."_".$iRow."_".$iCol;
                $sHtml.='<div class="cell" style="
                    margin:'.$this->getGutter().'px; 
                    min-width: '.$this->getProperty('System/Editor/Min. width (Cell)')->getValue().'px;
                    min-height: '.$this->getProperty('System/Editor/Min. height (Cell)')->getValue().'px;
                    " '.$this-> _getHtmlEditorDeskopContainerAttribute($sId).'>'.$this->_getHtmlEditorDesktopName($sId,$this->getHtmlEditorDesktopText(). " / Cell ".($iRow+1)."-".($iCol+1)).'</div>';
            }
            $sHtml.='</div>';
        }
        return $sHtml;
    }

    protected function _getHtmlInterpreterDesktopBody()
    {
        return $this->_getHtmlEditorDesktopBody();
    }

}