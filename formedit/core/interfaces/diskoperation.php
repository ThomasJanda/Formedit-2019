<?php
namespace formedit\core\interfaces;

abstract class diskoperation
{
    /**
     * @param string $sPath
     * @param \core\project $oProject
     * @return mixed
     */
    abstract function save(string $sPath, \formedit\core\project $oProject);

    /**
     * @param string $sPath
     * @return \core\project
     */
    abstract function load(string $sPath) : \formedit\core\project;
}