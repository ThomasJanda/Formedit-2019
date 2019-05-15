<?php
namespace formedit\core\interfaces\controls;

abstract class controlbutton extends \formedit\core\interfaces\control
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Group name',"Button"));

        parent::loadProperties();

        //modify
        $this->getProperty('System/Editor/Background color')->setValue('lightblue');
        $this->getProperty('System/Editor/Background color (selected)')->setValue('dodgerblue');
    }
}