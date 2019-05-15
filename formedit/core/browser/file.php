<?php
namespace formedit\core\browser;

class file extends \formedit\core\browser\browserobject
{
    /**
     * @return bool
     */
    public function isSelected()
    {
        $sId = \formedit\inc\session::getInstance()->getValue('browser_file_selected');
        if(($sId=="" && $this->_isRoot) || $this->getId()==$sId)
            return true;
        return false;
    }

    public function getHtml()
    {
        $sHtml="<div class='file ".($this->isActive()?'active':'inactive')."' data-file_path='".$this->getPath()."' data-file_name='".$this->getName()."'>
        <div class='img'>
            <img src='".$this->getImage()."' border='0'>
        </div>
        <div class='title'>".$this->getName()."</div>
        <div class='info'>Size: ".$this->getFileSize()." | Ext: ".$this->getExtension().($this->isImageType()?" | Size: ".implode("x",$this->getImageSize()):"")."</div>
        <div class='action'><button type='button' data-command='deleteFile' data-tippy='Delete file'>-</button></div>
        </div>";
        return $sHtml;
    }

    public function isActive()
    {
        $sExt = $this->getExtension();
        $aExt = \formedit\inc\config::getInstance()->getBrowserExtensions();
        if($sExt=="" || (count($aExt)>0 && !in_array($sExt, $aExt)))
            return false;
        return true;
    }

    public function getExtension()
    {
        $path_parts = pathinfo($this->getPathReal());
        return strtolower($path_parts['extension']);
    }

    public function isImageType()
    {
        $sExt = $this->getExtension();
        if($sExt=="png" || $sExt=="jpg")
            return true;
        return false;
    }

    public function getImageSize()
    {
        $aRet=[];
        $aRet['width']=0;
        $aRet['height']=0;

        if($aInfo = @getimagesize($this->getPathReal()))
        {
            $aRet['width']=$aInfo[0];
            $aRet['height']=$aInfo[1];
        }
        return $aRet;
    }

    public function getImage()
    {
        $sPath = "";
        $sExt = $this->getExtension();
        if($this->isImageType())
            $sPath=$this->getUrl();
        elseif(file_exists(__DIR__."/../../src/editor/img/mime/".$sExt."-icon-24x24.png"))
            $sPath=\formedit\inc\config::getInstance()->getUrl()."formedit/out/editor/img/mime/".$sExt."-icon-24x24.png";
        else
            $sPath=\formedit\inc\config::getInstance()->getUrl()."formedit/out/editor/img/file.png";
        return $sPath;
    }

    public function getFileSize()
    {
        $bytes = @filesize($this->getPathReal());
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}