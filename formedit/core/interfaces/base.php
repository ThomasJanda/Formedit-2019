<?php
namespace formedit\core\interfaces;

abstract class base
{

    /**
     * @return \formedit\inc\session
     */
    public function getSession():\formedit\inc\session
    {
        $oSession = \formedit\inc\session::getInstance();
        return $oSession;
    }

    /**
     * @return \formedit\inc\config
     */
    public function getConfig():\formedit\inc\config
    {
        $oConfig = \formedit\inc\config::getInstance();
        return $oConfig;
    }

    /**
     * @return \formedit\core\project|null
     */
    public function getProject():?\formedit\core\project
    {
        $oConfig = $this->getConfig();
        return $oConfig->getCurrentProject();
    }

    /**
     * @return \formedit\core\interfaces\connection|null
     */
    public function getConnection():?\formedit\core\interfaces\connection
    {
        if($oProject = $this->getProject())
        {
            return $oProject->getConnection();
        }
        return null;
    }

    /**
     * @return \formedit\core\form
     */
    public function getForm():\formedit\core\form
    {
        $oConfig = $this->getConfig();
        return $oConfig->getCurrentForm();

    }

    /**
     * @var \formedit\core\property[]
     */
    protected $_aProperties=[];

    public function getDataForSave()
    {
        $aData=[];
        foreach($this->getProperties() as $oProperty)
        {
            $aData[$oProperty->getName()]=$oProperty->getValue();
        }
        return $aData;
    }
    public function setDataFromSave($aData)
    {
        foreach($aData as $sKey => $sValue)
        {
            if($this->getProperty($sKey))
                $this->getProperty($sKey)->setValue($sValue);
        }
    }


    /**
     * init the control and load all properties
     */
    public function init()
    {
        $this->loadProperties();
        $this->loadPropertiesAfter();
    }

    /**
     * create all properties the object need
     */
    public function loadProperties()
    {
        $this->addProperty( new \formedit\core\property('System/Standard/Id',md5($this->getClassName().'|'.uniqid(''))) );
        $this->addProperty( new \formedit\core\property('System/Standard/Name',$this->getClassName()) );
    }
    public function loadPropertiesAfter()
    {

    }

    /**
     * base constructor.
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * @param \formedit\core\property $oProperty
     */
    public function addProperty($oProperty, $bOnlyIfNotExists=true)
    {
        if(!isset($this->_aProperties[$oProperty->getId()]) || $bOnlyIfNotExists==false)
            $this->_aProperties[$oProperty->getId()]=$oProperty;
        elseif(isset($this->_aProperties[$oProperty->getId()]))
        {
            //is present, do nothing
        }
    }

    /**
     * @param string $sName
     * @return \formedit\core\property|null
     */
    public function getProperty($sName, $bDefaultEmptyProperty = true):?\formedit\core\property
    {
        if($this->getProperties())
        {
            foreach($this->getProperties() as $oProperty)
            {
                if($oProperty->getName()==$sName)
                {
                    return $oProperty;
                }
            }
        }

        if($bDefaultEmptyProperty)
            return new \formedit\core\property();

        return null;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return get_class($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $sValue="";
        if($oProperty = $this->getProperty('System/Standard/Name'))
        {
            $sValue = $oProperty->getValue();
        }
        return $sValue;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $sValue="";
        if($oProperty = $this->getProperty('System/Standard/Type'))
        {
            $sValue = $oProperty->getValue();
        }
        return $sValue;
    }

    /**
     * @return \formedit\core\property[]
     */
    public function getProperties()
    {
        return $this->_aProperties;
    }

    /**
     * @return string
     */
    public function getPropertiesJson()
    {
        return json_encode($this->_aProperties,JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function getPropertiesHtml()
    {
        $sRet = "
        <div class='headline'>".$this->getName()."</div>
        <form>";

        $aGroups=[];
        foreach($this->_aProperties as $oProperty)
        {
            $aGroups[$oProperty->getName()]=$oProperty->getHtml();
        }

        //nat sort for keys
        $knatsort = function( &$array )
        {
            return uksort($array, function($a, $b) {

                $iRet = 0;
                $a = trim(strtolower($a));
                $b = trim(strtolower($b));
                if($a!=$b)
                {
                    //if first part is the same
                    $aPath = $a;
                    $bPath = $b;
                    if(strpos($a,"/")!==false && strpos($b,"/")!==false)
                    {
                        $aPath = dirname($a);
                        $bPath = dirname($b);
                    }

                    $iMinLen = strlen($aPath);
                    if(strlen($bPath)<$iMinLen)
                        $iMinLen = strlen($bPath);

                    //only compare the short part
                    $aShort = substr($a,0,$iMinLen);
                    $bShort = substr($b,0,$iMinLen);

                    if($aShort==$bShort)
                    {
                        if(strlen($a) > strlen($b))
                            $iRet = 1;
                        elseif(strlen($a) < strlen($b))
                            $iRet = -1;
                        else
                            $iRet = strcmp($a,$b);
                    }
                    else
                    {
                        $iRet = strcmp($aShort,$bShort);
                    }
                }
                return $iRet;
            });
        };
        $knatsort($aGroups);

        $sHeadline1="";
        $sHeadline2="";
        foreach($aGroups as $sGroup => $sHtml) {
            $sGroup = explode("/", $sGroup);
            $sName = array_pop($sGroup);
            $sHeadline1Tmp = array_shift($sGroup);
            $sHeadline2Tmp = (count($sGroup) == 0 ? "" : implode("/", $sGroup));


            $bHeadline1 = false;
            $bHeadline2 = false;
            if ($sHeadline1 == "" || $sHeadline1 != $sHeadline1Tmp) {
                $bHeadline1 = true;
                $bHeadline2 = true;
            } elseif ($sHeadline2 != $sHeadline2Tmp) {
                $bHeadline2 = true;
            }

            if ($bHeadline1) {
                if ($sHeadline1 != "") {
                    //close old
                    $sRet .= "</table></div></div>";
                }
                $sHeadline1 = $sHeadline1Tmp;

                if($sHeadline1!="")
                {
                    $oProject = \formedit\inc\config::getInstance()->getCurrentProject();
                    $sState = $oProject->getPropertyGroupState(($sHeadline1));

                    $sRet .= "<div class='group' data-group_container='" . $sState . "' data-group_name='" . ($sHeadline1) . "'>
                    <div class='group_headline'>" . $sHeadline1 . "</div>
                    <div class='group_container'>
                    <table>";
                }

            }
            if ($bHeadline2) {
                $sHeadline2 = $sHeadline2Tmp;
                //if($sHeadline2!="")
                $sRet .= '<tr><th colspan="2" class="group_subheader">' . $sHeadline2 . '</th></tr>';
            }

            //content
            $sRet .= $sHtml;
        }
        if($sHeadline1!="")
        {
            $sRet.="</table></div></div>";
        }

        $sRet.="<div class='group_footer'>
            <input type='hidden' name='type' value='".$this->getType()."'>
            <input type='hidden' name='id' value='".$this->getId()."'>
            <button type='button'>Save</button>
        </div>
        </form>";

        return $sRet;
    }

    /**
     * @param $aData
     */
    public function setProperties($aData)
    {
        foreach($aData as $aValue)
        {
            $sId=$aValue['name'];
            $sValue=$aValue['value'];

            foreach($this->_aProperties as $oProperty)
            {
                if($oProperty->getId()==$sId)
                    $oProperty->setValue($sValue);
            }
        }
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        $sId="";
        if($oProperty = $this->getProperty('System/Standard/Id'))
        {
            $sId = $oProperty->getValue();
        }
        return $sId;
    }

    /**
     * @param $sId
     */
    public function setId($sId)
    {
        $this->getProperty('System/Standard/Id')->setValue($sId);
    }
}