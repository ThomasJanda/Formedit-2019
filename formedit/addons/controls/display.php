<?php
namespace formedit\addons\controls;

class display extends \formedit\core\interfaces\controls\controlconnection
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Display'));

        //$this->addProperty(new \core\property('System/Style/Tab index',1000, \core\property::TYPE_textbox));
        $this->addProperty(new \formedit\core\property('Standard/Text',"", \formedit\core\property::TYPE_textbox, 'Text which should display in the label'));
        $this->addProperty(new \formedit\core\property('Standard/Style/Text align',"left", \formedit\core\property::TYPE_selectbox,  '', [ 'left'=>'Left', 'center'=>'Center', 'right' => 'Right' ]));
        $this->addProperty(new \formedit\core\property('Standard/Style/Format',"", \formedit\core\property::TYPE_selectbox, '', [ 'text'=>'Text', 'currency'=>'Currency', 'date' => 'Date', 'datetime' => 'Date/Time' ]));
        parent::loadProperties();
    }

    /**
     * @return null|string
     */
    public function getHtmlEditorDesktopText()
    {
        $sText = $this->getProperty('Connection/Field')->getValue();
        if($sText!="")
            $sText.=" (".$this->getName().")";
        else
            $sText = $this->getName();
        return $sText;
    }

    /**
     * @return string
     */
    public function getHtmlEditorDesktopCss()
    {
        $sCss = parent::getHtmlEditorDesktopCss();
        $sCss.='text-align:'.$sText = $this->getProperty('Standard/Style/Text align')->getValue().';';
        return $sCss;
    }


    protected function _getHtmlInterpreterDesktopBody()
    {
        $sText = $this->getProperty('Connection/Field')->getValue();

        $sHtml = "<div style='
            text-align:".$this->getProperty('Standard/Style/Text align')->getValue().";
            '>".$sText."</div>";
        return $sHtml;
    }
}