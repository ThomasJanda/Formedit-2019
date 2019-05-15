<?php
namespace formedit\core\browser;

class folder extends \formedit\core\browser\browserobject
{
    /**
     * @return \formedit\core\browser\folder
     */
    public static function getRootFolder()
    {
        return new folder(\formedit\inc\config::getInstance()->getBrowserPathReal(), true);
    }

    /**
     * @return \formedit\core\browser\folder
     */
    public static function getSelectedFolder()
    {
        $oFolder = folder::getRootFolder();


        /**
         * @param \formedit\core\browser\folder $oFolder
         * @return \formedit\core\browser\folder|null
         */
        function search($oFolder)
        {
            if($oFolder->isSelected())
                return $oFolder;

            $aFolder = $oFolder->getSubDirectories();
            foreach($aFolder as $oFolder)
            {
                if($oTmp = search($oFolder))
                {
                    return $oTmp;
                }
            }

            return null;
        }

        $oFolder = search($oFolder);

        return $oFolder;
    }

    /**
     * @var bool
     */
    protected $_bOpen=null;

    /**
     * @var bool
     */
    protected $_isRoot=false;

    /**
     * folder constructor.
     * @param $sPath
     * @param bool $bOpen
     * @param bool $isRoot
     */
    public function __construct($sPath, $isRoot=false)
    {
        parent::__construct($sPath);
        $this->_bOpen=($isRoot?true:null);
        $this->_isRoot=$isRoot;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        if($this->_bOpen===null)
        {
            $this->_bOpen=false;
            $aData = \formedit\inc\session::getInstance()->getValue('browser_tree_open');
            if(is_array($aData) && isset($aData[$this->getId()]) && $aData[$this->getId()]=="1")
                $this->_bOpen=true;
        }
        return $this->_bOpen;
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        $sId = \formedit\inc\session::getInstance()->getValue('browser_tree_selected_'.\formedit\inc\config::getInstance()->getCurrentFileBrowserConfig());
        if(($sId=="" && $this->_isRoot) || $this->getId()==$sId)
            return true;
        return false;
    }

    /**
     * @var \formedit\core\browser\folder[]|null
     */
    protected $_aSubDirectories=null;

    /**
     * @return \formedit\core\browser\folder[]
     */
    public function getSubDirectories()
    {
        if( $this->_aSubDirectories===null)
        {
            $this->_aSubDirectories=[];
            $sPath = $this->_sPath."/*";

            $aList = glob($sPath,GLOB_ONLYDIR);
            foreach($aList as $sFolder)
            {
                $this->_aSubDirectories[] = new folder($sFolder);
            }
        }

        return $this->_aSubDirectories;
    }

    /**
     * @return bool
     */
    public function hasSubDirectories()
    {
        if(count($this->getSubDirectories())==0)
            return false;
        return true;
    }

    protected $_aFiles=null;
    /**
     * @return \formedit\core\browser\file[]
     */
    public function getFiles()
    {
        if($this->_aFiles==null)
        {
            $this->_aFiles=[];

            $aList = glob($this->_sPath."/*.*");
            foreach($aList as $sPath)
            {
                $o = new \formedit\core\browser\file($sPath);
                $this->_aFiles[]=$o;
            }
        }
        return $this->_aFiles;
    }
}

