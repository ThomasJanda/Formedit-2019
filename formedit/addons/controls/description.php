<?php
namespace formedit\addons\controls;

class description extends \formedit\core\interfaces\control
{

    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Description'));

        $this->addProperty(new \formedit\core\property('Standard/Text',"", \formedit\core\property::TYPE_htmlarea, 'Text which should display in the block'));
        parent::loadProperties();
    }


    protected function _getHtmlEditorDesktopName($sId, $sTitle)
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        //$sText = htmlspecialchars_decode($sText);
        if($sText=="")
            $sText = '<title>'.$this->getName().'</title>';
        return $sText.'<!--CONTENT_'.$sId.'-->';
    }
}