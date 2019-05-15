<?php
namespace core;

class entity
{
    protected $_sTable="";

    protected $_sPrimaryColumn="";
    protected $_sPrimaryValue=null;

    protected $_aColumns=[];
    protected $_sValues=[];

    protected $_aSubEntites=[];

    public function setTable($sName)
    {
        $this->_sTable=$sName;
    }
    public function getTable()
    {
        return $this->_sTable;
    }


    public function setPrimaryColumn($sName)
    {
        $this->_sPrimaryColumn=$sName;
    }
    public function getPrimaryColumn()
    {
        return $this->_sPrimaryColumn;
    }


    public function setPrimaryValue($sValue)
    {
        $this->_sPrimaryValue=$sValue;
    }
    public function getPrimaryValue()
    {
        return $this->_sPrimaryValue;
    }


    public function addColumn($sName)
    {
        $this->_aColumns[]=$sName;
    }
    public function getColumns()
    {
        return $this->_aColumns;
    }


    public function setValue($sName, $sValue)
    {
        $this->_sValues[$sName] = $sValue;
    }
    public function getValues()
    {
        return $this->_sValues;
    }
}