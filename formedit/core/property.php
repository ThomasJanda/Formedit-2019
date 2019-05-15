<?php
namespace formedit\core;

class property implements \JsonSerializable
{
    const TYPE_label=0;
    const TYPE_textbox=1;
    const TYPE_checkbox=2;
    const TYPE_textarea=3;
    const TYPE_description=4;
    const TYPE_selectbox=5;
    const TYPE_labelcolor=6;
    const TYPE_labelparent=7;
    const TYPE_hidden=8;
    const TYPE_textboxInteger=9;
    const TYPE_textboxIntegerAndNegative=10;
    const TYPE_selectboxSwitch=11;
    const TYPE_textboxPopulate=12;
    const TYPE_selectboxForms=13;
    const TYPE_htmlarea=14;
    const TYPE_selectboxObjects=15;
    const TYPE_selectboxObjectMembers=16;
    const TYPE_dataset=17;
    const TYPE_datasetMembers=18;
    const TYPE_datasetFilter=19;

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'param' => serialize($this->getParam())
        ];
    }
    public function fromArray($aData)
    {
        if(isset($aData['name'])) $this->setName($aData['name']);
        if(isset($aData['value'])) $this->setValue($aData['value']);
        if(isset($aData['type'])) $this->setType($aData['type']);
        if(isset($aData['description'])) $this->setDescription($aData['description']);
        if(isset($aData['param'])) $this->setParam(unserialize($aData['param']));
    }


    public function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * @var string
     */
    protected $_sGroup="";
    /**
     * @var string
     */
    protected $_sSubGroup="";
    /**
     * @var string
     */
    protected $_sName="";
    /**
     * @var null
     */
    protected $_sValue=null;

    /**
     * @var null
     */
    protected $_sDefaultValue=null;

    /**
     * @var string
     */
    protected $_iType=property::TYPE_label;

    /**
     * @var string
     */
    protected $_sDescription="";

    /**
     * @var array
     */
    protected $_aParam=[];

    /**
     * @var string
     */
    protected $_sKey = "";

    /**
     * @var string
     */
    protected $_sGroupKey = "";

    /**
     * property constructor.
     * @param string $sName
     * @param null $oValue
     * @param int $sType
     * @param string $sDescription
     * @param string[] $aParam
     * @param string $sGroupKey
     * @param string $sKey
     */
    public function __construct($sName="", $oValue=null, $sType=property::TYPE_label, $sDescription="", $aParam=[], $sGroupKey="", $sKey="")
    {
        $this->setName($sName);
        $this->setValue($oValue);
        $this->_sDefaultValue=$oValue;
        $this->setType($sType);
        $this->setDescription($sDescription);
        $this->setParam($aParam);

        $this->setGroupKey($sGroupKey);
        $this->setKey($sKey);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * @param $sName
     */
    public function setName($sName)
    {
        $this->_sName=$sName;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->_sKey;
    }

    /**
     * @param $sKey
     */
    public function setKey($sKey)
    {
        $this->_sKey=$sKey;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->_sValue;
    }

    public function getValueDataMember()
    {
        if($this->getType()!=property::TYPE_datasetMembers)
            return $this->getValue();

        $aDataSetMembers=[];
        $aJson = json_decode($this->getValue(), true);
        if($aJson)
        {
            foreach ($aJson as $sKey => $sValue) {
                $sType = substr($sKey, 0, strpos($sKey, "["));
                $sColumn = trim(substr($sKey, strpos($sKey, "[")), "[]");
                $aDataSetMembers[$sColumn][$sType] = $sValue;
            }
        }
        return $aDataSetMembers;
    }

    /**
     * @param $sValue
     */
    public function setValue($sValue)
    {
        $this->_sValue=$sValue;
    }

    public function resetValue($bTryToConvert=true)
    {
        $oValueNew = $this->_sDefaultValue;
        if($bTryToConvert)
        {
            if($this->getType()==property::TYPE_datasetMembers)
            {
                //if dataset was saved because some format changes, it is not necessary to delete the complete members.
                //try to reuse if possible
                $aDatasetMembers = $this->getValueDataMember();
                if($aDatasetMembers) {

                    $aValueNew=[];

                    $oConfig = \formedit\inc\config::getInstance();
                    $aParam = $this->getParam();
                    $oProject = $oConfig->getCurrentProject();
                    $oConnection = $oProject->getConnection();
                    if ($oConnection) {
                        //id can be a formid or a control id
                        $sId = $aParam[0];
                        $oObject = $oProject->getControlById($sId);

                        if ($oObject) {
                            $sObject = $oObject->getProperty($aParam[1])->getValue();
                            if ($sObject != "") {
                                $oObject = json_decode($sObject);
                                if ($aMembers = $oConnection->getDatasetMembers($oObject)) {
                                    foreach ($aMembers as $oMember) {
                                        $sId = $oConnection->getObjectMemberId($oMember);

                                        if(isset($aDatasetMembers[$sId]))
                                        {
                                            $aValueNew['display['.$sId.']']=$aDatasetMembers[$sId]['display'];
                                            $aValueNew['primary['.$sId.']']=$aDatasetMembers[$sId]['primary'];
                                            $aValueNew['format['.$sId.']']=$aDatasetMembers[$sId]['format'];
                                            $aValueNew['can_order['.$sId.']']=$aDatasetMembers[$sId]['can_order'];
                                            $aValueNew['orderby_order['.$sId.']']=$aDatasetMembers[$sId]['orderby_order'];
                                            $aValueNew['orderby_direction['.$sId.']']=$aDatasetMembers[$sId]['orderby_direction'];
                                            $aValueNew['question_mark['.$sId.']']=$aDatasetMembers[$sId]['question_mark'];
                                            $aValueNew['width['.$sId.']']=$aDatasetMembers[$sId]['width'];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if(count($aValueNew)>0)
                    {
                        //new values found
                        $oValueNew=json_encode($aValueNew);
                    }
                }
            }
        }

        $this->setValue($oValueNew);
    }

    /**
     * @param $sKey
     */
    public function setGroupKey($sKey)
    {
        $this->_sGroupKey=$sKey;
    }

    /**
     * @return null
     */
    public function getGroupKey()
    {
        return $this->_sGroupKey;
    }

    /**
     * @param int $iType
     */
    public function setType($iType)
    {
        $this->_iType = $iType;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_iType;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        $fooClass = new \ReflectionClass ( '\formedit\core\property' );
        $constants = $fooClass->getConstants();

        $constName = "";
        foreach ( $constants as $name => $value )
        {
            if ( $value == $this->getType() )
            {
                $constName = $name;
                break;
            }
        }
        return $constName;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return basename($this->getName());
    }

    /**
     * @param string $sTitle
     */
    public function setTitle($sTitle)
    {
        $this->_sTitle = $sTitle;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_sDescription;
    }

    /**
     * @param $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->_sDescription = $sDescription;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $sHtml="";
        if($this->getType()==property::TYPE_label)
        {
            $sHtml.=$this->_getHtmlLabel();
        }
        elseif($this->getType()==property::TYPE_textbox)
        {
            $sHtml.=$this->_getHtmlTextbox();
        }
        elseif($this->getType()==property::TYPE_textboxPopulate)
        {
            $sHtml.=$this->_getHtmlTextboxPopulate();
        }
        elseif($this->getType()==property::TYPE_textboxInteger)
        {
            $sHtml.=$this->_getHtmlTextboxInteger();
        }
        elseif($this->getType()==property::TYPE_textboxIntegerAndNegative)
        {
            $sHtml.=$this->_getHtmlTextboxIntegerAndNegative();
        }
        elseif($this->getType()==property::TYPE_checkbox)
        {
            $sHtml.=$this->_getHtmlCheckbox();
        }
        elseif($this->getType()==property::TYPE_textarea)
        {
            $sHtml.=$this->_getHtmlTextarea();
        }
        elseif($this->getType()==property::TYPE_htmlarea)
        {
            $sHtml.=$this->_getHtmlHtmlarea();
        }
        elseif($this->getType()==property::TYPE_description)
        {
            $sHtml.=$this->_getHtmlDescription();
        }
        elseif($this->getType()==property::TYPE_selectbox)
        {
            $sHtml.=$this->_getHtmlSelectbox();
        }
        elseif($this->getType()==property::TYPE_selectboxSwitch)
        {
            $sHtml.=$this->_getHtmlSelectboxSwitch();
        }
        elseif($this->getType()==property::TYPE_labelcolor)
        {
            $sHtml.=$this->_getHtmlLabelColor();
        }
        elseif($this->getType()==property::TYPE_labelparent)
        {
            $sHtml.=$this->_getHtmlLabelParent();
        }
        elseif($this->getType()==property::TYPE_selectboxForms)
        {
            $sHtml.=$this->_getHtmlSelectboxForms();
        }
        elseif($this->getType()==property::TYPE_selectboxObjects)
        {
            $sHtml.=$this->_getHtmlSelectboxObjects();
        }
        elseif($this->getType()==property::TYPE_selectboxObjectMembers)
        {
            $sHtml.=$this->_getHtmlSelectboxObjectMembers();
        }
        elseif($this->getType()==property::TYPE_dataset)
        {
            $sHtml.=$this->_getHtmlDataset();
        }
        elseif($this->getType()==property::TYPE_datasetMembers)
        {
            $sHtml.=$this->_getHtmlDatasetMembers();
        }
        elseif($this->getType()==property::TYPE_datasetFilter)
        {
            $sHtml.=$this->_getHtmlDatasetFilter();
        }
        return $sHtml;
    }

    protected function _getTooltip()
    {
        $sRet="";
        if(trim($this->getDescription())!="")
            $sRet = "<span class='tooltip' data-tippy='".$this->getDescription()."'>?</span>";
        return $sRet;
    }

    protected function _getHtmlRowStart()
    {
        $sHtml = "<tr 
            data-groupkey='".$this->getGroupKey()."' 
            data-key='".$this->getKey()."' 
            data-searchtext='".strtolower($this->getName())."' 
            class='".$this->getTypeName()."'
            data-id='".$this->getId()."'
            >";
        return $sHtml;
    }

    protected function _getHtmlCheckbox()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><input data-name_property='".$this->getName()."'  type='checkbox' name='".$this->getId()."' value='1' ".($this->getValue()?'checked':'')."></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlSelectbox()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><select data-name_property='".$this->getName()."'  name='".$this->getId()."'>";
        foreach($this->getParam() as $sKey => $sValue)
        {
            $sHtml.='<option value="'.$sKey.'" '.($this->getValue()==$sKey?'selected':'').'>'.$sValue.'</option>';
        }
        $sHtml.="</select></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlSelectboxForms()
    {
        $oConfig = \formedit\inc\config::getInstance();
        $oProject = $oConfig->getCurrentProject();

        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><select data-name_property='".$this->getName()."'  name='".$this->getId()."'>";
        $sHtml.='<option value="#STARTPAGE#">#STARTPAGE#</option>';
        /**
         * @var \core\form $oForm
         */
        foreach($oProject->getChildren() as $oForm)
        {
            $sHtml.='<option value="'.$oForm->getId().'" '.($this->getValue()==$oForm->getId()?'selected':'').'>'.$oForm->getTitle().'</option>';
        }
        $sHtml.="</select></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlSelectboxObjects()
    {
        $oConfig = \formedit\inc\config::getInstance();
        $oProject = $oConfig->getCurrentProject();

        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><select data-name_property='".$this->getName()."'  name='".$this->getId()."'>";
        $sHtml.='<option value=""></option>';

        if($oConnecton = $oProject->getConnection())
        {
            if($oResult = $oConnecton->getObjects())
            {
                foreach($oResult->data as $oRow)
                {
                    $sName = $oConnecton->getObjectName($oRow);
                    $sId = $oConnecton->getObjectId($oRow);
                    $sHtml.='<option value="'.$sId.'" '.($this->getValue()==$sId?'selected':'').'>'.$sName.'</option>';
                }
            }
        }

        $sHtml.="</select></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlSelectboxObjectMembers()
    {
        $oConfig = \formedit\inc\config::getInstance();
        $oProject = $oConfig->getCurrentProject();

        $aParam = $this->getParam();

        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><select data-name_property='".$this->getName()."'  name='".$this->getId()."'>";
        $sHtml.='<option value=""></option>';

        if(count($aParam) > 0 && $oConnecton = $oProject->getConnection())
        {
            //id can be a formid or a control id
            $sId = $aParam[0];
            $oObject = $oProject->getFormById($sId);
            if($oObject===null)
                $oObject = $oProject->getFormByControlId($sId);

            if($oObject)
            {
                $sObject = $oObject->getProperty($aParam[1])->getValue();
                if($sObject!="")
                {
                    if($oResult = $oConnecton->getObjectMembers($sObject))
                    {
                        foreach($oResult->data as $oRow)
                        {
                            $sName = $oConnecton->getObjectMemberName($oRow);
                            $sAttributes = $oConnecton->getObjectMemberAttributes($oRow);
                            $sId = $oConnecton->getObjectMemberId($oRow);
                            $sHtml.='<option value="'.$sId.'" '.($this->getValue()==$sId?'selected':'').'>'.$sName.($sAttributes!=""?" (".$sAttributes.")":"").'</option>';
                        }
                    }
                }
            }
        }

        $sHtml.="</select></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlDataset()
    {
        $oConfig = \formedit\inc\config::getInstance();
        $oProject = $oConfig->getCurrentProject();
        $aParam = $this->getParam();

        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td>";

        $oProject = $oConfig->getCurrentProject();
        $oConnection = $oProject->getConnection();
        if($oConnection)
        {
            $sHtml.="<div class='property_popup' data-open='0' id='property_popup_".$this->getId()."'>
                <div class='property_popup_title'>Dataset</div>
                <div class='property_popup_close'></div>
                <div class='property_popup_content'>
                    <p>The values depends on the connector you choose for the project</p>
                    ".$oConnection->getDatasetPopupHtml()."
                </div>
                <div class='property_popup_buttons'><button class='property_popup_buttons_save' type='button'>Save</button><button class='property_popup_buttons_cancel' type='button'>Cancel</button></div>
            </div>
            <button type='button' class='property_popup_button' data-popup='property_popup_".$this->getId()."'>Dataset</button>
            <textarea data-name_property='".$this->getName()."'  name='".$this->getId()."' style='display:none; '>".$this->getValue()."</textarea>";
        }
        else
        {
            $sHtml.="Select a connector in the project properties";
        }

        $sHtml.="</td></tr>";

        return $sHtml;
    }
    protected function _getHtmlDatasetMembers()
    {
        $oConfig = \formedit\inc\config::getInstance();
        $aParam = $this->getParam();

        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td>";

        $oProject = $oConfig->getCurrentProject();
        $oConnection = $oProject->getConnection();
        if($oConnection)
        {
            //id can be a formid or a control id
            $sId = $aParam[0];
            $oObject = $oProject->getControlById($sId);

            $sPopupHtml="";
            if($oObject) {
                $sObject = $oObject->getProperty($aParam[1])->getValue();
                if ($sObject != "") {
                    $oObject = json_decode($sObject);
                    if($aMembers = $oConnection->getDatasetMembers($oObject))
                    {
                        $sPopupHtml="<table border='1' cellspacing='0' cellpadding='2'>
                        <tr>
                            <th>Name</th>
                            <th>Attribute</th>
                            <th>Display</th>
                            <th>Primary key</th>
                            <th>Format</th>
                            <th>User can order this column?</th>
                            <th>Order by (position/direction)</th>
                            <th>Callback</th>
                            <th>Question mark</th>
                            <th>Width (px/%)</th>
                            </tr>";
                        foreach($aMembers as $oMember)
                        {
                            $sId = $oConnection->getObjectMemberId($oMember);
                            $sPopupHtml.="<tr>
                            <th>".$oConnection->getObjectMemberName($oMember)."</th>
                            <td>".$oConnection->getObjectMemberAttributes($oMember)."</td>
                            <td><input type='text' data-name='display[$sId]' value='".$oConnection->getObjectMemberName($oMember)."' ></td>
                            <td><input type='checkbox' data-name='primary[$sId]' value='1' ></td>
                            <td>
                                <select data-name='format[$sId]'>
                                    <option value=''>Text</option>
                                    <option value='number'>Number</option>
                                    <option value='currency'>Currency</option>
                                    <option value='date'>Date</option>
                                    <option value='datetime'>Datetime</option>
                                    <option value='hidden'>Hidden</option>
                                    <option value='boolean'>Boolean</option>
                                </select>
                            </td>
                            <td><input type='checkbox' data-name='can_order[$sId]' value='1' checked></td>
                            <td>
                                <select data-name='orderby_order[$sId]' class='half'>
                                    <option value=''>-</option>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='4'>4</option>
                                    <option value='5'>5</option>
                                    <option value='6'>6</option>
                                    <option value='7'>7</option>
                                    <option value='8'>8</option>
                                    <option value='9'>9</option>
                                </select>
                                <select data-name='orderby_direction[$sId]' class='half'>
                                    <option value='asc'>ASC</option>
                                    <option value='DESC'>DESC</option>
                                </select>
                            </td>
                            <td>have to implement</td>
                            <td><input type='text' data-name='question_mark[$sId]' value=''></td>
                            <td><input type='text' data-name='width[$sId]' value=''></td>
                            </tr>";
                        }
                        $sPopupHtml.="</table>";
                    }
                }
            }
            if($sPopupHtml!="")
            {
                $sHtml.="<div class='property_popup' data-open='0' id='property_popup_".$this->getId()."'>
                    <div class='property_popup_title'>Dataset members</div>
                    <div class='property_popup_close'></div>
                    <div class='property_popup_content'>
                        <p>The values depends on the connector you choose for the project</p>
                        $sPopupHtml
                    </div>
                    <div class='property_popup_buttons'><button class='property_popup_buttons_save' type='button'>Save</button><button class='property_popup_buttons_cancel' type='button'>Cancel</button></div>
                </div>
                <button type='button' class='property_popup_button' data-popup='property_popup_".$this->getId()."'>Dataset members</button>
                <textarea data-name_property='".$this->getName()."'  name='".$this->getId()."' style='display:none; '>".$this->getValue()."</textarea>";
            }
            else
            {
                $sHtml.="Configure dataset first";
            }
        }
        else
        {
            $sHtml.="Select a connector in the project properties";
        }

        $sHtml.="</td></tr>";

        return $sHtml;
    }
    protected function _getHtmlDatasetFilter()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td>";

        $oConfig = \formedit\inc\config::getInstance();
        $oProject = $oConfig->getCurrentProject();
        $oConnection = $oProject->getConnection();
        if($oConnection)
        {
            $sHtml.="<div class='property_popup' data-open='0' id='property_popup_".$this->getId()."'>
                <div class='property_popup_title'>Dataset filter</div>
                <div class='property_popup_close'></div>
                <div class='property_popup_content'>
                    <p>The values depends on the connector you choose for the project</p>
                    ".$oConnection->getDatasetFilterPopupHtml()."
                </div>
                <div class='property_popup_buttons'><button class='property_popup_buttons_save' type='button'>Save</button><button class='property_popup_buttons_cancel' type='button'>Cancel</button></div>
            </div>
            <button type='button' class='property_popup_button' data-popup='property_popup_".$this->getId()."'>Dataset filter</button>
            <textarea data-name_property='".$this->getName()."'  name='".$this->getId()."' style='display:none; '>".$this->getValue()."</textarea>";
        }
        else
        {
            $sHtml.="Select a connector in the project properties";
        }

        return $sHtml;
    }

    protected function _getHtmlSelectboxSwitch()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><select data-name_property='".$this->getName()."'  name='".$this->getId()."'>";
        foreach($this->getParam() as $sKey => $sValue)
        {
            $sHtml.='<option value="'.$sKey.'" '.($this->getValue()==$sKey?'selected':'').'>'.$sValue.'</option>';
        }
        $sHtml.="</select></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlTextarea()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th colspan='2'>".$this->getTitle()." ".$this->_getTooltip()."</th>
        </tr>
        <tr data-searchtext='".strtolower($this->getName())."' class='".$this->getTypeName()."'>
        <td colspan='2'><textarea data-name_property='".$this->getName()."' name='".$this->getId()."'>".$this->getValue()."</textarea></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlHtmlarea()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th colspan='2'>".$this->getTitle()." ".$this->_getTooltip()."</th>
        </tr>
        <tr data-searchtext='".strtolower($this->getName())."' class='".$this->getTypeName()."'>
        <td colspan='2'><textarea data-name_property='".$this->getName()."' name='".$this->getId()."'>".$this->getValue()."</textarea></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlDescription()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th colspan='2'>".$this->getTitle()."</th>
        </tr>";
        $sHtml.= $this->_getHtmlRowStart();
        $sHtml.= "<td colspan='2'>".$this->getDescription()."</td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlTextbox()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><input type='text' data-name_property='".$this->getName()."'  name='".$this->getId()."' value='".$this->getValue()."'></td>
        </tr>";
        return $sHtml;
    }
    protected function _getHtmlTextboxPopulate()
    {
        $sData = json_encode($this->getParam(),JSON_PRETTY_PRINT);
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><input type='text' data-populate_to='".$sData."' data-name_property='".$this->getName()."'  name='".$this->getId()."' value='".$this->getValue()."'></td>
        </tr>";
        return $sHtml;
    }
    protected function _getHtmlTextboxInteger()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><input type='text' data-name_property='".$this->getName()."'  name='".$this->getId()."' value='".$this->getValue()."'></td>
        </tr>";
        return $sHtml;
    }
    protected function _getHtmlTextboxIntegerAndNegative()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><input type='text' data-name_property='".$this->getName()."'  name='".$this->getId()."' value='".$this->getValue()."'></td>
        </tr>";
        return $sHtml;
    }

    protected function _getHtmlLabel()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>";

        $sValue = $this->getValue();
        if(is_bool($sValue))
        {
            $sValue = ($sValue?'Yes':'No');
        }

        $sHtml.= "<td>".$sValue."</td></tr>";

        return $sHtml;
    }

    protected function _getHtmlLabelParent()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>";

        //find parent
        $sTmp="UNKNOWN";
        if($oProject = \formedit\inc\config::getInstance()->getCurrentProject())
        {
            $sValue = $this->getValue();
            if($oForm = $oProject->getChild($sValue))
            {
                $sTmp = $oForm->getName(). " (".$oForm->getId().")";
            }
            else
            {
                /**
                 * @var \core\form $oForm
                 */
                if($oForm = \formedit\inc\config::getInstance()->getCurrentForm())
                {
                    //main control separate from subcontainer
                    $sMainId = explode("_", $sValue)[0];

                    /**
                     * @var \formedit\core\interfaces\control $oControl
                     */
                    if($oControl = $oForm->getChild($sMainId))
                    {
                        $sTmp = $oControl->getHtmlEditorDesktopText()." (".$oControl->getId().")";
                    }
                }
            }
        }
        $sValue = $sTmp;

        $sHtml.= "<td>".$sValue."</td></tr>";

        return $sHtml;
    }

    protected function _getHtmlLabelColor()
    {
        $sHtml = $this->_getHtmlRowStart();
        $sHtml.= "<th>".$this->getTitle()." ".$this->_getTooltip()."</th>
        <td><div style='display: inline-block; width:10px; height:10px; background-color:".$this->getValue().";'>&nbsp;</div></td>
        </tr>";
        return $sHtml;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return md5($this->getName());
    }
}