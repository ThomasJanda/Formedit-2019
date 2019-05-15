<?php
namespace formedit\addons\controls;

class clearfix extends \formedit\core\interfaces\control
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','ClearFix'));
        parent::loadProperties();
    }
}