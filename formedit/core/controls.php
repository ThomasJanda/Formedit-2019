<?php
namespace formedit\core;

class controls
{

    /**
     * @return \formedit\core\interfaces\control[]
     */
    public static function getControls()
    {
        $o = new \formedit\core\controls();
        return $o->loadControls();
    }


    /**
     * @var \formedit\core\interfaces\control[]|null
     */
    protected $_aControls=null;


    /**
     * @return \formedit\core\interfaces\control[]
     */
    public function loadControls()
    {
        if($this->_aControls==null)
        {
            $this->_aControls=[];

            //load all controls
            $aPathList = glob(__DIR__."/../addons/controls/*.php");
            natsort($aPathList);
            foreach($aPathList as $sPath)
            {
                $sClass = "\\formedit\\addons\\controls\\".pathinfo($sPath, PATHINFO_FILENAME);
                /**
                 * @var \formedit\core\interfaces\control $oClass
                 */
                $oClass = new $sClass();
                $sGroupName = $oClass->getGroupName();
                if($sGroupName=="")
                    $sGroupName="Other";
                $this->_aControls[$sGroupName][$sClass]=$oClass;
            }
        }
        return $this->_aControls;
    }


    /**
     * @param string $sClassPath
     * @return interfaces\control|null
     */
    public static function getControl($sClassPath)
    {
        $aAllControls = self::getControls();
        foreach ($aAllControls as $sGroupName => $aControls)
        {
            /**
             * @var \formedit\core\interfaces\control $oControl
             */
            foreach($aControls as $sClass => $oControl)
            {
                if($oControl->getClassName()==$sClassPath)
                {
                    return $oControl;
                }
            }
        }
        return null;
    }

}