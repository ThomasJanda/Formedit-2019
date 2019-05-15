<?php
namespace formedit\core\interfaces\controls;

abstract class controlconnection extends \formedit\core\interfaces\control
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Group name', "Connection"));
        $this->addProperty(new \formedit\core\property('Connection/Field', '', \formedit\core\property::TYPE_selectboxObjectMembers, "To which field the value should save to (depends on the connector of the project)", ['', 'Connection/Table'], 'Connection', 'Field'));
        //$this->addProperty(new property('Connection/Field','', \core\property::TYPE_textbox, ));

        parent::loadProperties();

        //modify
        $this->getProperty('System/Editor/Background color')->setValue('lightgreen');
        $this->getProperty('System/Editor/Background color (selected)')->setValue('seagreen');
        $this->getProperty('System/Editor/Background color (drop)')->setValue('mediumseagreen');
    }

    public function loadPropertiesAfter()
    {
        parent::loadPropertiesAfter();

        $aParam = $this->getProperty('Connection/Field')->getParam();
        $aParam[0]=$this->getId();
        $this->getProperty('Connection/Field')->setParam($aParam);

    }
}