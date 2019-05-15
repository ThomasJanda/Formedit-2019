<?php
namespace formedit\core\browser;

abstract class browserobject
{
    /**
     * @var string
     */
    protected $_sPath = "";

    /**
     * browserobject constructor.
     * @param $sPath
     */
    public function __construct($sPath)
    {
        $this->_sPath=$sPath;
    }

    /**
     * @return bool
     */
    public abstract function isSelected();

    /**
     * @return string
     */
    public function getName()
    {
        return basename($this->_sPath);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return md5($this->_sPath);
    }

    /**
     * @return string
     */
    public function getPathReal()
    {
        return $this->_sPath;
    }

    public function getPath()
    {
        $sPathFull = $this->getPathReal();
        $sPathRoot = \formedit\inc\config::getInstance()->getBrowserPathReal();

        $sPath = substr($sPathFull,strlen($sPathRoot));

        return $sPath;
    }

    public function getUrl()
    {
        return \formedit\inc\config::getInstance()->getBrowserUrl().$this->getPath();
    }

}