<?php
namespace formedit\core;

class browser
{
    public static function getExtensions()
    {
        $aExt = [];
        $aPathList = glob(__DIR__."/../addons/diskoperations/*.php");
        natsort($aPathList);
        foreach($aPathList as $sPath)
        {
            $sExt = pathinfo($sPath, PATHINFO_FILENAME);
            $aExt[".".$sExt] = $sExt;
        }
        return $aExt;
    }

    public static function getHtmlTree()
    {
        $oFolderRoot = \formedit\core\browser\folder::getRootFolder();
        $oBrowser = new \formedit\core\browser();
        return $oBrowser->_displayFolders($oFolderRoot);
    }

    /**
     * @param \formedit\core\browser\folder $oFolder
     * @return string
     */
    protected function _displayFolders($oFolder)
    {
        $sHtml='<div 
            id="'.$oFolder->getId().'" 
            data-folderpath="'.$oFolder->getPath().'"
            class="folder '.($oFolder->hasSubDirectories()?'hassubfolders':'').' '.($oFolder->isSelected()?'selected':'').'" 
            '.($oFolder->hasSubDirectories()?($oFolder->isOpen()?'data-folder_open="1"':'data-folder_open="0"'):'').'>
            <div class="title">
                <span></span>
                <label>'.$oFolder->getName().'</label>
            </div>';

        if($oFolder->hasSubDirectories())
        {
            $sHtml.='<div class="folders">';
            $aFoldersSub = $oFolder->getSubDirectories();
            foreach($aFoldersSub as $oFolderSub)
            {
                $sHtml.=$this->_displayFolders($oFolderSub);
            }
            $sHtml.='</div>';
        }
        $sHtml.='</div>';

        return $sHtml;
    }

    /**
     * @param string $sDir
     */
    public static function createFolder($sDir)
    {
        @mkdir($sDir);
    }

    /**
     * @param string $sDir
     * @return bool
     */
    public static function deleteFolder($sDir)
    {
        $aFiles = array_diff(scandir($sDir), array('.','..'));
        foreach ($aFiles as $sFile) {
            (is_dir("$sDir/$sFile")) ? self::deleteFolder("$sDir/$sFile") : unlink("$sDir/$sFile");
        }
        return rmdir($sDir);
    }

    /**
     * @param $sDir
     */
    public static function deleteFile($sDir)
    {
        @unlink($sDir);
    }
}