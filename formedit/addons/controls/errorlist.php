<?php
namespace formedit\addons\controls;

class errorlist extends \formedit\core\interfaces\controls\controlvalidation
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Errorlist'));

        parent::loadProperties();
    }
}