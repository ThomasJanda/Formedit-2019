<?php
namespace formedit\core\interfaces\controls;

use formedit\core\property;

abstract class controlcontainer extends \formedit\core\interfaces\control
{
    /**
     *
     */
    public function loadProperties()
    {
        $this->addProperty(new property('System/Standard/Group name',"Container"));

        parent::loadProperties();

        //modify settings
        $this->getProperty('System/Standard/Has container')->setValue(true);
        $this->getProperty('System/Editor/Background color')->setValue('wheat');
        $this->getProperty('System/Editor/Background color (selected)')->setValue('navajowhite');
        $this->getProperty('System/Editor/Background color (drop)')->setValue('tan');
    }

    /**
     * have to implement and return an array with all ids of the container in the element (id + _0, id + _1....)
     * @return mixed
     */
    abstract public function getContainerIds();

    /**
     * @param $sId
     * @return string
     */
    protected function _getHtmlEditorDeskopContainerAttribute($sId)
    {
        $sRet = "";
        if($this->hasContainer())
        {
            if($aIds = $this->getContainerIds())
            {
                if(in_array($sId, $this->getContainerIds()))
                {
                    $sRet = '
                    data-dropable 
                    data-selectable 
                    data-sub_container_from_id="'.$this->getId().'" id="'.$sId.'" 
                    ';
                }
            }
        }
        return $sRet;
    }

}