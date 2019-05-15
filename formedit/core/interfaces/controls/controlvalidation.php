<?php
namespace formedit\core\interfaces\controls;


abstract class controlvalidation extends \formedit\core\interfaces\control
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Group name',"Validation"));

        parent::loadProperties();

        //override
        $this->getProperty('System/Editor/Background color')->setValue('lightcoral');
        $this->getProperty('System/Editor/Background color (selected)')->setValue('coral');
    }
}