<?php
namespace formedit\core;

class method implements \JsonSerializable
{

    /**
     * method constructor.
     *
     * @param string $sName
     * @param string $sDescription
     * @param array  $aParam
     */
    public function __construct(string $sName="", string $sDescription="", $aParam=[])
    {
        $this->setName($sName);
        $this->setDescription($sDescription);
        $this->setParam($aParam);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'param' => serialize($this->getParam())
        ];
    }

    /**
     * @param $aData
     */
    public function fromArray($aData)
    {
        if(isset($aData['name'])) $this->setName($aData['name']);
        if(isset($aData['description'])) $this->setDescription($aData['description']);
        if(isset($aData['param'])) $this->setParam(unserialize($aData['param']));
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
        return $this->toArray();
    }


    /**
     * @return string
     */
    public function getId()
    {
        return md5($this->getName());
    }

    /**
     * @var string
     */
    protected $_sName="";
    /**
     * @var string
     */
    protected $_sDescription="";
    /**
     * @var array
     */
    protected $_aParam=[];

    /**
     * @param string $sValue
     */
    public function setName(string $sValue)
    {
        $this->_sName=$sValue;
    }

    /**
     * @return string
     */
    public function getName():string
    {
        return $this->_sName;
    }

    /**
     * @param string $sValue
     */
    public function setDescription(string $sValue)
    {
        $this->_sDescription=$sValue;
    }

    /**
     * @return string
     */
    public function getDescription():string
    {
        return $this->_sDescription;
    }

    /**
     * @return string[]
     */
    public function getParam()
    {
        return $this->_aParam;
    }

    /**
     * @param string[] $aParam
     */
    public function setParam($aParam)
    {
        $this->_aParam = $aParam;
    }
}