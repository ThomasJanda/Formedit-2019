<?php
namespace formedit\addons\diskoperations;

class cpf2 extends \formedit\core\interfaces\diskoperation
{

    /**
     * @param string $sPath
     * @param \formedit\core\project $oProject
     */
    public function save(string $sPath, \formedit\core\project $oProject)
    {
        @unlink($sPath);
        $aData = $oProject->getDataForSave();
        $sJson = json_encode($aData,JSON_PRETTY_PRINT);
        file_put_contents($sPath, $sJson);
    }

    /**
     * @param string $sPath
     * @return \formedit\core\project
     */
    public function load(string $sPath) : \formedit\core\project
    {
        /**
         * @var \formedit\core\project $oProject
         */
        $oProject = new \formedit\core\project();
        $sJson = file_get_contents($sPath);
        $aData = json_decode($sJson, true);
        $oProject->setDataFromSave($aData);
        return $oProject;
    }

}