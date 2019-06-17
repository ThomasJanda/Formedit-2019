<?php
namespace formedit\addons\controls;

class button extends \formedit\core\interfaces\controls\controlbutton
{

    public function loadMethods()
    {
        $this->addMethod(new \formedit\core\method('click','click event' ,['event']));
        parent::loadMethods();
    }

    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Button'));
        $this->addProperty(new \formedit\core\property('Standard/Text',"Save", \formedit\core\property::TYPE_textbox, 'Text which should display in the button'));
        $this->addProperty(new \formedit\core\property('Standard/Action',"save", \formedit\core\property::TYPE_selectbox,  'Which action should execute?<br>Save = Save form<br>Cancel = cancel process<br>Close = close window', [ 'save'=>'Save', 'cancel'=>'Cancel', 'close' => 'Close' ]));
        $this->addProperty(new \formedit\core\property('Standard/Target',"#STARTFORM#", \formedit\core\property::TYPE_selectboxForms,  'Which form should load after action?'));

        parent::loadProperties();
    }

    /**
     * @return null|string
     */
    public function getHtmlEditorDesktopText()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        $sAction = $this->getProperty('Standard/Action')->getValue();
        if($sText!="")
            $sText.=" (".$this->getName().")";
        else
            $sText = $this->getName();
        $sText.=" - ".ucfirst($sAction);

        return $sText;
    }


    protected function _getHtmlInterpreterDesktopBody()
    {
        $sText = $this->getProperty('Standard/Text')->getValue();
        $sHtml = "<button 
            type='button' 
            tabindex='".$this->getProperty('System/Style/Tab index')->getValue()."'
            >".$sText."</button>";
        return $sHtml;
    }
}