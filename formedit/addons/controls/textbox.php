<?php
namespace formedit\addons\controls;

class textbox extends \formedit\core\interfaces\controls\controlconnection
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Textbox'));
        $this->addProperty(new \formedit\core\property('Standard/Text',"", \formedit\core\property::TYPE_textbox, 'Text which should display in the textbox'));
        parent::loadProperties();
    }


    protected function _getHtmlInterpreterDesktopBody()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        $sHtml = "<input type='text'
            tabindex='".$this->getProperty('System/Style/Tab index')->getValue()."'
            value ='".$sText."'>";
        return $sHtml;
    }
}