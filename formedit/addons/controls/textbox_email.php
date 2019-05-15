<?php
namespace formedit\addons\controls;

class textbox_email extends \formedit\core\interfaces\controls\controlconnection
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name','Textbox Email'));
        parent::loadProperties();
    }
}