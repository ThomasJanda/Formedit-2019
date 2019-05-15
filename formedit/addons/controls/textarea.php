<?php
namespace formedit\addons\controls;

class textarea extends \formedit\core\interfaces\controls\controlconnection
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Textarea'));
        $this->addProperty(new \formedit\core\property('Standard/Text',"", \formedit\core\property::TYPE_textbox, 'Text which should display in the textarea'));
        parent::loadProperties();
    }

    protected function _getHtmlInterpreterDesktopBody()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        $sHtml = "<textarea
            tabindex='".$this->getProperty('System/Style/Tab index')->getValue()."'
            >".$sText."</textarea>";
        return $sHtml;
    }
}