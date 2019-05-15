<?php
namespace formedit\addons\controls;

class tab extends \formedit\core\interfaces\controls\controlcontainer
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Tab'));
        $this->addProperty(new \formedit\core\property('Standard/Text',"Tab1|Tab2|Tab3", \formedit\core\property::TYPE_textbox,  'Seperate title with |'));

        parent::loadProperties();

        $this->getProperty('System/Editor/Min. height')->setValue(60);
        $this->getProperty('System/Editor/Min. width')->setValue(80);
    }



    /**
     * @return string
     */
    public function _getHtmlEditorDesktopBody()
    {
        $aTitle = explode("|",$this->getProperty('Standard/Text')->getValue());
        $aTitle = array_map('trim',$aTitle);

        $sHtml=$this->_getHtmlEditorDesktopName($this->getId(), $this->getHtmlEditorDesktopText()).'
        <div class="tabheader">';
        for($x=0; $x<count($aTitle); $x++)
        {
            $sId = $this->getId().'_'.$x;
            $sTitle=$aTitle[$x];

            $sHtml.='<button 
            data-tabcontainer_id="'.$sId.'" 
            type="button" 
            class="'.($x==0?'active':'').'"
            >'.$sTitle.'</button>';
        }
        $sHtml.='</div>
        <div class="tabcontent">';
        for($x=0; $x<count($aTitle); $x++)
        {
            $sId = $this->getId().'_'.$x;
            $sTitle=$aTitle[$x];

            $sHtml.='<div 
            '.$this-> _getHtmlEditorDeskopContainerAttribute($sId).'
            class="tabcontainer '.($x==0?'active':'').'"
            >'.$this->_getHtmlEditorDesktopName($sId, $this->getHtmlEditorDesktopText()." / ".$sTitle).'
            </div>';
        }
        $sHtml.='</div>';

        return $sHtml;
    }

    /**
     * @return array
     */
    public function getContainerIds()
    {
        $aRet=[];

        $aTitle = explode("|",$this->getProperty('Standard/Text')->getValue());
        $aTitle = array_map('trim',$aTitle);

        for($x=0; $x<count($aTitle); $x++)
        {
            $aRet[]=$this->getId().'_'.$x;
        }

        return $aRet;
    }





    public function _getHtmlInterpreterDesktopBody()
    {
        $aTitle = explode("|",$this->getProperty('Standard/Text')->getValue());
        $aTitle = array_map('trim',$aTitle);

        $sHtml='
        <div class="tabheader">';
        for($x=0; $x<count($aTitle); $x++)
        {
            $sId = $this->getId().'_'.$x;
            $sTitle=$aTitle[$x];

            $sHtml.='<button 
            data-tabcontainer_id="'.$sId.'" 
            type="button" 
            tabindex="'.$this->getProperty('System/Style/Tab index')->getValue().'"
            class="'.($x==0?'active':'').'"
            >'.$sTitle.'</button>';
        }
        $sHtml.='</div>
        <div class="tabcontent">';
        for($x=0; $x<count($aTitle); $x++)
        {
            $sId = $this->getId().'_'.$x;
            $sControlId = $this->getProperty('System/Standard/Control id')->getValue()."_".$x;

            $sHtml.='<div 
            '.$this-> _getHtmlInterpreterDeskopContainerAttribute($sId, $sControlId).'
            class="tabcontainer '.($x==0?'active':'').'"
            >'.$this->_getHtmlInterpreterDesktopName($sId).'
            </div>';
        }
        $sHtml.='</div>';

        return $sHtml;
    }
}