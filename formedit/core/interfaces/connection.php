<?php
namespace formedit\core\interfaces;

use formedit\core\project;

abstract class connection
{
    /**
     * @var array
     */
    protected $_aData = [];
    /**
     * @var string
     */
    protected $_sName = "";

    /**
     * @var \core\project
     */
    protected $_oProject = "";


    /**
     * connection constructor.
     * @param $oProject
     * @param $sName
     * @param $aData
     */
    public function __construct($oProject, $sName, $aData)
    {
        $this->_oProject = $oProject;
        $this->_sName=$sName;
        $this->_aData = $aData;
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
        return $this->_sName;
    }

    /**
     * @param $sKey
     * @param null $sDefault
     * @return mixed|null
     */
    public function getData($sKey, $sDefault=null)
    {
        $sValue=$sDefault;
        if(isset($this->_aData[$sKey]))
            $sValue = $this->_aData[$sKey];
        return $sValue;
    }


    /**
     * @return \core\project
     */
    public function getProject()
    {
        return $this->_oProject;
    }

    /**
     * retrun description of all tables, object
     * @return \string[]|null
     */
    public abstract function getObjects():?object ;

    /**
     * return an unique identifier of the description from a table, object (table name, class name with namespace...)
     *
     * @param object $oRow
     * @return string
    */
    public abstract function getObjectId(object $oRow):string;

    /**
     * return the name of a table, object which display to the user
     * @param object $oRow
     * @return string
     */
    public abstract function getObjectName(object $oRow):string;

    /**
     * return description of all columns, members of a table, object
     * @param string $sObject
     * @return object|null
     */
    public abstract function getObjectMembers(string $sObject):?object;

    /**
     * return an unique identifier of the description of the object member description (column name, protected member name...)
     * @param object $oRow
     * @return string
     */
    public abstract function getObjectMemberId(object $oRow):string;

    /**
     * retrun the name of a object member like a column name or the name which display to the user
     * @param object $oRow
     * @return string
     */
    public abstract function getObjectMemberName(object $oRow):string;

    /**
     * return all attributes of an member (column)
     * @param object $oRow
     * @return string
     */
    public abstract function getObjectMemberAttributes(object $oRow):string;
    /**
     * return form to configure the dataset (e.g. sql select, e.g. sql select count, e. g. select where)
     * @return string
     */
    public abstract function getDatasetPopupHtml():string;

    /**
     * return form to configure the dataset filters (e.g. where, having)
     * @return string
     */
    public abstract function getDatasetFilterPopupHtml():string;

    /**
     * return description of all columns, members of a dataset
     * @param object $oData
     * @return object|null
     */
    public abstract function getDatasetMembers(object $oData):?array;





    /**
     * @param \core\form $oForm
     * @param string $sId
     * @return array
     */
    public abstract function loadValues($oForm, $sId);

    /**
     * @param \core\form $oForm
     * @param array $aData
     */
    public abstract function saveValues($oForm, $aData);

    /**
     * @param \core\form $oForm
     * @param string $sId
     */
    public abstract function delete($oForm, $sId);

    /**
     * @param $sSql
     * @return object
     */
    public abstract function execute(string $sSql):?object;

    public abstract function getResult(object $oData):?object;

    /**
     * @param string $sSql
     * @return object|null
     */
    public abstract function getOne(string $sSql);

    public abstract function getResultCount(object $oData):?int;

}