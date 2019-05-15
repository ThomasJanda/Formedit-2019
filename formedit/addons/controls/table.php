<?php

namespace formedit\addons\controls;

class table extends \formedit\core\interfaces\control
{
    public function loadProperties()
    {
        $this->addProperty(new \formedit\core\property('System/Standard/Name', 'Table'));
        $this->addProperty(new \formedit\core\property('System/Standard/Group name', 'Table'));

        $this->addProperty(new \formedit\core\property('Connection/Dataset', '', \formedit\core\property::TYPE_dataset, "Data display in the table", null, 'Connection', 'Dataset'));
        $this->addProperty(new \formedit\core\property('Connection/Dataset members', '', \formedit\core\property::TYPE_datasetMembers, "Behavior of the data in the table", ['', 'Connection/Dataset'], 'Connection', 'Dataset members'));
        $this->addProperty(new \formedit\core\property('Connection/Dataset filter', '', \formedit\core\property::TYPE_datasetFilter, "Filter where the user can search in the table", ['', 'Connection/Dataset'], 'Connection', 'Dataset filter'));

        $this->addProperty(new \formedit\core\property('Navigation/New/Enable',1,\formedit\core\property::TYPE_checkbox));
        $this->addProperty(new \formedit\core\property('Navigation/New/Target',1,\formedit\core\property::TYPE_selectboxForms));
        $this->addProperty(new \formedit\core\property('Navigation/Edit/Enable',1,\formedit\core\property::TYPE_checkbox));
        $this->addProperty(new \formedit\core\property('Navigation/Edit/Target',1,\formedit\core\property::TYPE_selectboxForms));
        $this->addProperty(new \formedit\core\property('Navigation/Delete/Enable',1,\formedit\core\property::TYPE_checkbox));

        parent::loadProperties();

        $this->getProperty('System/Editor/Min. height')->setValue(60);
        $this->getProperty('System/Editor/Min. width')->setValue(80);
    }

    public function loadPropertiesAfter()
    {
        parent::loadPropertiesAfter(); // TODO: Change the autogenerated stub

        $aParam = $this->getProperty('Connection/Dataset members')->getParam();
        $aParam[0] = $this->getId();
        $this->getProperty('Connection/Dataset members')->setParam($aParam);
    }

    protected $_iOffset=30;

    protected $_oResult = null;
    protected $_iResultCount = 0;
    protected $_iPage=0;

    protected $_sSqlContent = "";
    protected $_sSqlCount = "";
    protected $_sSqlWhere = "";
    protected $_sSqlOrderBy = "";
    protected $_sSqlHaving = "";
    protected $_sSqlLimit = "";
    /**
     * Array
     * (
     * [oxid] => Array
     * (
     * [display] => oxid
     * [primary] => 1
     * [format] => hidden
     * [orderby_order] =>
     * [orderby_direction] => asc
     * [question_mark] =>
     * [width] =>
     * ),
     * [oxlname] => Array
     * (
     * [display] => Last name
     * [primary] => 0
     * [format] =>
     * [orderby_order] => 1
     * [orderby_direction] => asc
     * [question_mark] =>
     * [width] =>
     * )
     * )
     *
     * @var array
     */
    protected $_aDatasetMembers = [];

    protected function _reset()
    {
        $this->_oResult = null;
        $this->_iResultCount = 0;
        $this->_iPage=0;

        $this->_sSqlContent = "";
        $this->_sSqlCount = "";
        $this->_sSqlWhere = "";
        $this->_sSqlOrderBy = "";
        $this->_sSqlHaving = "";
        $this->_sSqlLimit = "";
    }

    protected function _initInterpreter()
    {
        $this->_reset();

        $oJson = json_decode($this->getProperty('Connection/Dataset')->getValue());
        $this->_sSqlContent = $oJson->sql_content;
        $this->_sSqlCount = $oJson->sql_count;
        $this->_sSqlWhere = $oJson->sql_where;

        $this->_aDatasetMembers = $this->getProperty('Connection/Dataset members')->getValueDataMember();

        $this->_generateWhere();
        $this->_generateHaving();
        $this->_generateOrderBy();
        $this->_generateLimit();
        $this->_getResultSet();
    }

    protected function _getResultSet()
    {
        if($oConnection = $this->getConnection())
        {
            $oData=new \stdClass();
            $oData->sql_content=$this->_sSqlContent;
            $oData->sql_where = $this->_sSqlWhere;
            $oData->sql_orderby = $this->_sSqlOrderBy;
            $oData->sql_having = $this->_sSqlHaving;
            $oData->sql_limit = $this->_sSqlLimit;
            $this->_oResult = $oConnection->getResult($oData);

            $oData=new \stdClass();
            $oData->sql_content=$this->_sSqlCount;
            $oData->sql_where = $this->_sSqlWhere;
            $oData->sql_orderby = "";
            $oData->sql_having = $this->_sSqlHaving;
            $oData->sql_limit = "";
            $this->_iResultCount = $oConnection->getResultCount($oData);

        }
    }

    protected function _generateLimit()
    {
        $iStart = $this->getConfig()->getRequestParameter($this->getId()."_LIMIT_START");
        $iOffset = $this->getConfig()->getRequestParameter($this->getId()."_LIMIT_OFFSET");
        if($iStart=="" || !is_numeric($iStart)) $iStart=0;
        if($iOffset=="" || !is_numeric($iOffset)) $iOffset=$this->_iOffset;

        $this->_iPage = $iStart / $iOffset;
        $this->_sSqlLimit = " LIMIT ".$iStart.", ".$iOffset;

    }

    protected function _generateHaving()
    {
        /* add all where to $this->_sSqlWhere */
        //$aWhere = $this->getConfig()->getRequestParameter($this->getId()."_WHERE");

        if ($this->_sSqlHaving != "")
            $this->_sSqlHaving = " HAVING " . $this->_sSqlHaving;
    }

    protected function _generateWhere()
    {
        /* add all where to $this->_sSqlWhere */
        //$aWhere = $this->getConfig()->getRequestParameter($this->getId()."_WHERE");

        if ($this->_sSqlWhere != "")
            $this->_sSqlWhere = " WHERE " . $this->_sSqlWhere;
    }

    protected function _generateOrderBy()
    {
        $sOrderByColumn = $this->getConfig()->getRequestParameter($this->getId()."_ORDERBY_COLUMN");
        $sOrderByDirection = $this->getConfig()->getRequestParameter($this->getId()."_ORDERBY_DIRECTION");
        if($sOrderByColumn!="" && $sOrderByDirection!="")
        {
            //correct dataset
            foreach ($this->_aDatasetMembers as $sColumn => $aData) {
                $this->_aDatasetMembers[$sColumn]['orderby_order']="";
                if($sColumn==$sOrderByColumn)
                {
                    $this->_aDatasetMembers[$sColumn]['orderby_order']='1';
                    $this->_aDatasetMembers[$sColumn]['orderby_direction']=$sOrderByDirection;
                }
            }
        }

        /* add all order by to $this->_sSqlOrderBy */
        if ($this->_sSqlOrderBy == "") {
            //search for standard order
            $aOrderBy = [];
            foreach ($this->_aDatasetMembers as $sColumn => $aData) {
                if ($aData['orderby_order'] != "") {
                    $aOrderBy[$aData['orderby_order']] = $sColumn . " " . $aData['orderby_direction'];
                }
            }
            if (count($aOrderBy) > 0) {
                sort($aOrderBy);
                $this->_sSqlOrderBy = implode(", ", $aOrderBy);
            }
        }
        if ($this->_sSqlOrderBy != "") {
            $this->_sSqlOrderBy = " ORDER BY " . $this->_sSqlOrderBy;
        }

    }

    public function getHtmlAjax()
    {
        $this->_initInterpreter();

        return $this->_getHtmlTable();
    }

    public function _getHtmlInterpreterDesktopBody()
    {
        $this->_initInterpreter();

        $sHtml = "";
        $sHtml.="<div style='overflow: scroll; position: absolute; left: 0; right:0; top:0; bottom:0; '>";
        $sHtml.='<div class="table_properties clearfix">';
        $sHtml.=$this->_getPages();
        $sHtml.=$this->_getFilter();
        $sHtml.='</div>';
        $sHtml.='<div class="table_data clearfix">';
        $sHtml.=$this->_getHtmlTable();
        $sHtml.='</div>';

        $sHtml.= "<pre>";
        $sHtml.= print_r($this->_oResult,true);
        $sHtml.= "</pre>";

        $sHtml.= "</div>";
        return $sHtml;
    }


    protected function _getHtmlTable()
    {
        $sHtml ="";

        if($this->getProperty('Navigation/New/Enable')->getValue()=="1" && $this->getProperty('Navigation/New/Target')->getValue()!="")
        {
            $sHtml.="<div><span title='new' class='navigation_new' data-target='".$this->getProperty('Navigation/New/Target')->getValue()."'>&#43;</span></div>";
        }
        
        $sHtml.="<table cellpadding='0' cellspacing='0' border='0'>";
        $sHtml.=$this->_getHtmlTableHeader();
        $sHtml.=$this->_getHtmlTableData();
        $sHtml.="</table>";
        return $sHtml;
    }
    protected function _getHtmlTableHeader()
    {
        $sHtml ="";
        $sHtml.="<thead><tr>";
        foreach($this->_oResult->columns as $oColumn)
        {
            $aFormat = $this->_aDatasetMembers[$oColumn->name];

            $sStyle="";
            if($aFormat['format']=="hidden")
                $sStyle.='display:none;';
            if($aFormat['format']=="number" || $aFormat['format']=="currency")
                $sStyle.='text-align:right;';
            if($aFormat['format']=="datetime" || $aFormat['format']=="date" || $aFormat['format']=="boolean")
                $sStyle.='text-align:center;';
            if($aFormat['width']!="")
                $sStyle.='width:'.$aFormat['width'];

            $sHtml.='<th style="'.$sStyle.'">';
            $sHtml.=$aFormat['display'];

            if($aFormat['question_mark']!="")
                $sHtml.="<span class='question_mark' title='".$aFormat['question_mark']."'>?</span>";

            if($aFormat['can_order']=="1")
            {
                $sHtml.="<span class='orderby'>
                    <span data-orderby_column='".$oColumn->name."' data-orderby_direction='asc'  class='orderby_direction_asc  ".($aFormat['orderby_order']!="" && $aFormat['orderby_direction']=="asc"?'selected':'')."' >&#9650;</span>
                    <span data-orderby_column='".$oColumn->name."' data-orderby_direction='desc' class='orderby_direction_desc ".($aFormat['orderby_order']!="" && $aFormat['orderby_direction']=="desc"?'selected':'')."'>&#9660;</span>
                </span>";
            }

            $sHtml.='</th>';
        }

        if($this->getProperty('Navigation/Delete/Enable')->getValue())
        {
            $sHtml .= "<th>&nbsp;</th>";
        }

        $sHtml.="</tr></thead>";
        return $sHtml;
    }
    protected function _getHtmlTableData()
    {
        //which is the primary column?
        $sColumnPrimary = "";
        foreach($this->_aDatasetMembers as $sColumn=>$aFormat)
        {
            if($aFormat['primary']=="1")
                $sColumnPrimary=$sColumn;
        }

        $bEdit = false;
        if($this->getProperty('Navigation/Edit/Enable')->getValue() && $this->getProperty('Navigation/Edit/Target')->getValue()!="")
            $bEdit=$this->getProperty('Navigation/Edit/Target')->getValue();

        $sHtml ="<tbody>";
        foreach($this->_oResult->data as $oRow)
        {
            $sPrimary = "";
            if($sColumnPrimary!="")
                $sPrimary=$oRow->{$sColumnPrimary};

            $sHtml.='<tr data-primary="' . $sPrimary . '" '.($bEdit!==false?'data-target="'.$bEdit.'"':'').' class="'.($bEdit!==false?'navigation_edit':'').'">';
            foreach($this->_oResult->columns as $oColumn)
            {
                $aFormat = $this->_aDatasetMembers[$oColumn->name];
                $sValue = $oRow->{$oColumn->name};

                $sStyle="";
                if($aFormat['format']=="hidden")
                    $sStyle.='display:none;';
                if($aFormat['format']=="number" || $aFormat['format']=="currency")
                    $sStyle.='text-align:right;';
                if($aFormat['format']=="datetime" || $aFormat['format']=="date" || $aFormat['format']=="boolean")
                    $sStyle.='text-align:center;';
                if($aFormat['width']!="")
                    $sStyle.='width:'.$aFormat['width'];

                if($aFormat['format']=="boolean")
                {
                    if($sValue=="0")
                        $sValue="&#9744;";
                    elseif($sValue=="1")
                        $sValue="&#9745;";
                }
                elseif($aFormat['format']=="currency")
                {
                    $sValue = money_format('%i', $sValue);
                }

                $sHtml .= '<td 
                    data-value="'.$oRow->{$oColumn->name}.'" style="'.$sStyle.'">' .$sValue. '</td>';
            }

            if($this->getProperty('Navigation/Delete/Enable')->getValue())
            {
                $sHtml .= "<td class='navigation'><span class='navigation_delete' title='delete'>&#x2212;</span></td>";
            }

            $sHtml.="</tr>";
        }
        $sHtml.="</tbody>";

        return $sHtml;
    }

    protected function _getPages()
    {
        $iMax = $this->_iResultCount;
        $iOffset = $this->_iOffset;
        $iPages = floor($iMax / $iOffset);

        $sHtml="";

        if($iPages>0)
        {
            $sHtml.="<div class='pages'>
                <span>Page</span>
                <select>";
            for($x=0;$x<=$iPages;$x++)
            {
                $sHtml.='<option '.($this->_iPage==$x?'selected':'').' value="'.($x*$iOffset).'">'.($x+1).'</option>';
            }
            $sHtml.= '</select>
                </div>';
        }

        return $sHtml;
    }

    protected function _getFilter()
    {
        $sHtml="";

        return $sHtml;
    }
}