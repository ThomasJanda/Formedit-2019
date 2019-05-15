<?php
//script which call this script have to be on the same domain
if(strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])!==false)
{
    $sPath = $_REQUEST['rootpath'].$_REQUEST['uploadpath'];
    foreach($_FILES as $aFile)
    {
        $sFile = $aFile['name'];
        move_uploaded_file($aFile['tmp_name'], $sPath."/".$sFile);
    }
}
